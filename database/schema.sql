-- Create database
CREATE DATABASE IF NOT EXISTS event_booking_system;
USE event_booking_system;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    location VARCHAR(200) NOT NULL,
    organizer VARCHAR(100) NOT NULL,
    organizer_contact VARCHAR(100),
    image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    total_tickets INT NOT NULL,
    available_tickets INT NOT NULL,
    status ENUM('active', 'inactive', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_reference VARCHAR(50) UNIQUE NOT NULL,
    attendee_name VARCHAR(100) NOT NULL,
    attendee_email VARCHAR(100) NOT NULL,
    attendee_phone VARCHAR(20),
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    booking_status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    qr_code VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
INSERT INTO admins (username, email, password, full_name) VALUES 
('admin', 'admin@eventbooking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator');

-- Insert sample events (using future dates and Cameroon locations)
-- Note: Image files should be placed in images/events/ directory
--
-- SOLUTIONS FOR IMAGES (choose one):
-- 1. Download real images from Unsplash/Pexels and save with these filenames
-- 2. Use create_svg_images.php to create SVG images (no GD required)
-- 3. Use create_html_images.php to create HTML-based images (no GD required)
-- 4. Set image to NULL to use default placeholder icons (see alternative below)

INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, image, price, total_tickets, available_tickets) VALUES
('Tech Conference 2025', 'Annual technology conference featuring latest trends in AI and Web Development', '2025-03-15', '09:00:00', 'Palais des Congrès', 'Yaoundé, Cameroon', 'Tech Events Cameroon', 'contact@techevents.cm', 'techconference.jpg', 150000, 500, 500),
('Music Festival Summer', 'Three-day music festival featuring top artists from around the world', '2025-06-20', '18:00:00', 'Stade Omnisports', 'Douala, Cameroon', 'Music Promoters Cameroon', 'info@musicfest.cm', 'musicfestival.jpg', 85000, 10000, 10000),
('Food & Wine Expo', 'Culinary experience with renowned chefs and wine tastings', '2025-04-10', '12:00:00', 'Hilton Hotel Ballroom', 'Douala, Cameroon', 'Culinary Events Cameroon', 'events@culinary.cm', 'foodwine.png', 45000, 200, 200),
('Business Networking Event', 'Connect with industry professionals and expand your network', '2025-02-28', '19:00:00', 'Centre de Conférences', 'Yaoundé, Cameroon', 'Professional Network Cameroon', 'network@business.cm', 'business.jpg', 25000, 150, 150),
('Art Gallery Opening', 'Exclusive opening of contemporary art exhibition', '2025-03-05', '18:30:00', 'Galerie d\'Art Moderne', 'Bafoussam, Cameroon', 'Art Collective Cameroon', 'info@artgallery.cm', 'artgallery.avif', 5000, 100, 100);

-- ALTERNATIVE: Use NULL for images (shows placeholder icons instead)
-- Uncomment the lines below and comment out the lines above if you prefer placeholder icons
/*
INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, image, price, total_tickets, available_tickets) VALUES
('Tech Conference 2025', 'Annual technology conference featuring latest trends in AI and Web Development', '2025-03-15', '09:00:00', 'Palais des Congrès', 'Yaoundé, Cameroon', 'Tech Events Cameroon', 'contact@techevents.cm', NULL, 150000, 500, 500),
('Music Festival Summer', 'Three-day music festival featuring top artists from around the world', '2025-06-20', '18:00:00', 'Stade Omnisports', 'Douala, Cameroon', 'Music Promoters Cameroon', 'info@musicfest.cm', NULL, 85000, 10000, 10000),
('Food & Wine Expo', 'Culinary experience with renowned chefs and wine tastings', '2025-04-10', '12:00:00', 'Hilton Hotel Ballroom', 'Douala, Cameroon', 'Culinary Events Cameroon', 'events@culinary.cm', NULL, 45000, 200, 200),
('Business Networking Event', 'Connect with industry professionals and expand your network', '2025-02-28', '19:00:00', 'Centre de Conférences', 'Yaoundé, Cameroon', 'Professional Network Cameroon', 'network@business.cm', NULL, 25000, 150, 150),
('Art Gallery Opening', 'Exclusive opening of contemporary art exhibition', '2025-03-05', '18:30:00', 'Galerie d\'Art Moderne', 'Bafoussam, Cameroon', 'Art Collective Cameroon', 'info@artgallery.cm', NULL, 5000, 100, 100);
*/
