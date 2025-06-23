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
    <title>Crear Clave</title>
    <link rel="stylesheet" href="../src/styles_Admin.css">
    <!-- Agregar SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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

    <div class="main-content">
        <center><h1>Crear Clave</h1></center>
        
        <section class="formulario-container">
            <form action="crear_clave.php" method="post">
                <input type="text" name="clave" class="crear-clave-input" placeholder="Generar Clave" required>
                <button type="submit" class="crear-clave-button">Generar Clave</button>
            </form>
        </section>
    <!-- Botón para ver claves -->
    <button id="ver-claves-button" class="ver-claves-button">Ver Claves</button>

<!-- Contenedor para mostrar las claves -->
<div id="claves-container" style="display: none;">
    <h2>Claves Creadas</h2>
    <table class="tabla-claves" id="tabla-claves">
        <thead>
            <tr>
                <th>ID</th>
                <th>Clave</th>
                <th>Uso</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
</div>
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

        // Mostrar y ocultar el contenedor de claves
        $('#ver-claves-button').click(function() {
            $('#claves-container').toggle();
            if ($('#claves-container').is(':visible')) {
                cargarClaves();
            }
        });

        // Función para cargar las claves desde la base de datos
        function cargarClaves() {
            $.ajax({
                url: 'obtener_claves.php',
                type: 'GET',
                success: function(response) {
                    $('#tabla-claves tbody').html(response);
                }
            });
        }

        function eliminarClave(idClave) {
    Swal.fire({
        title: '¿Está seguro de que desea eliminar esta clave?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar la solicitud AJAX para eliminar la clave
            $.ajax({
                type: 'POST',
                url: 'eliminar_clave.php',
                data: { eliminar_clave: idClave },
                success: function(response) {
                    // Si la eliminación fue exitosa, mostrar mensaje de éxito
                    Swal.fire(
                        '¡Clave eliminada!',
                        'La clave ha sido eliminada correctamente.',
                        'success'
                    ).then(() => {
                        // Recargar la página para reflejar los cambios
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    // Si hubo un error, mostrar mensaje de error
                    Swal.fire(
                        'Error',
                        'Ha ocurrido un error al intentar eliminar la clave.',
                        'error'
                    );
                    console.error(xhr.responseText);
                }
            });
        }
    });
}
$(document).ready(function() {
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo 'Swal.fire({
            icon: "success",
            title: "Clave creada exitosamente",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            // Eliminar los parámetros success y error de la URL
            history.replaceState(null, null, window.location.pathname);
        });';
    } elseif (isset($_GET['success']) && $_GET['success'] == 0) {
        echo 'Swal.fire({
            icon: "error",
            title: "Error al crear la clave",
            text: "La clave ya existe",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            // Eliminar los parámetros success y error de la URL
            history.replaceState(null, null, window.location.pathname);
        });';
    }
    ?>
});
    </script>
</body>
</html>
