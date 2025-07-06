<?php 
include 'dbconexion.php';

try {
    // Establecer conexión con la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del formulario
    $nombre_eap = $_POST['nombres'] ?? null;
    $codigo_eap = $_POST['codigo'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    $contrasena = $_POST['correo'] ?? null;

    // Validar campos obligatorios
    if (empty($nombre_eap) || empty($codigo_eap) || empty($direccion) || empty($tipo) || empty($contrasena)) {
        throw new Exception("Todos los campos obligatorios deben completarse.");
    }

    // Insertar los datos en la base de datos
    $query = "INSERT INTO eap (nombre, codigo, direccion, tipo, contrasena)
              VALUES (:nombre_eap, :codigo_eap, :direccion, :tipo, :contrasena)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre_eap', $nombre_eap);
    $stmt->bindParam(':codigo_eap', $codigo_eap);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':contrasena', $contrasena);

    $stmt->execute();

    // Confirmación del registro
    echo "<script>alert('Registro exitoso'); window.location.href = 'index.html';</script>";
} catch (PDOException $e) {
    // Manejar errores de conexión o SQL
    echo "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    // Manejar otros errores
    echo "Error: " . $e->getMessage();
}
?>
