<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update empresa request received: ' . json_encode($data));

    if (isset($data['id_empresa'], $data['nombre_empresa'], $data['direccion_empresa'], $data['telefono_empresa'], $data['correo_empresa'], $data['prefijo_empresa'])) {
        $id_empresa = $data['id_empresa'];
        $nombre = $data['nombre_empresa'];
        $direccion = $data['direccion_empresa'];
        $telefono = $data['telefono_empresa'];
        $correo = $data['correo_empresa'];
        $prefijo = $data['prefijo_empresa'];

        $stmt = $db->prepare('UPDATE empresa SET nombre_empresa = ?, direccion_empresa = ?, telefono_empresa = ?, correo_empresa = ?, prefijo_empresa = ? WHERE id_empresa = ?');
        $stmt->execute([$nombre, $direccion, $telefono, $correo, $prefijo, $id_empresa]);

        $logger->write('Empresa updated: ' . json_encode($data));
        echo json_encode(['message' => 'Empresa actualizada exitosamente']);
    } else {
        $logger->write('Missing fields in update empresa request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos requeridos']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>