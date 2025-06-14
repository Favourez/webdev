<?php
require_once '../config/database.php';
$page_title = 'Dashboard';

// Get statistics
try {
    // Total events
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM events");
    $total_events = $stmt->fetch()['total'];
    
    // Active events
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM events WHERE status = 'active'");
    $active_events = $stmt->fetch()['total'];
    
    // Total bookings
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
    $total_bookings = $stmt->fetch()['total'];
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'completed'");
    $total_revenue = $stmt->fetch()['total'] ?: 0;
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    // Recent bookings
    $stmt = $pdo->prepare("
        SELECT b.*, e.name as event_name, u.full_name as user_name
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_bookings = $stmt->fetchAll();
    
    // Popular events
    $stmt = $pdo->prepare("
        SELECT e.name, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
        FROM events e 
        LEFT JOIN bookings b ON e.id = b.event_id 
        WHERE e.status = 'active'
        GROUP BY e.id, e.name 
        ORDER BY booking_count DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $popular_events = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_events = $active_events = $total_bookings = $total_revenue = $total_users = 0;
    $recent_bookings = $popular_events = [];
}

include 'includes/header.php';
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_events; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_bookings; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_revenue, 0); ?> CFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Bookings -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                <a href="bookings.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <p class="text-muted text-center">No bookings found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Event</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['booking_reference']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['event_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                        <td><?php echo number_format($booking['total_amount'], 0); ?> CFA</td>
                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Popular Events -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Popular Events</h6>
            </div>
            <div class="card-body">
                <?php if (empty($popular_events)): ?>
                    <p class="text-muted text-center">No events found.</p>
                <?php else: ?>
                    <?php foreach ($popular_events as $event): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($event['name']); ?></h6>
                                <small class="text-muted"><?php echo $event['booking_count']; ?> bookings</small>
                            </div>
                            <div class="text-end">
                                <strong><?php echo number_format($event['revenue'] ?: 0, 0); ?> CFA</strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="events.php" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar-alt me-2"></i>Manage Events
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="bookings.php" class="btn btn-success btn-block">
                            <i class="fas fa-ticket-alt me-2"></i>Manage Bookings
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="users.php" class="btn btn-info btn-block">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="reports.php" class="btn btn-warning btn-block">
                            <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
