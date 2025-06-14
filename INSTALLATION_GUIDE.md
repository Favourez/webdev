# 🚀 Event Booking System - Complete Installation Guide

## 📋 Quick Start Checklist

- [ ] XAMPP/WAMP installed and running
- [ ] Project files in web directory
- [ ] Database setup completed
- [ ] All features tested

## 🛠️ Step-by-Step Installation

### 1. **Prerequisites**
- **XAMPP/WAMP/LAMP**: Apache + MySQL + PHP 7.4+
- **Web Browser**: Chrome, Firefox, Safari, or Edge
- **Internet Connection**: For QR code generation and maps

### 2. **Download & Setup**
```bash
# Place all project files in your web server directory
# For XAMPP: C:\xampp\htdocs\webdev\
# For WAMP: C:\wamp64\www\webdev\
```

### 3. **Start Services**
- Start **Apache** web server
- Start **MySQL** database server
- Verify both are running (green lights in XAMPP)

### 4. **Database Setup (Automatic)**
1. Open browser and go to: `http://localhost/webdev/setup.php`
2. Click **"Run Database Setup"**
3. Wait for success message
4. Database and sample data will be created automatically

### 5. **Test Installation**
Visit: `http://localhost/webdev/test_functionality.php`

## 🎯 Feature Testing Guide

### **Map Functionality Test**
1. Go to: `http://localhost/webdev/event_details.php?id=1`
2. Scroll to "Location Map" section
3. Verify:
   - [ ] Map loads properly
   - [ ] "View on Map" button works
   - [ ] "Directions" button opens OpenStreetMap

### **QR Code Test**
1. Go to: `http://localhost/webdev/test_qr.php`
2. Verify all QR codes display properly
3. Test different providers and sizes

### **Complete User Flow Test**
1. **Register**: `http://localhost/webdev/auth/register.php`
2. **Login**: Use your new credentials
3. **Browse Events**: View event listings with maps
4. **Add to Cart**: Test AJAX cart functionality
5. **Checkout**: Complete a booking
6. **Dashboard**: View your bookings
7. **Download Ticket**: Test QR code ticket download

### **Admin Panel Test**
1. Go to: `http://localhost/webdev/admin/login.php`
2. Login with: `admin` / `admin123`
3. Test dashboard and event management

## 📱 QR Code & Download Features

### **QR Code Providers**
The system uses multiple QR code providers for reliability:
- **Primary**: QR Server API (`api.qrserver.com`)
- **Backup**: QuickChart.io (`quickchart.io`)
- **Fallback**: Automatic switching if one fails

### **Download Options**
- **HTML Ticket**: Complete ticket with QR code
- **QR Code Only**: PNG image of QR code
- **Print Version**: Print-friendly format

### **Testing Downloads**
1. Complete a booking
2. Go to Dashboard
3. Click download buttons:
   - 👁️ View Ticket
   - 📥 Download Ticket
   - 📱 Download QR Code

## 🔧 Troubleshooting

### **Common Issues & Solutions**

#### **Database Connection Error**
```
Error: Connection failed
```
**Solution:**
- Check MySQL is running in XAMPP
- Verify database credentials in `config/database.php`
- Run setup.php again

#### **QR Codes Not Displaying**
```
QR Code images broken/not loading
```
**Solution:**
- Check internet connection
- Test different providers at `test_qr.php`
- Clear browser cache

#### **Map Not Loading**
```
Map section shows placeholder
```
**Solution:**
- Check internet connection
- Verify JavaScript is enabled
- Check browser console for errors

#### **Download Not Working**
```
Files not downloading to computer
```
**Solution:**
- Check browser download settings
- Try right-click "Save As"
- Verify file permissions

#### **404 Errors**
```
The requested URL was not found
```
**Solution:**
- Verify Apache is running
- Check file paths are correct
- Ensure files are in correct directory

### **File Permissions (Linux/Mac)**
```bash
chmod 755 images/events/
chmod 644 *.php
```

### **PHP Extensions Check**
Required extensions:
- ✅ PDO
- ✅ PDO_MySQL
- ✅ cURL
- ✅ JSON

## 🌐 URLs Reference

### **Main Application**
- Homepage: `http://localhost/webdev/`
- Events: `http://localhost/webdev/events.php`
- Login: `http://localhost/webdev/auth/login.php`
- Register: `http://localhost/webdev/auth/register.php`
- Dashboard: `http://localhost/webdev/dashboard.php`

### **Admin Panel**
- Admin Login: `http://localhost/webdev/admin/login.php`
- Admin Dashboard: `http://localhost/webdev/admin/`

### **Testing Pages**
- Setup: `http://localhost/webdev/setup.php`
- Functionality Test: `http://localhost/webdev/test_functionality.php`
- QR Code Test: `http://localhost/webdev/test_qr.php`

### **Sample Event**
- Event Details: `http://localhost/webdev/event_details.php?id=1`

## 🎉 Success Indicators

### **✅ Installation Successful When:**
- [ ] Setup page shows "Setup Complete!"
- [ ] Test page shows all green checkmarks
- [ ] QR codes display properly
- [ ] Maps load with real locations
- [ ] Downloads work to local machine
- [ ] Admin panel accessible
- [ ] User registration/login works

### **✅ All Features Working When:**
- [ ] Can browse events with search/filter
- [ ] Event details show interactive maps
- [ ] Cart functionality works with AJAX
- [ ] Checkout process completes
- [ ] Dashboard shows bookings
- [ ] Tickets download with QR codes
- [ ] QR codes scan properly
- [ ] Admin panel manages events

## 📞 Support

### **If You Need Help:**
1. Check this guide first
2. Test with `test_functionality.php`
3. Verify all services are running
4. Check browser console for errors
5. Try different browsers

### **Common Test Data:**
- **Admin**: admin / admin123
- **Sample Events**: 5 events with future dates
- **Test Booking**: Use any event for testing

---

**🎯 Goal**: Complete working event booking system with maps and QR codes!

**⏱️ Setup Time**: 5-10 minutes for experienced users

**🔧 Difficulty**: Beginner-friendly with automatic setup
