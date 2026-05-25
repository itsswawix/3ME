<?php
class Database {
    private $host = "localhost";
    private $db_name = "3me_hr";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // First try to connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->exec("SET NAMES utf8mb4");
        } catch(PDOException $exception) {
            // If database doesn't exist, try to create it
            if (strpos($exception->getMessage(), 'Unknown database') !== false) {
                try {
                    // Connect without database name to create it
                    $tempConn = new PDO(
                        "mysql:host=" . $this->host,
                        $this->username,
                        $this->password
                    );
                    $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Create database
                    $tempConn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    
                    // Now connect to the created database
                    $this->conn = new PDO(
                        "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                        $this->username,
                        $this->password
                    );
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    $this->conn->exec("SET NAMES utf8mb4");
                    
                    error_log("Database '{$this->db_name}' was created automatically");
                } catch(PDOException $createException) {
                    error_log("Database Creation Error: " . $createException->getMessage());
                    throw new Exception("Database connection failed. Please run setup_database.php first or check your MySQL configuration.");
                }
            } else {
                error_log("Database Connection Error: " . $exception->getMessage());
                throw new Exception("Database connection failed. Please check your MySQL configuration or run setup_database.php");
            }
        }

        return $this->conn;
    }
    
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    public function commit() {
        return $this->conn->commit();
    }
    
    public function rollback() {
        return $this->conn->rollBack();
    }
}
?>

