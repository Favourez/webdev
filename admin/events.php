<?php
require_once '../config/database.php';
$page_title = 'Manage Events';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'add') {
            $stmt = $pdo->prepare("
                INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, image, price, total_tickets, available_tickets, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([
                $_POST['name'], $_POST['description'], $_POST['date'], $_POST['time'],
                $_POST['venue'], $_POST['location'], $_POST['organizer'], $_POST['organizer_contact'],
                $_POST['image'], $_POST['price'], $_POST['total_tickets'], $_POST['total_tickets']
            ]);
            $_SESSION['success_message'] = 'Event added successfully!';
            
        } elseif ($action == 'edit') {
            $stmt = $pdo->prepare("
                UPDATE events SET name=?, description=?, date=?, time=?, venue=?, location=?, 
                       organizer=?, organizer_contact=?, image=?, price=?, total_tickets=?, status=? 
                WHERE id=?
            ");
            $stmt->execute([
                $_POST['name'], $_POST['description'], $_POST['date'], $_POST['time'],
                $_POST['venue'], $_POST['location'], $_POST['organizer'], $_POST['organizer_contact'],
                $_POST['image'], $_POST['price'], $_POST['total_tickets'], $_POST['status'], $_POST['id']
            ]);
            $_SESSION['success_message'] = 'Event updated successfully!';
            
        } elseif ($action == 'delete') {
            $stmt = $pdo->prepare("UPDATE events SET status = 'deleted' WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $_SESSION['success_message'] = 'Event deleted successfully!';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }
    
    header('Location: events.php');
    exit();
}

// Get events
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM events WHERE status != 'deleted'";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR organizer LIKE ? OR location LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY date DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
    $_SESSION['error_message'] = 'Error fetching events: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-calendar-alt me-2"></i>Manage Events</h2>
        <p class="text-muted mb-0">Add, edit, and manage all events</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
        <i class="fas fa-plus me-2"></i>Add New Event
    </button>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Search Events</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Event name, organizer, location..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Events Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Events List (<?php echo count($events); ?> events)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No events found</h5>
                <p class="text-muted">Add your first event to get started.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="fas fa-plus me-2"></i>Add Event
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Tickets</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if ($event['image']): ?>
                                                <img src="../images/events/<?php echo htmlspecialchars($event['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($event['name']); ?>" 
                                                     class="rounded" style="width: 50px; height: 40px; object-fit: cover;"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <div style="display: none; width: 50px; height: 40px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php else: ?>
                                                <div style="width: 50px; height: 40px; background: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-calendar text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($event['organizer']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo date('M j, Y', strtotime($event['date'])); ?></div>
                                    <small class="text-muted"><?php echo date('g:i A', strtotime($event['time'])); ?></small>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($event['venue']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($event['location']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo number_format($event['price'], 0); ?> CFA</strong>
                                </td>
                                <td>
                                    <div><?php echo $event['available_tickets']; ?> / <?php echo $event['total_tickets']; ?></div>
                                    <small class="text-muted">
                                        <?php 
                                        $sold = $event['total_tickets'] - $event['available_tickets'];
                                        echo $sold . ' sold';
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $event['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="../event_details.php?id=<?php echo $event['id']; ?>" target="_blank" class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-delete" onclick="deleteEvent(<?php echo $event['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="addEventForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Event Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="organizer" class="form-label">Organizer *</label>
                            <input type="text" class="form-control" name="organizer" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date *</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time" class="form-label">Time *</label>
                            <input type="time" class="form-control" name="time" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="venue" class="form-label">Venue *</label>
                            <input type="text" class="form-control" name="venue" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control" name="location" placeholder="City, Cameroon" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="organizer_contact" class="form-label">Organizer Contact</label>
                            <input type="email" class="form-control" name="organizer_contact" placeholder="email@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Image Filename</label>
                            <input type="text" class="form-control" name="image" placeholder="event-image.jpg">
                            <small class="text-muted">Place image in images/events/ directory</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (CFA) *</label>
                            <input type="number" class="form-control" name="price" min="0" step="1000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="total_tickets" class="form-label">Total Tickets *</label>
                            <input type="number" class="form-control" name="total_tickets" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editEventForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <!-- Same fields as add form but with edit_ prefix for IDs -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Event Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_organizer" class="form-label">Organizer *</label>
                            <input type="text" class="form-control" name="organizer" id="edit_organizer" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description *</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_date" class="form-label">Date *</label>
                            <input type="date" class="form-control" name="date" id="edit_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_time" class="form-label">Time *</label>
                            <input type="time" class="form-control" name="time" id="edit_time" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_venue" class="form-label">Venue *</label>
                            <input type="text" class="form-control" name="venue" id="edit_venue" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_location" class="form-label">Location *</label>
                            <input type="text" class="form-control" name="location" id="edit_location" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_organizer_contact" class="form-label">Organizer Contact</label>
                            <input type="email" class="form-control" name="organizer_contact" id="edit_organizer_contact">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_image" class="form-label">Image Filename</label>
                            <input type="text" class="form-control" name="image" id="edit_image">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_status" class="form-label">Status *</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_price" class="form-label">Price (CFA) *</label>
                            <input type="number" class="form-control" name="price" id="edit_price" min="0" step="1000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_total_tickets" class="form-label">Total Tickets *</label>
                            <input type="number" class="form-control" name="total_tickets" id="edit_total_tickets" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editEvent(event) {
    document.getElementById('edit_id').value = event.id;
    document.getElementById('edit_name').value = event.name;
    document.getElementById('edit_organizer').value = event.organizer;
    document.getElementById('edit_description').value = event.description;
    document.getElementById('edit_date').value = event.date;
    document.getElementById('edit_time').value = event.time;
    document.getElementById('edit_venue').value = event.venue;
    document.getElementById('edit_location').value = event.location;
    document.getElementById('edit_organizer_contact').value = event.organizer_contact || '';
    document.getElementById('edit_image').value = event.image || '';
    document.getElementById('edit_status').value = event.status;
    document.getElementById('edit_price').value = event.price;
    document.getElementById('edit_total_tickets').value = event.total_tickets;
    
    new bootstrap.Modal(document.getElementById('editEventModal')).show();
}

function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${eventId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Set minimum date to today for new events
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="date"]').setAttribute('min', today);
    document.getElementById('edit_date').setAttribute('min', today);
});
</script>

<?php include 'includes/footer.php'; ?>
