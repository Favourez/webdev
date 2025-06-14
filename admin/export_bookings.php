<?php
require_once '../config/database.php';

// Check if user is admin (you may want to add proper authentication)
session_start();

// Get filter parameters (same as bookings.php)
$search = $_GET['search'] ?? '';
$event_filter = $_GET['event'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query (same as bookings.php)
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
} catch (PDOException $e) {
    die('Error fetching bookings: ' . $e->getMessage());
}

// Set headers for CSV download
$filename = 'bookings_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
$headers = [
    'Booking Reference',
    'Event Name',
    'Event Date',
    'Event Time',
    'Venue',
    'Location',
    'Customer Name',
    'Customer Email',
    'Customer Phone',
    'Username',
    'Quantity',
    'Total Amount (CFA)',
    'Payment Status',
    'Booking Status',
    'Booking Date',
    'Booking Time'
];

fputcsv($output, $headers);

// Write data rows
foreach ($bookings as $booking) {
    $row = [
        $booking['booking_reference'],
        $booking['event_name'],
        date('Y-m-d', strtotime($booking['event_date'])),
        date('H:i', strtotime($booking['event_time'])),
        $booking['venue'],
        $booking['location'],
        $booking['attendee_name'],
        $booking['attendee_email'],
        $booking['attendee_phone'] ?? '',
        $booking['username'] ?? 'Guest',
        $booking['quantity'],
        number_format($booking['total_amount'], 0, '.', ''),
        $booking['payment_status'],
        $booking['booking_status'],
        date('Y-m-d', strtotime($booking['created_at'])),
        date('H:i:s', strtotime($booking['created_at']))
    ];
    
    fputcsv($output, $row);
}

// Close output stream
fclose($output);
exit();
?>
