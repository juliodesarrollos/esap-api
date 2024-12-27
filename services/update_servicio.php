<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update servicio request received: ' . json_encode($data));

    if (isset($data['id_servicio'], $data['id_empresa'], $data['created_by'])) {
        try {
            // Actualizar el servicio
            $stmt = $db->prepare('UPDATE servicio SET id_empresa = ?, created_by = ? WHERE id_servicio = ?');
            $result = $stmt->execute([
                $data['id_empresa'],
                $data['created_by'],
                $data['id_servicio']
            ]);

            if ($result) {
                $logger->write('Servicio updated successfully: ' . json_encode($data));
                http_response_code(200);
                echo json_encode(['message' => 'Servicio actualizado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update servicio: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar el servicio', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar el servicio', 'error' => $e->getMessage()]);
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