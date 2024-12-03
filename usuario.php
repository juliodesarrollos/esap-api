<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id_usuario'])) {
            $stmt = $db->prepare('SELECT * FROM usuario WHERE id_usuario = ?');
            $stmt->execute([$_GET['id_usuario']]);
            $logger->write('GET request for user ID ' . $_GET['id_usuario']);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $db->query('SELECT * FROM usuario');
            $logger->write('GET request for all users');
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('INSERT INTO usuario (id_empresa, nombre_usuario, direccion_usuario, telefono_usuario, correo_usuario, contraseña_usuario, tipo_usuario, first_login, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([$data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT), $data['tipo_usuario'], $data['first_login'], $data['created_by']]);
        $logger->write('POST request: new user created with email ' . $data['correo_usuario']);
        echo json_encode(['message' => 'Usuario creado']);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare('UPDATE usuario SET id_empresa = ?, nombre_usuario = ?, direccion_usuario = ?, telefono_usuario = ?, correo_usuario = ?, contraseña_usuario = ?, tipo_usuario = ?, first_login = ? WHERE id_usuario = ?');
        $stmt->execute([$data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT), $data['tipo_usuario'], $data['first_login'], $data['id_usuario']]);
        $logger->write('PUT request: user ID ' . $data['id_usuario'] . ' updated');
        echo json_encode(['message' => 'Usuario actualizado']);
        break;
    case 'DELETE':
        $id_usuario = $_GET['id_usuario'];
        $stmt = $db->prepare('UPDATE usuario SET tipo_usuario = "inactivo" WHERE id_usuario = ?');
        $stmt->execute([$id_usuario]);
        $logger->write('DELETE request: user ID ' . $id_usuario . ' marked as inactive');
        echo json_encode(['message' => 'Usuario marcado como inactivo']);
        break;
    default:
        $logger->write('Unhandled request method: ' . $_SERVER['REQUEST_METHOD']);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}
?>
