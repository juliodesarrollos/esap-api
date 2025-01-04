<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update evaluacion_extintor request received: ' . json_encode($data));

    if (isset($data['id_evaluacion_extintor'])) {
        try {
            $stmt = $db->prepare('
                UPDATE evaluacion_extintor 
                SET id_evaluacion = ?, id_extintor = ?, evaluacion_cilindro = ?, evaluacion_manguera = ?, evaluacion_valvula = ?, 
                    evaluacion_manometro = ?, evaluacion_presion_peso = ?, evaluacion_seguro_sello = ?, evaluacion_señalamiento = ?, 
                    evaluacion_etiqueta = ?, evaluacion_soporte = ?, evaluacion_collarin = ?, evaluacion_rueda_um = ?, 
                    evaluacion_gabinete_bolsa = ?, comentario_evaluacion = ?, created_at = ?, created_by = ?, status = ?
                WHERE id_evaluacion_extintor = ?
            ');

            $result = $stmt->execute([
                $data['id_evaluacion'],
                $data['id_extintor'],
                $data['evaluacion_cilindro'],
                $data['evaluacion_manguera'],
                $data['evaluacion_valvula'],
                $data['evaluacion_manometro'],
                $data['evaluacion_presion_peso'],
                $data['evaluacion_seguro_sello'],
                $data['evaluacion_señalamiento'],
                $data['evaluacion_etiqueta'],
                $data['evaluacion_soporte'],
                $data['evaluacion_collarin'],
                $data['evaluacion_rueda_um'],
                $data['evaluacion_gabinete_bolsa'],
                $data['comentario_evaluacion'],
                $data['created_at'],
                $data['created_by'],
                $data['status'],
                $data['id_evaluacion_extintor']
            ]);

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