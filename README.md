# EventHive - Event Booking Platform

EventHive is Cameroon's premier event booking platform that connects event enthusiasts with amazing experiences across the country. Built with PHP and MySQL, it provides a seamless, secure, and user-friendly platform for discovering, booking, and managing event tickets.

## 🎯 Project Overview

EventHive serves as a comprehensive event management solution designed specifically for the Cameroon market, featuring CFA franc pricing, local event focus, and culturally appropriate design elements.

### 🌟 Key Features

#### For Event Attendees
- **Event Discovery**: Browse and search events by name, location, and date
- **Secure Booking**: Safe and reliable ticket purchasing system
- **Digital Tickets**: QR code-enabled tickets for easy event entry
- **User Dashboard**: Manage bookings and view event history
- **Profile Management**: Update personal information and preferences
- **Mobile-Friendly**: Responsive design works on all devices

#### For Event Organizers
- **Event Management**: Create and manage event listings
- **Booking Analytics**: Track ticket sales and attendee data
- **Customer Communication**: Direct access to attendee information

#### For Administrators
- **Comprehensive Dashboard**: Real-time analytics and reporting
- **User Management**: Monitor and manage user accounts
- **Event Oversight**: Approve and manage all platform events
- **Data Export**: Generate reports for business intelligence

## 🏗️ Technical Architecture

### Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Libraries**: Font Awesome, jQuery
- **Security**: Password hashing, prepared statements, session management

### File Structure
```
EventHive/
├── admin/                  # Admin panel
│   ├── bookings.php       # Booking management
│   ├── events.php         # Event management
│   ├── users.php          # User management
│   ├── reports.php        # Analytics and reports
│   └── includes/          # Admin components
├── auth/                  # Authentication system
│   ├── login.php          # User login
│   ├── register.php       # User registration
│   └── logout.php         # Session termination
├── config/                # Configuration files
│   └── database.php       # Database connection
├── includes/              # Shared components
│   ├── header.php         # Site header
│   ├── footer.php         # Site footer
│   ├── session.php        # Session management
│   └── qr_generator.php   # QR code generation
├── images/                # Event images
│   └── events/            # Event-specific images
├── css/                   # Stylesheets
├── js/                    # JavaScript files
├── ajax/                  # AJAX endpoints
├── database/              # Database schema
└── *.php                  # Main application pages
```

## 🚀 Installation Guide

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher with extensions:
  - PDO MySQL
  - GD (for image processing)
  - Mail (for notifications)
- MySQL 5.7 or higher

### Setup Steps

1. **Clone/Download the Project**
   ```bash
   # Place files in your web server directory
   # e.g., /var/www/html/eventhive or C:\xampp\htdocs\eventhive
   ```

2. **Database Configuration**
   ```sql
   -- Create database
   CREATE DATABASE eventhive;

   -- Import schema
   mysql -u username -p eventhive < database/schema.sql
   ```

3. **Configure Database Connection**
   ```php
   // Edit config/database.php
   $host = 'localhost';
   $dbname = 'eventhive';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Set File Permissions**
   ```bash
   chmod 755 images/events/
   chmod 644 config/database.php
   ```

5. **Create Admin Account**
   - Visit: `your-domain.com/admin/create_admin.php`
   - Create your admin credentials
   - Delete the create_admin.php file after use

## 📱 User Guide

### For Event Attendees

1. **Registration & Login**
   - Create account with email and password
   - Verify email address
   - Login to access full features

2. **Browsing Events**
   - Use search filters (location, date, keywords)
   - View event details and images
   - Check ticket availability and pricing

3. **Booking Process**
   - Add tickets to cart
   - Review booking details
   - Complete secure checkout
   - Receive confirmation email

4. **Managing Bookings**
   - Access dashboard for booking history
   - Download QR code tickets
   - Print receipt copies
   - Update profile information

### For Administrators

1. **Admin Dashboard**
   - Login at `/admin/login.php`
   - View real-time statistics
   - Monitor platform activity

2. **Event Management**
   - Create new events
   - Upload event images
   - Set pricing and availability
   - Monitor ticket sales

3. **User Management**
   - View user accounts
   - Monitor user activity
   - Handle support requests

4. **Reporting**
   - Generate sales reports
   - Export user data
   - Analyze booking trends

## 🔒 Security Features

### Data Protection
- **Password Security**: Bcrypt hashing with salt
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization and output encoding
- **Session Security**: Secure session handling
- **File Upload Security**: Image validation and sanitization

### Privacy Compliance
- **Data Minimization**: Collect only necessary information
- **User Rights**: Profile management and account deletion
- **Secure Storage**: Encrypted sensitive data
- **Access Controls**: Role-based permissions

## 💰 Cameroon-Specific Features

### Localization
- **Currency**: All prices in CFA francs
- **Locations**: Focus on major Cameroon cities (Yaoundé, Douala, Bafoussam)
- **Language**: English with local context
- **Cultural Elements**: Cameroon flag and local references

### Event Categories
- Technology conferences
- Music festivals
- Business networking
- Art exhibitions
- Food & wine events
- Educational workshops
- Cultural celebrations
- Sports events

## 📧 Contact & Support

### Contact Information
- **Email**: nopoleflairan@gmail.com
- **Phone**: +237 123 456 789
- **Location**: Yaoundé, Cameroon 🇨🇲

### Support Features
- Contact form with email notifications
- FAQ section
- User profile management
- Account deletion option

## 📄 Legal Pages

### Included Legal Documents
- **Terms of Service**: Comprehensive user agreement
- **Privacy Policy**: Data protection and user rights
- **About Page**: Platform introduction and mission
- **Contact Page**: Support and communication

## 🔧 Maintenance & Updates

### Regular Tasks
- Database backups
- Security updates
- Performance monitoring
- User feedback review

### Monitoring
- Event booking success rates
- User registration trends
- Payment processing status
- System performance metrics

## 🎨 Design Philosophy

EventHive maintains a clean, professional design that builds trust while remaining accessible to all users. The interface prioritizes:

- **Clarity**: Clear navigation and information hierarchy
- **Trust**: Professional appearance and secure processes
- **Accessibility**: Works on all devices and connection speeds
- **Local Appeal**: Cameroon-focused content and imagery

## 📊 Analytics & Reporting

### Available Reports
- Event booking statistics
- User registration trends
- Revenue analytics
- Popular event categories
- Geographic distribution

### Export Options
- CSV format for spreadsheet analysis
- PDF reports for presentations
- Real-time dashboard metrics

## 🚀 Future Enhancements

### Planned Features
- Mobile app development
- Advanced payment integrations
- Event organizer self-service portal
- Enhanced analytics dashboard
- Multi-language support

---

**EventHive** - Connecting Cameroon to Amazing Events 🇨🇲

*Built with ❤️ for the Cameroon event community*
