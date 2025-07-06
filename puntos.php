<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$file = 'puntos.json';

if ($method === 'GET') {
    // Leer y devolver los puntos
    if (file_exists($file)) {
        echo file_get_contents($file);
    } else {
        echo json_encode([]);
    }
} elseif ($method === 'POST') {
    // Leer datos existentes
    $data = json_decode(file_get_contents($file), true) ?: [];

    // Obtener el nuevo punto del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $data[] = $input;

        // Guardar el nuevo conjunto de puntos en el archivo
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

        echo json_encode(['status' => 'success', 'message' => 'Punto agregado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
