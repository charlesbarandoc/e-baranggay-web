<?php
// db.php - Improved database connection with error handling
session_start();

$host = 'localhost';
$db   = 'barangay_portal';
$user = 'root';
$pass = ''; // Change this in production

$pdo = null;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Log error to file instead of displaying to user
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show user-friendly error
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection error. Please contact the administrator.'
    ]));
}

// CSRF token generation
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token validation
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Check if user is admin (simple implementation - enhance for production)
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}
?>