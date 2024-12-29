<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_servicio'])) {
        $id_servicio = $_GET['id_servicio'];
        $logger->write('Fetching servicio with ID: ' . $id_servicio);

        $stmt = $db->prepare('
            SELECT * 
            FROM servicio 
            WHERE id_servicio = ?
            ORDER BY id_servicio ASC
        ');
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
    } elseif (isset($_GET['id_empresa'])) {
        $id_empresa = $_GET['id_empresa'];
        $logger->write('Fetching servicios with empresa ID: ' . $id_empresa);

        $stmt = $db->prepare('
            SELECT s.*, 
                   (SELECT COUNT(*) FROM evaluacion e WHERE e.id_servicio = s.id_servicio) AS numero_evaluaciones
            FROM servicio s
            WHERE s.id_empresa = ?
            ORDER BY s.id_servicio ASC
        ');
        $stmt->execute([$id_empresa]);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($servicios) {
            $logger->write('Servicios data fetched: ' . json_encode($servicios));
            echo json_encode($servicios);
        } else {
            $logger->write('No servicios found with empresa ID: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron servicios para la empresa especificada']);
        }
    } else {
        $logger->write('Fetching all servicios');

        $stmt = $db->query('
            SELECT * 
            FROM servicio 
            ORDER BY id_servicio ASC
        ');
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