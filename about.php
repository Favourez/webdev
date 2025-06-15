<?php
session_start();
$page_title = 'About Us';
include 'includes/header.php';
?>

<div class="container py-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">About EventHive</h1>
            <p class="lead">Cameroon's premier platform for discovering and booking tickets to the most popular and exciting events across the country.</p>
            <p class="text-muted">From tech conferences in Yaoundé to music festivals in Douala, we connect event enthusiasts with unforgettable experiences throughout Cameroon.</p>
        </div>
        <div class="col-lg-6 text-center">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                <i class="fas fa-calendar-alt fa-5x"></i>
            </div>
        </div>
    </div>
    
    <!-- Mission Section -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Our Mission</h2>
                        <div class="bg-primary" style="width: 60px; height: 4px; margin: 0 auto;"></div>
                    </div>
                    <p class="lead text-center">
                        To revolutionize the event discovery and booking experience in Cameroon by providing a seamless, 
                        secure, and user-friendly platform that connects event organizers with enthusiastic attendees.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- What We Offer -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <h2 class="text-center fw-bold mb-5">What We Offer</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-search fa-2x"></i>
                            </div>
                            <h5 class="card-title">Event Discovery</h5>
                            <p class="card-text">Easily discover amazing events happening across Cameroon, from business conferences to cultural festivals.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-ticket-alt fa-2x"></i>
                            </div>
                            <h5 class="card-title">Secure Booking</h5>
                            <p class="card-text">Book your tickets securely with our trusted payment system and receive instant confirmation.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-mobile-alt fa-2x"></i>
                            </div>
                            <h5 class="card-title">Digital Tickets</h5>
                            <p class="card-text">Get digital tickets with QR codes that you can download and use offline at the event entrance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Why Choose EventHive -->
    <div class="row mb-5">
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Why Choose EventHive?</h2>
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="d-flex">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold">Trusted & Secure</h6>
                            <p class="text-muted mb-0">Your personal information and payments are protected with industry-standard security measures.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="d-flex">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold">Local Focus</h6>
                            <p class="text-muted mb-0">Specifically designed for Cameroon, featuring events in major cities like Yaoundé, Douala, and Bafoussam.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="d-flex">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold">24/7 Support</h6>
                            <p class="text-muted mb-0">Our dedicated support team is always ready to help you with any questions or issues.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="d-flex">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold">CFA Currency</h6>
                            <p class="text-muted mb-0">All prices are displayed in CFA francs, making it easy for local users to understand costs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Popular Event Categories</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-laptop text-primary me-2"></i>Technology Conferences</li>
                        <li class="mb-2"><i class="fas fa-music text-primary me-2"></i>Music Festivals</li>
                        <li class="mb-2"><i class="fas fa-briefcase text-primary me-2"></i>Business Networking</li>
                        <li class="mb-2"><i class="fas fa-palette text-primary me-2"></i>Art Exhibitions</li>
                        <li class="mb-2"><i class="fas fa-utensils text-primary me-2"></i>Food & Wine Events</li>
                        <li class="mb-2"><i class="fas fa-graduation-cap text-primary me-2"></i>Educational Workshops</li>
                        <li class="mb-2"><i class="fas fa-heart text-primary me-2"></i>Cultural Celebrations</li>
                        <li class="mb-2"><i class="fas fa-running text-primary me-2"></i>Sports Events</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-5">
                    <h2 class="text-center fw-bold mb-5">EventHive by the Numbers</h2>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="h1 fw-bold">1000+</div>
                            <p class="mb-0">Events Listed</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h1 fw-bold">5000+</div>
                            <p class="mb-0">Happy Customers</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h1 fw-bold">10+</div>
                            <p class="mb-0">Cities Covered</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="h1 fw-bold">99%</div>
                            <p class="mb-0">Customer Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <h2 class="text-center fw-bold mb-5">Our Commitment</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users text-primary me-2"></i>For Event Attendees</h5>
                            <p class="card-text">We provide a seamless experience for discovering and booking tickets to the best events in Cameroon. Our platform ensures you never miss out on amazing experiences.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-bullhorn text-primary me-2"></i>For Event Organizers</h5>
                            <p class="card-text">We help event organizers reach their target audience and manage ticket sales efficiently. Our platform provides tools to maximize event success.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-light">
                <div class="card-body text-center p-5">
                    <h3 class="fw-bold mb-3">Ready to Discover Amazing Events?</h3>
                    <p class="lead mb-4">Join thousands of event enthusiasts who trust EventHive for their event booking needs.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Events
                        </a>
                        <a href="contact.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
