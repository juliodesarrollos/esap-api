<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_empresa'])) {
        $id_empresa = $_GET['id_empresa'];
        $logger->write('Fetching empresa with ID: ' . $id_empresa);

        $stmt = $db->prepare('SELECT * FROM empresa WHERE id_empresa = ?');
        $stmt->execute([$id_empresa]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            $logger->write('Empresa data fetched: ' . json_encode($empresa));
            echo json_encode($empresa);
        } else {
            $logger->write('Empresa not found with ID: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'Empresa no encontrada']);
        }
    } else {
        $logger->write('Fetching all empresas');

        $stmt = $db->query('SELECT * FROM empresa');
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All empresa data fetched: ' . json_encode($empresas));
        echo json_encode($empresas);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>