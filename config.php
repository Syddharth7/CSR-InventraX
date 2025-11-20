<?php
// config.php - Database Configuration & Session Management

// Start session
session_start();

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventrax_db');

// Application settings
define('SITE_NAME', 'InventraX');
define('BASE_URL', 'http://localhost/inventrax/');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);

// Create database connection
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            error_log("Query Error: " . $this->conn->error);
        }
        return $result;
    }
}

// Helper functions
function redirect($url) {
    header("Location: " . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        redirect('dashboard.php');
    }
}

function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_destroy();
            redirect('login.php?timeout=1');
        }
    }
    $_SESSION['last_activity'] = time();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function logActivity($user_id, $action, $module = '', $details = '') {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, module, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action, $module, $details, $ip);
    $stmt->execute();
    $stmt->close();
}

function createNotification($type, $message, $user_id = null) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $type, $message);
    $stmt->execute();
    $stmt->close();
}

function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

// Check session timeout if logged in
if (isLoggedIn()) {
    checkSessionTimeout();
}

// Get database connection
$db = Database::getInstance();
$conn = $db->getConnection();
?>