<?php
/**
 * Create HTML-based image files (no GD extension required)
 * These create HTML files that look like images
 */

// Image configurations
$images = [
    'techconference.jpg' => [
        'title' => 'Tech Conference 2025',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '150,000 CFA',
        'bg_color' => '#007bff',
        'text_color' => '#ffffff'
    ],
    'musicfestival.jpg' => [
        'title' => 'Music Festival Summer',
        'subtitle' => 'Douala, Cameroon',
        'price' => '85,000 CFA',
        'bg_color' => '#dc3545',
        'text_color' => '#ffffff'
    ],
    'foodwine.png' => [
        'title' => 'Food & Wine Expo',
        'subtitle' => 'Douala, Cameroon',
        'price' => '45,000 CFA',
        'bg_color' => '#28a745',
        'text_color' => '#ffffff'
    ],
    'business.jpg' => [
        'title' => 'Business Networking',
        'subtitle' => 'Yaoundé, Cameroon',
        'price' => '25,000 CFA',
        'bg_color' => '#6c757d',
        'text_color' => '#ffffff'
    ],
    'artgallery.avif' => [
        'title' => 'Art Gallery Opening',
        'subtitle' => 'Bafoussam, Cameroon',
        'price' => '5,000 CFA',
        'bg_color' => '#6f42c1',
        'text_color' => '#ffffff'
    ]
];

$created = [];
$errors = [];

// Create images directory if it doesn't exist
$imageDir = 'images/events/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

function createHTMLImage($filename, $config) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; width: 800px; height: 600px; overflow: hidden; }
        .image-container {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, ' . $config['bg_color'] . ', ' . $config['bg_color'] . '99);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: ' . $config['text_color'] . ';
            font-family: Arial, sans-serif;
            position: relative;
        }
        .title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .subtitle {
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            opacity: 0.9;
        }
        .price {
            font-size: 40px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            padding: 15px 30px;
            border-radius: 10px;
            text-align: center;
        }
        .decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .decoration1 { width: 100px; height: 100px; top: 50px; left: 50px; }
        .decoration2 { width: 80px; height: 80px; top: 100px; right: 100px; }
        .decoration3 { width: 120px; height: 120px; bottom: 80px; left: 100px; }
        .decoration4 { width: 60px; height: 60px; bottom: 50px; right: 50px; }
    </style>
</head>
<body>
    <div class="image-container">
        <div class="decoration decoration1"></div>
        <div class="decoration decoration2"></div>
        <div class="decoration decoration3"></div>
        <div class="decoration decoration4"></div>
        <div class="title">' . htmlspecialchars($config['title']) . '</div>
        <div class="subtitle">' . htmlspecialchars($config['subtitle']) . '</div>
        <div class="price">' . htmlspecialchars($config['price']) . '</div>
    </div>
</body>
</html>';
    
    return $html;
}

foreach ($images as $filename => $config) {
    try {
        $html = createHTMLImage($filename, $config);
        $filepath = $imageDir . $filename;
        
        if (file_put_contents($filepath, $html)) {
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
    <title>HTML Image Creator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h2><i class="fas fa-code me-2"></i>HTML Image Creator</h2>
                        <p class="mb-0">Creates HTML files that display as images (No GD extension required)</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($created)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>HTML Images Created Successfully!</h5>
                                <p>The following HTML image files have been created:</p>
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
                        
                        <h5 class="mb-3">Created HTML Images Preview:</h5>
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
                                                    <iframe src="<?php echo $imagePath; ?>?v=<?php echo time(); ?>" 
                                                            style="width: 200px; height: 150px; border: none; transform: scale(0.25); transform-origin: top left;"
                                                            title="<?php echo htmlspecialchars($config['title']); ?>"></iframe>
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
                            <h6><i class="fas fa-info-circle me-2"></i>How This Works:</h6>
                            <ul class="mb-0">
                                <li><strong>No GD Required:</strong> Uses pure HTML/CSS instead of image libraries</li>
                                <li><strong>Browser Compatible:</strong> All modern browsers can display these</li>
                                <li><strong>Responsive:</strong> Scales properly on different devices</li>
                                <li><strong>Customizable:</strong> Easy to modify colors and text</li>
                                <li><strong>Lightweight:</strong> Small file sizes, fast loading</li>
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
