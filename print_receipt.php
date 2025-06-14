<?php
require_once 'config/database.php';

// Get booking reference from QR code
$booking_ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';

if (!$booking_ref) {
    die('Invalid booking reference');
}

try {
    // Get booking details with event information
    $stmt = $pdo->prepare("
        SELECT b.*, e.name, e.description, e.date, e.time, e.venue, e.location, e.organizer, e.organizer_contact
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.booking_reference = ? AND b.booking_status = 'confirmed'
    ");
    $stmt->execute([$booking_ref]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        die('Booking not found or invalid');
    }
    
} catch (PDOException $e) {
    die('Database error occurred');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo htmlspecialchars($booking['booking_reference']); ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            color: black;
        }
        .receipt {
            border: 2px solid #000;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        .value {
            text-align: right;
        }
        .total-section {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 10px 0;
            margin: 15px 0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        .qr-info {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #000;
        }
        @media print {
            body { margin: 0; padding: 10px; }
            .receipt { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">EVENTBOOK CAMEROON</div>
            <div>Event Booking System</div>
            <div>Yaoundé, Cameroon</div>
        </div>
        
        <div class="receipt-title">TICKET RECEIPT</div>
        
        <div class="section">
            <div class="row">
                <span class="label">Receipt #:</span>
                <span class="value"><?php echo htmlspecialchars($booking['booking_reference']); ?></span>
            </div>
            <div class="row">
                <span class="label">Date:</span>
                <span class="value"><?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></span>
            </div>
        </div>
        
        <div class="section">
            <div class="row">
                <span class="label">Customer:</span>
                <span class="value"><?php echo htmlspecialchars($booking['attendee_name']); ?></span>
            </div>
            <div class="row">
                <span class="label">Email:</span>
                <span class="value"><?php echo htmlspecialchars($booking['attendee_email']); ?></span>
            </div>
            <?php if ($booking['attendee_phone']): ?>
            <div class="row">
                <span class="label">Phone:</span>
                <span class="value"><?php echo htmlspecialchars($booking['attendee_phone']); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="row">
                <span class="label">Event:</span>
                <span class="value"><?php echo htmlspecialchars($booking['name']); ?></span>
            </div>
            <div class="row">
                <span class="label">Date:</span>
                <span class="value"><?php echo date('d/m/Y', strtotime($booking['date'])); ?></span>
            </div>
            <div class="row">
                <span class="label">Time:</span>
                <span class="value"><?php echo date('H:i', strtotime($booking['time'])); ?></span>
            </div>
            <div class="row">
                <span class="label">Venue:</span>
                <span class="value"><?php echo htmlspecialchars($booking['venue']); ?></span>
            </div>
            <div class="row">
                <span class="label">Location:</span>
                <span class="value"><?php echo htmlspecialchars($booking['location']); ?></span>
            </div>
        </div>
        
        <div class="section">
            <div class="row">
                <span class="label">Tickets:</span>
                <span class="value"><?php echo $booking['quantity']; ?> x <?php echo number_format($booking['total_amount'] / $booking['quantity'], 0); ?> CFA</span>
            </div>
            <div class="row">
                <span class="label">Service Fee:</span>
                <span class="value">0 CFA</span>
            </div>
        </div>
        
        <div class="total-section">
            <div class="row total">
                <span class="label">TOTAL PAID:</span>
                <span class="value"><?php echo number_format($booking['total_amount'], 0); ?> CFA</span>
            </div>
        </div>
        
        <div class="section">
            <div class="row">
                <span class="label">Payment Status:</span>
                <span class="value"><?php echo strtoupper($booking['payment_status']); ?></span>
            </div>
            <div class="row">
                <span class="label">Booking Status:</span>
                <span class="value"><?php echo strtoupper($booking['booking_status']); ?></span>
            </div>
        </div>
        
        <div class="qr-info">
            <div><strong>VALID TICKET</strong></div>
            <div>Present this receipt at event entrance</div>
            <div>Booking Ref: <?php echo htmlspecialchars($booking['booking_reference']); ?></div>
        </div>
        
        <div class="footer">
            <div>Thank you for choosing EventBook!</div>
            <div>For support: support@eventbook.cm</div>
            <div>Generated: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>
    </div>
    
    <script>
        // Auto-print when opened (optional)
        // window.onload = function() { window.print(); }
        
        // Add print button functionality
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
