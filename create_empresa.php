<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Create empresa request received: ' . json_encode($data));

    if (isset($data['nombre_empresa'], $data['direccion_empresa'], $data['telefono_empresa'], $data['correo_empresa'], $data['contraseña_empresa'], $data['prefijo_empresa'], $data['created_by'])) {
        $nombre = $data['nombre_empresa'];
        $direccion = $data['direccion_empresa'];
        $telefono = $data['telefono_empresa'];
        $correo = $data['correo_empresa'];
        $contraseña = password_hash($data['contraseña_empresa'], PASSWORD_BCRYPT);
        $prefijo = $data['prefijo_empresa'];
        $created_by = $data['created_by'];
        $created_at = date('Y-m-d H:i:s');

        $stmt = $db->prepare('INSERT INTO empresa (nombre_empresa, direccion_empresa, telefono_empresa, correo_empresa, contraseña_empresa, prefijo_empresa, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $direccion, $telefono, $correo, $contraseña, $prefijo, $created_at, $created_by]);

        $logger->write('Empresa created: ' . json_encode($data));
        echo json_encode(['message' => 'Empresa creada exitosamente']);
    } else {
        $logger->write('Missing fields in create empresa request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos requeridos']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>