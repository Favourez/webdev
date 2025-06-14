<?php
require_once '../config/database.php';
$page_title = 'Manage Bookings';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'] ?? 0;
    
    try {
        if ($action == 'update_status') {
            $new_status = $_POST['status'];
            $stmt = $pdo->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $booking_id]);
            $_SESSION['success_message'] = 'Booking status updated successfully!';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: bookings.php');
    exit();
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$event_filter = $_GET['event'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$query = "
    SELECT b.*, e.name as event_name, e.date as event_date, e.time as event_time, e.venue, e.location,
           u.username, u.email as user_email
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    LEFT JOIN users u ON b.user_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (b.booking_reference LIKE ? OR b.attendee_name LIKE ? OR b.attendee_email LIKE ? OR u.username LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($event_filter)) {
    $query .= " AND b.event_id = ?";
    $params[] = $event_filter;
}

if (!empty($status_filter)) {
    $query .= " AND b.booking_status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $query .= " AND b.created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
}

if (!empty($date_to)) {
    $query .= " AND b.created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
}

$query .= " ORDER BY b.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
    
    // Get events for filter dropdown
    $events_stmt = $pdo->query("SELECT id, name FROM events WHERE status = 'active' ORDER BY name");
    $events_list = $events_stmt->fetchAll();
    
    // Get statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_bookings,
            SUM(total_amount) as total_revenue,
            COUNT(CASE WHEN booking_status = 'confirmed' THEN 1 END) as confirmed_bookings,
            COUNT(CASE WHEN booking_status = 'pending' THEN 1 END) as pending_bookings,
            COUNT(CASE WHEN booking_status = 'cancelled' THEN 1 END) as cancelled_bookings
        FROM bookings
    ";
    $stats_stmt = $pdo->query($stats_query);
    $stats = $stats_stmt->fetch();
    
} catch (PDOException $e) {
    $bookings = [];
    $events_list = [];
    $stats = ['total_bookings' => 0, 'total_revenue' => 0, 'confirmed_bookings' => 0, 'pending_bookings' => 0, 'cancelled_bookings' => 0];
    $_SESSION['error_message'] = 'Error fetching bookings: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-ticket-alt me-2"></i>Manage Bookings</h2>
        <p class="text-muted mb-0">View and manage all event bookings</p>
    </div>
    <div class="d-flex gap-2">
        <a href="reports.php" class="btn btn-outline-primary">
            <i class="fas fa-chart-bar me-2"></i>Generate Reports
        </a>
        <button class="btn btn-success" onclick="exportBookings()">
            <i class="fas fa-download me-2"></i>Export CSV
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['total_bookings']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['total_revenue'], 0); ?> CFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Confirmed</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['confirmed_bookings']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['pending_bookings']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Cancelled</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['cancelled_bookings']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Booking ref, name, email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label for="event" class="form-label">Event</label>
                <select class="form-select" id="event" name="event">
                    <option value="">All Events</option>
                    <?php foreach ($events_list as $event): ?>
                        <option value="<?php echo $event['id']; ?>" <?php echo $event_filter == $event['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($event['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Bookings List (<?php echo count($bookings); ?> bookings)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No bookings found</h5>
                <p class="text-muted">No bookings match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking Reference</th>
                            <th>Event</th>
                            <th>Customer</th>
                            <th>Tickets</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['event_name']); ?></strong>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y g:i A', strtotime($booking['event_date'] . ' ' . $booking['event_time'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['attendee_name']); ?></strong>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars($booking['attendee_email']); ?></small>
                                    <?php if ($booking['username']): ?>
                                        <br><small class="text-info">User: <?php echo htmlspecialchars($booking['username']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $booking['quantity']; ?> tickets</span>
                                </td>
                                <td>
                                    <strong><?php echo number_format($booking['total_amount'], 0); ?> CFA</strong>
                                    <br><small class="text-muted">
                                        Payment: <span class="badge bg-<?php echo $booking['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </small>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select" 
                                            data-booking-id="<?php echo $booking['id']; ?>"
                                            onchange="updateBookingStatus(this)">
                                        <option value="pending" <?php echo $booking['booking_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $booking['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $booking['booking_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <div><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></div>
                                    <small class="text-muted"><?php echo date('g:i A', strtotime($booking['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" onclick="viewBookingDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                            <a href="../view_ticket.php?id=<?php echo $booking['id']; ?>" target="_blank" class="btn btn-outline-success" title="View Ticket">
                                                <i class="fas fa-ticket-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-primary" onclick="sendEmail('<?php echo htmlspecialchars($booking['attendee_email']); ?>')" title="Send Email">
                                            <i class="fas fa-envelope"></i>
                                        </button>
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

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateBookingStatus(select) {
    const bookingId = select.dataset.bookingId;
    const newStatus = select.value;
    
    if (confirm(`Are you sure you want to change the booking status to "${newStatus}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="booking_id" value="${bookingId}">
            <input type="hidden" name="status" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    } else {
        // Reset select to original value
        location.reload();
    }
}

function viewBookingDetails(booking) {
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Booking Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Reference:</strong></td><td>${booking.booking_reference}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${booking.booking_status == 'confirmed' ? 'success' : booking.booking_status == 'pending' ? 'warning' : 'danger'}">${booking.booking_status}</span></td></tr>
                    <tr><td><strong>Quantity:</strong></td><td>${booking.quantity} tickets</td></tr>
                    <tr><td><strong>Total Amount:</strong></td><td>${parseInt(booking.total_amount).toLocaleString()} CFA</td></tr>
                    <tr><td><strong>Payment Status:</strong></td><td><span class="badge bg-${booking.payment_status == 'completed' ? 'success' : 'warning'}">${booking.payment_status}</span></td></tr>
                    <tr><td><strong>Booking Date:</strong></td><td>${new Date(booking.created_at).toLocaleString()}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>${booking.attendee_name}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${booking.attendee_email}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${booking.attendee_phone || 'Not provided'}</td></tr>
                    <tr><td><strong>Username:</strong></td><td>${booking.username || 'Guest'}</td></tr>
                </table>
                
                <h6 class="mt-3">Event Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Event:</strong></td><td>${booking.event_name}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>${new Date(booking.event_date + ' ' + booking.event_time).toLocaleString()}</td></tr>
                    <tr><td><strong>Venue:</strong></td><td>${booking.venue}</td></tr>
                    <tr><td><strong>Location:</strong></td><td>${booking.location}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('bookingDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('bookingDetailsModal')).show();
}

function sendEmail(email) {
    window.location.href = `mailto:${email}?subject=Regarding your event booking`;
}

function exportBookings() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'export_bookings.php?' + params.toString();
}
</script>

<?php include 'includes/footer.php'; ?>
