<?php
require_once 'config/database.php';

$page_title = 'Image Debug';
include 'includes/header.php';

// Get events from database
try {
    $stmt = $pdo->query("SELECT * FROM events WHERE status = 'active' LIMIT 5");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2>Image Loading Debug</h2>
            <p>This page helps debug image loading issues by showing detailed information about each image.</p>
        </div>
    </div>
    
    <!-- Database Events -->
    <div class="row mb-5">
        <div class="col-12">
            <h4>Events from Database</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image Filename</th>
                            <th>File Exists</th>
                            <th>File Size</th>
                            <th>Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <?php 
                            $imagePath = 'images/events/' . $event['image'];
                            $fullPath = __DIR__ . '/' . $imagePath;
                            $fileExists = file_exists($fullPath);
                            $fileSize = $fileExists ? filesize($fullPath) : 0;
                            ?>
                            <tr>
                                <td><?php echo $event['id']; ?></td>
                                <td><?php echo htmlspecialchars($event['name']); ?></td>
                                <td><code><?php echo htmlspecialchars($event['image']); ?></code></td>
                                <td>
                                    <?php if ($fileExists): ?>
                                        <span class="badge bg-success">✅ Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">❌ No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($fileSize); ?> bytes</td>
                                <td>
                                    <div style="width: 100px; height: 60px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                        <?php if ($fileExists): ?>
                                            <img src="<?php echo $imagePath; ?>?v=<?php echo time(); ?>" 
                                                 alt="<?php echo htmlspecialchars($event['name']); ?>" 
                                                 style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <small style="display: none; color: red;">Error</small>
                                        <?php else: ?>
                                            <small class="text-muted">No file</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Directory Check -->
    <div class="row mb-5">
        <div class="col-12">
            <h4>Directory Information</h4>
            <div class="card">
                <div class="card-body">
                    <?php
                    $eventsDir = __DIR__ . '/images/events/';
                    $webEventsDir = 'images/events/';
                    ?>
                    <p><strong>Full Path:</strong> <code><?php echo htmlspecialchars($eventsDir); ?></code></p>
                    <p><strong>Web Path:</strong> <code><?php echo htmlspecialchars($webEventsDir); ?></code></p>
                    <p><strong>Directory Exists:</strong> 
                        <?php if (is_dir($eventsDir)): ?>
                            <span class="badge bg-success">✅ Yes</span>
                        <?php else: ?>
                            <span class="badge bg-danger">❌ No</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Directory Readable:</strong> 
                        <?php if (is_readable($eventsDir)): ?>
                            <span class="badge bg-success">✅ Yes</span>
                        <?php else: ?>
                            <span class="badge bg-danger">❌ No</span>
                        <?php endif; ?>
                    </p>
                    
                    <h6 class="mt-4">Files in Directory:</h6>
                    <?php if (is_dir($eventsDir)): ?>
                        <ul class="list-group">
                            <?php
                            $files = scandir($eventsDir);
                            foreach ($files as $file) {
                                if ($file != '.' && $file != '..') {
                                    $filePath = $eventsDir . $file;
                                    $isFile = is_file($filePath);
                                    $size = $isFile ? filesize($filePath) : 0;
                                    $webPath = $webEventsDir . $file;
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-<?php echo $isFile ? 'file' : 'folder'; ?> me-2"></i>
                                            <strong><?php echo htmlspecialchars($file); ?></strong>
                                            <br>
                                            <small class="text-muted">Size: <?php echo number_format($size); ?> bytes</small>
                                        </div>
                                        <div>
                                            <?php if ($isFile): ?>
                                                <a href="<?php echo $webPath; ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <div style="width: 60px; height: 40px; border: 1px solid #ddd; display: inline-flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                                    <img src="<?php echo $webPath; ?>?v=<?php echo time(); ?>" 
                                                         alt="<?php echo htmlspecialchars($file); ?>" 
                                                         style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <small style="display: none; font-size: 10px;">❌</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-warning">Directory does not exist!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Image Loading -->
    <div class="row mb-5">
        <div class="col-12">
            <h4>Test Image Loading</h4>
            <div class="row">
                <?php
                $testImages = ['techconference.jpg', 'musicfestival.jpg', 'foodwine.png', 'business.jpg', 'artgallery.avif'];
                foreach ($testImages as $testImage):
                    $testPath = 'images/events/' . $testImage;
                    $testFullPath = __DIR__ . '/' . $testPath;
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><?php echo htmlspecialchars($testImage); ?></h6>
                            </div>
                            <div class="card-body text-center">
                                <div style="height: 120px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa; margin-bottom: 10px;">
                                    <img src="<?php echo $testPath; ?>?v=<?php echo time(); ?>" 
                                         alt="<?php echo htmlspecialchars($testImage); ?>" 
                                         style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; flex-direction: column; align-items: center;">
                                        <i class="fas fa-exclamation-triangle text-warning mb-1"></i>
                                        <small>Failed to load</small>
                                    </div>
                                </div>
                                <div>
                                    <?php if (file_exists($testFullPath)): ?>
                                        <span class="badge bg-success">File exists</span><br>
                                        <small><?php echo number_format(filesize($testFullPath)); ?> bytes</small>
                                    <?php else: ?>
                                        <span class="badge bg-danger">File missing</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="row">
        <div class="col-12 text-center">
            <a href="generate_images.php" class="btn btn-primary me-2">
                <i class="fas fa-magic me-2"></i>Generate Images
            </a>
            <a href="index.php" class="btn btn-success me-2">
                <i class="fas fa-home me-2"></i>View Events
            </a>
            <a href="test_functionality.php" class="btn btn-outline-secondary">
                <i class="fas fa-cog me-2"></i>System Tests
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
