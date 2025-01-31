<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

try {
    if (isset($_GET['id_extintor'])) {
        $id_extintor = $_GET['id_extintor'];
        $logger->write('Fetching extintor with ID: ' . $id_extintor);

        $stmt = $db->prepare('
            SELECT e.*, a.agente, c.capacidad, m.marca 
            FROM extintor e
            JOIN agente a ON e.id_agente = a.id_agente
            JOIN capacidad c ON e.id_capacidad = c.id_capacidad
            JOIN marca m ON e.id_marca = m.id_marca
            WHERE e.id_extintor = ?
            ORDER BY e.id_extintor ASC
        ');
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
    } elseif (isset($_GET['id_empresa'])) {
        $id_empresa = $_GET['id_empresa'];
        $logger->write('Fetching extintores with empresa ID: ' . $id_empresa);

        $stmt = $db->prepare('
            SELECT e.*, a.agente, c.capacidad, m.marca 
            FROM extintor e
            JOIN agente a ON e.id_agente = a.id_agente
            JOIN capacidad c ON e.id_capacidad = c.id_capacidad
            JOIN marca m ON e.id_marca = m.id_marca
            WHERE e.id_empresa = ?
            ORDER BY e.id_extintor ASC
        ');
        $stmt->execute([$id_empresa]);
        $extintores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($extintores) {
            $logger->write('Extintores data fetched: ' . json_encode($extintores));
            echo json_encode($extintores);
        } else {
            $logger->write('No extintores found with empresa ID: ' . $id_empresa);
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron extintores para la empresa especificada']);
        }
    } else {
        $logger->write('Fetching all extintores');

        $stmt = $db->query('
            SELECT e.*, a.agente, c.capacidad, m.marca 
            FROM extintor e
            JOIN agente a ON e.id_agente = a.id_agente
            JOIN capacidad c ON e.id_capacidad = c.id_capacidad
            JOIN marca m ON e.id_marca = m.id_marca
            ORDER BY e.id_extintor ASC
        ');
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