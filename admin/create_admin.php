<?php
require_once '../config/database.php';

// This script creates/updates admin users
// Run this once to set up admin accounts

$admins = [
    [
        'username' => 'admin',
        'email' => 'admin@eventbook.cm',
        'password' => 'admin123',
        'full_name' => 'System Administrator'
    ],
    [
        'username' => 'eventbook',
        'email' => 'admin@cameroon.cm',
        'password' => 'cameroon2024',
        'full_name' => 'EventBook Admin'
    ]
];

$created = [];
$updated = [];
$errors = [];

try {
    foreach ($admins as $admin) {
        $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT);
        
        // Check if admin exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$admin['username'], $admin['email']]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing admin
            $stmt = $pdo->prepare("UPDATE admins SET password = ?, full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $admin['full_name'], $admin['email'], $existing['id']]);
            $updated[] = $admin['username'];
        } else {
            // Create new admin
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$admin['username'], $admin['email'], $hashed_password, $admin['full_name']]);
            $created[] = $admin['username'];
        }
    }
} catch (PDOException $e) {
    $errors[] = 'Database error: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-user-shield me-2"></i>Admin User Setup</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Admin Users Created</h5>
                                <ul class="mb-0">
                                    <?php foreach ($created as $username): ?>
                                        <li>Created admin user: <strong><?php echo htmlspecialchars($username); ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($updated)): ?>
                            <div class="alert alert-info">
                                <h5><i class="fas fa-sync-alt me-2"></i>Admin Users Updated</h5>
                                <ul class="mb-0">
                                    <?php foreach ($updated as $username): ?>
                                        <li>Updated admin user: <strong><?php echo htmlspecialchars($username); ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Errors</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <h5>Admin Credentials</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                        <th>Full Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admins as $admin): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($admin['username']); ?></code></td>
                                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                            <td><code><?php echo htmlspecialchars($admin['password']); ?></code></td>
                                            <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Security Note</h6>
                            <p class="mb-0">
                                For production use, please:
                            </p>
                            <ul class="mb-0">
                                <li>Change the default passwords</li>
                                <li>Use strong, unique passwords</li>
                                <li>Delete this file after setup</li>
                                <li>Implement proper admin role management</li>
                            </ul>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="btn btn-primary me-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Admin Login
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Back to Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
