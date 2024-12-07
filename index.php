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
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} elseif (strpos($url, '/empresa') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'create_empresa.php';
            break;
        case 'GET':
            require 'read_empresa.php';
            break;
        case 'PUT':
            require 'update_empresa.php';
            break;
        case 'DELETE':
            require 'delete_empresa.php';
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>