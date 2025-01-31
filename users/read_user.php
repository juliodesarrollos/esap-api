<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_usuario'])) {
        $id_usuario = $_GET['id_usuario'];
        $logger->write('Fetching usuario with ID: ' . $id_usuario);

        $stmt = $db->prepare('
            SELECT * 
            FROM usuario 
            WHERE id_usuario = ?
            ORDER BY id_usuario ASC
        ');
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $logger->write('Usuario data fetched: ' . json_encode($usuario));
            echo json_encode($usuario);
        } else {
            $logger->write('Usuario not found with ID: ' . $id_usuario);
            http_response_code(404);
            echo json_encode(['message' => 'Usuario no encontrado']);
        }
    } elseif (isset($_GET['id_empresa'])) {
        $id_empresa = $_GET['id_empresa'];
        $logger->write('Fetching usuarios with empresa ID: ' . $id_empresa);

        $stmt = $db->prepare('
            SELECT * 
            FROM usuario 
            WHERE id_empresa = ?
            ORDER BY id_usuario ASC
        ');
        $stmt->execute([$id_empresa]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($usuarios) {
            $logger->write('Usuarios data fetched: ' . json_encode($usuarios));
            echo json_encode($usuarios);
        } else {
            $logger->write('No usuarios found with empresa ID: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron usuarios para la empresa especificada']);
        }
    } else {
        $logger->write('Fetching all usuarios');

        $stmt = $db->query('
            SELECT * 
            FROM usuario 
            ORDER BY id_usuario ASC
        ');
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All usuario data fetched: ' . json_encode($usuarios));
        echo json_encode($usuarios);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>