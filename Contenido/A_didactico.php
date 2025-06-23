<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: ../Logs/inicio.php");
    exit();
}

include_once '../ConexionBD/db.php';

// Verificar si el ID del usuario está en la sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "ID de usuario no encontrado en la sesión.";
    exit();
}

$idDocente = $_SESSION['id_usuario'];

// Obtener la información del docente
$query = "
    SELECT R.nombre, R.apellidoPaterno, R.apellidoMaterno, R.usuario, R.correo, G.grado, G.grupo, G.turno, R.foto_perfil
    FROM Registro R
    LEFT JOIN Grupos G ON R.grupo_id = G.id
    WHERE R.id = $idDocente
";
$result = mysqli_query($conn, $query);

if ($result) {
    $docente = mysqli_fetch_assoc($result);
} else {
    echo "Error al obtener los datos del perfil: " . mysqli_error($conn);
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Documentos</title>
    <link rel="stylesheet" href="../src/styles_Contenido.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
<div class="navbar">
    <div class="bienvenida">Bienvenido/a <?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
    <a href="InicioD.php">Inicio</a>
    <a href="Actividades.php">Actividades</a>
    <a href="A_didactico.php">A. Didáctico</a>
    <a href="DiarioDCampo.php">Diario de Campo</a>
    <div class="user-icon" onclick="toggleUserMenu()">
    <img src="perfil/<?php echo htmlspecialchars($docente['foto_perfil'] ? $docente['foto_perfil'] : 'profile_placeholder.png'); ?>" alt="User Icon" id="profile-image">
        <div class="user-menu" id="user-menu">
            <button class="profile-button" onclick="window.location.href = 'ver_perfil.php';">Ver Perfil</button>
            <button onclick="cerrarSesion()" class="salir-button">Cerrar Sesión</button>
        </div>
    </div>
</div>
    <div class="container">
        <div class="header">
            <button id="addTableBtn" class="Crear_ap">Crear apartado</button>
        </div>
        <div class="table-container" id="tableContainer">
            <div class="table" id="table1">
                <div class="table-header">
                    <span>Presentaciones</span>
                    <button class="edit-table-btn">Editar</button>
                    <button class="delete-table-btn">Eliminar</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><input type="file"></td>
                            <td><input type="date"></td>
                            <td>
                                <button class="view-btn">👁️</button>
                                <button class="edit-row-btn">✏️</button>
                                <button class="save-row-btn">💾</button>
                                <button class="delete-row-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><input type="file"></td>
                            <td><input type="date"></td>
                            <td>
                                <button class="view-btn">👁️</button>
                                <button class="edit-row-btn">✏️</button>
                                <button class="save-row-btn">💾</button>
                                <button class="delete-row-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><input type="file"></td>
                            <td><input type="date"></td>
                            <td>
                                <button class="view-btn">👁️</button>
                                <button class="edit-row-btn">✏️</button>
                                <button class="save-row-btn">💾</button>
                                <button class="delete-row-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="add-row-btn">➕</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../src/scripts_Contenido.js"></script>
</body>
</html>
