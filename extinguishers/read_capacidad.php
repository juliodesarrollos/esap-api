<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_capacidad'])) {
        $id_capacidad = $_GET['id_capacidad'];
        $logger->write('Fetching capacidad with ID: ' . $id_capacidad);

        $stmt = $db->prepare('SELECT * FROM capacidad WHERE id_capacidad = ?');
        $stmt->execute([$id_capacidad]);
        $capacidad = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($capacidad) {
            $logger->write('Capacidad data fetched: ' . json_encode($capacidad));
            echo json_encode($capacidad);
        } else {
            $logger->write('Capacidad not found with ID: ' . $id_capacidad);
            http_response_code(404);
            echo json_encode(['message' => 'Capacidad no encontrada']);
        }
    } else {
        $logger->write('Fetching all capacidades');

        $stmt = $db->query('SELECT * FROM capacidad');
        $capacidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All capacidad data fetched: ' . json_encode($capacidades));
        echo json_encode($capacidades);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>