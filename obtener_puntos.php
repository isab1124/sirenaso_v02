<?php
include 'dbconexion.php';

$query = "SELECT id, latitud, longitud, nombre, fecha, nivel_situacion, descripcion, foto, ST_AsGeoJSON(geom) as geom FROM registros_comunidad";
$result = pg_query($conn, $query);

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
