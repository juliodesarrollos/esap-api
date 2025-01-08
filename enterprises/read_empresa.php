<?php
require 'db.php';

$db = Database::getInstance();
$logger = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->prepare('
            SELECT e.*, u.id_usuario, u.nombre_usuario, u.direccion_usuario, u.telefono_usuario, u.correo_usuario, u.contraseña_usuario, u.tipo_usuario, u.first_login, u.created_at AS usuario_created_at, u.created_by AS usuario_created_by
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
                $usuario = [
                    'id_usuario' => $empresa['id_usuario'] ?? '',
                    'nombre_usuario' => $empresa['nombre_usuario'] ?? '',
                    'direccion_usuario' => $empresa['direccion_usuario'] ?? '',
                    'telefono_usuario' => $empresa['telefono_usuario'] ?? '',
                    'correo_usuario' => $empresa['correo_usuario'] ?? '',
                    'contraseña_usuario' => $empresa['contraseña_usuario'] ?? '',
                    'tipo_usuario' => $empresa['tipo_usuario'] ?? '',
                    'first_login' => $empresa['first_login'] ?? false,
                    'created_at' => $empresa['usuario_created_at'] ?? '',
                    'created_by' => $empresa['usuario_created_by'] ?? ''
                ];
                unset($empresa['id_usuario'], $empresa['nombre_usuario'], $empresa['direccion_usuario'], $empresa['telefono_usuario'], $empresa['correo_usuario'], $empresa['contraseña_usuario'], $empresa['tipo_usuario'], $empresa['first_login'], $empresa['usuario_created_at'], $empresa['usuario_created_by']);
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