<?php
require_once '../config/database.php';
$page_title = 'Manage Users';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;
    
    try {
        if ($action == 'toggle_status') {
            $current_status = $_POST['current_status'];
            $new_status = $current_status == 'active' ? 'inactive' : 'active';
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            $_SESSION['success_message'] = 'User status updated successfully!';
            
        } elseif ($action == 'delete_user') {
            $stmt = $pdo->prepare("UPDATE users SET status = 'deleted' WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['success_message'] = 'User deleted successfully!';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: users.php');
    exit();
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
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
    
    // Get user statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_users,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
            COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_users,
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_30_days
        FROM users 
        WHERE status != 'deleted'
    ";
    $stats_stmt = $pdo->query($stats_query);
    $stats = $stats_stmt->fetch();
    
} catch (PDOException $e) {
    $users = [];
    $stats = ['total_users' => 0, 'active_users' => 0, 'inactive_users' => 0, 'new_users_30_days' => 0];
    $_SESSION['error_message'] = 'Error fetching users: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-users me-2"></i>Manage Users</h2>
        <p class="text-muted mb-0">View and manage user accounts</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" onclick="exportUsers()">
            <i class="fas fa-download me-2"></i>Export CSV
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['total_users']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Active Users</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['active_users']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Inactive Users</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['inactive_users']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">New (30 days)</div>
                        <div class="h5 mb-0 font-weight-bold"><?php echo number_format($stats['new_users_30_days']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x"></i>
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
            <div class="col-md-8">
                <label for="search" class="form-label">Search Users</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Username, email, full name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
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

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Users List (<?php echo count($users); ?> users)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">No users match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Registration</th>
                            <th>Bookings</th>
                            <th>Total Spent</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($user['full_name'] ?? 'No full name'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($user['email']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($user['phone'] ?? 'No phone'); ?></small>
                                </td>
                                <td>
                                    <div><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                                    <small class="text-muted"><?php echo date('g:i A', strtotime($user['created_at'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo number_format($user['total_bookings'] ?? 0); ?> bookings</span>
                                </td>
                                <td>
                                    <strong><?php echo number_format($user['total_spent'] ?? 0, 0); ?> CFA</strong>
                                </td>
                                <td>
                                    <?php if ($user['last_booking']): ?>
                                        <div><?php echo date('M j, Y', strtotime($user['last_booking'])); ?></div>
                                        <small class="text-muted">Last booking</small>
                                    <?php else: ?>
                                        <small class="text-muted">No bookings</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" onclick="viewUserDetails(<?php echo htmlspecialchars(json_encode($user)); ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-<?php echo $user['status'] == 'active' ? 'warning' : 'success'; ?>" 
                                                onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')" 
                                                title="<?php echo $user['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-<?php echo $user['status'] == 'active' ? 'user-times' : 'user-check'; ?>"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="sendEmail('<?php echo htmlspecialchars($user['email']); ?>')" title="Send Email">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete User">
                                            <i class="fas fa-trash"></i>
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

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="user_id" value="${userId}">
            <input type="hidden" name="current_status" value="${currentStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function viewUserDetails(user) {
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>User Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Username:</strong></td><td>${user.username}</td></tr>
                    <tr><td><strong>Full Name:</strong></td><td>${user.full_name || 'Not provided'}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${user.email}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${user.phone || 'Not provided'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${user.status == 'active' ? 'success' : 'warning'}">${user.status}</span></td></tr>
                    <tr><td><strong>Registration:</strong></td><td>${new Date(user.created_at).toLocaleString()}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Booking Statistics</h6>
                <table class="table table-sm">
                    <tr><td><strong>Total Bookings:</strong></td><td>${user.total_bookings || 0}</td></tr>
                    <tr><td><strong>Total Spent:</strong></td><td>${parseInt(user.total_spent || 0).toLocaleString()} CFA</td></tr>
                    <tr><td><strong>Last Booking:</strong></td><td>${user.last_booking ? new Date(user.last_booking).toLocaleDateString() : 'Never'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('userDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
}

function sendEmail(email) {
    window.location.href = `mailto:${email}?subject=Regarding your EventBook account`;
}

function exportUsers() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = 'export_users.php?' + params.toString();
}
</script>

<?php include 'includes/footer.php'; ?>
