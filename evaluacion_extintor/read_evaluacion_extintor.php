<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_evaluacion'])) {
        $id_evaluacion = $_GET['id_evaluacion'];
        $logger->write('Fetching evaluacion_extintor with ID: ' . $id_evaluacion);

        $stmt = $db->prepare('
            SELECT * 
            FROM evaluacion_extintor 
            WHERE id_evaluacion = ?
            ORDER BY id_evaluacion_servicio ASC
        ');
        $stmt->execute([$id_evaluacion]);
        $evaluaciones_extintor = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($evaluaciones_extintor) {
            $logger->write('Evaluacion_extintor data fetched: ' . json_encode($evaluaciones_extintor));
            echo json_encode($evaluaciones_extintor);
        } else {
            $logger->write('No evaluacion_extintor found with ID: ' . $id_evaluacion);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron evaluaciones extintor para el ID especificado']);
        }
    } else {
        $logger->write('Missing id_evaluacion in GET data');
        http_response_code(400);
        echo json_encode(['message' => 'Falta el id_evaluacion en los datos de la solicitud']);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>