<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/qr_generator.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$booking_id) {
    http_response_code(404);
    exit('Invalid booking ID');
}

try {
    // Get booking details
    $stmt = $pdo->prepare("
        SELECT b.*, e.name
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.id = ? AND b.user_id = ? AND b.booking_status = 'confirmed'
    ");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        http_response_code(404);
        exit('Booking not found');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database error');
}

// Generate QR code URL
$qrCodeUrl = QRCodeGenerator::generateBookingQR($booking);

// Get the QR code image data
$imageData = @file_get_contents($qrCodeUrl);

if ($imageData === false) {
    // Try backup provider
    $backupUrl = QRCodeGenerator::generateQRCodeBackup(json_encode([
        'booking_ref' => $booking['booking_reference'],
        'event_id' => $booking['event_id'],
        'user_id' => $booking['user_id'],
        'quantity' => $booking['quantity'],
        'amount' => $booking['total_amount'],
        'date' => date('Y-m-d H:i:s')
    ]), 200);
    
    $imageData = @file_get_contents($backupUrl);
    
    if ($imageData === false) {
        http_response_code(500);
        exit('Failed to generate QR code');
    }
}

// Set headers for image download
$filename = 'qr_code_' . $booking['booking_reference'] . '.png';
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($imageData));
header('Cache-Control: no-cache, must-revalidate');

// Output the image
echo $imageData;
exit();
?>
