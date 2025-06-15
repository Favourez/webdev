<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$page_title = 'Dashboard';

// Get user bookings
try {
    $stmt = $pdo->prepare("
        SELECT b.*, e.name, e.date, e.time, e.venue, e.location, e.image
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();
    
    // Get statistics
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_bookings,
            SUM(total_amount) as total_spent,
            COUNT(CASE WHEN CONCAT(e.date, ' ', e.time) >= NOW() THEN 1 END) as upcoming_events,
            COUNT(CASE WHEN CONCAT(e.date, ' ', e.time) < NOW() THEN 1 END) as past_events
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id = ? AND b.booking_status = 'confirmed'
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
    
} catch (PDOException $e) {
    $bookings = [];
    $stats = ['total_bookings' => 0, 'total_spent' => 0, 'upcoming_events' => 0, 'past_events' => 0];
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                <small class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</small>
            </h2>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                    <div class="stat-number"><?php echo $stats['total_bookings']; ?></div>
                    <div class="text-muted">Total Bookings</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                    <div class="stat-number"><?php echo $stats['upcoming_events']; ?></div>
                    <div class="text-muted">Upcoming Events</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-history fa-3x text-info mb-3"></i>
                    <div class="stat-number"><?php echo $stats['past_events']; ?></div>
                    <div class="text-muted">Past Events</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                    <div class="stat-number"><?php echo number_format($stats['total_spent'], 0); ?> CFA</div>
                    <div class="text-muted">Total Spent</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bookings Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>My Bookings
                    </h5>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="filter" id="all" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm" for="all">All</label>
                        
                        <input type="radio" class="btn-check" name="filter" id="upcoming" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="upcoming">Upcoming</label>
                        
                        <input type="radio" class="btn-check" name="filter" id="past" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="past">Past</label>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No bookings found</h5>
                            <p class="text-muted">You haven't booked any events yet.</p>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Browse Events
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Tickets</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <?php
                                        // Check if event is upcoming (event date is today or in the future)
                                        $event_datetime = strtotime($booking['date'] . ' ' . $booking['time']);
                                        $current_datetime = time();
                                        $is_upcoming = $event_datetime >= $current_datetime;
                                        $status_class = $booking['booking_status'] == 'confirmed' ? 'success' : 'danger';
                                        ?>
                                        <tr class="booking-row" data-type="<?php echo $is_upcoming ? 'upcoming' : 'past'; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php if ($booking['image']): ?>
                                                            <img src="images/events/<?php echo htmlspecialchars($booking['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($booking['name']); ?>" 
                                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px;">
                                                                <i class="fas fa-calendar-alt text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($booking['name']); ?></h6>
                                                        <small class="text-muted">
                                                            Ref: <?php echo htmlspecialchars($booking['booking_reference']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <?php echo date('M d, Y', strtotime($booking['date'])); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('g:i A', strtotime($booking['time'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($booking['venue']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($booking['location']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $booking['quantity']; ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo number_format($booking['total_amount'], 0); ?> CFA</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $status_class; ?>">
                                                    <?php echo ucfirst($booking['booking_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="view_ticket.php?id=<?php echo $booking['id']; ?>"
                                                       class="btn btn-outline-primary" title="View Ticket">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($is_upcoming && $booking['booking_status'] == 'confirmed'): ?>
                                                        <a href="download_ticket.php?id=<?php echo $booking['id']; ?>"
                                                           class="btn btn-outline-success" title="Download Receipt" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <a href="download_qr.php?id=<?php echo $booking['id']; ?>"
                                                           class="btn btn-outline-info" title="Download QR Code" download>
                                                            <i class="fas fa-qrcode"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter bookings
$('input[name="filter"]').on('change', function() {
    var filter = $(this).attr('id');
    
    if (filter === 'all') {
        $('.booking-row').show();
    } else {
        $('.booking-row').hide();
        $('.booking-row[data-type="' + filter + '"]').show();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
