<?php
$page_title = 'Image Test';
include 'includes/header.php';

// List of expected images
$images = [
    'techconference.jpg' => 'Tech Conference 2025',
    'musicfestival.jpg' => 'Music Festival Summer',
    'foodwine.png' => 'Food & Wine Expo',
    'business.jpg' => 'Business Networking Event',
    'artgallery.avif' => 'Art Gallery Opening'
];
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2>Image Display Test</h2>
            <p>Testing if event images are loading properly from the images/events/ directory.</p>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($images as $filename => $title): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6><?php echo htmlspecialchars($title); ?></h6>
                        <small class="text-muted"><?php echo $filename; ?></small>
                    </div>
                    <div class="card-body text-center">
                        <?php 
                        $imagePath = 'images/events/' . $filename;
                        $fullPath = __DIR__ . '/' . $imagePath;
                        ?>
                        
                        <!-- File existence check -->
                        <div class="mb-3">
                            <?php if (file_exists($fullPath)): ?>
                                <span class="badge bg-success">✅ File Exists</span>
                                <small class="d-block text-muted">Size: <?php echo number_format(filesize($fullPath)); ?> bytes</small>
                            <?php else: ?>
                                <span class="badge bg-danger">❌ File Missing</span>
                                <small class="d-block text-muted">Path: <?php echo $fullPath; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Image display test -->
                        <div style="height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <img src="<?php echo $imagePath; ?>" 
                                 alt="<?php echo htmlspecialchars($title); ?>" 
                                 class="img-fluid"
                                 style="max-height: 100%; max-width: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display: none; text-align: center; color: #666;">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                                <small>Image failed to load</small>
                            </div>
                        </div>
                        
                        <!-- Direct link test -->
                        <div class="mt-3">
                            <a href="<?php echo $imagePath; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Open Direct
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Directory listing -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Directory Contents: images/events/</h5>
                </div>
                <div class="card-body">
                    <?php
                    $eventsDir = __DIR__ . '/images/events/';
                    if (is_dir($eventsDir)) {
                        $files = scandir($eventsDir);
                        echo '<ul class="list-group">';
                        foreach ($files as $file) {
                            if ($file != '.' && $file != '..') {
                                $filePath = $eventsDir . $file;
                                $size = is_file($filePath) ? filesize($filePath) : 0;
                                $type = is_file($filePath) ? 'File' : 'Directory';
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<span><i class="fas fa-' . (is_file($filePath) ? 'file' : 'folder') . ' me-2"></i>' . htmlspecialchars($file) . '</span>';
                                echo '<span><small class="text-muted">' . $type . ' (' . number_format($size) . ' bytes)</small></span>';
                                echo '</li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="alert alert-warning">Directory does not exist: ' . htmlspecialchars($eventsDir) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="index.php" class="btn btn-primary me-2">
                <i class="fas fa-home me-2"></i>Back to Events
            </a>
            <a href="test_functionality.php" class="btn btn-outline-secondary">
                <i class="fas fa-cog me-2"></i>System Tests
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
