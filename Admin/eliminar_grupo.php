<?php
include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos

$id = $_GET['id'];

// Verificar si el grupo está asignado a algún docente
$checkQuery = "SELECT * FROM Registro WHERE clave_id = $id";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    echo json_encode(['success' => false, 'message' => 'El grupo está asignado a un docente y no puede ser eliminado']);
} else {
    $sql = "DELETE FROM Grupos WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el grupo: ' . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>
