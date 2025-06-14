<?php
require_once 'config/database.php';
require_once 'includes/qr_generator.php';

$page_title = 'Test Functionality';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2>Test Functionality</h2>
            <p>This page tests various functionalities of the system.</p>
        </div>
    </div>
    
    <!-- Test Database Connection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Database Connection Test</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
                        $result = $stmt->fetch();
                        echo '<div class="alert alert-success">✅ Database connected successfully! Found ' . $result['count'] . ' events.</div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">❌ Database connection failed: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test QR Code Generation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>QR Code Generation Test</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $testData = "Test QR Code - " . date('Y-m-d H:i:s');
                        $qrUrl = QRCodeGenerator::generateQRCode($testData);
                        $qrUrlAlt = QRCodeGenerator::generateQRCodeAlt($testData);
                        $qrUrlBackup = QRCodeGenerator::generateQRCodeBackup($testData);

                        echo '<div class="alert alert-success">✅ QR Code generated successfully!</div>';
                        echo '<div class="row text-center">';

                        echo '<div class="col-md-4">';
                        echo '<h6>Primary Provider</h6>';
                        echo '<img src="' . $qrUrl . '" alt="Primary QR Code" class="img-fluid mb-2" style="max-width: 150px;">';
                        echo '<p><small>QR Server API</small></p>';
                        echo '</div>';

                        echo '<div class="col-md-4">';
                        echo '<h6>Alternative Provider</h6>';
                        echo '<img src="' . $qrUrlAlt . '" alt="Alternative QR Code" class="img-fluid mb-2" style="max-width: 150px;">';
                        echo '<p><small>QR Server API (Enhanced)</small></p>';
                        echo '</div>';

                        echo '<div class="col-md-4">';
                        echo '<h6>Backup Provider</h6>';
                        echo '<img src="' . $qrUrlBackup . '" alt="Backup QR Code" class="img-fluid mb-2" style="max-width: 150px;">';
                        echo '<p><small>QuickChart.io</small></p>';
                        echo '</div>';

                        echo '</div>';
                        echo '<p class="mt-3"><strong>Test Data:</strong> ' . htmlspecialchars($testData) . '</p>';
                        echo '<a href="test_qr.php" class="btn btn-info btn-sm">Detailed QR Tests</a>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">❌ QR Code generation failed: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Event Listing -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Event Listing Test</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM events WHERE status = 'active' LIMIT 3");
                        $events = $stmt->fetchAll();
                        
                        if (!empty($events)) {
                            echo '<div class="alert alert-success">✅ Events loaded successfully!</div>';
                            echo '<div class="row">';
                            foreach ($events as $event) {
                                echo '<div class="col-md-4 mb-3">';
                                echo '<div class="card">';
                                echo '<div class="card-body">';
                                echo '<h6>' . htmlspecialchars($event['name']) . '</h6>';
                                echo '<p class="small text-muted">' . date('M j, Y', strtotime($event['date'])) . '</p>';
                                echo '<p class="text-primary">' . number_format($event['price'], 0) . ' CFA</p>';
                                echo '<a href="event_details.php?id=' . $event['id'] . '" class="btn btn-sm btn-primary">View Details</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-warning">⚠️ No events found in database.</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">❌ Event listing failed: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Session -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Session Test</h5>
                </div>
                <div class="card-body">
                    <?php
                    if (isLoggedIn()) {
                        echo '<div class="alert alert-success">✅ User is logged in as: ' . htmlspecialchars($_SESSION['username']) . '</div>';
                    } else {
                        echo '<div class="alert alert-info">ℹ️ User is not logged in. <a href="auth/login.php">Login here</a></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Map Functionality -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Map Functionality Test</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">✅ Map functionality uses OpenStreetMap embed and Nominatim API</div>
                    <div style="height: 300px;">
                        <iframe 
                            width="100%" 
                            height="300" 
                            frameborder="0" 
                            scrolling="no" 
                            marginheight="0" 
                            marginwidth="0"
                            src="https://www.openstreetmap.org/export/embed.html?bbox=-74.0059,-73.9352,40.7128,40.7589&layer=mapnik&marker=40.7589,-73.9851"
                            loading="lazy">
                        </iframe>
                    </div>
                    <p class="mt-2">
                        <a href="https://www.openstreetmap.org/search?query=New York, NY" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>Test Map Search
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Navigation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="index.php" class="btn btn-outline-primary btn-block">Home</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="events.php" class="btn btn-outline-primary btn-block">All Events</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="event_details.php?id=1" class="btn btn-outline-primary btn-block">Event Details</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="admin/login.php" class="btn btn-outline-dark btn-block">Admin Panel</a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3 mb-2">
                            <a href="test_qr.php" class="btn btn-outline-info btn-block">QR Code Tests</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="debug_images.php" class="btn btn-outline-warning btn-block">Debug Images</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="generate_images.php" class="btn btn-outline-primary btn-block">Generate Images</a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="setup.php" class="btn btn-outline-secondary btn-block">Database Setup</a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <a href="dashboard.php" class="btn btn-outline-success btn-block">User Dashboard</a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="test_images.php" class="btn btn-outline-info btn-block">Image Tests</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
