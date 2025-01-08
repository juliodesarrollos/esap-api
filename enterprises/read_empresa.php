<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->prepare('
            SELECT e.*, u.nombre_usuario
            FROM empresa e
            LEFT JOIN usuario u ON e.id_empresa = u.id_empresa
            AND u.id_usuario = (
                SELECT MIN(id_usuario)
                FROM usuario
                WHERE id_empresa = e.id_empresa
            )
        ');
        $stmt->execute();
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($empresas) {
            $result = [];
            foreach ($empresas as $empresa) {
                $usuario = $empresa['nombre_usuario'] ?? 'Sin usuarios registrados';
                unset($empresa['nombre_usuario']);
                $empresa['usuario'] = $usuario;
                $result[] = $empresa;
            }
            $logger->write('Empresas data fetched: ' . json_encode($result));
            echo json_encode($result);
        } else {
            $logger->write('No empresas found');
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron empresas']);
        }
    } catch (Exception $e) {
        $logger->write('Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Error interno del servidor']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>