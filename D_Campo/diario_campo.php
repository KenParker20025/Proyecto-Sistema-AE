<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "administracion";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Manejar las operaciones CRUD
$action = $_POST['action'];

if ($action == 'add') {
    $titulo = $_POST['titulo'];
    $archivo = $_POST['archivo'];
    $fecha = $_POST['fecha'];
    $sql = "INSERT INTO diario_campo (titulo, archivo, fecha) VALUES ('$titulo', '$archivo', '$fecha')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'update') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $archivo = $_POST['archivo'];
    $fecha = $_POST['fecha'];
    $sql = "UPDATE diario_campo SET titulo='$titulo', archivo='$archivo', fecha='$fecha' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM diario_campo WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'fetch') {
    $sql = "SELECT * FROM diario_campo";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}

$conn->close();
?>
