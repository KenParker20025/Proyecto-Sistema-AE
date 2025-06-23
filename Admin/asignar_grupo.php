<?php
include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$idDocente = $data['idDocente'];
$idGrupo = $data['idGrupo'];

// Consulta para verificar si el docente ya está asignado a un grupo
$queryVerificarAsignacion = "SELECT grupo_id FROM Registro WHERE id = $idDocente";
$resultVerificarAsignacion = mysqli_query($conn, $queryVerificarAsignacion);

if ($resultVerificarAsignacion) {
    $row = mysqli_fetch_assoc($resultVerificarAsignacion);
    if ($row['grupo_id'] !== null) {
        // El docente ya está asignado a un grupo, devolver un código de estado 409 (Conflicto)
        http_response_code(409);
    } else {
        // Consulta para asignar el grupo al docente
        $queryAsignarGrupo = "UPDATE Registro SET grupo_id = $idGrupo WHERE id = $idDocente";

        if (mysqli_query($conn, $queryAsignarGrupo)) {
            // Asignación exitosa, devolver un código de estado 200 (Éxito)
            http_response_code(200);
        } else {
            // Error del servidor, devolver un código de estado 500 (Error del servidor)
            http_response_code(500);
        }
    }
} else {
    // Error del servidor al verificar la asignación, devolver un código de estado 500 (Error del servidor)
    http_response_code(500);
}
?>
