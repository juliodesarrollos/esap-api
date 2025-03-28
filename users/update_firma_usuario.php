<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;

    if (!$id_usuario || !isset($_FILES['firma']) || $_FILES['firma']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['message' => 'Faltan datos o la imagen no es válida']);
        exit;
    }

    try {
        $uploadDir = 'firmas/users/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $firmaPath = $uploadDir . $id_usuario . '.png';

        if (move_uploaded_file($_FILES['firma']['tmp_name'], $firmaPath)) {
            http_response_code(200);
            echo json_encode(['message' => 'Firma actualizada correctamente']);
        } else {
            throw new Exception('Error al mover el archivo');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Error al actualizar la firma', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
