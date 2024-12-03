<?php
header("Content-Type: application/json");

$url = $_SERVER['REQUEST_URI'];

if (strpos($url, '/login') !== false) {
    require 'login.php';
} elseif (strpos($url, '/users') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'usuarios/create_user.php';
            break;
        case 'GET':
            require 'usuarios/read_user.php';
            break;
        case 'PUT':
            require 'usuarios/update_user.php';
            break;
        case 'DELETE':
            require 'usuarios/delete_user.php';
            break;
        default:
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} else {
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>
