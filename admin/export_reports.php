<?php
require_once '../config/database.php';

// Check if user is admin
session_start();

// Get parameters
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');
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

} catch (PDOException $e) {
    die('Error generating report: ' . $e->getMessage());
}

// Set headers for CSV download
$filename = 'report_' . $report_type . '_' . $date_from . '_to_' . $date_to . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Open output stream
$output = fopen('php://output', 'w');

// Write report header
fputcsv($output, ['EventBook Cameroon - Business Report']);
fputcsv($output, ['Report Type: ' . ucfirst($report_type)]);
fputcsv($output, ['Period: ' . $date_from . ' to ' . $date_to]);
fputcsv($output, ['Generated: ' . date('Y-m-d H:i:s')]);
fputcsv($output, []); // Empty row

// Overview Section
fputcsv($output, ['OVERVIEW STATISTICS']);
fputcsv($output, ['Metric', 'Value']);
fputcsv($output, ['Total Bookings', number_format($overview['total_bookings'] ?? 0)]);
fputcsv($output, ['Total Revenue (CFA)', number_format($overview['total_revenue'] ?? 0, 0, '.', '')]);
fputcsv($output, ['Unique Customers', number_format($overview['unique_customers'] ?? 0)]);
fputcsv($output, ['Total Tickets Sold', number_format($overview['total_tickets_sold'] ?? 0)]);
fputcsv($output, ['Average Booking Value (CFA)', number_format($overview['avg_booking_value'] ?? 0, 0, '.', '')]);
fputcsv($output, ['Events Booked', number_format($overview['events_booked'] ?? 0)]);
fputcsv($output, []); // Empty row

// Revenue by Event Section
fputcsv($output, ['REVENUE BY EVENT']);
$headers = [
    'Event Name',
    'Event Date',
    'Total Bookings',
    'Tickets Sold',
    'Total Tickets',
    'Occupancy Rate (%)',
    'Revenue (CFA)'
];
fputcsv($output, $headers);

foreach ($revenue_by_event as $event) {
    $row = [
        $event['event_name'],
        date('Y-m-d', strtotime($event['event_date'])),
        $event['total_bookings'] ?? 0,
        $event['sold_tickets'],
        $event['total_tickets'],
        number_format($event['occupancy_rate'], 2),
        number_format($event['revenue'] ?? 0, 0, '.', '')
    ];
    fputcsv($output, $row);
}

fputcsv($output, []); // Empty row

// Summary
$total_revenue_all_events = array_sum(array_column($revenue_by_event, 'revenue'));
$total_bookings_all_events = array_sum(array_column($revenue_by_event, 'total_bookings'));

fputcsv($output, ['SUMMARY']);
fputcsv($output, ['Total Events', count($revenue_by_event)]);
fputcsv($output, ['Total Revenue All Events (CFA)', number_format($total_revenue_all_events, 0, '.', '')]);
fputcsv($output, ['Total Bookings All Events', $total_bookings_all_events]);

// Close output stream
fclose($output);
exit();
?>
