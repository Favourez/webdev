<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$user_id = getCurrentUserId();

// Validate input
if (!$event_id || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid event or quantity.']);
    exit();
}

try {
    // Check if event exists and is available
    $stmt = $pdo->prepare("SELECT id, name, price, available_tickets FROM events WHERE id = ? AND status = 'active'");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        echo json_encode(['success' => false, 'message' => 'Event not found or not available.']);
        exit();
    }
    
    // Check if enough tickets are available
    if ($event['available_tickets'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough tickets available.']);
        exit();
    }
    
    // Check if item already exists in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$user_id, $event_id]);
    $existing_item = $stmt->fetch();
    
    if ($existing_item) {
        // Update existing cart item
        $new_quantity = $existing_item['quantity'] + $quantity;
        
        // Check if total quantity exceeds available tickets
        if ($new_quantity > $event['available_tickets']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more tickets. Not enough available.']);
            exit();
        }
        
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $existing_item['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully!']);
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, event_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $event_id, $quantity]);
        
        echo json_encode(['success' => true, 'message' => 'Item added to cart successfully!']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
