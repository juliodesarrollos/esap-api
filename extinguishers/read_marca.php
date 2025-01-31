<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_marca'])) {
        $id_marca = $_GET['id_marca'];
        $logger->write('Fetching marca with ID: ' . $id_marca);

        $stmt = $db->prepare('SELECT * FROM marca WHERE id_marca = ?');
        $stmt->execute([$id_marca]);
        $marca = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($marca) {
            $logger->write('Marca data fetched: ' . json_encode($marca));
            echo json_encode($marca);
        } else {
            $logger->write('Marca not found with ID: ' . $id_marca);
            http_response_code(404);
            echo json_encode(['message' => 'Marca no encontrada']);
        }
    } else {
        $logger->write('Fetching all marcas');

        $stmt = $db->query('SELECT * FROM marca');
        $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All marca data fetched: ' . json_encode($marcas));
        echo json_encode($marcas);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>