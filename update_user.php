<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$data = json_decode(file_get_contents('php://input'), true);
$logger->write('Put data received: ' . json_encode($data));

if (isset($data['id_usuario'], $data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['contraseña_usuario'], $data['tipo_usuario'], $data['first_login'])) {
    $stmt = $db->prepare('UPDATE usuario SET id_empresa = ?, nombre_usuario = ?, direccion_usuario = ?, telefono_usuario = ?, correo_usuario = ?, contraseña_usuario = ?, tipo_usuario = ?, first_login = ? WHERE id_usuario = ?');
    $logger->write('Prepared statement: ' . $stmt->queryString);

    try {
        $result = $stmt->execute([
            $data['id_empresa'],
            $data['nombre_usuario'],
            $data['direccion_usuario'],
            $data['telefono_usuario'],
            $data['correo_usuario'],
            password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT),
            $data['tipo_usuario'],
            $data['first_login'],
            $data['id_usuario']
        ]);

        if ($result) {
            $logger->write('User updated successfully: ' . json_encode($data));
            echo json_encode(['message' => 'Usuario actualizado']);
        } else {
            $errorInfo = $stmt->errorInfo();
            $logger->write('Failed to update user: ' . json_encode($errorInfo));
            echo json_encode(['message' => 'Error al actualizar el usuario', 'error' => $errorInfo]);
        }
    } catch (PDOException $e) {
        $logger->write('PDOException: ' . $e->getMessage());
        echo json_encode(['message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()]);
    }
} else {
    $logger->write('Missing required fields in PUT data: ' . json_encode($data));
    echo json_encode(['message' => 'Faltan campos requeridos en los datos', 'data' => $data]);
}
?>
