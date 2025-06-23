<?php
// Incluir el archivo de conexión a la base de datos
include '../ConexionBD/db.php';

// Obtener la clave enviada desde el frontend
$data = json_decode(file_get_contents('php://input'), true);
$clave = $data['clave'];

// Consultar la base de datos para verificar la clave
$sql = "SELECT * FROM clave_regdir WHERE clave='$clave'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si la clave es correcta, devolver un mensaje de éxito
    echo json_encode(array("status" => "success"));
} else {
    // Si la clave es incorrecta, devolver un mensaje de error
    echo json_encode(array("status" => "error"));
}

// Cerrar la conexión
$conn->close();
?>
