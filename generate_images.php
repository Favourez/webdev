<?php
/**
 * Generate simple placeholder images for events
 */

// Image configurations
$images = [
    'techconference.jpg' => [
        'title' => 'Tech Conference 2025',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '150,000 CFA',
        'color' => '#007bff',
        'bg' => '#e3f2fd'
    ],
    'musicfestival.jpg' => [
        'title' => 'Music Festival Summer',
        'subtitle' => 'Douala, Cameroon',
        'price' => '85,000 CFA',
        'color' => '#dc3545',
        'bg' => '#ffebee'
    ],
    'foodwine.png' => [
        'title' => 'Food & Wine Expo',
        'subtitle' => 'Douala, Cameroon',
        'price' => '45,000 CFA',
        'color' => '#28a745',
        'bg' => '#e8f5e8'
    ],
    'business.jpg' => [
        'title' => 'Business Networking',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '25,000 CFA',
        'color' => '#6c757d',
        'bg' => '#f8f9fa'
    ],
    'artgallery.avif' => [
        'title' => 'Art Gallery Opening',
        'subtitle' => 'Bafoussam, Cameroon',
        'price' => '5,000 CFA',
        'color' => '#6f42c1',
        'bg' => '#f3e5f5'
    ]
];

$created = [];
$errors = [];

// Create images directory if it doesn't exist
$imageDir = 'images/events/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

foreach ($images as $filename => $config) {
    try {
        // Create SVG content
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
  <!-- Background -->
  <rect width="100%" height="100%" fill="' . $config['bg'] . '"/>
  
  <!-- Main color overlay -->
  <rect x="0" y="0" width="100%" height="150" fill="' . $config['color'] . '"/>
  <rect x="0" y="450" width="100%" height="150" fill="' . $config['color'] . '" opacity="0.8"/>
  
  <!-- Content area -->
  <rect x="50" y="150" width="700" height="300" fill="white" rx="20" opacity="0.9"/>
  
  <!-- Title -->
  <text x="400" y="220" font-family="Arial, sans-serif" font-size="36" font-weight="bold" text-anchor="middle" fill="' . $config['color'] . '">
    ' . htmlspecialchars($config['title']) . '
  </text>
  
  <!-- Subtitle -->
  <text x="400" y="260" font-family="Arial, sans-serif" font-size="24" text-anchor="middle" fill="#666">
    ' . htmlspecialchars($config['subtitle']) . '
  </text>
  
  <!-- Price -->
  <text x="400" y="320" font-family="Arial, sans-serif" font-size="32" font-weight="bold" text-anchor="middle" fill="' . $config['color'] . '">
    ' . htmlspecialchars($config['price']) . '
  </text>
  
  <!-- Decorative elements -->
  <circle cx="150" cy="75" r="30" fill="white" opacity="0.3"/>
  <circle cx="650" cy="75" r="25" fill="white" opacity="0.2"/>
  <circle cx="100" cy="525" r="35" fill="white" opacity="0.2"/>
  <circle cx="700" cy="525" r="20" fill="white" opacity="0.3"/>
  
  <!-- Icon placeholder -->
  <rect x="350" y="350" width="100" height="60" fill="' . $config['color'] . '" opacity="0.1" rx="10"/>
  <text x="400" y="385" font-family="Arial, sans-serif" font-size="16" text-anchor="middle" fill="' . $config['color'] . '">EVENT</text>
</svg>';

        // Save SVG file
        $filepath = $imageDir . $filename;
        
        // For non-SVG extensions, we'll still save as SVG but with the original extension
        // This ensures the file exists with the expected name
        if (file_put_contents($filepath, $svg)) {
            $created[] = $filepath;
        } else {
            $errors[] = "Failed to write file: $filepath";
        }
        
    } catch (Exception $e) {
        $errors[] = "Error creating $filename: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Event Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2><i class="fas fa-image me-2"></i>Event Image Generator</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Images Generated Successfully!</h5>
                                <ul class="mb-0">
                                    <?php foreach ($created as $file): ?>
                                        <li><?php echo htmlspecialchars($file); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="mb-3">Generated Images Preview:</h5>
                        <div class="row">
                            <?php foreach ($images as $filename => $config): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6><?php echo htmlspecialchars($config['title']); ?></h6>
                                            <div class="mb-3" style="height: 150px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                                <?php 
                                                $imagePath = 'images/events/' . $filename;
                                                if (file_exists($imagePath)): ?>
                                                    <img src="<?php echo $imagePath; ?>?v=<?php echo time(); ?>" 
                                                         alt="<?php echo htmlspecialchars($config['title']); ?>" 
                                                         class="img-fluid" 
                                                         style="max-height: 100%; max-width: 100%; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display: none;">
                                                        <i class="fas fa-exclamation-triangle text-warning"></i><br>
                                                        <small>Failed to load</small>
                                                    </div>
                                                <?php else: ?>
                                                    <div>
                                                        <i class="fas fa-times text-danger"></i><br>
                                                        <small>File not found</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="small text-muted mb-1"><?php echo $filename; ?></p>
                                            <p class="small text-primary"><?php echo $config['price']; ?></p>
                                            <?php if (file_exists($imagePath)): ?>
                                                <span class="badge bg-success">✅ Created</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Missing</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-2"></i>View Events
                            </a>
                            <a href="test_images.php" class="btn btn-outline-info me-2">
                                <i class="fas fa-image me-2"></i>Test Images
                            </a>
                            <a href="test_functionality.php" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i>System Tests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
