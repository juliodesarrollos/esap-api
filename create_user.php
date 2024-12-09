<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Create user request received: ' . json_encode($data));

    if (isset($data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['contraseña_usuario'], $data['tipo_usuario'], $data['first_login'], $data['created_by'])) {
        try {
            $stmt = $db->prepare('INSERT INTO usuario (nombre_usuario, direccion_usuario, telefono_usuario, correo_usuario, contraseña_usuario, tipo_usuario, first_login, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $data['nombre_usuario'],
                $data['direccion_usuario'],
                $data['telefono_usuario'],
                $data['correo_usuario'],
                password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT),
                $data['tipo_usuario'],
                $data['first_login'],
                $data['created_by']
            ]);

            if ($result) {
                $logger->write('User created successfully: ' . json_encode($data));
                http_response_code(201);
                echo json_encode(['message' => 'Usuario creado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to create user: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al crear el usuario', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(501);
            echo json_encode(['message' => 'Error al crear el usuario', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Missing required fields in POST data: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos requeridos en los datos', 'data' => $data]);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>