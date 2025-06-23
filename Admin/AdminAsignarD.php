<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'director') {
    header("Location: ../Logs/inicio.php");
    exit();
}

include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos

// Consulta para obtener la lista de docentes
$queryDocentes = "SELECT id, nombre, apellidoPaterno, apellidoMaterno FROM Registro";
$resultDocentes = mysqli_query($conn, $queryDocentes);

// Consulta para obtener la lista de grupos
$queryGrupos = "SELECT id, CONCAT(grado, ' - ', grupo, ' - ', turno) AS nombre_grupo FROM Grupos";
$resultGrupos = mysqli_query($conn, $queryGrupos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación</title>
    <link rel="stylesheet" href="../src/styles_Admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Añadir SweetAlert2 -->
</head>
<body>
    <div class="sidebar">
        <div class="titulo">Modo Admin</div>
        <div class="bienvenida">Bienvenido Director/a <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></div>
        <a href="AdminDirector.php" class="opcion">Docentes</a>
        <a href="AdminAsignarD.php" class="opcion">Asignar Docentes</a>
        <a href="AdminGrupo.php" class="opcion">Grupos</a>
        <a href="AdminDiario.php" class="opcion">Diario de Campo</a>
        <a href="AdminCrearClave.php" class="opcion">Crear Clave</a>
    </div>

    <div class="main-content">
        <center><h1>Asignar Docente</h1></center>        
        <section id="tabla-container" class="tabla-container">
            <table class="tabla-diario">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Asignar</th>
                        <th>Desasignar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultDocentes)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['apellidoPaterno']; ?></td>
                            <td><?php echo $row['apellidoMaterno']; ?></td>
                            <td>
                                <button onclick="asignarGrupo(<?php echo $row['id']; ?>)" class="boton-asignar">Asignar</button>
                            </td>
                            <td>
                                <button onclick="desasignarGrupo(<?php echo $row['id']; ?>)" class="boton-desasignar">Desasignar</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    <button id="ver-claves-button" class="ver-claves-button">Ver Grupos</button>

    <section id="claves-container" style="display: none;">
            <h2>Grupos Disponibles</h2>
            <table class="tabla-claves" id="tabla-claves">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Grado - Grupo - Turno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($resultGrupos, 0); // Reiniciar el puntero del resultado ?>
                    <?php while ($row = mysqli_fetch_assoc($resultGrupos)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['nombre_grupo']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
        </div>
    </div>

    <button onclick="cerrarSesion()" class="salir-button">Salir</button>

    <script>
        const verGruposButton = document.getElementById('ver-claves-button');
        const gruposContainer = document.getElementById('claves-container');

        verGruposButton.addEventListener('click', () => {
            if (gruposContainer.style.display === 'none') {
                gruposContainer.style.display = 'block';
                verGruposButton.textContent = 'Ocultar Grupos';
            } else {
                gruposContainer.style.display = 'none';
                verGruposButton.textContent = 'Ver Grupos';
            }
        });

        function cerrarSesion() {
            Swal.fire({
                title: '¿Está seguro de que desea cerrar sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cerrar_sesion.php';
                }
            });
        }

        function asignarGrupo(idDocente) {
            mostrarDialogoGrupo(idDocente, 'Asignación');
        }

        function desasignarGrupo(idDocente) {
            mostrarDialogoGrupo(idDocente, 'Desasignación');
        }

        function mostrarDialogoGrupo(idDocente, accion) {
            Swal.fire({
                title: `Seleccione un grupo para ${accion}`,
                input: 'select',
                inputOptions: {
                    <?php mysqli_data_seek($resultGrupos, 0); // Reiniciar el puntero del resultado ?>
                    <?php while ($row = mysqli_fetch_assoc($resultGrupos)) { ?>
                        '<?php echo $row['id']; ?>': '<?php echo $row['nombre_grupo']; ?>',
                    <?php } ?>
                },
                inputPlaceholder: 'Seleccione un grupo',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe seleccionar un grupo';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const idGrupo = result.value;
                    const url = accion === 'Asignación' ? 'asignar_grupo.php' : 'desasignar_grupo.php';
                    // Enviar la información al servidor para asignar o desasignar el grupo
                    fetch(url, {
                        method: 'POST',
                        body: JSON.stringify({ idDocente, idGrupo }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            // Mostrar mensaje de éxito
                            Swal.fire('Éxito', `La ${accion.toLowerCase()} se realizó correctamente`, 'success');
                            // Puedes recargar la página para reflejar los cambios
                            // window.location.reload();
                        } else if (response.status === 409) {
                            // Docente ya está asignado al grupo
                            Swal.fire('Error', 'Este profesor ya está asignado a este grupo', 'error');
                        } else {
                            // Mostrar mensaje de error
                            Swal.fire('Error', `Hubo un problema al ${accion.toLowerCase()}`, 'error');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        // Mostrar mensaje de error
                        Swal.fire('Error', `Hubo un problema al ${accion.toLowerCase()}`, 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
