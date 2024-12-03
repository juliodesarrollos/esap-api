<?php
header("Content-Type: application/json");

$url = $_SERVER['REQUEST_URI'];

if (strpos($url, '/usuarios') !== false) {
    require 'usuario.php';
} else {
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>
