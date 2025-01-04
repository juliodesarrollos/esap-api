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
            SELECT ee.*, e.id_extintor AS extintor_id, e.*, m.nombre AS marca, a.nombre AS agente
            FROM evaluacion_extintor ee
            JOIN extintor e ON ee.id_extintor = e.id_extintor
            JOIN marca m ON e.id_marca = m.id_marca
            JOIN agente a ON e.id_agente = a.id_agente
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
                    'id_empresa' => $evaluacion['id_empresa'],
                    'posicion_extintor' => $evaluacion['posicion_extintor'],
                    'id_agente' => $evaluacion['id_agente'],
                    'agente' => $evaluacion['agente'],
                    'id_capacidad' => $evaluacion['id_capacidad'],
                    'capacidad' => $evaluacion['capacidad'],
                    'id_marca' => $evaluacion['id_marca'],
                    'marca' => $evaluacion['marca'],
                    'fecha_fabricacion_extintor' => $evaluacion['fecha_fabricacion_extintor'],
                    'extintor_activo' => $evaluacion['extintor_activo'],
                    'baja_extintor' => $evaluacion['baja_extintor'],
                    'fecha_servicio' => $evaluacion['fecha_servicio'],
                    'fecha_prueba' => $evaluacion['fecha_prueba'],
                    'created_at' => $evaluacion['created_at'],
                    'created_by' => $evaluacion['created_by'],
                    // Agrega aquí otros campos del extintor según sea necesario
                ];
                unset($evaluacion['extintor_id'], $evaluacion['id_empresa'], $evaluacion['posicion_extintor'], $evaluacion['id_agente'], $evaluacion['agente'], $evaluacion['id_capacidad'], $evaluacion['capacidad'], $evaluacion['id_marca'], $evaluacion['marca'], $evaluacion['fecha_fabricacion_extintor'], $evaluacion['extintor_activo'], $evaluacion['baja_extintor'], $evaluacion['fecha_servicio'], $evaluacion['fecha_prueba'], $evaluacion['created_at'], $evaluacion['created_by']);
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