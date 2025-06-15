<?php
session_start();
$page_title = 'Terms of Service';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">Terms of Service</h1>
                <p class="lead text-muted">Last updated: <?php echo date('F d, Y'); ?></p>
            </div>
            
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4">1. Acceptance of Terms</h2>
                    <p>By accessing and using EventHive ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">2. Description of Service</h2>
                    <p>EventHive is an online platform that allows users to discover, browse, and purchase tickets for various events taking place in Cameroon. We act as an intermediary between event organizers and ticket purchasers.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">3. User Accounts</h2>
                    <h5 class="fw-semibold">3.1 Account Creation</h5>
                    <p>To use certain features of our service, you must create an account. You agree to provide accurate, current, and complete information during the registration process.</p>
                    
                    <h5 class="fw-semibold">3.2 Account Security</h5>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
                    
                    <h5 class="fw-semibold">3.3 Account Termination</h5>
                    <p>We reserve the right to terminate or suspend your account at any time for violations of these terms or for any other reason we deem appropriate.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">4. Ticket Purchases</h2>
                    <h5 class="fw-semibold">4.1 Pricing</h5>
                    <p>All ticket prices are displayed in CFA francs and include applicable taxes unless otherwise stated. Prices are subject to change without notice.</p>
                    
                    <h5 class="fw-semibold">4.2 Payment</h5>
                    <p>Payment must be made in full at the time of purchase. We accept various payment methods including mobile money, bank transfers, and credit cards.</p>
                    
                    <h5 class="fw-semibold">4.3 Confirmation</h5>
                    <p>Upon successful payment, you will receive a confirmation email with your digital ticket(s) containing QR codes for event entry.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">5. Refunds and Cancellations</h2>
                    <h5 class="fw-semibold">5.1 Event Cancellation</h5>
                    <p>If an event is cancelled by the organizer, you will receive a full refund within 7-14 business days.</p>
                    
                    <h5 class="fw-semibold">5.2 User Cancellation</h5>
                    <p>Refund policies for user-initiated cancellations vary by event and are determined by the event organizer. Please check the specific event's refund policy before purchasing.</p>
                    
                    <h5 class="fw-semibold">5.3 No-Show Policy</h5>
                    <p>No refunds will be provided for failure to attend an event ("no-show").</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">6. User Conduct</h2>
                    <p>You agree not to:</p>
                    <ul>
                        <li>Use the service for any unlawful purpose or in violation of any local, state, national, or international law</li>
                        <li>Transmit any material that is defamatory, offensive, or otherwise objectionable</li>
                        <li>Attempt to gain unauthorized access to any portion of the service</li>
                        <li>Interfere with or disrupt the service or servers connected to the service</li>
                        <li>Reproduce, duplicate, copy, sell, or resell any portion of the service without permission</li>
                    </ul>
                    
                    <h2 class="fw-bold mb-4 mt-5">7. Intellectual Property</h2>
                    <p>The service and its original content, features, and functionality are and will remain the exclusive property of EventHive and its licensors. The service is protected by copyright, trademark, and other laws.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">8. Privacy Policy</h2>
                    <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the service, to understand our practices.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">9. Disclaimers</h2>
                    <h5 class="fw-semibold">9.1 Service Availability</h5>
                    <p>We do not guarantee that the service will be available at all times. We may experience hardware, software, or other problems or need to perform maintenance related to the service.</p>
                    
                    <h5 class="fw-semibold">9.2 Event Information</h5>
                    <p>While we strive to provide accurate event information, we are not responsible for any errors or omissions in event details provided by organizers.</p>
                    
                    <h5 class="fw-semibold">9.3 Third-Party Events</h5>
                    <p>EventHive is not responsible for the quality, safety, or legality of events listed on our platform. Event organizers are solely responsible for their events.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">10. Limitation of Liability</h2>
                    <p>In no event shall EventHive, its directors, employees, or agents be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">11. Governing Law</h2>
                    <p>These terms shall be interpreted and governed by the laws of Cameroon. Any disputes arising from these terms or your use of the service shall be subject to the jurisdiction of the courts of Cameroon.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">12. Changes to Terms</h2>
                    <p>We reserve the right to modify or replace these terms at any time. If a revision is material, we will try to provide at least 30 days notice prior to any new terms taking effect.</p>
                    
                    <h2 class="fw-bold mb-4 mt-5">13. Contact Information</h2>
                    <p>If you have any questions about these Terms of Service, please contact us at:</p>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-1"><strong>Email:</strong> nopoleflairan@gmail.com</p>
                        <p class="mb-1"><strong>Phone:</strong> +237 650 877 656</p>
                        <p class="mb-0"><strong>Address:</strong> Yaoundé, Cameroon</p>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> By continuing to use EventHive after any changes to these terms, you agree to be bound by the revised terms.
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Return to Home
                </a>
                <a href="contact.php" class="btn btn-outline-primary ms-2">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
