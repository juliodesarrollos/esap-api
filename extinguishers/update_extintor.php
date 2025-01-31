<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update extintor request received: ' . json_encode($data));

    if (isset($data['id_extintor'], $data['id_empresa'], $data['posicion_extintor'], $data['id_agente'], $data['id_capacidad'], $data['id_marca'], $data['fecha_fabricacion_extintor'], $data['extintor_activo'], $data['fecha_servicio'], $data['fecha_prueba'])) {
        try {
            // Actualizar el extintor
            $stmt = $db->prepare('UPDATE extintor SET id_empresa = ?, posicion_extintor = ?, id_agente = ?, id_capacidad = ?, id_marca = ?, fecha_fabricacion_extintor = ?, extintor_activo = ?, fecha_servicio = ?, fecha_prueba = ? WHERE id_extintor = ?');
            $result = $stmt->execute([
                $data['id_empresa'],
                $data['posicion_extintor'],
                $data['id_agente'],
                $data['id_capacidad'],
                $data['id_marca'],
                $data['fecha_fabricacion_extintor'],
                $data['extintor_activo'],
                $data['fecha_servicio'],
                $data['fecha_prueba'],
                $data['id_extintor']
            ]);

            if ($result) {
                $logger->write('Extintor updated successfully: ' . json_encode($data));
                http_response_code(200);
                echo json_encode(['message' => 'Extintor actualizado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update extintor: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar el extintor', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar el extintor', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Missing required fields in PUT data: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos requeridos en los datos', 'data' => $data]);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>