<?php 
include 'dbconexion.php';

try {
    // Establecer conexión con la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? null;
    $apellido = $_POST['apellido'] ?? null;
    $codigo = $_POST['codigo'] ?? null;
    $contrasena = $_POST['contrasena'] ?? null;

    // Validar campos obligatorios
    if (empty($nombre) || empty($apellido) || empty($codigo) || empty($contrasena)) {
        throw new Exception("Todos los campos obligatorios deben completarse.");
    }

    // Ajuste del nombre de la columna "contraseña"
    $query = 'INSERT INTO comunidad (nombre, apellido, codigo, contrasena)
              VALUES (:nombre, :apellido, :codigo, :contrasena)';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':codigo', $codigo);
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
