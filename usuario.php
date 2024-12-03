<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id_usuario'])) {
            $id_usuario = $_GET['id_usuario'];
            $logger->write('Fetching user with ID: ' . $id_usuario);

            $stmt = $db->prepare('SELECT * FROM usuario WHERE id_usuario = ?');
            $stmt->execute([$id_usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $logger->write('User data fetched: ' . json_encode($user));
            echo json_encode($user);
        } else {
            $logger->write('Fetching all users');

            $stmt = $db->query('SELECT * FROM usuario');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $logger->write('All user data fetched: ' . json_encode($users));
            echo json_encode($users);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $logger->write('Post data received: ' . json_encode($data));

        if (isset($data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['contraseña_usuario'], $data['tipo_usuario'], $data['first_login'], $data['created_by'])) {
            $stmt = $db->prepare('INSERT INTO usuario (id_empresa, nombre_usuario, direccion_usuario, telefono_usuario, correo_usuario, contraseña_usuario, tipo_usuario, first_login, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
            $result = $stmt->execute([$data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT), $data['tipo_usuario'], $data['first_login'], $data['created_by']]);

            if ($result) {
                $logger->write('User created with data: ' . json_encode($data));
                echo json_encode(['message' => 'Usuario creado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to create user: ' . json_encode($errorInfo));
                echo json_encode(['message' => 'Error al crear el usuario', 'error' => $errorInfo]);
            }
        } else {
            $logger->write('Missing required fields in POST data: ' . json_encode($data));
            echo json_encode(['message' => 'Faltan campos requeridos en los datos', 'data' => $data]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $logger->write('Put data received: ' . json_encode($data));

        if (isset($data['id_usuario'], $data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['contraseña_usuario'], $data['tipo_usuario'], $data['first_login'])) {
            $stmt = $db->prepare('UPDATE usuario SET id_empresa = ?, nombre_usuario = ?, direccion_usuario = ?, telefono_usuario = ?, correo_usuario = ?, contraseña_usuario = ?, tipo_usuario = ?, first_login = ? WHERE id_usuario = ?');
            $result = $stmt->execute([$data['id_empresa'], $data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT), $data['tipo_usuario'], $data['first_login'], $data['id_usuario']]);

            if ($result) {
                $logger->write('User updated with data: ' . json_encode($data));
                echo json_encode(['message' => 'Usuario actualizado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update user: ' . json_encode($errorInfo));
                echo json_encode(['message' => 'Error al actualizar el usuario', 'error' => $errorInfo]);
            }
        } else {
            $logger->write('Missing required fields in PUT data: ' . json_encode($data));
            echo json_encode(['message' => 'Faltan campos requeridos en los datos', 'data' => $data]);
        }
        break;
    case 'DELETE':
        $id_usuario = $_GET['id_usuario'];
        $logger->write('Delete request received for user ID: ' . $id_usuario);

        $stmt = $db->prepare('UPDATE usuario SET tipo_usuario = "inactivo" WHERE id_usuario = ?');
        $result = $stmt->execute([$id_usuario]);

        if ($result) {
            $logger->write('User marked as inactive with ID: ' . $id_usuario);
            echo json_encode(['message' => 'Usuario marcado como inactivo']);
        } else {
            $errorInfo = $stmt->errorInfo();
            $logger->write('Failed to mark user as inactive: ' . json_encode($errorInfo));
            echo json_encode(['message' => 'Error al marcar el usuario como inactivo', 'error' => $errorInfo]);
        }
        break;
    default:
        $logger->write('Unhandled request method: ' . $_SERVER['REQUEST_METHOD']);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}
?>