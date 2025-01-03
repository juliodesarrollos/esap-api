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
            SELECT e.*, 
                   ue.id_usuario AS evaluador_id, ue.nombre_usuario AS evaluador_nombre, ue.correo_usuario AS evaluador_email, 
                   ur.id_usuario AS responsable_id, ur.nombre_usuario AS responsable_nombre, ur.correo_usuario AS responsable_email
            FROM evaluacion e
            LEFT JOIN usuario ue ON e.id_evaluador = ue.id_usuario
            LEFT JOIN usuario ur ON e.id_responsable = ur.id_usuario
            WHERE e.id_evaluacion = ?
            ORDER BY e.id_evaluacion ASC
        ');
        $stmt->execute([$id_evaluacion]);
        $evaluacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($evaluacion) {
            $evaluacion['evaluador'] = [
                'id_usuario' => $evaluacion['evaluador_id'],
                'nombre_usuario' => $evaluacion['evaluador_nombre'],
                'correo_usuario' => $evaluacion['evaluador_email']
            ];
            unset($evaluacion['evaluador_id'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_email']);

            $evaluacion['responsable'] = [
                'id_usuario' => $evaluacion['responsable_id'],
                'nombre_usuario' => $evaluacion['responsable_nombre'],
                'correo_usuario' => $evaluacion['responsable_email']
            ];
            unset($evaluacion['responsable_id'], $evaluacion['responsable_nombre'], $evaluacion['responsable_email']);

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
            SELECT e.*, 
                   ue.id_usuario AS evaluador_id, ue.nombre_usuario AS evaluador_nombre, ue.correo_usuario AS evaluador_email, 
                   ur.id_usuario AS responsable_id, ur.nombre_usuario AS responsable_nombre, ur.correo_usuario AS responsable_email
            FROM evaluacion e
            LEFT JOIN usuario ue ON e.id_evaluador = ue.id_usuario
            LEFT JOIN usuario ur ON e.id_responsable = ur.id_usuario
            WHERE e.id_servicio = ?
            ORDER BY e.id_evaluacion ASC
        ');
        $stmt->execute([$id_servicio]);
        $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($evaluaciones) {
            foreach ($evaluaciones as &$evaluacion) {
                $evaluacion['evaluador'] = [
                    'id_usuario' => $evaluacion['evaluador_id'],
                    'nombre_usuario' => $evaluacion['evaluador_nombre'],
                    'correo_usuario' => $evaluacion['evaluador_email']
                ];
                unset($evaluacion['evaluador_id'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_email']);

                $evaluacion['responsable'] = [
                    'id_usuario' => $evaluacion['responsable_id'],
                    'nombre_usuario' => $evaluacion['responsable_nombre'],
                    'correo_usuario' => $evaluacion['responsable_email']
                ];
                unset($evaluacion['responsable_id'], $evaluacion['responsable_nombre'], $evaluacion['responsable_email']);
            }

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
            SELECT e.*, 
                   ue.id_usuario AS evaluador_id, ue.nombre_usuario AS evaluador_nombre, ue.correo_usuario AS evaluador_email, 
                   ur.id_usuario AS responsable_id, ur.nombre_usuario AS responsable_nombre, ur.correo_usuario AS responsable_email
            FROM evaluacion e
            LEFT JOIN usuario ue ON e.id_evaluador = ue.id_usuario
            LEFT JOIN usuario ur ON e.id_responsable = ur.id_usuario
            ORDER BY e.id_evaluacion ASC
        ');
        $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($evaluaciones) {
            foreach ($evaluaciones as &$evaluacion) {
                $evaluacion['evaluador'] = [
                    'id_usuario' => $evaluacion['evaluador_id'],
                    'nombre_usuario' => $evaluacion['evaluador_nombre'],
                    'correo_usuario' => $evaluacion['evaluador_email']
                ];
                unset($evaluacion['evaluador_id'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_email']);

                $evaluacion['responsable'] = [
                    'id_usuario' => $evaluacion['responsable_id'],
                    'nombre_usuario' => $evaluacion['responsable_nombre'],
                    'correo_usuario' => $evaluacion['responsable_email']
                ];
                unset($evaluacion['responsable_id'], $evaluacion['responsable_nombre'], $evaluacion['responsable_email']);
            }

            $logger->write('All evaluacion data fetched: ' . json_encode($evaluaciones));
            echo json_encode($evaluaciones);
        } else {
            $logger->write('No evaluaciones found');
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron evaluaciones']);
        }
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>