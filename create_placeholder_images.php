<?php
/**
 * Script to create placeholder images for events
 * This will generate simple colored placeholder images
 */

// Image configurations
$images = [
    'techconference.jpg' => [
        'text' => 'Tech Conference 2025',
        'color' => '#007bff',
        'bg' => '#f8f9fa'
    ],
    'musicfestival.jpg' => [
        'text' => 'Music Festival Summer',
        'color' => '#dc3545',
        'bg' => '#fff3cd'
    ],
    'foodwine.png' => [
        'text' => 'Food & Wine Expo',
        'color' => '#28a745',
        'bg' => '#d4edda'
    ],
    'business.jpg' => [
        'text' => 'Business Networking',
        'color' => '#6c757d',
        'bg' => '#e9ecef'
    ],
    'artgallery.avif' => [
        'text' => 'Art Gallery Opening',
        'color' => '#6f42c1',
        'bg' => '#f3e5f5'
    ]
];

// Create images directory if it doesn't exist
$imageDir = 'images/events/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

// Function to create a simple placeholder image
function createPlaceholderImage($filename, $text, $textColor, $bgColor) {
    $width = 800;
    $height = 600;
    
    // Create image
    $image = imagecreate($width, $height);
    
    // Convert hex colors to RGB
    $bg = imagecolorallocate($image, 
        hexdec(substr($bgColor, 1, 2)), 
        hexdec(substr($bgColor, 3, 2)), 
        hexdec(substr($bgColor, 5, 2))
    );
    
    $textColorRGB = imagecolorallocate($image,
        hexdec(substr($textColor, 1, 2)),
        hexdec(substr($textColor, 3, 2)),
        hexdec(substr($textColor, 5, 2))
    );
    
    // Fill background
    imagefill($image, 0, 0, $bg);
    
    // Add text
    $fontSize = 24;
    $fontFile = null; // Use default font
    
    // Calculate text position (center)
    $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
    $textWidth = $textBox[4] - $textBox[0];
    $textHeight = $textBox[1] - $textBox[7];
    
    $x = ($width - $textWidth) / 2;
    $y = ($height + $textHeight) / 2;
    
    // Add text to image
    if (function_exists('imagettftext') && $fontFile) {
        imagettftext($image, $fontSize, 0, $x, $y, $textColorRGB, $fontFile, $text);
    } else {
        // Use built-in font
        $x = ($width - strlen($text) * 15) / 2;
        $y = ($height - 15) / 2;
        imagestring($image, 5, $x, $y, $text, $textColorRGB);
    }
    
    // Save image
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $filepath = 'images/events/' . $filename;
    
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($image, $filepath, 90);
            break;
        case 'png':
            imagepng($image, $filepath);
            break;
        default:
            // For AVIF and other formats, save as JPEG
            $filepath = str_replace('.avif', '.jpg', $filepath);
            imagejpeg($image, $filepath, 90);
            break;
    }
    
    imagedestroy($image);
    return $filepath;
}

$created = [];
$errors = [];

// Create placeholder images
foreach ($images as $filename => $config) {
    try {
        $filepath = createPlaceholderImage($filename, $config['text'], $config['color'], $config['bg']);
        $created[] = $filepath;
    } catch (Exception $e) {
        $errors[] = "Failed to create $filename: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Placeholder Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2><i class="fas fa-image me-2"></i>Placeholder Image Generator</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Images Created Successfully!</h5>
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
                        
                        <div class="row">
                            <?php foreach ($images as $filename => $config): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6><?php echo htmlspecialchars($config['text']); ?></h6>
                                            <?php 
                                            $displayFile = str_replace('.avif', '.jpg', $filename);
                                            $imagePath = 'images/events/' . $displayFile;
                                            ?>
                                            <?php if (file_exists($imagePath)): ?>
                                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($config['text']); ?>" 
                                                     class="img-fluid rounded" style="max-height: 150px;">
                                                <p class="small text-success mt-2">✅ Created</p>
                                            <?php else: ?>
                                                <div class="bg-light p-3 rounded">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                    <p class="small text-danger mt-2">❌ Not Found</p>
                                                </div>
                                            <?php endif; ?>
                                            <p class="small text-muted"><?php echo $filename; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-2"></i>View Events
                            </a>
                            <a href="test_functionality.php" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-2"></i>Test System
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
