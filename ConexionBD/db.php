<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_sistemaae";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
