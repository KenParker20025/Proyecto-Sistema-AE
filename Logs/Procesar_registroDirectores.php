<?php
// Incluir el archivo de conexión a la base de datos
include '../ConexionBD/db.php';

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellidoPaterno = $_POST['apellidoPaterno'];
$apellidoMaterno = $_POST['apellidoMaterno'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Crear la consulta SQL para insertar los datos
$sql_insert = "INSERT INTO directores (nombre, apellidoPaterno, apellidoMaterno, correo, usuario, contraseña)
               VALUES ('$nombre', '$apellidoPaterno', '$apellidoMaterno', '$correo', '$usuario', '$contraseña')";

// Ejecutar la consulta y verificar si fue exitosa
if ($conn->query($sql_insert) === TRUE) {
    // Mostrar un mensaje de éxito con SweetAlert2
    echo json_encode(array("status" => "success", "message" => "¡Director registrado con éxito!"));
} else {
    // Mostrar un mensaje de error con SweetAlert2
    echo json_encode(array("status" => "error", "message" => "Error al registrar: " . $conn->error));
}

// Cerrar la conexión
$conn->close();
?>
