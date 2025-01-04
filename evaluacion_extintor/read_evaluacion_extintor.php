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
            SELECT ee.*, e.id_extintor AS extintor_id, e.*
            FROM evaluacion_extintor ee
            JOIN extintor e ON ee.id_extintor = e.id_extintor
            WHERE ee.id_evaluacion = ?
            ORDER BY ee.id_evaluacion_extintor ASC
        ');
        $stmt->execute([$id_evaluacion]);
        $evaluaciones_extintor = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($evaluaciones_extintor) {
            $result = [];
            foreach ($evaluaciones_extintor as $evaluacion) {
                $extintor = [
                    'id_extintor' => $evaluacion['extintor_id'],
                    'nombre_extintor' => $evaluacion['nombre_extintor'],
                    'tipo_extintor' => $evaluacion['tipo_extintor'],
                    'capacidad_extintor' => $evaluacion['capacidad_extintor'],
                    'fecha_fabricacion' => $evaluacion['fecha_fabricacion'],
                    // Agrega aquí otros campos del extintor según sea necesario
                ];
                unset($evaluacion['extintor_id'], $evaluacion['nombre_extintor'], $evaluacion['tipo_extintor'], $evaluacion['capacidad_extintor'], $evaluacion['fecha_fabricacion']);
                $evaluacion['extintor'] = $extintor;
                $result[] = $evaluacion;
            }
            $logger->write('Evaluacion_extintor data fetched: ' . json_encode($result));
            echo json_encode($result);
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