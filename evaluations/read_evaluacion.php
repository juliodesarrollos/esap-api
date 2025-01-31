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
                   ue.id_usuario AS evaluador_id, ue.id_empresa AS evaluador_empresa, ue.nombre_usuario AS evaluador_nombre, 
                   ue.direccion_usuario AS evaluador_direccion, ue.telefono_usuario AS evaluador_telefono, ue.correo_usuario AS evaluador_email, 
                   ue.contraseña_usuario AS evaluador_contraseña, ue.tipo_usuario AS evaluador_tipo, ue.first_login AS evaluador_first_login, 
                   ue.created_at AS evaluador_created_at, ue.created_by AS evaluador_created_by,
                   ur.id_usuario AS responsable_id, ur.id_empresa AS responsable_empresa, ur.nombre_usuario AS responsable_nombre, 
                   ur.direccion_usuario AS responsable_direccion, ur.telefono_usuario AS responsable_telefono, ur.correo_usuario AS responsable_email, 
                   ur.contraseña_usuario AS responsable_contraseña, ur.tipo_usuario AS responsable_tipo, ur.first_login AS responsable_first_login, 
                   ur.created_at AS responsable_created_at, ur.created_by AS responsable_created_by
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
                'id_empresa' => $evaluacion['evaluador_empresa'],
                'nombre_usuario' => $evaluacion['evaluador_nombre'],
                'direccion_usuario' => $evaluacion['evaluador_direccion'],
                'telefono_usuario' => $evaluacion['evaluador_telefono'],
                'correo_usuario' => $evaluacion['evaluador_email'],
                'contraseña_usuario' => $evaluacion['evaluador_contraseña'],
                'tipo_usuario' => $evaluacion['evaluador_tipo'],
                'first_login' => $evaluacion['evaluador_first_login'],
                'created_at' => $evaluacion['evaluador_created_at'],
                'created_by' => $evaluacion['evaluador_created_by']
            ];
            unset($evaluacion['evaluador_id'], $evaluacion['evaluador_empresa'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_direccion'], 
                  $evaluacion['evaluador_telefono'], $evaluacion['evaluador_email'], $evaluacion['evaluador_contraseña'], $evaluacion['evaluador_tipo'], 
                  $evaluacion['evaluador_first_login'], $evaluacion['evaluador_created_at'], $evaluacion['evaluador_created_by']);

            $evaluacion['responsable'] = [
                'id_usuario' => $evaluacion['responsable_id'],
                'id_empresa' => $evaluacion['responsable_empresa'],
                'nombre_usuario' => $evaluacion['responsable_nombre'],
                'direccion_usuario' => $evaluacion['responsable_direccion'],
                'telefono_usuario' => $evaluacion['responsable_telefono'],
                'correo_usuario' => $evaluacion['responsable_email'],
                'contraseña_usuario' => $evaluacion['responsable_contraseña'],
                'tipo_usuario' => $evaluacion['responsable_tipo'],
                'first_login' => $evaluacion['responsable_first_login'],
                'created_at' => $evaluacion['responsable_created_at'],
                'created_by' => $evaluacion['responsable_created_by']
            ];
            unset($evaluacion['responsable_id'], $evaluacion['responsable_empresa'], $evaluacion['responsable_nombre'], $evaluacion['responsable_direccion'], 
                  $evaluacion['responsable_telefono'], $evaluacion['responsable_email'], $evaluacion['responsable_contraseña'], $evaluacion['responsable_tipo'], 
                  $evaluacion['responsable_first_login'], $evaluacion['responsable_created_at'], $evaluacion['responsable_created_by']);

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
                   ue.id_usuario AS evaluador_id, ue.id_empresa AS evaluador_empresa, ue.nombre_usuario AS evaluador_nombre, 
                   ue.direccion_usuario AS evaluador_direccion, ue.telefono_usuario AS evaluador_telefono, ue.correo_usuario AS evaluador_email, 
                   ue.contraseña_usuario AS evaluador_contraseña, ue.tipo_usuario AS evaluador_tipo, ue.first_login AS evaluador_first_login, 
                   ue.created_at AS evaluador_created_at, ue.created_by AS evaluador_created_by,
                   ur.id_usuario AS responsable_id, ur.id_empresa AS responsable_empresa, ur.nombre_usuario AS responsable_nombre, 
                   ur.direccion_usuario AS responsable_direccion, ur.telefono_usuario AS responsable_telefono, ur.correo_usuario AS responsable_email, 
                   ur.contraseña_usuario AS responsable_contraseña, ur.tipo_usuario AS responsable_tipo, ur.first_login AS responsable_first_login, 
                   ur.created_at AS responsable_created_at, ur.created_by AS responsable_created_by
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
                    'id_empresa' => $evaluacion['evaluador_empresa'],
                    'nombre_usuario' => $evaluacion['evaluador_nombre'],
                    'direccion_usuario' => $evaluacion['evaluador_direccion'],
                    'telefono_usuario' => $evaluacion['evaluador_telefono'],
                    'correo_usuario' => $evaluacion['evaluador_email'],
                    'contraseña_usuario' => $evaluacion['evaluador_contraseña'],
                    'tipo_usuario' => $evaluacion['evaluador_tipo'],
                    'first_login' => $evaluacion['evaluador_first_login'],
                    'created_at' => $evaluacion['evaluador_created_at'],
                    'created_by' => $evaluacion['evaluador_created_by']
                ];
                unset($evaluacion['evaluador_id'], $evaluacion['evaluador_empresa'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_direccion'], 
                      $evaluacion['evaluador_telefono'], $evaluacion['evaluador_email'], $evaluacion['evaluador_contraseña'], $evaluacion['evaluador_tipo'], 
                      $evaluacion['evaluador_first_login'], $evaluacion['evaluador_created_at'], $evaluacion['evaluador_created_by']);

                $evaluacion['responsable'] = [
                    'id_usuario' => $evaluacion['responsable_id'],
                    'id_empresa' => $evaluacion['responsable_empresa'],
                    'nombre_usuario' => $evaluacion['responsable_nombre'],
                    'direccion_usuario' => $evaluacion['responsable_direccion'],
                    'telefono_usuario' => $evaluacion['responsable_telefono'],
                    'correo_usuario' => $evaluacion['responsable_email'],
                    'contraseña_usuario' => $evaluacion['responsable_contraseña'],
                    'tipo_usuario' => $evaluacion['responsable_tipo'],
                    'first_login' => $evaluacion['responsable_first_login'],
                    'created_at' => $evaluacion['responsable_created_at'],
                    'created_by' => $evaluacion['responsable_created_by']
                ];
                unset($evaluacion['responsable_id'], $evaluacion['responsable_empresa'], $evaluacion['responsable_nombre'], $evaluacion['responsable_direccion'], 
                      $evaluacion['responsable_telefono'], $evaluacion['responsable_email'], $evaluacion['responsable_contraseña'], $evaluacion['responsable_tipo'], 
                      $evaluacion['responsable_first_login'], $evaluacion['responsable_created_at'], $evaluacion['responsable_created_by']);
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
                   ue.id_usuario AS evaluador_id, ue.id_empresa AS evaluador_empresa, ue.nombre_usuario AS evaluador_nombre, 
                   ue.direccion_usuario AS evaluador_direccion, ue.telefono_usuario AS evaluador_telefono, ue.correo_usuario AS evaluador_email, 
                   ue.contraseña_usuario AS evaluador_contraseña, ue.tipo_usuario AS evaluador_tipo, ue.first_login AS evaluador_first_login, 
                   ue.created_at AS evaluador_created_at, ue.created_by AS evaluador_created_by,
                   ur.id_usuario AS responsable_id, ur.id_empresa AS responsable_empresa, ur.nombre_usuario AS responsable_nombre, 
                   ur.direccion_usuario AS responsable_direccion, ur.telefono_usuario AS responsable_telefono, ur.correo_usuario AS responsable_email, 
                   ur.contraseña_usuario AS responsable_contraseña, ur.tipo_usuario AS responsable_tipo, ur.first_login AS responsable_first_login, 
                   ur.created_at AS responsable_created_at, ur.created_by AS responsable_created_by
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
                    'id_empresa' => $evaluacion['evaluador_empresa'],
                    'nombre_usuario' => $evaluacion['evaluador_nombre'],
                    'direccion_usuario' => $evaluacion['evaluador_direccion'],
                    'telefono_usuario' => $evaluacion['evaluador_telefono'],
                    'correo_usuario' => $evaluacion['evaluador_email'],
                    'contraseña_usuario' => $evaluacion['evaluador_contraseña'],
                    'tipo_usuario' => $evaluacion['evaluador_tipo'],
                    'first_login' => $evaluacion['evaluador_first_login'],
                    'created_at' => $evaluacion['evaluador_created_at'],
                    'created_by' => $evaluacion['evaluador_created_by']
                ];
                unset($evaluacion['evaluador_id'], $evaluacion['evaluador_empresa'], $evaluacion['evaluador_nombre'], $evaluacion['evaluador_direccion'], 
                      $evaluacion['evaluador_telefono'], $evaluacion['evaluador_email'], $evaluacion['evaluador_contraseña'], $evaluacion['evaluador_tipo'], 
                      $evaluacion['evaluador_first_login'], $evaluacion['evaluador_created_at'], $evaluacion['evaluador_created_by']);

                $evaluacion['responsable'] = [
                    'id_usuario' => $evaluacion['responsable_id'],
                    'id_empresa' => $evaluacion['responsable_empresa'],
                    'nombre_usuario' => $evaluacion['responsable_nombre'],
                    'direccion_usuario' => $evaluacion['responsable_direccion'],
                    'telefono_usuario' => $evaluacion['responsable_telefono'],
                    'correo_usuario' => $evaluacion['responsable_email'],
                    'contraseña_usuario' => $evaluacion['responsable_contraseña'],
                    'tipo_usuario' => $evaluacion['responsable_tipo'],
                    'first_login' => $evaluacion['responsable_first_login'],
                    'created_at' => $evaluacion['responsable_created_at'],
                    'created_by' => $evaluacion['responsable_created_by']
                ];
                unset($evaluacion['responsable_id'], $evaluacion['responsable_empresa'], $evaluacion['responsable_nombre'], $evaluacion['responsable_direccion'], 
                      $evaluacion['responsable_telefono'], $evaluacion['responsable_email'], $evaluacion['responsable_contraseña'], $evaluacion['responsable_tipo'], 
                      $evaluacion['responsable_first_login'], $evaluacion['responsable_created_at'], $evaluacion['responsable_created_by']);
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