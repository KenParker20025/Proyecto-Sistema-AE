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

// Consulta para obtener las claves
$sql = "SELECT id, clave, usada FROM claves";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Construir el HTML de las claves
    $html = "";
    while($row = $result->fetch_assoc()) {
        $html .= "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["clave"] . "</td>
                    <td>" . $row["usada"] . "</td>
                    <td><button onclick='eliminarClave(" . $row["id"] . ")' class='eliminar-clave-button'>Eliminar</button></td>
                  </tr>";
    }
    echo $html;
} else {
    echo "<tr><td colspan='3'>No hay claves registradas</td></tr>";
}

$conn->close();
?>
