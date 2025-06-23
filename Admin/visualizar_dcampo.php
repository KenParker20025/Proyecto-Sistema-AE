<?php
// Verificar si se ha recibido un ID válido para la actividad a eliminar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtener el ID de la actividad a eliminar
    $id_actividad = $_GET['id'];

    // Realizar la conexión a la base de datos (ajusta los detalles según tu configuración)
    $conn = new mysqli("localhost", "root", "", "bd_sistemaae");

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Preparar la consulta SQL para eliminar la actividad
    $sql = "DELETE FROM actividades WHERE id = $id_actividad";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        // La actividad se eliminó correctamente
        echo "La actividad se eliminó correctamente.";
    } else {
        // Error al eliminar la actividad
        echo "Error al eliminar la actividad: " . $conn->error;
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // No se recibió un ID válido para la actividad a eliminar
    echo "No se recibió un ID válido para la actividad a eliminar.";
}
?>
