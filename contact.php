<?php
session_start();
$page_title = 'Contact Us';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate form data
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Prepare email
        $to = 'nopoleflairan@gmail.com';
        $email_subject = 'EventHive Contact Form: ' . $subject;
        
        $email_body = "
New contact form submission from EventHive:

Name: $name
Email: $email
Subject: $subject

Message:
$message

---
Sent from EventHive Contact Form
Time: " . date('Y-m-d H:i:s') . "
IP Address: " . $_SERVER['REMOTE_ADDR'];
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Send email
        if (mail($to, $email_subject, $email_body, $headers)) {
            $success_message = 'Thank you for your message! We will get back to you soon.';
            // Clear form data
            $name = $email = $subject = $message = '';
        } else {
            $error_message = 'Sorry, there was an error sending your message. Please try again later.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">Contact Us</h1>
                <p class="lead text-muted">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Send us a Message</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Choose a subject...</option>
                                        <option value="General Inquiry" <?php echo ($subject ?? '') == 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                        <option value="Event Booking Support" <?php echo ($subject ?? '') == 'Event Booking Support' ? 'selected' : ''; ?>>Event Booking Support</option>
                                        <option value="Payment Issues" <?php echo ($subject ?? '') == 'Payment Issues' ? 'selected' : ''; ?>>Payment Issues</option>
                                        <option value="Technical Support" <?php echo ($subject ?? '') == 'Technical Support' ? 'selected' : ''; ?>>Technical Support</option>
                                        <option value="Event Organizer Inquiry" <?php echo ($subject ?? '') == 'Event Organizer Inquiry' ? 'selected' : ''; ?>>Event Organizer Inquiry</option>
                                        <option value="Partnership Opportunity" <?php echo ($subject ?? '') == 'Partnership Opportunity' ? 'selected' : ''; ?>>Partnership Opportunity</option>
                                        <option value="Feedback" <?php echo ($subject ?? '') == 'Feedback' ? 'selected' : ''; ?>>Feedback</option>
                                        <option value="Other" <?php echo ($subject ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="6" 
                                              placeholder="Please describe your inquiry in detail..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6><i class="fas fa-envelope text-primary me-2"></i>Email</h6>
                                <p class="text-muted mb-0">nopoleflairan@gmail.com</p>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-phone text-primary me-2"></i>Phone</h6>
                                <p class="text-muted mb-0">+237 650 877 656</p>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-map-marker-alt text-primary me-2"></i>Location</h6>
                                <p class="text-muted mb-0">Yaoundé, Cameroon 🇨🇲</p>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-clock text-primary me-2"></i>Response Time</h6>
                                <p class="text-muted mb-0">We typically respond within 24 hours</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h6>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            How do I book tickets?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Simply browse events, select your preferred event, choose the number of tickets, and complete the booking process.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            What payment methods do you accept?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            We accept various payment methods including mobile money, bank transfers, and credit cards.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            Can I cancel my booking?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Cancellation policies vary by event. Please check the specific event's terms or contact us for assistance.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
