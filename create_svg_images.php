<?php
/**
 * Create SVG image files (no GD extension required)
 * SVG files are supported by all modern browsers
 */

// Image configurations
$images = [
    'techconference.jpg' => [
        'title' => 'Tech Conference 2025',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '150,000 CFA',
        'bg_color' => '#007bff',
        'accent_color' => '#0056b3'
    ],
    'musicfestival.jpg' => [
        'title' => 'Music Festival Summer',
        'subtitle' => 'Douala, Cameroon',
        'price' => '85,000 CFA',
        'bg_color' => '#dc3545',
        'accent_color' => '#c82333'
    ],
    'foodwine.png' => [
        'title' => 'Food & Wine Expo',
        'subtitle' => 'Douala, Cameroon',
        'price' => '45,000 CFA',
        'bg_color' => '#28a745',
        'accent_color' => '#1e7e34'
    ],
    'business.jpg' => [
        'title' => 'Business Networking',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '25,000 CFA',
        'bg_color' => '#6c757d',
        'accent_color' => '#545b62'
    ],
    'artgallery.avif' => [
        'title' => 'Art Gallery Opening',
        'subtitle' => 'Bafoussam, Cameroon',
        'price' => '5,000 CFA',
        'bg_color' => '#6f42c1',
        'accent_color' => '#59359a'
    ]
];

$created = [];
$errors = [];

// Create images directory if it doesn't exist
$imageDir = 'images/events/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

function createSVGImage($filename, $config) {
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="800" height="600" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:' . $config['bg_color'] . ';stop-opacity:1" />
      <stop offset="100%" style="stop-color:' . $config['accent_color'] . ';stop-opacity:1" />
    </linearGradient>
    <linearGradient id="overlayGradient" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" style="stop-color:rgba(255,255,255,0.1);stop-opacity:1" />
      <stop offset="50%" style="stop-color:rgba(255,255,255,0.2);stop-opacity:1" />
      <stop offset="100%" style="stop-color:rgba(255,255,255,0.1);stop-opacity:1" />
    </linearGradient>
  </defs>
  
  <!-- Background -->
  <rect width="100%" height="100%" fill="url(#bgGradient)"/>
  
  <!-- Overlay -->
  <rect width="100%" height="100%" fill="url(#overlayGradient)"/>
  
  <!-- Decorative circles -->
  <circle cx="150" cy="100" r="60" fill="rgba(255,255,255,0.1)"/>
  <circle cx="650" cy="150" r="40" fill="rgba(255,255,255,0.08)"/>
  <circle cx="100" cy="500" r="80" fill="rgba(255,255,255,0.06)"/>
  <circle cx="700" cy="450" r="50" fill="rgba(255,255,255,0.1)"/>
  
  <!-- Content background -->
  <rect x="50" y="200" width="700" height="200" rx="20" fill="rgba(255,255,255,0.15)"/>
  
  <!-- Title -->
  <text x="400" y="260" font-family="Arial, sans-serif" font-size="42" font-weight="bold" 
        text-anchor="middle" fill="white" stroke="rgba(0,0,0,0.3)" stroke-width="1">
    ' . htmlspecialchars($config['title']) . '
  </text>
  
  <!-- Subtitle -->
  <text x="400" y="310" font-family="Arial, sans-serif" font-size="28" 
        text-anchor="middle" fill="rgba(255,255,255,0.9)">
    ' . htmlspecialchars($config['subtitle']) . '
  </text>
  
  <!-- Price background -->
  <rect x="300" y="330" width="200" height="50" rx="25" fill="rgba(255,255,255,0.2)"/>
  
  <!-- Price -->
  <text x="400" y="360" font-family="Arial, sans-serif" font-size="24" font-weight="bold" 
        text-anchor="middle" fill="white">
    ' . htmlspecialchars($config['price']) . '
  </text>
  
  <!-- Bottom decoration -->
  <rect x="0" y="550" width="800" height="50" fill="rgba(0,0,0,0.2)"/>
  <text x="400" y="575" font-family="Arial, sans-serif" font-size="16" 
        text-anchor="middle" fill="rgba(255,255,255,0.8)">
    EventBook Cameroon
  </text>
</svg>';
    
    return $svg;
}

foreach ($images as $filename => $config) {
    try {
        $svg = createSVGImage($filename, $config);
        $filepath = $imageDir . $filename;
        
        if (file_put_contents($filepath, $svg)) {
            $created[] = $filepath;
        } else {
            $errors[] = "Failed to create: $filename";
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
    <title>SVG Image Creator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h2><i class="fas fa-vector-square me-2"></i>SVG Image Creator</h2>
                        <p class="mb-0">Creates SVG vector images (No GD extension required)</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>SVG Images Created Successfully!</h5>
                                <p>The following SVG image files have been created:</p>
                                <ul class="mb-0">
                                    <?php foreach ($created as $file): ?>
                                        <li><code><?php echo htmlspecialchars($file); ?></code></li>
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
                        
                        <h5 class="mb-3">Created SVG Images Preview:</h5>
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
                                                         style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div>
                                                        <i class="fas fa-times text-danger"></i><br>
                                                        <small>Not created</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="small text-muted mb-1"><?php echo $filename; ?></p>
                                            <p class="small text-primary"><?php echo $config['price']; ?></p>
                                            <?php if (file_exists($imagePath)): ?>
                                                <span class="badge bg-success">✅ Created</span>
                                                <br><small class="text-muted"><?php echo number_format(filesize($imagePath)); ?> bytes</small>
                                                <br><a href="<?php echo $imagePath; ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">View Full</a>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Failed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>SVG Advantages:</h6>
                            <ul class="mb-0">
                                <li><strong>No GD Required:</strong> Pure XML/SVG format</li>
                                <li><strong>Vector Graphics:</strong> Scales perfectly at any size</li>
                                <li><strong>Small File Size:</strong> Efficient and fast loading</li>
                                <li><strong>Browser Support:</strong> Supported by all modern browsers</li>
                                <li><strong>Professional Look:</strong> Gradients and smooth graphics</li>
                                <li><strong>Editable:</strong> Can be modified with any text editor</li>
                            </ul>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-2"></i>View Events
                            </a>
                            <a href="debug_images.php" class="btn btn-outline-info me-2">
                                <i class="fas fa-bug me-2"></i>Debug Images
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
