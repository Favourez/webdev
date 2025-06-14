<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/qr_generator.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error occurred.';
    header('Location: dashboard.php');
    exit();
}

// Generate QR code with fallback
$qrCodeUrl = QRCodeGenerator::generateBookingQR($booking);
$qrCodeUrlBackup = QRCodeGenerator::generateQRCodeBackup(json_encode([
    'booking_ref' => $booking['booking_reference'],
    'event_id' => $booking['event_id'],
    'user_id' => $booking['user_id'],
    'quantity' => $booking['quantity'],
    'amount' => $booking['total_amount'],
    'date' => date('Y-m-d H:i:s')
]), 200);

$page_title = 'Event Ticket';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Ticket -->
            <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
                <!-- Ticket Header -->
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                    <h2 class="mb-2"><?php echo htmlspecialchars($booking['name']); ?></h2>
                    <p class="mb-0 opacity-75">Event Ticket</p>
                </div>
                
                <!-- Ticket Body -->
                <div class="card-body p-4">
                    <!-- Event Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Event Details</h6>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($booking['date'])); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Time:</strong> <?php echo date('g:i A', strtotime($booking['time'])); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Venue:</strong> <?php echo htmlspecialchars($booking['venue']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-location-arrow text-primary me-2"></i>
                                <strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>Organizer:</strong> <?php echo htmlspecialchars($booking['organizer']); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Booking Details</h6>
                            <div class="mb-2">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <strong>Attendee:</strong> <?php echo htmlspecialchars($booking['attendee_name']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>Email:</strong> <?php echo htmlspecialchars($booking['attendee_email']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                <strong>Tickets:</strong> <?php echo $booking['quantity']; ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-dollar-sign text-primary me-2"></i>
                                <strong>Amount:</strong> <?php echo number_format($booking['total_amount'], 0); ?> CFA
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                <strong>Booked:</strong> <?php echo date('M j, Y', strtotime($booking['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code Section -->
                    <div class="text-center p-4 bg-light rounded">
                        <h6 class="mb-3">Scan QR Code for Entry</h6>
                        <div class="mb-3">
                            <img id="qr-code-main" src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="img-fluid" style="max-width: 200px;"
                                 onerror="this.src='<?php echo $qrCodeUrlBackup; ?>'; this.onerror=null;">
                        </div>
                        <div class="booking-reference">
                            <strong class="text-primary fs-5">
                                Booking Reference: <?php echo htmlspecialchars($booking['booking_reference']); ?>
                            </strong>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                QR Code contains encrypted booking verification data
                            </small>
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Please present this ticket (digital or printed) at the event entrance. 
                        The QR code will be scanned for verification.
                    </div>
                </div>
                
                <!-- Ticket Footer -->
                <div class="card-footer text-center bg-light">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Ticket
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="download_ticket.php?id=<?php echo $booking['id']; ?>" class="btn btn-success" download>
                                <i class="fas fa-download me-2"></i>Download Receipt
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <button class="btn btn-info" onclick="downloadQRCode()">
                                <i class="fas fa-qrcode me-2"></i>Save QR Code
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="shareTicket()">
                                <i class="fas fa-share me-1"></i>Share
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="copyBookingRef()">
                                <i class="fas fa-copy me-1"></i>Copy Reference
                            </button>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Generated on <?php echo date('F j, Y \a\t g:i A'); ?>
                    </small>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .navbar, .main-content > .container > .alert, footer, .btn, .text-center.mt-4 {
        display: none !important;
    }

    .card {
        box-shadow: none !important;
        border: 2px solid #000 !important;
    }

    body {
        background: white !important;
    }

    .card-header {
        background: #000 !important;
        color: white !important;
    }
}
</style>

<script>
// Download QR Code function
function downloadQRCode() {
    var qrImg = document.getElementById('qr-code-main');
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    canvas.width = qrImg.naturalWidth || 200;
    canvas.height = qrImg.naturalHeight || 200;

    ctx.drawImage(qrImg, 0, 0);

    // Create download link
    var link = document.createElement('a');
    link.download = 'qr_code_<?php echo $booking['booking_reference']; ?>.png';
    link.href = canvas.toDataURL();
    link.click();
}

// Share ticket function
function shareTicket() {
    if (navigator.share) {
        navigator.share({
            title: 'Event Ticket - <?php echo addslashes($booking['name']); ?>',
            text: 'My ticket for <?php echo addslashes($booking['name']); ?> - Booking Reference: <?php echo $booking['booking_reference']; ?>',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            alert('Ticket URL copied to clipboard!');
        }).catch(function() {
            prompt('Copy this URL to share your ticket:', url);
        });
    }
}

// Copy booking reference
function copyBookingRef() {
    var bookingRef = '<?php echo $booking['booking_reference']; ?>';
    navigator.clipboard.writeText(bookingRef).then(function() {
        // Show success message
        var btn = event.target;
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');

        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
        }, 2000);
    }).catch(function() {
        prompt('Copy this booking reference:', bookingRef);
    });
}

// QR Code fallback handling
$(document).ready(function() {
    $('#qr-code-main').on('error', function() {
        console.log('Primary QR code failed, trying backup...');
        $(this).attr('src', '<?php echo $qrCodeUrlBackup; ?>');
        $(this).off('error'); // Remove error handler to prevent infinite loop

        // If backup also fails, show a message
        $(this).on('error', function() {
            $(this).parent().html(
                '<div class="alert alert-warning">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                'QR Code temporarily unavailable. Please use booking reference: <strong><?php echo $booking['booking_reference']; ?></strong>' +
                '</div>'
            );
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
