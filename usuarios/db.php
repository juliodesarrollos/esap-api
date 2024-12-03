<?php
require 'log.php';

class Database {
    private static $instance = null;
    private $connection;
    private $host = '127.0.0.1';
    private $dbname = 'u943683090_esap_stg';
    private $username = 'u943683090_esap_stg';
    private $password = 'E.s.a.p.1';
    private $logger;

    private function __construct() {
        $this->logger = new Log();
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->logger->write('Database connection established');
        } catch (PDOException $e) {
            $this->logger->write('Connection failed: ' . $e->getMessage());
            echo 'Connection failed: ' . $e->getMessage();
            exit();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
?>
