<?php
header("Content-Type: application/json");

$url = $_SERVER['REQUEST_URI'];

if (strpos($url, '/login') !== false) {
    require 'login.php';
} elseif (strpos($url, '/usuarios') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'users/create_user.php';
            break;
        case 'GET':
            require 'users/read_user.php';
            break;
        case 'PUT':
            require 'users/update_user.php';
            break;
        case 'DELETE':
            require 'users/delete_user.php';
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
} elseif (strpos($url, '/servicio') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'create_servicio.php';
            break;
        case 'GET':
            require 'read_servicio.php';
            break;
        case 'PUT':
            require 'update_servicio.php';
            break;
        case 'DELETE':
            require 'delete_servicio.php';
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} elseif (strpos($url, '/extintor') !== false) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            require 'create_extintor.php';
            break;
        case 'GET':
            require 'read_extintor.php';
            break;
        case 'PUT':
            require 'update_extintor.php';
            break;
        case 'DELETE':
            require 'delete_extintor.php';
            break;
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido']);
            break;
    }
} elseif (strpos($url, '/agente') !== false) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require 'read_agente.php';
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
    }
} elseif (strpos($url, '/capacidad') !== false) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require 'read_capacidad.php';
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
    }
} elseif (strpos($url, '/marca') !== false) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require 'read_marca.php';
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
    }
} elseif (strpos($url, '/view_log') !== false) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require 'view_log.php';
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>