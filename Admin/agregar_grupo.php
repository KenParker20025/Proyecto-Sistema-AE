<?php
include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos

$data = json_decode(file_get_contents('php://input'), true);

$grado = $data['grado'];
$grupo = $data['grupo'];
$turno = $data['turno'];

$sql = "INSERT INTO Grupos (grado, grupo, turno) VALUES ('$grado', '$grupo', '$turno')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar el grupo: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
