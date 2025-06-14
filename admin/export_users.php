<?php
require_once '../config/database.php';

// Check if user is admin
session_start();

// Get filter parameters (same as users.php)
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query (same as users.php)
$query = "
    SELECT u.*, 
           COUNT(b.id) as total_bookings,
           SUM(b.total_amount) as total_spent,
           MAX(b.created_at) as last_booking
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status = 'confirmed'
    WHERE u.status != 'deleted'
";
$params = [];

if (!empty($search)) {
    $query .= " AND (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($status_filter)) {
    $query .= " AND u.status = ?";
    $params[] = $status_filter;
}

$query .= " GROUP BY u.id ORDER BY u.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Error fetching users: ' . $e->getMessage());
}

// Set headers for CSV download
$filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
$headers = [
    'User ID',
    'Username',
    'Full Name',
    'Email',
    'Phone',
    'Status',
    'Registration Date',
    'Registration Time',
    'Total Bookings',
    'Total Spent (CFA)',
    'Last Booking Date'
];

fputcsv($output, $headers);

// Write data rows
foreach ($users as $user) {
    $row = [
        $user['id'],
        $user['username'],
        $user['full_name'] ?? '',
        $user['email'],
        $user['phone'] ?? '',
        $user['status'],
        date('Y-m-d', strtotime($user['created_at'])),
        date('H:i:s', strtotime($user['created_at'])),
        $user['total_bookings'] ?? 0,
        number_format($user['total_spent'] ?? 0, 0, '.', ''),
        $user['last_booking'] ? date('Y-m-d', strtotime($user['last_booking'])) : ''
    ];
    
    fputcsv($output, $row);
}

// Close output stream
fclose($output);
exit();
?>
