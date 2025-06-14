<?php
require_once 'includes/qr_generator.php';

$page_title = 'QR Code Test';
include 'includes/header.php';

// Test data
$testBooking = [
    'booking_reference' => 'BK20241201TEST',
    'event_id' => 1,
    'user_id' => 1,
    'quantity' => 2,
    'total_amount' => 150000
];
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2>QR Code Generation Test</h2>
            <p>Testing different QR code providers to ensure they work properly.</p>
        </div>
    </div>
    
    <!-- Test Simple QR Code -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Simple Text QR Code</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $simpleText = "Hello World - " . date('Y-m-d H:i:s');
                    $simpleQR = QRCodeGenerator::generateQRCode($simpleText, 200);
                    ?>
                    <img src="<?php echo $simpleQR; ?>" alt="Simple QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p><strong>Data:</strong> <?php echo htmlspecialchars($simpleText); ?></p>
                    <p><strong>URL:</strong> <small><?php echo htmlspecialchars($simpleQR); ?></small></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Alternative QR Code Provider</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $altQR = QRCodeGenerator::generateQRCodeAlt($simpleText, 200);
                    ?>
                    <img src="<?php echo $altQR; ?>" alt="Alternative QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p><strong>Data:</strong> <?php echo htmlspecialchars($simpleText); ?></p>
                    <p><strong>URL:</strong> <small><?php echo htmlspecialchars($altQR); ?></small></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Booking QR Code -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Booking QR Code</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $bookingQR = QRCodeGenerator::generateBookingQR($testBooking);
                    ?>
                    <img src="<?php echo $bookingQR; ?>" alt="Booking QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p><strong>Booking Ref:</strong> <?php echo htmlspecialchars($testBooking['booking_reference']); ?></p>
                    <p><strong>URL:</strong> <small><?php echo htmlspecialchars($bookingQR); ?></small></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Backup QR Code Provider</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $backupData = json_encode($testBooking);
                    $backupQR = QRCodeGenerator::generateQRCodeBackup($backupData, 200);
                    ?>
                    <img src="<?php echo $backupQR; ?>" alt="Backup QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p><strong>Provider:</strong> QuickChart.io</p>
                    <p><strong>URL:</strong> <small><?php echo htmlspecialchars($backupQR); ?></small></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Different Sizes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Different QR Code Sizes</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <?php
                        $sizes = [100, 150, 200, 250];
                        foreach ($sizes as $size) {
                            $sizeQR = QRCodeGenerator::generateQRCodeAlt("Size test: {$size}px", $size);
                            echo '<div class="col-md-3 mb-3">';
                            echo '<img src="' . $sizeQR . '" alt="QR Code ' . $size . 'px" class="img-fluid mb-2" style="max-width: ' . $size . 'px;">';
                            echo '<p><strong>' . $size . 'px</strong></p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- URL Testing -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>QR Code URL Testing</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $testData = "Test QR Code";
                                $providers = [
                                    'QR Server' => QRCodeGenerator::generateQRCodeAlt($testData, 100),
                                    'QuickChart' => QRCodeGenerator::generateQRCodeBackup($testData, 100),
                                    'Primary' => QRCodeGenerator::generateQRCode($testData, 100)
                                ];
                                
                                foreach ($providers as $name => $url) {
                                    echo '<tr>';
                                    echo '<td><strong>' . $name . '</strong></td>';
                                    echo '<td><small>' . htmlspecialchars($url) . '</small></td>';
                                    echo '<td><span class="badge bg-success">Active</span></td>';
                                    echo '<td><img src="' . $url . '" alt="' . $name . '" style="width: 50px; height: 50px;"></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Test -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>JavaScript QR Code Test</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="generateDynamicQR()">Generate Dynamic QR Code</button>
                    <div id="dynamic-qr" class="mt-3 text-center"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <a href="test_functionality.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to Tests
                </a>
                <a href="dashboard.php" class="btn btn-outline-success">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function generateDynamicQR() {
    var timestamp = new Date().toISOString();
    var data = "Dynamic QR: " + timestamp;
    var encodedData = encodeURIComponent(data);
    var qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodedData + "&format=png&margin=10";
    
    document.getElementById('dynamic-qr').innerHTML = 
        '<img src="' + qrUrl + '" alt="Dynamic QR Code" class="img-fluid mb-2" style="max-width: 200px;"><br>' +
        '<small>Generated at: ' + timestamp + '</small>';
}

// Test image loading
$(document).ready(function() {
    $('img').on('error', function() {
        $(this).attr('src', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkVycm9yIExvYWRpbmcgUVIgQ29kZTwvdGV4dD48L3N2Zz4=');
        $(this).parent().append('<div class="alert alert-warning mt-2"><small>Failed to load QR code from this provider</small></div>');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
