<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'director') {
    header("Location: ../Logs/inicio.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Admin</title>
    <link rel="stylesheet" href="../src/styles_Admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Añadir SweetAlert2 -->
</head>
<body>
    <div class="sidebar">
        <div class="titulo">Modo Admin</div>
        <div class="bienvenida">Bienvenido Director/a <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></div> <!-- Mostrar el nombre del director -->
        <a href="AdminDirector.php" class="opcion">Docentes</a>
        <a href="AdminAsignarD.php" class="opcion">Asignar Docentes</a>
        <a href="AdminGrupo.php" class="opcion">Grupos</a>
        <a href="AdminDiario.php" class="opcion">Diario de Campo</a>
        <a href="AdminCrearClave.php" class="opcion">Crear Clave</a>
    </div>

    <!-- Botón para salir -->
    <button onclick="cerrarSesion()" class="salir-button">Salir</button>

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

