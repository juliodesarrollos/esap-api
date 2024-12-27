<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_usuario'])) {
        $id_usuario = $_GET['id_usuario'];
        $logger->write('Fetching user with ID: ' . $id_usuario);

        $stmt = $db->prepare('SELECT * FROM usuario WHERE id_usuario = ? ORDER BY tipo_usuario ASC');
        $stmt->execute([$id_usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $logger->write('User data fetched: ' . json_encode($user));
            echo json_encode($user);
        } else {
            $logger->write('User not found with ID: ' . $id_usuario);
            http_response_code(404);
            echo json_encode(['message' => 'Usuario no encontrado']);
        }
    } elseif (isset($_GET['id_empresa'])) {
        $id_empresa = $_GET['id_empresa'];
        $logger->write('Fetching users with id_empresa: ' . $id_empresa);

        $stmt = $db->prepare('SELECT * FROM usuario WHERE id_empresa = ? ORDER BY tipo_usuario ASC');
        $stmt->execute([$id_empresa]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            $logger->write('Users data fetched: ' . json_encode($users));
            echo json_encode($users);
        } else {
            $logger->write('No users found with id_empresa: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron usuarios para la empresa especificada']);
        }
    } else {
        $logger->write('Fetching all users ordered by tipo_usuario ASC');

        $stmt = $db->query('SELECT * FROM usuario ORDER BY tipo_usuario ASC');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All user data fetched: ' . json_encode($users));
        echo json_encode($users);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>