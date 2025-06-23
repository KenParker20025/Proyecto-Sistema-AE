<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_sistemaae";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar si se recibió el ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar el registro con el ID correspondiente
    $sql = "DELETE FROM registro WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Usuario eliminado exitosamente.');
                window.location.href = 'AdminDirector.php';
              </script>";
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }
} else {
    echo "ID de usuario no especificado.";
}

$conn->close();
?>
r