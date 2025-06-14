<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Get current user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current admin ID
function getCurrentAdminId() {
    return isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: auth/login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        // Check if we're already in admin directory
        $current_path = $_SERVER['REQUEST_URI'];
        if (strpos($current_path, '/admin/') !== false) {
            header('Location: login.php');
        } else {
            header('Location: admin/login.php');
        }
        exit();
    }
}

// Logout user
function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Logout admin
function adminLogout() {
    session_destroy();
    header('Location: admin/login.php');
    exit();
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
