<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update evaluacion_extintor request received: ' . json_encode($data));

    if (isset($data['id_evaluacion_extintor'], $data['status'])) {
        try {
            if ($data['status'] === 'initiated' && isset($data['id_evaluador'])) {
                // Actualizar la evaluación con id_evaluador y status
                $stmt = $db->prepare('UPDATE evaluacion_extintor SET id_evaluador = ?, status = ? WHERE id_evaluacion_extintor = ?');
                $result = $stmt->execute([
                    $data['id_evaluador'],
                    $data['status'],
                    $data['id_evaluacion_extintor']
                ]);
            } elseif ($data['status'] === 'terminated' && isset($data['id_responsable'])) {
                // Actualizar la evaluación con id_responsable y status
                $stmt = $db->prepare('UPDATE evaluacion_extintor SET id_responsable = ?, status = ? WHERE id_evaluacion_extintor = ?');
                $result = $stmt->execute([
                    $data['id_responsable'],
                    $data['status'],
                    $data['id_evaluacion_extintor']
                ]);
            } else {
                $logger->write('Invalid data for update: ' . json_encode($data));
                http_response_code(400);
                echo json_encode(['message' => 'Datos inválidos para la actualización']);
                exit;
            }

            if ($result) {
                $logger->write('Evaluacion_extintor updated successfully: ' . json_encode($data));
                http_response_code(200);
                echo json_encode(['message' => 'Evaluacion_extintor actualizada']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update evaluacion_extintor: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar la evaluacion_extintor', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la evaluacion_extintor', 'error' => $e->getMessage()]);
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