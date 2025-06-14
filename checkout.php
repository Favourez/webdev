<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$page_title = 'Checkout';

// Check if direct event booking
$direct_event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($direct_event_id) {
    // Get event details for direct booking
    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND status = 'active'");
        $stmt->execute([$direct_event_id]);
        $event = $stmt->fetch();
        
        if (!$event) {
            header('Location: index.php');
            exit();
        }
        
        $checkout_items = [[
            'event_id' => $event['id'],
            'name' => $event['name'],
            'date' => $event['date'],
            'time' => $event['time'],
            'venue' => $event['venue'],
            'location' => $event['location'],
            'price' => $event['price'],
            'quantity' => 1
        ]];
        $total = $event['price'];
        
    } catch (PDOException $e) {
        header('Location: index.php');
        exit();
    }
} else {
    // Get cart items
    try {
        $stmt = $pdo->prepare("
            SELECT c.quantity, e.id as event_id, e.name, e.date, e.time, 
                   e.venue, e.location, e.price
            FROM cart c 
            JOIN events e ON c.event_id = e.id 
            WHERE c.user_id = ? AND e.status = 'active'
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $checkout_items = $stmt->fetchAll();
        
        if (empty($checkout_items)) {
            header('Location: cart.php');
            exit();
        }
        
        // Calculate total
        $total = 0;
        foreach ($checkout_items as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        
    } catch (PDOException $e) {
        header('Location: cart.php');
        exit();
    }
}

// Get user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $user = null;
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendee_name = trim($_POST['attendee_name']);
    $attendee_email = trim($_POST['attendee_email']);
    $attendee_phone = trim($_POST['attendee_phone']);
    $payment_method = $_POST['payment_method'];
    
    // Validation
    $errors = [];
    if (empty($attendee_name)) $errors[] = 'Attendee name is required.';
    if (empty($attendee_email)) $errors[] = 'Attendee email is required.';
    if (!filter_var($attendee_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (empty($payment_method)) $errors[] = 'Payment method is required.';
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Process each item
            foreach ($checkout_items as $item) {
                // Generate booking reference
                $booking_reference = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));

                // Generate QR code data
                $qr_data = json_encode([
                    'booking_ref' => $booking_reference,
                    'event_id' => $item['event_id'],
                    'user_id' => $user_id,
                    'quantity' => $item['quantity'],
                    'amount' => $item['quantity'] * $item['price'],
                    'date' => date('Y-m-d H:i:s')
                ]);
                
                // Check ticket availability
                $stmt = $pdo->prepare("SELECT available_tickets FROM events WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['event_id']]);
                $event_check = $stmt->fetch();
                
                if (!$event_check || $event_check['available_tickets'] < $item['quantity']) {
                    throw new Exception('Not enough tickets available for ' . $item['name']);
                }
                
                // Create booking
                $stmt = $pdo->prepare("
                    INSERT INTO bookings (user_id, event_id, quantity, total_amount, booking_reference,
                                        attendee_name, attendee_email, attendee_phone, payment_status, booking_status, qr_code)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'completed', 'confirmed', ?)
                ");
                $stmt->execute([
                    $user_id,
                    $item['event_id'],
                    $item['quantity'],
                    $item['quantity'] * $item['price'],
                    $booking_reference,
                    $attendee_name,
                    $attendee_email,
                    $attendee_phone,
                    $qr_data
                ]);
                
                // Update available tickets
                $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['event_id']]);
            }
            
            // Clear cart if not direct booking
            if (!$direct_event_id) {
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
            }
            
            $pdo->commit();
            
            $_SESSION['success_message'] = 'Booking confirmed successfully!';
            header('Location: dashboard.php');
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = implode(' ', $errors);
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-credit-card me-2"></i>Checkout
            </h2>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form method="POST">
                <!-- Attendee Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Attendee Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="attendee_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="attendee_name" name="attendee_name" 
                                       value="<?php echo htmlspecialchars($attendee_name ?? $user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attendee_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="attendee_email" name="attendee_email" 
                                       value="<?php echo htmlspecialchars($attendee_email ?? $user['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="attendee_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="attendee_phone" name="attendee_phone" 
                                   value="<?php echo htmlspecialchars($attendee_phone ?? $user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method *</label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="credit_card" value="credit_card" checked>
                                        <label class="form-check-label" for="credit_card">
                                            <i class="fas fa-credit-card me-2"></i>Credit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2"></i>PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank_transfer" value="bank_transfer">
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university me-2"></i>Bank Transfer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This is a demo checkout. No actual payment will be processed.
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check me-2"></i>Complete Booking
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($checkout_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted">
                                    <?php echo date('M d, Y', strtotime($item['date'])); ?> • 
                                    <?php echo htmlspecialchars($item['location']); ?>
                                </small>
                                <div class="small text-muted">
                                    Qty: <?php echo $item['quantity']; ?> × <?php echo number_format($item['price'], 0); ?> CFA
                                </div>
                            </div>
                            <div class="text-end">
                                <strong><?php echo number_format($item['quantity'] * $item['price'], 0); ?> CFA</strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo number_format($total, 0); ?> CFA</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service Fee:</span>
                        <span>0 CFA</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-success"><?php echo number_format($total, 0); ?> CFA</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
