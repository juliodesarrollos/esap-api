<?php
header("Content-Type: application/json");

$url = $_SERVER['REQUEST_URI'];

if (strpos($url, '/login') !== false) {
    require 'login.php';
} elseif (strpos($url, '/usuarios') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'create_user.php';
            break;
        case 'GET':
            require 'read_user.php';
            break;
        case 'PUT':
            require 'update_user.php';
            break;
        case 'DELETE':
            require 'delete_user.php';
            break;
        default:
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} else {
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>
