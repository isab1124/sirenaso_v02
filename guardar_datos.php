<?php 
include 'dbconexion.php';

try {
    // Establecer conexión con la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $nivel = $_POST['nivel'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $foto = $_POST['foto'] ?? null;
    $latitud = $_POST['latitud'] ?? null;
    $longitud = $_POST['longitud'] ?? null;

    // Validar campos obligatorios
    if (empty($nombre) || empty($fecha) || empty($nivel) || empty($latitud) || empty($longitud)) {
        throw new Exception("Todos los campos obligatorios deben completarse.");
    }

    // Validar formato de la fecha
    if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
        throw new Exception("El formato de la fecha no es válido. Use YYYY-MM-DD.");
    }

    // Validar formato de coordenadas
    if (!is_numeric($latitud) || !is_numeric($longitud)) {
        throw new Exception("Las coordenadas deben ser valores numéricos.");
    }

    // Insertar los datos en la base de datos
    $query = "INSERT INTO registros_comunidad (nombre, fecha, nivel_situacion, descripcion, foto, latitud, longitud)
              VALUES (:nombre, :fecha, :nivel, :descripcion, :foto, :latitud, :longitud)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':nivel', $nivel);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':latitud', $latitud);
    $stmt->bindParam(':longitud', $longitud);

    $stmt->execute();

    // Confirmación del registro
    echo "<script>alert('Registro exitoso'); window.location.href = 'index_sig_comunidad.html';</script>";
} catch (PDOException $e) {
    // Manejar errores de conexión o SQL
    echo "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    // Manejar otros errores
    echo "Error: " . $e->getMessage();
}
?>
