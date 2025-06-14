<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$page_title = 'Shopping Cart';

// Get cart items
try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.quantity, e.id as event_id, e.name, e.date, e.time, 
               e.venue, e.location, e.price, e.image, e.available_tickets
        FROM cart c 
        JOIN events e ON c.event_id = e.id 
        WHERE c.user_id = ? AND e.status = 'active'
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['quantity'] * $item['price'];
    }
    
} catch (PDOException $e) {
    $cart_items = [];
    $total = 0;
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                <?php if (!empty($cart_items)): ?>
                    <span class="badge bg-primary"><?php echo count($cart_items); ?></span>
                <?php endif; ?>
            </h2>
        </div>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">Your cart is empty</h4>
                    <p class="text-muted mb-4">Looks like you haven't added any events to your cart yet.</p>
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($cart_items as $index => $item): ?>
                            <div class="cart-item p-4" id="cart-item-<?php echo $item['id']; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <?php if ($item['image']): ?>
                                                <img src="images/events/<?php echo htmlspecialchars($item['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="img-fluid rounded" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-calendar-alt fa-2x text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h6 class="mb-1">
                                            <a href="event_details.php?id=<?php echo $item['event_id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y', strtotime($item['date'])); ?>
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($item['location']); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <div class="quantity-controls">
                                            <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                                    data-cart-id="<?php echo $item['id']; ?>" 
                                                    data-action="decrease">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="mx-2 fw-bold" id="quantity-<?php echo $item['id']; ?>">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                            <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                                    data-cart-id="<?php echo $item['id']; ?>" 
                                                    data-action="increase">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <div class="fw-bold text-primary" id="subtotal-<?php echo $item['id']; ?>">
                                            <?php echo number_format($item['quantity'] * $item['price'], 0); ?> CFA
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format($item['price'], 0); ?> CFA each
                                        </small>
                                    </div>
                                    
                                    <div class="col-md-2 text-center">
                                        <button class="btn btn-outline-danger btn-sm remove-from-cart" 
                                                data-cart-id="<?php echo $item['id']; ?>"
                                                title="Remove from cart">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span id="cart-total"><?php echo number_format($total, 0); ?> CFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Service Fee:</span>
                            <span>0 CFA</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="final-total"><?php echo number_format($total, 0); ?> CFA</strong>
                        </div>
                        
                        <div class="d-grid">
                            <a href="checkout.php" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-shield-alt text-success mb-1"></i>
                                <small class="d-block text-muted">Secure</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-mobile-alt text-success mb-1"></i>
                                <small class="d-block text-muted">Mobile</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-undo text-success mb-1"></i>
                                <small class="d-block text-muted">Refundable</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Update final total when cart total changes
$(document).on('DOMSubtreeModified', '#cart-total', function() {
    $('#final-total').text($(this).text());
});
</script>

<?php include 'includes/footer.php'; ?>
