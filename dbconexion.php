<?php
$host = "localhost";
$port = "5432";
$dbname = "sirenaso_sc";
$user = "postgres";
$password = "Armendarisa123.";  // Asegúrate de reemplazar 'tu_contraseña' con la contraseña correcta

// Crear conexión
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
}
?>