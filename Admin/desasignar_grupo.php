<?php
include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$idDocente = $data['idDocente'];

// Consulta para desasignar el grupo del docente
$queryDesasignarGrupo = "UPDATE Registro SET grupo_id = NULL WHERE id = $idDocente";

if (mysqli_query($conn, $queryDesasignarGrupo)) {
    http_response_code(200); // Éxito
} else {
    http_response_code(500); // Error del servidor
}
?>
