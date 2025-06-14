<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$user_id = getCurrentUserId();

if (!$cart_id || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item or quantity.']);
    exit();
}

try {
    // Verify cart item belongs to user and get event info
    $stmt = $pdo->prepare("
        SELECT c.id, c.event_id, e.price, e.available_tickets 
        FROM cart c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$cart_id, $user_id]);
    $cart_item = $stmt->fetch();
    
    if (!$cart_item) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
        exit();
    }
    
    // Check if enough tickets are available
    if ($quantity > $cart_item['available_tickets']) {
        echo json_encode(['success' => false, 'message' => 'Not enough tickets available.']);
        exit();
    }
    
    // Update cart item
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);
    
    // Calculate new subtotal
    $subtotal = $quantity * $cart_item['price'];
    
    // Calculate new total
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * e.price) as total 
        FROM cart c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    $total = $result['total'] ? $result['total'] : 0;
    
    echo json_encode([
        'success' => true,
        'subtotal' => number_format($subtotal, 0) . ' CFA',
        'total' => number_format($total, 0) . ' CFA'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
