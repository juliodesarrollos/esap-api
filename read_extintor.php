<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_extintor'])) {
        $id_extintor = $_GET['id_extintor'];
        $logger->write('Fetching extintor with ID: ' . $id_extintor);

        $stmt = $db->prepare('SELECT * FROM extintor WHERE id_extintor = ?');
        $stmt->execute([$id_extintor]);
        $extintor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($extintor) {
            $logger->write('Extintor data fetched: ' . json_encode($extintor));
            echo json_encode($extintor);
        } else {
            $logger->write('Extintor not found with ID: ' . $id_extintor);
            http_response_code(404);
            echo json_encode(['message' => 'Extintor no encontrado']);
        }
    } else {
        $logger->write('Fetching all extintores');

        $stmt = $db->query('SELECT * FROM extintor');
        $extintores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logger->write('All extintor data fetched: ' . json_encode($extintores));
        echo json_encode($extintores);
    }
} catch (Exception $e) {
    $logger->write('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error interno del servidor']);
}
?>