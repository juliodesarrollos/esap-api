<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_servicio'])) {
        $id_servicio = $_GET['id_servicio'];
        $logger->write('Fetching servicio with ID: ' . $id_servicio);

        $stmt = $db->prepare('SELECT * FROM servicio WHERE id_servicio = ?');
        $stmt->execute([$id_servicio]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servicio) {
            $logger->write('Servicio data fetched: ' . json_encode($servicio));
            echo json_encode($servicio);
        } else {
            $logger->write('Servicio not found with ID: ' . $id_servicio);
            http_response_code(404);
            echo json_encode(['message' => 'Servicio no encontrado']);
        }
    } else {
        $logger->write('Fetching all servicios');

        $stmt = $db->query('SELECT * FROM servicio');
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All servicio data fetched: ' . json_encode($servicios));
        echo json_encode($servicios);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>