<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update evaluacion request received: ' . json_encode($data));

    if (isset($data['id_evaluacion'], $data['status'])) {
        try {
            if ($data['status'] === 'initiated' && isset($data['id_evaluador'])) {
                // Actualizar la evaluación con id_evaluador y status
                $stmt = $db->prepare('UPDATE evaluacion SET id_evaluador = ?, status = ? WHERE id_evaluacion = ?');
                $result = $stmt->execute([
                    $data['id_evaluador'],
                    $data['status'],
                    $data['id_evaluacion']
                ]);
            } elseif ($data['status'] === 'terminated' && isset($data['id_responsable'])) {
                // Actualizar la evaluación con id_responsable y status
                $stmt = $db->prepare('UPDATE evaluacion SET id_responsable = ?, status = ? WHERE id_evaluacion = ?');
                $result = $stmt->execute([
                    $data['id_responsable'],
                    $data['status'],
                    $data['id_evaluacion']
                ]);
            } else {
                $logger->write('Invalid data for update: ' . json_encode($data));
                http_response_code(400);
                echo json_encode(['message' => 'Datos inválidos para la actualización']);
                exit;
            }

            if ($result) {
                $logger->write('Evaluacion updated successfully: ' . json_encode($data));
                http_response_code(200);
                echo json_encode(['message' => 'Evaluacion actualizada']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update evaluacion: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar la evaluacion', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la evaluacion', 'error' => $e->getMessage()]);
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