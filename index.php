<?php
require_once 'config/database.php';
$page_title = 'Home';

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

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Discover Amazing Events</h1>
                <p class="lead mb-4">Find and book tickets for the best events in your area. From concerts to conferences, we have it all!</p>
                <a href="#events" class="btn btn-light btn-lg">
                    <i class="fas fa-search me-2"></i>Browse Events
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-calendar-alt" style="font-size: 8rem; opacity: 0.3;"></i>
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
<section id="events" class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-center mb-4">
                    <?php if (!empty($search) || !empty($location) || !empty($date)): ?>
                        Search Results
                    <?php else: ?>
                        Upcoming Events
                    <?php endif; ?>
                </h2>

                <?php if (!empty($search) || !empty($location) || !empty($date)): ?>
                    <div class="text-center mb-3">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No events found</h4>
                <p class="text-muted">Try adjusting your search criteria or check back later for new events.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; position: relative;">
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; position: relative;">
                                    <img src="images/events/<?php echo htmlspecialchars($event['image']); ?>"
                                        alt="<?php echo htmlspecialchars($event['name']); ?>"
                                        class="img-fluid"
                                        style="max-height: 100%; max-width: 100%; object-fit: cover; border-radius: 8px;"
                                        onerror="this.style.display='none';">
                                </div>

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

                                    <div class="d-grid">
                                        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-info-circle me-2"></i>View Details
                                        </a>
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
