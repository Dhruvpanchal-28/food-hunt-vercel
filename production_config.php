<?php
/*
 * Production Configuration File
 * Deploy this file to your production server
 */

// Database Configuration for Production
define('DB_HOST', 'localhost');
define('DB_USER', 'your_production_db_user');
define('DB_PASS', 'your_production_db_password');
define('DB_NAME', 'your_production_db_name');

// Application Configuration
define('APP_NAME', 'Food Hunt');
define('APP_URL', 'https://yourdomain.com');
define('APP_ENV', 'production');

// Security Settings
define('ENCRYPTION_KEY', 'your_32_character_encryption_key');
define('SESSION_LIFETIME', 3600); // 1 hour

// Error Reporting (Production)
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// CORS Settings (if needed)
header('Access-Control-Allow-Origin: ' . APP_URL);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Production Database Connection
function getProductionConnection() {
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$con) {
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Database connection failed. Please try again later.");
    }
    
    // Set charset
    mysqli_set_charset($con, 'utf8mb4');
    
    return $con;
}

// Email Configuration (for production emails)
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_smtp_username');
define('SMTP_PASSWORD', 'your_smtp_password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', APP_NAME);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// SSL Redirect (if SSL is enabled)
if (APP_ENV === 'production' && empty($_SERVER['HTTPS'])) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Rate Limiting (simple implementation)
function checkRateLimit($limit = 100, $window = 3600) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'rate_limit_' . md5($ip);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'start' => time()];
    }
    
    $data = $_SESSION[$key];
    
    if (time() - $data['start'] > $window) {
        $_SESSION[$key] = ['count' => 1, 'start' => time()];
        return true;
    }
    
    if ($data['count'] >= $limit) {
        http_response_code(429);
        die('Too many requests. Please try again later.');
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

// Input Sanitization
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('CSRF token validation failed.');
    }
    return true;
}

// Log function
function logMessage($message, $level = 'INFO') {
    $logFile = __DIR__ . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
