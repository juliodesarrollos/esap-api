<?php

$logger = new Log();
$log_file = 'app.log'; // Reemplaza con la ruta correcta a tu archivo de log

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $num_lines = isset($_GET['lines']) ? intval($_GET['lines']) : 200; // Número de líneas a mostrar, por defecto 200

    if (file_exists($log_file)) {
        $lines = tailCustom($log_file, $num_lines);
        header('Content-Type: text/plain');
        echo implode("\n", $lines);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Archivo de log no encontrado']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}

function tailCustom($filepath, $lines = 200) {
    $f = fopen($filepath, "rb");
    fseek($f, -1, SEEK_END);
    if (fread($f, 1) != "\n") {
        $lines -= 1;
    }
    
    $output = '';
    $chunk = 4096;
    fseek($f, -$chunk, SEEK_END);
    $pos = ftell($f);
    while ($lines >= 0 && $pos > 0) {
        $data = fread($f, $chunk);
        $output = $data . $output;
        $lines -= substr_count($data, "\n");
        $pos -= $chunk;
        fseek($f, $pos);
    }
    fclose($f);
    return array_slice(explode("\n", $output), -$lines);
}
?>