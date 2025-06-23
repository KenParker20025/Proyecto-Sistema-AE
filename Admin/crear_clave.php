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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clave = $_POST['clave'];

    // Valida la clave si es necesario
    if (!empty($clave)) {
        // Conexión a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Prepara y ejecuta la consulta
        $stmt = $conn->prepare("INSERT INTO claves (clave) VALUES (?)");
        $stmt->bind_param("s", $clave);

        if ($stmt->execute()) {
            // Cierra la conexión
            $stmt->close();
            $conn->close();

            // Redirige a AdminCrearClave.php con mensaje de éxito por 5 segundos
            header("Location: AdminCrearClave.php?success=1");
            exit();
        } else {
            // Cierra la conexión
            $stmt->close();
            $conn->close();

            // Redirige a AdminCrearClave.php con mensaje de error por 5 segundos
            header("Location: AdminCrearClave.php?success=0");
            exit();
        }
    } else {
        echo "Por favor, ingresa una clave.";
    }
}
?>
