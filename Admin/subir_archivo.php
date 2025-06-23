<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'director') {
    header("Location: ../Logs/inicio.php");
    exit();
}

// Verificar que se haya enviado un archivo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
    $nombreArchivo = $_FILES['archivo']['name'];
    $rutaArchivo = '../uploads/' . basename($nombreArchivo);
    $autor = $_SESSION['nombre_usuario'];

    // Mover el archivo subido a la carpeta de destino
    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaArchivo)) {
        // Conectar a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bd_sistemaae";
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insertar los detalles del archivo en la base de datos
        $stmt = $conn->prepare("INSERT INTO d_campo (nombre, ruta_archivo, autor) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombreArchivo, $rutaArchivo, $autor);
        if ($stmt->execute()) {
            echo "El archivo se ha subido y registrado correctamente.";
        } else {
            echo "Error al registrar el archivo en la base de datos.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "No se ha enviado ningún archivo.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivo</title>
    <link rel="stylesheet" href="../src/styles_Admin.css">
</head>
<body>
    <div class="main-content">
        <center><h1>Subir Archivo</h1></center>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="archivo">Seleccione un archivo:</label>
            <input type="file" name="archivo" id="archivo" required>
            <button type="submit">Subir</button>
        </form>
    </div>
</body>
</html>
