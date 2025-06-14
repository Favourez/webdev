<?php
/**
 * Create simple placeholder images using PHP GD library
 * This creates actual image files that browsers can display
 */

// Check if GD extension is available
if (!extension_loaded('gd')) {
    die('GD extension is not available. Please enable GD extension in PHP.');
}

// Image configurations
$images = [
    'techconference.jpg' => [
        'title' => 'Tech Conference 2025',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '150,000 CFA',
        'bg_color' => [0, 123, 255], // Blue
        'text_color' => [255, 255, 255] // White
    ],
    'musicfestival.jpg' => [
        'title' => 'Music Festival Summer',
        'subtitle' => 'Douala, Cameroon',
        'price' => '85,000 CFA',
        'bg_color' => [220, 53, 69], // Red
        'text_color' => [255, 255, 255] // White
    ],
    'foodwine.png' => [
        'title' => 'Food & Wine Expo',
        'subtitle' => 'Douala, Cameroon',
        'price' => '45,000 CFA',
        'bg_color' => [40, 167, 69], // Green
        'text_color' => [255, 255, 255] // White
    ],
    'business.jpg' => [
        'title' => 'Business Networking',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '25,000 CFA',
        'bg_color' => [108, 117, 125], // Gray
        'text_color' => [255, 255, 255] // White
    ],
    'artgallery.avif' => [
        'title' => 'Art Gallery Opening',
        'subtitle' => 'Bafoussam, Cameroon',
        'price' => '5,000 CFA',
        'bg_color' => [111, 66, 193], // Purple
        'text_color' => [255, 255, 255] // White
    ]
];

$created = [];
$errors = [];

// Create images directory if it doesn't exist
$imageDir = 'images/events/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

function createImage($filename, $config) {
    $width = 800;
    $height = 600;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Create colors
    $bgColor = imagecolorallocate($image, $config['bg_color'][0], $config['bg_color'][1], $config['bg_color'][2]);
    $textColor = imagecolorallocate($image, $config['text_color'][0], $config['text_color'][1], $config['text_color'][2]);
    $lightColor = imagecolorallocate($image, 255, 255, 255);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add gradient effect (simple rectangles with transparency)
    $overlayColor = imagecolorallocatealpha($image, 255, 255, 255, 100);
    imagefilledrectangle($image, 0, 0, $width, 150, $overlayColor);
    imagefilledrectangle($image, 0, $height-150, $width, $height, $overlayColor);
    
    // Add text
    $font = 5; // Built-in font
    
    // Title
    $titleX = ($width - strlen($config['title']) * imagefontwidth($font)) / 2;
    $titleY = $height / 2 - 60;
    imagestring($image, $font, $titleX, $titleY, $config['title'], $textColor);
    
    // Subtitle
    $subtitleX = ($width - strlen($config['subtitle']) * imagefontwidth($font)) / 2;
    $subtitleY = $height / 2 - 20;
    imagestring($image, $font, $subtitleX, $subtitleY, $config['subtitle'], $textColor);
    
    // Price
    $priceX = ($width - strlen($config['price']) * imagefontwidth($font)) / 2;
    $priceY = $height / 2 + 20;
    imagestring($image, $font, $priceX, $priceY, $config['price'], $lightColor);
    
    // Add decorative elements
    imageellipse($image, 150, 150, 100, 100, $lightColor);
    imageellipse($image, $width-150, $height-150, 80, 80, $lightColor);
    
    return $image;
}

foreach ($images as $filename => $config) {
    try {
        $image = createImage($filename, $config);
        $filepath = $imageDir . $filename;
        
        // Determine file type and save
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                if (imagejpeg($image, $filepath, 90)) {
                    $created[] = $filepath;
                } else {
                    $errors[] = "Failed to save JPEG: $filename";
                }
                break;
                
            case 'png':
                if (imagepng($image, $filepath)) {
                    $created[] = $filepath;
                } else {
                    $errors[] = "Failed to save PNG: $filename";
                }
                break;
                
            case 'avif':
                // AVIF not supported by GD, save as JPEG instead
                $jpegPath = str_replace('.avif', '.jpg', $filepath);
                if (imagejpeg($image, $jpegPath, 90)) {
                    $created[] = $jpegPath;
                    // Also create the .avif file as a copy
                    if (copy($jpegPath, $filepath)) {
                        $created[] = $filepath;
                    }
                } else {
                    $errors[] = "Failed to save AVIF as JPEG: $filename";
                }
                break;
                
            default:
                // Default to JPEG
                if (imagejpeg($image, $filepath, 90)) {
                    $created[] = $filepath;
                } else {
                    $errors[] = "Failed to save default JPEG: $filename";
                }
                break;
        }
        
        imagedestroy($image);
        
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
    <title>Create Simple Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h2><i class="fas fa-image me-2"></i>Simple Image Creator</h2>
                        <p class="mb-0">Creates actual image files using PHP GD library</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Images Created Successfully!</h5>
                                <p>The following image files have been created:</p>
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
                        
                        <h5 class="mb-3">Created Images Preview:</h5>
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
                                                         style="max-height: 100%; max-width: 100%; object-fit: cover;">
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
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Failed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>What This Does:</h6>
                            <ul class="mb-0">
                                <li>Creates actual image files (JPEG/PNG) using PHP GD library</li>
                                <li>Images are saved in <code>images/events/</code> directory</li>
                                <li>Each image shows event name, location, and price</li>
                                <li>Color-coded by event type for easy identification</li>
                                <li>Compatible with all browsers and devices</li>
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
