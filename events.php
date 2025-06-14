<?php
require_once 'config/database.php';
$page_title = 'Events';

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Build query
$query = "SELECT * FROM events WHERE status = 'active'";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ? OR organizer LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($location)) {
    $query .= " AND location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($date)) {
    $query .= " AND date = ?";
    $params[] = $date;
}

$query .= " ORDER BY date ASC, time ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>All Events
                </h1>
                <p class="mb-0 opacity-75">Discover amazing events happening near you</p>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Events</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Event name, organizer..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               placeholder="City, State" value="<?php echo htmlspecialchars($location); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="<?php echo htmlspecialchars($date); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <?php if (!empty($search) || !empty($location) || !empty($date)): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Search Results (<?php echo count($events); ?> events found)</h3>
                        <a href="events.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                <?php else: ?>
                    <h3>All Events (<?php echo count($events); ?> events)</h3>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                <h4 class="text-muted">No events found</h4>
                <p class="text-muted">Try adjusting your search criteria or check back later for new events.</p>
                <a href="events.php" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>View All Events
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm event-card">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; position: relative;">
                                <?php if ($event['image']): ?>
                                    <img src="images/events/<?php echo htmlspecialchars($event['image']); ?>"
                                         alt="<?php echo htmlspecialchars($event['name']); ?>"
                                         class="img-fluid"
                                         style="max-height: 100%; max-width: 100%; object-fit: cover; border-radius: 8px;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100" style="display: none; flex-direction: column;">
                                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($event['name']); ?></small>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 flex-column">
                                        <i class="fas fa-calendar-alt fa-3x text-muted mb-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($event['name']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-calendar me-2"></i><?php echo date('M d, Y', strtotime($event['date'])); ?>
                                    <i class="fas fa-clock ms-3 me-2"></i><?php echo date('g:i A', strtotime($event['time'])); ?>
                                </p>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($event['location']); ?>
                                </p>
                                <p class="card-text text-muted small mb-3">
                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($event['organizer']); ?>
                                </p>
                                
                                <p class="card-text flex-grow-1">
                                    <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0"><?php echo number_format($event['price'], 0); ?> CFA</span>
                                        <small class="text-muted"><?php echo $event['available_tickets']; ?> tickets left</small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-info-circle me-2"></i>View Details
                                        </a>
                                        <?php if (isLoggedIn() && $event['available_tickets'] > 0): ?>
                                            <button class="btn btn-outline-success btn-sm add-to-cart" 
                                                    data-event-id="<?php echo $event['id']; ?>">
                                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
