<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Delete servicio request received: ' . json_encode($data));

    if (isset($data['id_servicio'])) {
        $id_servicio = $data['id_servicio'];

        try {
            // Eliminar el servicio
            $stmt = $db->prepare('DELETE FROM servicio WHERE id_servicio = ?');
            $result = $stmt->execute([$id_servicio]);

            if ($result) {
                $logger->write('Servicio deleted successfully with ID: ' . $id_servicio);
                http_response_code(200);
                echo json_encode(['message' => 'Servicio eliminado']);
            } else {
                $errorInfo = $stmt->errorInfo();
                $logger->write('Failed to delete servicio: ' . json_encode($errorInfo));
                http_response_code(500);
                echo json_encode(['message' => 'Error al eliminar el servicio', 'error' => $errorInfo]);
            }
        } catch (PDOException $e) {
            $logger->write('PDOException: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Error al eliminar el servicio', 'error' => $e->getMessage()]);
        }
    } else {
        $logger->write('Missing id_servicio in delete servicio request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Falta el ID del servicio']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>