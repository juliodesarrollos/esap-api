<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_evaluacion'])) {
        $id_evaluacion = $_GET['id_evaluacion'];
        $logger->write('Fetching evaluacion with ID: ' . $id_evaluacion);

        $stmt = $db->prepare('
            SELECT * 
            FROM evaluacion 
            WHERE id_evaluacion = ?
            ORDER BY id_evaluacion ASC
        ');
        $stmt->execute([$id_evaluacion]);
        $evaluacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($evaluacion) {
            $logger->write('Evaluacion data fetched: ' . json_encode($evaluacion));
            echo json_encode($evaluacion);
        } else {
            $logger->write('Evaluacion not found with ID: ' . $id_evaluacion);
            http_response_code(404);
            echo json_encode(['message' => 'Evaluacion no encontrada']);
        }
    } elseif (isset($_GET['id_servicio'])) {
        $id_servicio = $_GET['id_servicio'];
        $logger->write('Fetching evaluaciones with servicio ID: ' . $id_servicio);

        $stmt = $db->prepare('
            SELECT * 
            FROM evaluacion 
            WHERE id_servicio = ?
            ORDER BY id_evaluacion ASC
        ');
        $stmt->execute([$id_servicio]);
        $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($evaluaciones) {
            $logger->write('Evaluaciones data fetched: ' . json_encode($evaluaciones));
            echo json_encode($evaluaciones);
        } else {
            $logger->write('No evaluaciones found with servicio ID: ' . $id_servicio);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron evaluaciones para el servicio especificado']);
        }
    } else {
        $logger->write('Fetching all evaluaciones');

        $stmt = $db->query('
            SELECT * 
            FROM evaluacion 
            ORDER BY id_evaluacion ASC
        ');
        $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All evaluacion data fetched: ' . json_encode($evaluaciones));
        echo json_encode($evaluaciones);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>