<?php
require 'db.php'; 

$db = Database::getInstance();
$logger = new Log();

$logger->write('Request received: ' . $_SERVER['REQUEST_METHOD']);

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];
    $logger->write('Fetching user with ID: ' . $id_usuario);

    $stmt = $db->prepare('SELECT * FROM usuario WHERE id_usuario = ? ORDER BY tipo_usuario ASC');
    $stmt->execute([$id_usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $logger->write('User data fetched: ' . json_encode($user));
    echo json_encode($user);
} else {
    $logger->write('Fetching all users ordered by tipo_usuario ASC');

    $stmt = $db->query('SELECT * FROM usuario ORDER BY tipo_usuario ASC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $logger->write('All user data fetched: ' . json_encode($users));
    echo json_encode($users);
}
?>
