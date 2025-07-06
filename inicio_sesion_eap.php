<?php
// Iniciar la sesión
session_start();

// Configuración de conexión
$host = "localhost";  // Cambia si tu servidor no está en localhost
$dbname = "sirenaso_sc"; // Nombre de la base de datos
$user = "postgres";  // Usuario de PostgreSQL
$password = "Armendarisa123.";  // Contraseña del usuario PostgreSQL

// Establecer conexión
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . pg_last_error());
}

// Verificar datos enviados por el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo = $_POST['codigo'];
    $contrasena = $_POST['contrasena'];

    // Consulta para validar usuario con marcadores posicionales
    $sql = "SELECT * FROM eap WHERE codigo = $1 AND contrasena = $2";

    // Preparar y ejecutar la consulta
    $result = pg_query_params($conn, $sql, array($codigo, $contrasena));

    // Verificar resultados
    if (pg_num_rows($result) > 0) {
        $_SESSION['user'] = $codigo;
        header("Location: index_sig_rutas_clear.php"); // Redirigir a la página principal de EAP
        exit();
    } else {
        echo "Código o contraseña incorrectos.";
    }
}

// Cerrar la conexión
pg_close($conn);
?>
