<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Create extintor request received: ' . json_encode($data));

    if (isset($data['id_empresa'], $data['posicion_extintor'], $data['id_agente'], $data['id_capacidad'], $data['id_marca'], $data['fecha_fabricacion_extintor'], $data['extintor_activo'], $data['created_by'])) {
        try {
            // Insertar el nuevo extintor
            $stmt = $db->prepare('INSERT INTO extintor (id_empresa, posicion_extintor, id_agente, id_capacidad, id_marca, fecha_fabricacion_extintor, extintor_activo, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([
                $data['id_empresa'],
                $data['posicion_extintor'],
                $data['id_agente'],
                $data['id_capacidad'],
                $data['id_marca'],
                $data['fecha_fabricacion_extintor'],
                $data['extintor_activo'],
                date('Y-m-d H:i:s'), // created_at
                $data['created_by']
            ]);

            if ($result) {
                $logger->write('Extintor created successfully: ' . json_encode($data));
                http_response_code(201);
                echo json_encode(['message' => 'Extintor creado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to create extintor: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al crear el extintor', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear el extintor', 'error' => $e->getMessage()]);
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