<?php
require_once 'config/database.php';

// Get event ID
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    header('Location: index.php');
    exit();
}

// Get event details
try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND status = 'active'");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: index.php');
    exit();
}

$page_title = $event['name'];
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Event Image and Basic Info -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 400px; position: relative;">
                    <img src="images/events/<?php echo htmlspecialchars($event['image']); ?>"
                        alt="<?php echo htmlspecialchars($event['name']); ?>"
                        class="img-fluid"
                        style="max-height: 100%; max-width: 100%; object-fit: cover; border-radius: 8px;"
                        onerror="this.style.display='none';">
                </div>

                <div class="card-body">
                    <h1 class="card-title mb-3"><?php echo htmlspecialchars($event['name']); ?></h1>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($event['date'])); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Time:</strong> <?php echo date('g:i A', strtotime($event['time'])); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-location-arrow text-primary me-2"></i>
                                <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?>
                            </p>
                            <?php if ($event['organizer_contact']): ?>
                            <p class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>Contact:</strong> 
                                <a href="mailto:<?php echo htmlspecialchars($event['organizer_contact']); ?>">
                                    <?php echo htmlspecialchars($event['organizer_contact']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>About This Event</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    </div>
                    
                    <!-- Map Section -->
                    <div class="mb-4">
                        <h5>Location Map</h5>
                        <div class="card">
                            <div class="card-body p-0">
                                <div id="map-container" style="height: 300px; position: relative;">
                                    <iframe
                                        id="location-map"
                                        width="100%"
                                        height="300"
                                        frameborder="0"
                                        scrolling="no"
                                        marginheight="0"
                                        marginwidth="0"
                                        style="border-radius: 8px;"
                                        src="https://www.openstreetmap.org/export/embed.html?bbox=-74.0059,-73.9352,40.7128,40.7589&layer=mapnik&marker=40.7589,-73.9851"
                                        loading="lazy">
                                    </iframe>
                                </div>
                                <div class="p-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($event['venue']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($event['location']); ?></small>
                                        </div>
                                        <div>
                                            <a href="https://www.openstreetmap.org/search?query=<?php echo urlencode($event['venue'] . ', ' . $event['location']); ?>"
                                               target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i>View on Map
                                            </a>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="getDirections()">
                                                <i class="fas fa-directions me-1"></i>Directions
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Book Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="price-display text-primary">
                            <?php echo number_format($event['price'], 0); ?> CFA
                        </div>
                        <small class="text-muted">per ticket</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Available Tickets:</span>
                            <span class="badge bg-success"><?php echo $event['available_tickets']; ?></span>
                        </div>
                    </div>
                    
                    <?php if ($event['available_tickets'] > 0): ?>
                        <?php if (isLoggedIn()): ?>
                            <form id="booking-form">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <select class="form-select" id="quantity" name="quantity">
                                        <?php for ($i = 1; $i <= min(10, $event['available_tickets']); $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Subtotal:</span>
                                        <span id="subtotal"><?php echo number_format($event['price'], 0); ?> CFA</span>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-lg add-to-cart" 
                                            data-event-id="<?php echo $event['id']; ?>">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                    <a href="checkout.php?event_id=<?php echo $event['id']; ?>" 
                                       class="btn btn-success btn-lg">
                                        <i class="fas fa-bolt me-2"></i>Book Now
                                    </a>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <p class="text-muted mb-3">Please login to book tickets</p>
                                <div class="d-grid gap-2">
                                    <a href="auth/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                    </a>
                                    <a href="auth/register.php" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This event is sold out
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-shield-alt text-success mb-2"></i>
                            <small class="d-block text-muted">Secure</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-mobile-alt text-success mb-2"></i>
                            <small class="d-block text-muted">Mobile Tickets</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo text-success mb-2"></i>
                            <small class="d-block text-muted">Refundable</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update subtotal when quantity changes
$('#quantity').on('change', function() {
    var quantity = parseInt($(this).val());
    var price = <?php echo $event['price']; ?>;
    var subtotal = quantity * price;
    $('#subtotal').text(subtotal.toLocaleString() + ' CFA');
});

// Map functionality
$(document).ready(function() {
    // Initialize map with event location
    var venue = "<?php echo addslashes($event['venue']); ?>";
    var location = "<?php echo addslashes($event['location']); ?>";
    var fullAddress = venue + ', ' + location;

    // Update map iframe with search query
    var mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=-180,-90,180,90&layer=mapnik&marker=' +
                 encodeURIComponent(fullAddress);

    // Use Nominatim API to get coordinates and update map
    geocodeAddress(fullAddress);
});

// Geocode address using Nominatim API with Cameroon focus
function geocodeAddress(address) {
    // Add Cameroon to the search for better accuracy
    var searchAddress = address + ', Cameroon';
    var nominatimUrl = 'https://nominatim.openstreetmap.org/search?format=json&countrycodes=cm&q=' + encodeURIComponent(searchAddress);

    $.ajax({
        url: nominatimUrl,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lon = parseFloat(data[0].lon);

                // Update map with actual coordinates
                var bbox = (lon - 0.01) + ',' + (lat - 0.01) + ',' + (lon + 0.01) + ',' + (lat + 0.01);
                var mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' + bbox +
                           '&layer=mapnik&marker=' + lat + ',' + lon;

                $('#location-map').attr('src', mapUrl);

                // Update the map info with found location details
                if (data[0].display_name) {
                    console.log('Location found: ' + data[0].display_name);
                }
            } else {
                // Fallback: try with just the city name
                fallbackCitySearch(address);
            }
        },
        error: function() {
            console.log('Could not geocode address, trying fallback');
            fallbackCitySearch(address);
        }
    });
}

