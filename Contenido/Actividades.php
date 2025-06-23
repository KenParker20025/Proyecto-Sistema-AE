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
    <title>Actividades</title>
    <link rel="stylesheet" href="../src/styles_Contenido.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

    <div class="table-container" id="table-container">
        <div class="table-section" id="table-lunes-section">
            <h2>Actividades Lunes</h2>
            <table id="table-lunes">
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
                        <td><input type="file" accept="application/pdf"></td>
                        <td><input type="text" class="datepicker"></td>
                        <td>
                            <button onclick="viewFile(1)">👁</button>
                            <button onclick="editRow(1)">✏</button>
                            <button onclick="deleteRow(1)">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><input type="file" accept="application/pdf"></td>
                        <td><input type="text" class="datepicker"></td>
                        <td>
                            <button onclick="viewFile(2)">👁</button>
                            <button onclick="editRow(2)">✏</button>
                            <button onclick="deleteRow(2)">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><input type="file" accept="application/pdf"></td>
                        <td><input type="text" class="datepicker"></td>
                        <td>
                            <button onclick="viewFile(3)">👁</button>
                            <button onclick="editRow(3)">✏</button>
                            <button onclick="deleteRow(3)">🗑</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="add-row-button" onclick="addRow('table-lunes')">➕</button>
            <button class="delete-table-button" onclick="deleteTable('table-lunes-section')">Eliminar Tabla</button>
        </div>

        <div class="table-section" id="table-martes-section">
            <h2>Actividades Martes</h2>
            <table id="table-martes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Grupo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Filas de ejemplo, similar a las de Lunes -->
                </tbody>
            </table>
            <button class="add-row-button" onclick="addRow('table-martes')">➕</button>
            <button class="delete-table-button" onclick="deleteTable('table-martes-section')">Eliminar Tabla</button>
        </div>
    </div>

    <div class="create-section">
        <button class="create-section-button" onclick="createSection()">Crear apartado</button>
    </div>

    <script src="../src/scripts_Contenido.js"></script>
    <script>
        function cerrarSesion() {
            Swal.fire({
                title: '¿Está seguro de que desea cerrar sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a un archivo PHP que cierre la sesión
                    window.location.href = 'cerrar_sesion.php';
                }
            });
        }
    </script>
</body>
</html>