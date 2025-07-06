<?php
require 'dbconexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_comunidad'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $codigo = $_POST['codigo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO comunidad (id_comunidad, nombre, apellido, codigo, contraseña) VALUES (:id, :nombre, :apellido, :codigo, :contraseña)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':id_comunidad', $id);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':contraseña', $contraseña);

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar.";
    }
}
?>