// Fallback function to search for major Cameroon cities
function fallbackCitySearch(address) {
    var cityCoordinates = {
        'yaoundé': { lat: 3.8480, lon: 11.5021 },
        'yaounde': { lat: 3.8480, lon: 11.5021 },
        'douala': { lat: 4.0511, lon: 9.7679 },
        'bafoussam': { lat: 5.4781, lon: 10.4167 },
        'bamenda': { lat: 5.9631, lon: 10.1591 },
        'garoua': { lat: 9.3265, lon: 13.3958 },
        'maroua': { lat: 10.5906, lon: 14.3197 },
        'ngaoundéré': { lat: 7.3167, lon: 13.5833 },
        'ngaoundere': { lat: 7.3167, lon: 13.5833 }
    };

    var cityFound = false;
    var addressLower = address.toLowerCase();

    for (var city in cityCoordinates) {
        if (addressLower.includes(city)) {
            var coords = cityCoordinates[city];
            var bbox = (coords.lon - 0.05) + ',' + (coords.lat - 0.05) + ',' + (coords.lon + 0.05) + ',' + (coords.lat + 0.05);
            var mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' + bbox +
                       '&layer=mapnik&marker=' + coords.lat + ',' + coords.lon;

            $('#location-map').attr('src', mapUrl);
            cityFound = true;
            console.log('Using fallback coordinates for: ' + city);
            break;
        }
    }

    if (!cityFound) {
        // Default to Cameroon center
        var defaultLat = 7.3697;
        var defaultLon = 12.3547;
        var bbox = (defaultLon - 2) + ',' + (defaultLat - 2) + ',' + (defaultLon + 2) + ',' + (defaultLat + 2);
        var mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' + bbox +
                   '&layer=mapnik&marker=' + defaultLat + ',' + defaultLon;

        $('#location-map').attr('src', mapUrl);
        console.log('Using default Cameroon coordinates');
    }
}

// Get directions function
function getDirections() {
    var venue = "<?php echo addslashes($event['venue']); ?>";
    var location = "<?php echo addslashes($event['location']); ?>";
    var fullAddress = venue + ', ' + location + ', Cameroon';

    // Try multiple mapping services for better coverage in Cameroon
    var services = [
        {
            name: 'OpenStreetMap',
            url: 'https://www.openstreetmap.org/directions?to=' + encodeURIComponent(fullAddress)
        },
        {
            name: 'Google Maps',
            url: 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(fullAddress)
        }
    ];

    // Show options to user
    var serviceChoice = confirm('Choose mapping service:\nOK = OpenStreetMap\nCancel = Google Maps');
    var selectedService = serviceChoice ? services[0] : services[1];

    window.open(selectedService.url, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
