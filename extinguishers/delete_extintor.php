<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Delete extintor request received: ' . json_encode($data));

    if (isset($data['id_extintor'])) {
        $id_extintor = $data['id_extintor'];

        try {
            // Marcar el extintor como inactivo
            $stmt = $db->prepare('UPDATE extintor SET extintor_activo = 0, baja_extintor = ? WHERE id_extintor = ?');
            $result = $stmt->execute([date('Y-m-d H:i:s'), $id_extintor]);

            if ($result) {
                $logger->write('Extintor marked as inactive with ID: ' . $id_extintor);
                http_response_code(200);
                echo json_encode(['message' => 'Extintor marcado como inactivo exitosamente']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to mark extintor as inactive: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al marcar el extintor como inactivo', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al marcar el extintor como inactivo', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Missing id_extintor in delete extintor request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Falta el ID del extintor']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>