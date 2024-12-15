<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_agente'])) {
        $id_agente = $_GET['id_agente'];
        $logger->write('Fetching agente with ID: ' . $id_agente);

        $stmt = $db->prepare('SELECT * FROM agente WHERE id_agente = ?');
        $stmt->execute([$id_agente]);
        $agente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($agente) {
            $logger->write('Agente data fetched: ' . json_encode($agente));
            echo json_encode($agente);
        } else {
            $logger->write('Agente not found with ID: ' . $id_agente);
            http_response_code(404);
            echo json_encode(['message' => 'Agente no encontrado']);
        }
    } else {
        $logger->write('Fetching all agentes');

        $stmt = $db->query('SELECT * FROM agente');
        $agentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All agente data fetched: ' . json_encode($agentes));
        echo json_encode($agentes);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>