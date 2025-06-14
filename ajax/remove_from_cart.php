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
$user_id = getCurrentUserId();

if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item.']);
    exit();
}

try {
    // Verify cart item belongs to user
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
        exit();
    }
    
    // Remove cart item
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);
    
    // Calculate new total
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * e.price) as total, COUNT(*) as item_count
        FROM cart c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    $total = $result['total'] ? $result['total'] : 0;
    $cart_empty = $result['item_count'] == 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart.',
        'total' => number_format($total, 0) . ' CFA',
        'cart_empty' => $cart_empty
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
