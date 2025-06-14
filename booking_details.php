<?php
require_once 'config/database.php';
require_once 'includes/session.php';

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
        SELECT b.*, e.name, e.description, e.date, e.time, e.venue, e.location, e.organizer, e.organizer_contact, e.image
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.id = ? AND b.user_id = ?
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

$is_upcoming = strtotime($booking['date']) >= strtotime('today');
$page_title = 'Booking Details';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-receipt me-2"></i>Booking Details
                </h2>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Event Information -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Event Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <?php if ($booking['image']): ?>
                                    <img src="images/events/<?php echo htmlspecialchars($booking['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($booking['name']); ?>" 
                                         class="img-fluid rounded" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-3"><?php echo htmlspecialchars($booking['name']); ?></h4>
                            
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong>Date:</strong>
                                    </div>
                                    <div class="ms-4"><?php echo date('l, F j, Y', strtotime($booking['date'])); ?></div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <strong>Time:</strong>
                                    </div>
                                    <div class="ms-4"><?php echo date('g:i A', strtotime($booking['time'])); ?></div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Venue:</strong>
                                    </div>
                                    <div class="ms-4"><?php echo htmlspecialchars($booking['venue']); ?></div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-location-arrow text-primary me-2"></i>
                                        <strong>Location:</strong>
                                    </div>
                                    <div class="ms-4"><?php echo htmlspecialchars($booking['location']); ?></div>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>Organizer:</strong>
                                    </div>
                                    <div class="ms-4"><?php echo htmlspecialchars($booking['organizer']); ?></div>
                                </div>
                                
                                <?php if ($booking['organizer_contact']): ?>
                                <div class="col-sm-6 mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <strong>Contact:</strong>
                                    </div>
                                    <div class="ms-4">
                                        <a href="mailto:<?php echo htmlspecialchars($booking['organizer_contact']); ?>">
                                            <?php echo htmlspecialchars($booking['organizer_contact']); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($booking['description']): ?>
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p class="text-muted mt-2"><?php echo nl2br(htmlspecialchars($booking['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Booking Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking Reference:</span>
                            <strong class="text-primary"><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking Date:</span>
                            <span><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Number of Tickets:</span>
                            <span><?php echo $booking['quantity']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Amount:</span>
                            <strong><?php echo number_format($booking['total_amount'], 0); ?> CFA</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Status:</span>
                            <span class="badge bg-<?php echo $booking['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Booking Status:</span>
                            <span class="badge bg-<?php echo $booking['booking_status'] == 'confirmed' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6>Attendee Information</h6>
                        <div class="small">
                            <div class="mb-1">
                                <strong>Name:</strong> <?php echo htmlspecialchars($booking['attendee_name']); ?>
                            </div>
                            <div class="mb-1">
                                <strong>Email:</strong> <?php echo htmlspecialchars($booking['attendee_email']); ?>
                            </div>
                            <?php if ($booking['attendee_phone']): ?>
                            <div class="mb-1">
                                <strong>Phone:</strong> <?php echo htmlspecialchars($booking['attendee_phone']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($is_upcoming && $booking['booking_status'] == 'confirmed'): ?>
                    <div class="d-grid gap-2">
                        <a href="view_ticket.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-ticket-alt me-2"></i>View Ticket
                        </a>
                        <a href="download_ticket.php?id=<?php echo $booking['id']; ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download Receipt
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Event Status -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Event Status
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($is_upcoming): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            <strong>Upcoming Event</strong><br>
                            <small>Don't forget to attend this event!</small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-history me-2"></i>
                            <strong>Past Event</strong><br>
                            <small>This event has already occurred.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
