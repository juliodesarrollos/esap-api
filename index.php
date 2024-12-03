<?php
header("Content-Type: application/json");

$url = $_SERVER['REQUEST_URI'];
$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

if (strpos($url, '/usuarios') !== false) {
    require 'usuario.php';
} else {
    echo json_encode(['message' => 'Endpoint no encontrado']);
}
?>
