<?php
session_start();
include 'dbconexion.php'; // Asegúrate de que este archivo existe y funciona correctamente

// Obtener los datos enviados desde JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Validar los datos recibidos
$codigo = isset($data['codigo']) ? $data['codigo'] : null;
$contraseña = isset($data['contraseña']) ? $data['contraseña'] : null;

if (!$codigo || !$contraseña) {
    echo json_encode(['message' => 'Datos incompletos. Por favor, llena ambos campos.']);
    exit;
}

try {
    // Conexión a la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL corregida
    $query = "SELECT * FROM comunidad WHERE codigo = :codigo AND contraseña = :contrasena"; // Nota: Se corrigió el nombre del parámetro a :contrasena
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $contraseña, PDO::PARAM_STR); // También se corrigió aquí
    $stmt->execute();

    // Verificar si se encontró un usuario
    if ($stmt->rowCount() > 0) {
        $_SESSION['usuario'] = $codigo; // Guardar el usuario en la sesión
        echo json_encode(['redirect' => 'index.html']); // Redirigir a index.html
    } else {
        echo json_encode(['message' => 'Credenciales incorrectas.']);
    }
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}
?>
