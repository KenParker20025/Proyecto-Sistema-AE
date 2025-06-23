<?php
session_start();

// Verificar si se proporcionó un ID y una acción válida a través de GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['accion'])) {
    require_once '../ConexionBD/db.php'; // Archivo de conexión a la base de datos

    // Obtener el ID y la acción desde la URL
    $id = $_GET['id'];
    $accion = $_GET['accion'];

    // Verificar si la acción es editar
    if ($accion === 'editar') {
        // Redirigir a la página de edición con el ID del archivo
        header("Location: editar_archivo.php?id=$id");
        exit();
    } elseif ($accion === 'eliminar') {
        // Consulta para obtener la ruta del archivo
        $sql = "SELECT ruta_archivo FROM d_campo WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Obtener la ruta del archivo
            $row = $result->fetch_assoc();
            $ruta_archivo = $row['ruta_archivo'];

            // Eliminar el archivo de la carpeta de almacenamiento
            unlink($ruta_archivo);

            // Eliminar el registro de la base de datos
            $sql_delete = "DELETE FROM d_campo WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();

            // Redirigir a alguna página después de eliminar
            header("Location: DiarioDCampo.php");
            exit();
        } else {
            // No se encontró el archivo, puedes mostrar un mensaje de error o redirigir a alguna página
            header("Location: DiarioDCampo.php");
            exit();
        }
    } elseif ($accion === 'descargar') {
        // Consulta para obtener la ruta del archivo
        $sql = "SELECT ruta_archivo FROM d_campo WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Obtener la ruta del archivo
            $row = $result->fetch_assoc();
            $ruta_archivo = $row['ruta_archivo'];

            // Descargar el archivo
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($ruta_archivo) . '"');
            readfile($ruta_archivo);
            exit();
        } else {
            // No se encontró el archivo, puedes mostrar un mensaje de error o redirigir a alguna página
            header("Location: DiarioDCampo.php");
            exit();
        }
    } else {
        // Acción no válida, puedes mostrar un mensaje de error o redirigir a alguna página
        header("Location: DiarioDCampo.php");
        exit();
    }
} else {
    // No se proporcionó un ID o una acción válida a través de GET
    header("Location: DiarioDCampo.php");
    exit();
}
?>
