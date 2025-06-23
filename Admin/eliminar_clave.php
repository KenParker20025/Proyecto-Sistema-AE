<?php
// Verificar la sesión del director
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'director') {
    header("Location: ../Logs/inicio.php");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_sistemaae";

// Función para eliminar una clave
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_clave'])) {
    // Obtener el ID de la clave a eliminar
    $idClave = $_POST['eliminar_clave'];

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta SQL para eliminar la clave
    $sql = "DELETE FROM claves WHERE id = $idClave";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        // Redirigir a AdminCrearClave.php con mensaje de éxito
        header("Location: AdminCrearClave.php?success=1");
        exit();
    } else {
        // Redirigir a AdminCrearClave.php con mensaje de error
        header("Location: AdminCrearClave.php?error=1");
        exit();
    }

    // Cerrar conexión
    $conn->close();
} else {
    // Redirigir si no se proporciona un ID de clave
    header("Location: AdminCrearClave.php");
    exit();
}
?>
