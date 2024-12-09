<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Delete empresa request received: ' . json_encode($data));

    if (isset($data['id_empresa'])) {
        $id_empresa = $data['id_empresa'];

        // Obtener el prefijo de la empresa
        $stmt = $db->prepare('SELECT prefijo_empresa FROM empresa WHERE id_empresa = ?');
        $stmt->execute([$id_empresa]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            $nuevo_prefijo = 'inactivo_' . $empresa['prefijo_empresa'];

            // Marcar la empresa como inactiva y actualizar el prefijo
            $stmt = $db->prepare('UPDATE empresa SET prefijo_empresa = ? WHERE id_empresa = ?');
            $stmt->execute([$nuevo_prefijo, $id_empresa]);

            $logger->write('Empresa marked as inactive with ID: ' . $id_empresa);
            echo json_encode(['message' => 'Empresa marcada como inactiva exitosamente']);
        } else {
            $logger->write('Empresa not found with ID: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'Empresa no encontrada']);
        }
    } else {
        $logger->write('Missing id_empresa in delete empresa request: ' . json_encode($data));
        http_response_code(400);
        echo json_encode(['message' => 'Falta el ID de la empresa']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>