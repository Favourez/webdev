<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/qr_generator.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$format = isset($_GET['format']) ? $_GET['format'] : 'html'; // html or pdf

if (!$booking_id) {
    $_SESSION['error_message'] = 'Invalid booking ID.';
    header('Location: dashboard.php');
    exit();
}

try {
    // Get booking details with event information
    $stmt = $pdo->prepare("
        SELECT b.*, e.name, e.description, e.date, e.time, e.venue, e.location, e.organizer, e.organizer_contact
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.id = ? AND b.user_id = ? AND b.booking_status = 'confirmed'
    ");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $_SESSION['error_message'] = 'Booking not found or access denied.';
        header('Location: dashboard.php');
        exit();
    }

    // Check if event is upcoming
    $event_date = strtotime($booking['date']);
    $current_date = strtotime('today');

    if ($event_date < $current_date) {
        $_SESSION['error_message'] = 'Tickets can only be downloaded for upcoming events.';
        header('Location: dashboard.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error occurred.';
    header('Location: dashboard.php');
    exit();
}

// Generate receipt HTML (simpler format for download)
$receiptHTML = generateReceiptHTML($booking);

// Set headers for download
$filename = 'receipt_' . $booking['booking_reference'] . '.html';

// Force download headers
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($receiptHTML));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Output the receipt
echo $receiptHTML;
exit();

// Function to generate simple receipt HTML
function generateReceiptHTML($booking) {
    return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - ' . htmlspecialchars($booking['booking_reference']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .receipt { border: 2px solid #000; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 15px; }
        .section { margin-bottom: 15px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { font-size: 18px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; }
        @media print { body { margin: 0; padding: 10px; } }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>EVENTBOOK CAMEROON</h2>
            <p>Event Booking Receipt</p>
        </div>

        <div class="section">
            <div class="row"><span>Receipt #:</span><span>' . htmlspecialchars($booking['booking_reference']) . '</span></div>
            <div class="row"><span>Date:</span><span>' . date('d/m/Y H:i', strtotime($booking['created_at'])) . '</span></div>
        </div>

        <div class="section">
            <div class="row"><span>Customer:</span><span>' . htmlspecialchars($booking['attendee_name']) . '</span></div>
            <div class="row"><span>Email:</span><span>' . htmlspecialchars($booking['attendee_email']) . '</span></div>
        </div>

        <div class="section">
            <div class="row"><span>Event:</span><span>' . htmlspecialchars($booking['name']) . '</span></div>
            <div class="row"><span>Date:</span><span>' . date('d/m/Y', strtotime($booking['date'])) . '</span></div>
            <div class="row"><span>Time:</span><span>' . date('H:i', strtotime($booking['time'])) . '</span></div>
            <div class="row"><span>Venue:</span><span>' . htmlspecialchars($booking['venue']) . '</span></div>
            <div class="row"><span>Location:</span><span>' . htmlspecialchars($booking['location']) . '</span></div>
        </div>

        <div class="section">
            <div class="row"><span>Tickets:</span><span>' . $booking['quantity'] . '</span></div>
            <div class="row total"><span>TOTAL PAID:</span><span>' . number_format($booking['total_amount'], 0) . ' CFA</span></div>
        </div>

        <div class="section" style="text-align: center; margin-top: 20px;">
            <p><strong>VALID TICKET</strong></p>
            <p>Present this receipt at event entrance</p>
            <p>Thank you for choosing EventBook!</p>
        </div>
    </div>
</body>
</html>';
}
?>
