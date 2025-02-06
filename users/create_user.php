<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode($_POST['data'], true);
    $logger->write('Create user request received: ' . json_encode($data));

    if (isset($data['nombre_usuario'], $data['direccion_usuario'], $data['telefono_usuario'], $data['correo_usuario'], $data['contraseña_usuario'], $data['tipo_usuario'])) {
        try {
            $db->beginTransaction();

            // Insertar el nuevo usuario
            $stmt = $db->prepare('INSERT INTO usuario (id_empresa, nombre_usuario, direccion_usuario, telefono_usuario, correo_usuario, contraseña_usuario, tipo_usuario, first_login, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $data['id_empresa'],
                $data['nombre_usuario'],
                $data['direccion_usuario'],
                $data['telefono_usuario'],
                $data['correo_usuario'],
                password_hash($data['contraseña_usuario'], PASSWORD_DEFAULT),
                $data['tipo_usuario'],
                $data['first_login'] ?? false,
                date('Y-m-d H:i:s'),
                $data['created_by']
            ]);

            if ($result) {
                $id_usuario = $db->lastInsertId();
                $logger->write('Usuario created successfully with ID: ' . $id_usuario);

                // Manejar la carga de la imagen de firma
                if (isset($_FILES['firma']) && $_FILES['firma']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'firmas/users/';
                    // Verificar y crear el directorio si no existe
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $firmaPath = $uploadDir . $id_usuario . '.png';
                    if (!move_uploaded_file($_FILES['firma']['tmp_name'], $firmaPath)) {
                        throw new Exception('Error al mover el archivo de firma');
                    }
                    $logger->write('Firma guardada en: ' . $firmaPath);
                } else {
                    $logger->write('Error al guardar la firma');
                }

                $db->commit();
                http_response_code(201);
                echo json_encode(['message' => 'Usuario creado exitosamente']);
            } else {
                $db->rollBack();
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to create usuario: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al crear el usuario', 'error' => $errorInfo]);
            }
        } catch (Exception $e) {
            $db->rollBack();
            $logger->write('Exception: ' . $e->getMessage());
            http_response_code(500);
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