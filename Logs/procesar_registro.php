<?php
// Incluir el archivo de conexión a la base de datos
include '../ConexionBD/db.php';

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellidoPaterno = $_POST['apellidoPaterno'];
$apellidoMaterno = $_POST['apellidoMaterno'];
$clave = $_POST['clave'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Verificar que la clave sea válida y no esté usada
$sql_clave = "SELECT id FROM claves WHERE clave='$clave' AND usada=FALSE";
$result_clave = $conn->query($sql_clave);

if ($result_clave->num_rows > 0) {
    $row_clave = $result_clave->fetch_assoc();
    $clave_id = $row_clave['id'];
    
    // Crear la consulta SQL para insertar los datos
    $sql_insert = "INSERT INTO registro (nombre, apellidoPaterno, apellidoMaterno, clave, correo, usuario, contraseña, clave_id)
                   VALUES ('$nombre', '$apellidoPaterno', '$apellidoMaterno', '$clave', '$correo', '$usuario', '$contraseña', '$clave_id')";

    // Ejecutar la consulta y verificar si fue exitosa
    if ($conn->query($sql_insert) === TRUE) {
        // Marcar la clave como usada
        $sql_update_clave = "UPDATE claves SET usada=TRUE WHERE id=$clave_id";
        $conn->query($sql_update_clave);
        
        // Mostrar un mensaje de éxito con SweetAlert2
        echo json_encode(array("status" => "success", "message" => "¡Docente Registrado con éxito!"));
    } else {
        // Mostrar un mensaje de error con SweetAlert2
        echo json_encode(array("status" => "error", "message" => "Error al registrar: " . $conn->error));
    }
} else {
    // Mostrar un mensaje de error con SweetAlert2
    echo json_encode(array("status" => "error", "message" => "Clave inválida o ya utilizada."));
}

// Cerrar la conexión
$conn->close();
?>