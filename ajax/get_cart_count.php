<?php
require_once '../config/database.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit();
}

$user_id = getCurrentUserId();

try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    $count = $result['total_items'] ? (int)$result['total_items'] : 0;
    
    echo json_encode(['success' => true, 'count' => $count]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'count' => 0]);
}
?>
