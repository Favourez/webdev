<?php
require_once '../config/database.php';
$page_title = 'Reports & Analytics';

// Get date range parameters
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
$date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
$report_type = $_GET['report_type'] ?? 'overview';

try {
    // Overview Statistics
    $overview_query = "
        SELECT 
            COUNT(DISTINCT b.id) as total_bookings,
            COUNT(DISTINCT b.user_id) as unique_customers,
            COUNT(DISTINCT b.event_id) as events_booked,
            SUM(b.total_amount) as total_revenue,
            SUM(b.quantity) as total_tickets_sold,
            AVG(b.total_amount) as avg_booking_value
        FROM bookings b
        WHERE b.created_at BETWEEN ? AND ?
        AND b.booking_status = 'confirmed'
    ";
    $stmt = $pdo->prepare($overview_query);
    $stmt->execute([$date_from . ' 00:00:00', $date_to . ' 23:59:59']);
    $overview = $stmt->fetch();

    // Revenue by Event
    $revenue_by_event_query = "
        SELECT 
            e.name as event_name,
            e.date as event_date,
            COUNT(b.id) as total_bookings,
            SUM(b.quantity) as tickets_sold,
            SUM(b.total_amount) as revenue,
            e.total_tickets,
            (e.total_tickets - e.available_tickets) as sold_tickets,
            ROUND((e.total_tickets - e.available_tickets) / e.total_tickets * 100, 2) as occupancy_rate
        FROM events e
        LEFT JOIN bookings b ON e.id = b.event_id AND b.booking_status = 'confirmed'
        WHERE e.status = 'active'
        GROUP BY e.id, e.name, e.date, e.total_tickets, e.available_tickets
        ORDER BY revenue DESC
    ";
    $stmt = $pdo->prepare($revenue_by_event_query);
    $stmt->execute();
    $revenue_by_event = $stmt->fetchAll();

    // Bookings by Date
    $bookings_by_date_query = "
        SELECT 
            DATE(b.created_at) as booking_date,
            COUNT(b.id) as bookings_count,
            SUM(b.total_amount) as daily_revenue,
            SUM(b.quantity) as tickets_sold
        FROM bookings b
        WHERE b.created_at BETWEEN ? AND ?
        AND b.booking_status = 'confirmed'
        GROUP BY DATE(b.created_at)
        ORDER BY booking_date DESC
    ";
    $stmt = $pdo->prepare($bookings_by_date_query);
    $stmt->execute([$date_from . ' 00:00:00', $date_to . ' 23:59:59']);
    $bookings_by_date = $stmt->fetchAll();

    // Top Customers
    $top_customers_query = "
        SELECT 
            b.attendee_name,
            b.attendee_email,
            u.username,
            COUNT(b.id) as total_bookings,
            SUM(b.quantity) as total_tickets,
            SUM(b.total_amount) as total_spent
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.created_at BETWEEN ? AND ?
        AND b.booking_status = 'confirmed'
        GROUP BY b.attendee_email, b.attendee_name, u.username
        ORDER BY total_spent DESC
        LIMIT 10
    ";
    $stmt = $pdo->prepare($top_customers_query);
    $stmt->execute([$date_from . ' 00:00:00', $date_to . ' 23:59:59']);
    $top_customers = $stmt->fetchAll();

    // Payment Status Distribution
    $payment_status_query = "
        SELECT 
            payment_status,
            COUNT(*) as count,
            SUM(total_amount) as amount
        FROM bookings
        WHERE created_at BETWEEN ? AND ?
        GROUP BY payment_status
    ";
    $stmt = $pdo->prepare($payment_status_query);
    $stmt->execute([$date_from . ' 00:00:00', $date_to . ' 23:59:59']);
    $payment_status = $stmt->fetchAll();

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Error generating reports: ' . $e->getMessage();
    $overview = ['total_bookings' => 0, 'unique_customers' => 0, 'events_booked' => 0, 'total_revenue' => 0, 'total_tickets_sold' => 0, 'avg_booking_value' => 0];
    $revenue_by_event = [];
    $bookings_by_date = [];
    $top_customers = [];
    $payment_status = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h2>
        <p class="text-muted mb-0">Comprehensive business insights and analytics</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" onclick="exportReport()">
            <i class="fas fa-download me-2"></i>Export Report
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-md-3">
                <label for="report_type" class="form-label">Report Type</label>
                <select class="form-select" id="report_type" name="report_type">
                    <option value="overview" <?php echo $report_type == 'overview' ? 'selected' : ''; ?>>Overview</option>
                    <option value="detailed" <?php echo $report_type == 'detailed' ? 'selected' : ''; ?>>Detailed</option>
                    <option value="financial" <?php echo $report_type == 'financial' ? 'selected' : ''; ?>>Financial</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-chart-line me-2"></i>Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['total_bookings'] ?? 0); ?></div>
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
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['total_revenue'] ?? 0, 0); ?> CFA</div>
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
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Unique Customers</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['unique_customers'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x"></i>
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
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Tickets Sold</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['total_tickets_sold'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Avg Booking</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['avg_booking_value'] ?? 0, 0); ?> CFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Events Booked</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($overview['events_booked'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue by Event -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Revenue by Event</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Bookings</th>
                                <th>Tickets Sold</th>
                                <th>Occupancy</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenue_by_event as $event): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($event['event_name']); ?></strong>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($event['event_date'])); ?></td>
                                    <td><?php echo number_format($event['total_bookings'] ?? 0); ?></td>
                                    <td>
                                        <?php echo number_format($event['sold_tickets']); ?> / <?php echo number_format($event['total_tickets']); ?>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $event['occupancy_rate']; ?>%">
                                                <?php echo number_format($event['occupancy_rate'], 1); ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo number_format($event['revenue'] ?? 0, 0); ?> CFA</strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Customers</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_customers)): ?>
                    <p class="text-muted text-center">No customer data available for this period.</p>
                <?php else: ?>
                    <?php foreach ($top_customers as $index => $customer): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($customer['attendee_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($customer['attendee_email']); ?></small>
                                <br><small class="text-info"><?php echo $customer['total_bookings']; ?> bookings</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success"><?php echo number_format($customer['total_spent'], 0); ?> CFA</div>
                                <small class="text-muted"><?php echo $customer['total_tickets']; ?> tickets</small>
                            </div>
                        </div>
                        <?php if ($index < count($top_customers) - 1): ?>
                            <hr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Daily Bookings Chart -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daily Bookings & Revenue</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bookings</th>
                                <th>Tickets Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings_by_date as $day): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($day['booking_date'])); ?></td>
                                    <td><?php echo number_format($day['bookings_count']); ?></td>
                                    <td><?php echo number_format($day['tickets_sold']); ?></td>
                                    <td><?php echo number_format($day['daily_revenue'], 0); ?> CFA</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Status Distribution</h5>
            </div>
            <div class="card-body">
                <?php foreach ($payment_status as $status): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-<?php echo $status['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($status['payment_status']); ?>
                            </span>
                        </div>
                        <div class="text-end">
                            <div><?php echo number_format($status['count']); ?> bookings</div>
                            <small class="text-muted"><?php echo number_format($status['amount'], 0); ?> CFA</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'export_reports.php?' + params.toString();
}

// Print styles
const printStyles = `
    @media print {
        .btn, .card-header .btn { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; }
        body { font-size: 12px; }
        .table { font-size: 11px; }
    }
`;

const styleSheet = document.createElement("style");
styleSheet.type = "text/css";
styleSheet.innerText = printStyles;
document.head.appendChild(styleSheet);
</script>

<?php include 'includes/footer.php'; ?>
