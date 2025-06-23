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
    <title>Inicio - Docentes</title>
    <link rel="stylesheet" href="../src/styles_Contenido.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
    </style>
</head>
<body>
    <div class="navbar">
        <div class="bienvenida">Bienvenido/a <?php echo htmlspecialchars($_SESSION['usuario']); ?></div> <!-- Mostrar el nombre del Docente -->
        <a href="InicioD.php">Inicio</a>
        <div class="user-icon" onclick="toggleUserMenu()">
        <img src="perfil/<?php echo htmlspecialchars($docente['foto_perfil'] ? $docente['foto_perfil'] : 'profile_placeholder.png'); ?>" alt="User Icon" id="profile-image">
            <div class="user-menu" id="user-menu">
                <button class="profile-button" onclick="window.location.href = 'ver_perfil.php';">Ver Perfil</button>
                <button onclick="cerrarSesion()" class="salir-button">Cerrar Sesión</button>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card">
            <a href="DiarioDCampo.php">
                <img src="../img/Tab-D_Campo.jpg" alt="Diario de Campo">
                <div class="card-title">Diario de Campo</div>
            </a>
        </div>
        <div class="card">
            <a href="Actividades.php">
                <img src="../img/Tab-Actividades.jpg" alt="Actividades">
                <div class="card-title">Actividades</div>
            </a>
        </div>
        <div class="card">
            <a href="A_didactico.php">
                <img src="../img/Tab-A_Didactico.jpg" alt="Apoyo Didáctico">
                <div class="card-title">Apoyo Didáctico</div>
            </a>
        </div>
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

        function toggleUserMenu() {
            var userMenu = document.getElementById('user-menu');
            if (userMenu.style.display === 'block') {
                userMenu.style.display = 'none';
            } else {
                userMenu.style.display = 'block';
            }
        }

        window.onclick = function(event) {
            var userMenu = document.getElementById('user-menu');
            if (!event.target.matches('.user-icon img')) {
                if (userMenu.style.display === 'block') {
                    userMenu.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
