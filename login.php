<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger->write('Login request received: ' . json_encode($data));

    if (isset($data['correo_usuario']) && isset($data['contraseña_usuario'])) {
        $correo = $data['correo_usuario'];
        $contraseña = $data['contraseña_usuario'];

        $stmt = $db->prepare('SELECT * FROM usuario WHERE correo_usuario = ?');
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($contraseña, $user['contraseña_usuario'])) {
            $logger->write('User authenticated: ' . json_encode($user));
            echo json_encode(['message' => 'Login exitoso']);
        } else {
            $logger->write('Login failed for: ' . $correo);
            echo json_encode(['message' => 'Correo o contraseña incorrectos']);
        }
    } else {
        $logger->write('Correo o contraseña faltantes en la solicitud: ' . json_encode($data));
        echo json_encode(['message' => 'Correo o contraseña faltantes']);
    }
} else {
    $logger->write('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['message' => 'Método no permitido']);
}
?>
