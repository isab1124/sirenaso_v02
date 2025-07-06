<?php
header('Content-Type: application/json');

try {
    // Conexión a la base de datos
    $pdo = new PDO('pgsql:host=localhost;dbname=sirenaso_sc', 'postgres', 'Armendarisa123.');

    // Obtén los datos enviados por POST
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['rutas'])) {
        throw new Exception('Datos inválidos.');
    }

    $rutas = $input['rutas'];

    // Insertar cada ruta en la tabla
    $stmt = $pdo->prepare('INSERT INTO rutas (nombre, distancia, duracion, geom, tipo_ruta) VALUES (:nombre, :distancia, :duracion, ST_GeomFromText(:geom, 4326), :tipo_ruta)');
    foreach ($rutas as $ruta) {
        $stmt->execute([
            ':nombre' => $ruta['nombre'],
            ':distancia' => $ruta['distancia'],
            ':duracion' => $ruta['duracion'],
            ':geom' => $ruta['geometria'],
            ':tipo_ruta' => $ruta['tipo_ruta'],
        ]);
    }

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}



