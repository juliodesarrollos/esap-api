<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Create servicio request received: ' . json_encode($data));

    if (isset($data['id_empresa'], $data['created_by'], $data['created_at'])) {
        try {
            // Insertar el nuevo servicio
            $stmt = $db->prepare('INSERT INTO servicio (id_empresa, created_at, created_by) VALUES (?, ?, ?)');
            $result = $stmt->execute([
                $data['id_empresa'],
                $data['created_at'], // created_at from request
                $data['created_by']
            ]);

            if ($result) {
                $logger->write('Servicio created successfully: ' . json_encode($data));
                http_response_code(201);
                echo json_encode(['message' => 'Servicio creado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to create servicio: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al crear el servicio', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear el servicio', 'error' => $e->getMessage()]);
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