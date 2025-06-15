<?php
/**
 * Simple QR Code Generator using Google Charts API
 * This is a fallback solution that works without external libraries
 */

class QRCodeGenerator {
    
    /**
     * Generate QR code using QR Server API (more reliable)
     * @param string $data The data to encode
     * @param int $size The size of the QR code (default: 200)
     * @return string The QR code image URL
     */
    public static function generateQRCode($data, $size = 200) {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}&format=png&margin=10";
    }
    
    /**
     * Generate QR code and save as image
     * @param string $data The data to encode
     * @param string $filename The filename to save
     * @param int $size The size of the QR code
     * @return bool Success status
     */
    public static function saveQRCode($data, $filename, $size = 200) {
        $qrUrl = self::generateQRCode($data, $size);
        
        // Create directory if it doesn't exist
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Download and save the image
        $imageData = @file_get_contents($qrUrl);
        if ($imageData !== false) {
            return file_put_contents($filename, $imageData) !== false;
        }
        
        return false;
    }
    
    /**
     * Generate QR code for booking
     * @param array $booking Booking details
     * @return string QR code URL
     */
    public static function generateBookingQR($booking) {
        // Create URL that works for both mobile and desktop
        // Mobile devices will download the ticket, desktop will redirect to website
        $ticketUrl = self::getBaseUrl() . '/qr_ticket.php?ref=' . urlencode($booking['booking_reference']);

        // Use alternative QR server for better reliability
        return self::generateQRCodeAlt($ticketUrl, 150);
    }

    /**
     * Get base URL for the application
     * @return string Base URL
     */
    private static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');

        // Remove any trailing slashes and ensure we're at the root of the application
        $base_path = rtrim($script_dir, '/');
        if (strpos($base_path, '/webdev') === false) {
            $base_path = '/webdev';
        }

        return $protocol . '://' . $host . $base_path;
    }
    
    /**
     * Alternative QR code generator using QR Server API
     * @param string $data The data to encode
     * @param int $size The size of the QR code
     * @return string The QR code image URL
     */
    public static function generateQRCodeAlt($data, $size = 200) {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}&format=png&margin=10&ecc=M";
    }

    /**
     * Generate QR code using QuickChart API (backup option)
     * @param string $data The data to encode
     * @param int $size The size of the QR code
     * @return string The QR code image URL
     */
    public static function generateQRCodeBackup($data, $size = 200) {
        $encodedData = urlencode($data);
        return "https://quickchart.io/qr?text={$encodedData}&size={$size}";
    }

    /**
     * Get QR code with fallback options
     * @param string $data The data to encode
     * @param int $size The size of the QR code
     * @return string The QR code image URL
     */
    public static function getQRCodeWithFallback($data, $size = 200) {
        // Try primary service first
        $primaryUrl = self::generateQRCodeAlt($data, $size);

        // Return primary URL (browser will handle fallback)
        return $primaryUrl;
    }
}

/**
 * Ticket Generator Class
 */
class TicketGenerator {
    
    /**
     * Generate ticket HTML
     * @param array $booking Booking details
     * @param array $event Event details
     * @return string HTML content
     */
    public static function generateTicketHTML($booking, $event) {
        $qrCodeUrl = QRCodeGenerator::generateBookingQR($booking);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Event Ticket - ' . htmlspecialchars($event['name']) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                .ticket { background: white; max-width: 600px; margin: 0 auto; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .ticket-header { background: linear-gradient(135deg, #007bff, #0056b3); color: white; padding: 20px; text-align: center; }
                .ticket-body { padding: 30px; }
                .ticket-section { margin-bottom: 20px; }
                .ticket-title { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                .ticket-subtitle { font-size: 16px; opacity: 0.9; }
                .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #eee; }
                .info-label { font-weight: bold; color: #333; }
                .info-value { color: #666; }
                .qr-section { text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; }
                .qr-code { margin: 10px 0; }
                .booking-ref { font-size: 18px; font-weight: bold; color: #007bff; margin-top: 10px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                @media print { body { background: white; } .ticket { box-shadow: none; } }
            </style>
        </head>
        <body>
            <div class="ticket">
                <div class="ticket-header">
                    <div class="ticket-title">' . htmlspecialchars($event['name']) . '</div>
                    <div class="ticket-subtitle">Event Ticket</div>
                </div>
                
                <div class="ticket-body">
                    <div class="ticket-section">
                        <div class="info-row">
                            <span class="info-label">Event Date:</span>
                            <span class="info-value">' . date('l, F j, Y', strtotime($event['date'])) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Event Time:</span>
                            <span class="info-value">' . date('g:i A', strtotime($event['time'])) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Venue:</span>
                            <span class="info-value">' . htmlspecialchars($event['venue']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Location:</span>
                            <span class="info-value">' . htmlspecialchars($event['location']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Organizer:</span>
                            <span class="info-value">' . htmlspecialchars($event['organizer']) . '</span>
                        </div>
                    </div>
                    
                    <div class="ticket-section">
                        <div class="info-row">
                            <span class="info-label">Attendee Name:</span>
                            <span class="info-value">' . htmlspecialchars($booking['attendee_name']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Attendee Email:</span>
                            <span class="info-value">' . htmlspecialchars($booking['attendee_email']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Number of Tickets:</span>
                            <span class="info-value">' . $booking['quantity'] . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Amount:</span>
                            <span class="info-value">' . number_format($booking['total_amount'], 0) . ' CFA</span>
                        </div>
                    </div>
                    
                    <div class="qr-section">
                        <div><strong>Scan QR Code for Entry</strong></div>
                        <div class="qr-code">
                            <img src="' . $qrCodeUrl . '" alt="QR Code" style="max-width: 150px;">
                        </div>
                        <div class="booking-ref">Booking Reference: ' . htmlspecialchars($booking['booking_reference']) . '</div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Please present this ticket (digital or printed) at the event entrance.</p>
                    <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
?>
