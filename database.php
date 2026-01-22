<?php
/**
 * Database Configuration
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change if different
define('DB_PASS', '');              // XAMPP default is empty
define('DB_NAME', 'cardekho_db');

/**
 * Database Connection Class (Singleton Pattern)
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

/**
 * Get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Base URL
 */
define('BASE_URL', 'http://localhost/cardekho/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
?>
