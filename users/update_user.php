<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update user request received: ' . json_encode($data));

    if (isset($data['id_usuario'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['tipo_usuario'], $data['first_login'], $data['id_empresa'])) {
        try {
            // Verificar si el correo ya existe para otro usuario
            $stmt = $db->prepare('SELECT COUNT(*) FROM usuario WHERE correo_usuario = ? AND id_usuario != ?');
            $stmt->execute([$data['correo_usuario'], $data['id_usuario']]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {                                               
                $logger->write('Correo ya existe para otro usuario: ' . $data['correo_usuario']);
                http_response_code(409);
                echo json_encode(['message' => 'El correo ya está registrado para otro usuario']);
                exit;
            }

            // Actualizar el usuario
            $stmt = $db->prepare('UPDATE usuario SET nombre_usuario = ?, direccion_usuario = ?, telefono_usuario = ?, correo_usuario = ?, tipo_usuario = ?, first_login = ?, id_empresa = ? WHERE id_usuario = ?');
            $result = $stmt->execute([
                $data['nombre_usuario'],
                $data['direccion_usuario'],
                $data['telefono_usuario'],
                $data['correo_usuario'],
                $data['tipo_usuario'],
                $data['first_login'],
                $data['id_empresa'],
                $data['id_usuario']
            ]);

            if ($result) {
                $logger->write('User updated successfully: ' . json_encode($data));
                http_response_code(200);
                echo json_encode(['message' => 'Usuario actualizado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update user: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar el usuario', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()]);
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