<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

try {
    $stmt = $db->query('SELECT 1');
    $logger->write('Database connection successful');
    echo 'Database connection successful';
} catch (PDOException $e) {
    $logger->write('Database connection failed: ' . $e->getMessage());
    echo 'Database connection failed: ' . $e->getMessage();
}
?>
