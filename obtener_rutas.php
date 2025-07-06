<?php
header('Content-Type: application/json');

try {
    // Conexión a la base de datos
    $pdo = new PDO('pgsql:host=localhost;dbname=sirenaso_sc', 'postgres', 'Armendarisa123.');

    // Consultar las rutas almacenadas
    $stmt = $pdo->query("SELECT id, nombre, distancia, duracion, ST_AsText(geom) AS geometria FROM rutas");
    $rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'rutas' => $rutas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

