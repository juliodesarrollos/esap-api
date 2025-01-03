<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Create evaluacion_extintor request received: ' . json_encode($data));

    if (isset($data['evaluaciones']) && is_array($data['evaluaciones'])) {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare('
                INSERT INTO evaluacion_extintor (
                    id_evaluacion, id_extintor, evaluacion_cilindro, evaluacion_manguera, evaluacion_valvula, 
                    evaluacion_manometro, evaluacion_presion_peso, evaluacion_seguro_sello, evaluacion_señalamiento, 
                    evaluacion_etiqueta, evaluacion_soporte, evaluacion_collarin, evaluacion_rueda_um, 
                    evaluacion_gabinete_bolsa, comentario_evaluacion, created_at, created_by, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');

            foreach ($data['evaluaciones'] as $evaluacion) {
                $stmt->execute([
                    $evaluacion['id_evaluacion'],
                    $evaluacion['id_extintor'],
                    $evaluacion['evaluacion_cilindro'],
                    $evaluacion['evaluacion_manguera'],
                    $evaluacion['evaluacion_valvula'],
                    $evaluacion['evaluacion_manometro'],
                    $evaluacion['evaluacion_presion_peso'],
                    $evaluacion['evaluacion_seguro_sello'],
                    $evaluacion['evaluacion_señalamiento'],
                    $evaluacion['evaluacion_etiqueta'],
                    $evaluacion['evaluacion_soporte'],
                    $evaluacion['evaluacion_collarin'],
                    $evaluacion['evaluacion_rueda_um'],
                    $evaluacion['evaluacion_gabinete_bolsa'],
                    $evaluacion['comentario_evaluacion'],
                    $evaluacion['created_at'],
                    $evaluacion['created_by'],
                    $evaluacion['status']
                ]);
            }

            $db->commit();
            $logger->write('Evaluaciones_extintor created successfully');
            http_response_code(201);
            echo json_encode(['message' => 'Evaluaciones_extintor creadas']);
        } catch (PDOException $e) {
            $db->rollBack();
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear las evaluaciones_extintor', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Invalid data for creation: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Datos inválidos para la creación']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>