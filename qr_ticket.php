<?php
require_once 'config/database.php';

// Get QR code parameters
$booking_ref = $_GET['ref'] ?? '';
$action = $_GET['action'] ?? 'view';

if (!$booking_ref) {
    http_response_code(400);
    die('Invalid QR code');
}

try {
    // Get booking details
    $stmt = $pdo->prepare("
        SELECT b.*, e.name, e.description, e.date, e.time, e.venue, e.location, e.organizer, e.image
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.booking_reference = ? AND b.booking_status = 'confirmed'
    ");
    $stmt->execute([$booking_ref]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        http_response_code(404);
        die('Booking not found');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    die('Database error');
}

// Check if this is a mobile device
$is_mobile = preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'] ?? '');

// If action is download or mobile device, generate downloadable ticket
if ($action === 'download' || $is_mobile) {
    // Generate ticket content
    $ticket_content = generateTicketHTML($booking);
    
    // Set headers for download
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="ticket_' . $booking['booking_reference'] . '.html"');
    header('Cache-Control: no-cache, must-revalidate');
    
    echo $ticket_content;
    exit();
} else {
    // Redirect to website
    header('Location: view_ticket.php?id=' . $booking['id']);
    exit();
}

function generateTicketHTML($booking) {
    $event_date = date('l, F j, Y', strtotime($booking['date']));
    $event_time = date('g:i A', strtotime($booking['time']));
    $booking_date = date('M j, Y', strtotime($booking['created_at']));
    
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket - ' . htmlspecialchars($booking['name']) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
        }
        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .ticket-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .ticket-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .ticket-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .ticket-body {
            padding: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #007bff;
        }
        .detail-value {
            text-align: right;
        }
        .qr-section {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            margin: 20px 0;
            border-radius: 10px;
        }
        .booking-ref {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 15px 0;
            letter-spacing: 2px;
        }
        .instructions {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
        .cameroon-flag {
            display: inline-block;
            margin: 0 5px;
        }
        @media print {
            body { background: white; }
            .ticket { box-shadow: none; border: 2px solid #000; }
        }
        @media (max-width: 600px) {
            .detail-row { flex-direction: column; }
            .detail-value { text-align: left; margin-top: 5px; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1>' . htmlspecialchars($booking['name']) . '</h1>
            <p>🇨🇲 EventBook Cameroon - Official Ticket</p>
        </div>
        
        <div class="ticket-body">
            <div class="detail-row">
                <span class="detail-label">📅 Event Date:</span>
                <span class="detail-value">' . $event_date . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🕐 Event Time:</span>
                <span class="detail-value">' . $event_time . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📍 Venue:</span>
                <span class="detail-value">' . htmlspecialchars($booking['venue']) . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🌍 Location:</span>
                <span class="detail-value">' . htmlspecialchars($booking['location']) . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">👤 Attendee:</span>
                <span class="detail-value">' . htmlspecialchars($booking['attendee_name']) . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📧 Email:</span>
                <span class="detail-value">' . htmlspecialchars($booking['attendee_email']) . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🎫 Tickets:</span>
                <span class="detail-value">' . $booking['quantity'] . '</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">💰 Amount Paid:</span>
                <span class="detail-value">' . number_format($booking['total_amount'], 0) . ' CFA</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📅 Booking Date:</span>
                <span class="detail-value">' . $booking_date . '</span>
            </div>
            
            <div class="qr-section">
                <h3>Booking Reference</h3>
                <div class="booking-ref">' . htmlspecialchars($booking['booking_reference']) . '</div>
                <p><strong>Present this ticket at the event entrance</strong></p>
            </div>
            
            <div class="instructions">
                <h4>📋 Important Instructions:</h4>
                <ul>
                    <li>Arrive at least 30 minutes before the event starts</li>
                    <li>Present this ticket (digital or printed) at the entrance</li>
                    <li>Bring a valid ID for verification</li>
                    <li>This ticket is non-transferable and non-refundable</li>
                    <li>Keep your booking reference safe</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>EventBook Cameroon</strong> 🇨🇲</p>
            <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
            <p>For support: contact@eventbook.cm</p>
        </div>
    </div>
    
    <script>
        // Auto-print on mobile devices
        if (/Mobile|Android|iPhone|iPad/.test(navigator.userAgent)) {
            setTimeout(function() {
                if (confirm("Would you like to save this ticket to your device?")) {
                    window.print();
                }
            }, 1000);
        }
    </script>
</body>
</html>';
}
?>
