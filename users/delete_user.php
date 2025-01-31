<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

$id_usuario = $_GET['id_usuario'];
$logger->write('Delete request received for user ID: ' . $id_usuario);

try {
    $stmt_select = $db->prepare('SELECT tipo_usuario FROM usuario WHERE id_usuario = ?');
    $stmt_select->execute([$id_usuario]);
    $current_tipo_usuario = $stmt_select->fetchColumn();

    $new_tipo_usuario = 'inactivo_' . $current_tipo_usuario;
    $logger->write('Current tipo_usuario: ' . $current_tipo_usuario . ', New tipo_usuario: ' . $new_tipo_usuario);

    $stmt_update = $db->prepare('UPDATE usuario SET tipo_usuario = ? WHERE id_usuario = ?');
    $logger->write('Prepared statement for update: ' . $stmt_update->queryString);

    $result = $stmt_update->execute([$new_tipo_usuario, $id_usuario]);

    if ($result) {
        $logger->write('User marked as inactive successfully with new tipo_usuario: ' . $new_tipo_usuario);
        echo json_encode(['message' => 'Usuario marcado como inactivo']);
    } else {
        $errorInfo = $stmt_update->errorInfo();
        $logger->write('Failed to mark user as inactive: ' . json_encode($errorInfo));
        echo json_encode(['message' => 'Error al marcar el usuario como inactivo', 'error' => $errorInfo]);
    }
} catch (PDOException $e) {
    $logger->write('PDOException: ' . $e->getMessage());
    echo json_encode(['message' => 'Error al marcar el usuario como inactivo', 'error' => $e->getMessage()]);
}
?>
