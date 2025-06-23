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
    <title>Docentes</title>
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

    <div class="main-content">
        <center><h1>Docentes Registrados</h1></center>
        <section class="tabla-container">
            <table class="tabla-maestros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Grupo asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
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

                        // Consulta para obtener datos
                        $sql = "SELECT id, nombre, apellidoPaterno, apellidoMaterno, correo, usuario, grupo_id FROM registro";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Mostrar datos en la tabla
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row["id"] . "</td>
                                        <td>" . $row["nombre"] . "</td>
                                        <td>" . $row["apellidoPaterno"] . "</td>
                                        <td>" . $row["apellidoMaterno"] . "</td>
                                        <td>" . $row["correo"] . "</td>
                                        <td>" . $row["usuario"] . "</td>
                                        <td>" . $row["grupo_id"] . "</td>
                                        <td><button class='eliminar-button' onclick='eliminarUsuario(" . $row["id"] . ")'>Eliminar</button></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron resultados</td></tr>";
                        }
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </section>
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

        function eliminarUsuario(id) {
            Swal.fire({
                title: '¿Está seguro de que desea eliminar este Docente?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a un archivo PHP que elimina el usuario
                    window.location.href = 'eliminar_usuario.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>
