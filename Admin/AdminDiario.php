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
    <title>Diario de Campo</title>
    <link rel="stylesheet" href="../src/styles_Admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Añadir SweetAlert2 -->
    <style>
        
    </style>
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
        <center><h1>Diario de Campo</h1></center>
        
        <section class="tabla-container">
            <table class="tabla-diario">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Archivo</th>
                        <th>Autor</th>
                        <th>Fecha</th>
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

                        $sql = "SELECT id, nombre, autor, fecha, ruta_archivo FROM d_campo";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $id = $row["id"];
                                $nombre = $row["nombre"];
                                $autor = $row["autor"];
                                $fecha = $row["fecha"];
                                $ruta_archivo = "../Contenido/" . $row["ruta_archivo"];
                                echo "<tr>
                                        <td>$id</td>
                                        <td>$nombre</td>
                                        <td>$autor</td>
                                        <td>$fecha</td>
                                        <td><button class='ver-button' onclick='visualizarArchivo(\"$ruta_archivo\")'>Visualizar</button></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay resultados</td></tr>";
                        }

                        $conn->close();
                    ?>
                </tbody>
            </table>
        </section>
    </div>

    <button onclick="cerrarSesion()" class="salir-button">Salir</button>

    <!-- Modal para visualizar archivos -->
    <div id="modalArchivo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <iframe id="iframeArchivo" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
        </div>
    </div>

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
                    window.location.href = 'cerrar_sesion.php';
                }
            });
        }

        function visualizarArchivo(ruta) {
            var modal = document.getElementById("modalArchivo");
            var iframe = document.getElementById("iframeArchivo");
            iframe.src = ruta;
            modal.style.display = "flex";
        }

        function cerrarModal() {
            var modal = document.getElementById("modalArchivo");
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById("modalArchivo");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
