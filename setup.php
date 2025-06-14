<?php
/**
 * Database Setup Script
 * This script helps initialize the database for the Event Booking System
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'event_booking_system';

$success_messages = [];
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Connect to MySQL server (without database)
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
        $success_messages[] = "Database '$database' created successfully.";
        
        // Connect to the specific database
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Read and execute SQL file
        $sql = file_get_contents('database/schema.sql');
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        $success_messages[] = "Database tables created successfully.";
        $success_messages[] = "Sample data inserted successfully.";
        $success_messages[] = "Setup completed! You can now use the application.";
        
    } catch (PDOException $e) {
        $error_messages[] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error_messages[] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Event Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h2><i class="fas fa-database me-2"></i>Event Booking System Setup</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_messages)): ?>
                            <?php foreach ($success_messages as $message): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_messages)): ?>
                            <?php foreach ($error_messages as $message): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (empty($success_messages)): ?>
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>Database Setup Required</h5>
                                <p>This script will create the database and tables required for the Event Booking System.</p>
                                <ul>
                                    <li>Create database: <code>event_booking_system</code></li>
                                    <li>Create all required tables</li>
                                    <li>Insert sample data</li>
                                    <li>Create admin user (username: admin, password: admin123)</li>
                                </ul>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Current Configuration:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Host:</strong> <?php echo htmlspecialchars($host); ?></li>
                                    <li><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></li>
                                    <li><strong>Database:</strong> <?php echo htmlspecialchars($database); ?></li>
                                </ul>
                                <small class="text-muted">
                                    If these settings are incorrect, please update them in <code>config/database.php</code>
                                </small>
                            </div>
                            
                            <form method="POST">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-play me-2"></i>Run Database Setup
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <h4 class="text-success mb-4">
                                    <i class="fas fa-check-circle me-2"></i>Setup Complete!
                                </h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <a href="index.php" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-home me-2"></i>Go to Website
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="admin/login.php" class="btn btn-dark btn-lg w-100">
                                            <i class="fas fa-user-shield me-2"></i>Admin Panel
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6>Default Login Credentials:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Admin Panel:</strong><br>
                                            Username: admin<br>
                                            Password: admin123
                                        </div>
                                        <div class="col-md-6">
                                            <strong>User Registration:</strong><br>
                                            Users can register on the main website
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- System Requirements -->
                <div class="card shadow mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-cogs me-2"></i>System Requirements Check</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>PHP Extensions:</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="fas fa-<?php echo extension_loaded('pdo') ? 'check text-success' : 'times text-danger'; ?> me-2"></i>
                                        PDO: <?php echo extension_loaded('pdo') ? 'Enabled' : 'Disabled'; ?>
                                    </li>
                                    <li>
                                        <i class="fas fa-<?php echo extension_loaded('pdo_mysql') ? 'check text-success' : 'times text-danger'; ?> me-2"></i>
                                        PDO MySQL: <?php echo extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled'; ?>
                                    </li>
                                    <li>
                                        <i class="fas fa-<?php echo extension_loaded('curl') ? 'check text-success' : 'times text-danger'; ?> me-2"></i>
                                        cURL: <?php echo extension_loaded('curl') ? 'Enabled' : 'Disabled'; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>File Permissions:</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="fas fa-<?php echo is_writable('images/events') ? 'check text-success' : 'times text-danger'; ?> me-2"></i>
                                        images/events: <?php echo is_writable('images/events') ? 'Writable' : 'Not Writable'; ?>
                                    </li>
                                    <li>
                                        <i class="fas fa-<?php echo is_readable('database/schema.sql') ? 'check text-success' : 'times text-danger'; ?> me-2"></i>
                                        database/schema.sql: <?php echo is_readable('database/schema.sql') ? 'Readable' : 'Not Readable'; ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
