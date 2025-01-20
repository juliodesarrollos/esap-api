<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Update user request received: ' . json_encode($data));

    if (isset($data['id_usuario']) && isset($data['contraseña_usuario'])) {
        $id_usuario = $data['id_usuario'];
        $nueva_contraseña = $data['contraseña_usuario'];

        try {
            // Actualizar la contraseña
            $stmt = $db->prepare('UPDATE usuario SET contraseña_usuario = ? WHERE id_usuario = ?');
            $result = $stmt->execute([password_hash($nueva_contraseña, PASSWORD_DEFAULT), $id_usuario]);

            if ($result) {
                $logger->write('Password updated for user ID: ' . $id_usuario);
                http_response_code(200);
                echo json_encode(['message' => 'Contraseña actualizada exitosamente']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to update password: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al actualizar la contraseña', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la contraseña', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Missing required fields in update password request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos requeridos']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>