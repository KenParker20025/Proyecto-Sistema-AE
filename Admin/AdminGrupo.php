<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'director') {
    header("Location: ../Logs/inicio.php");
    exit();
}

include_once '../ConexionBD/db.php'; // Incluir archivo de conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos Registrados</title>
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
        <center><h1>Grupos Registrados</h1></center>
        <button class="boton-asignar" onclick="mostrarAgregarGrupo()">Agregar Grupo</button>
        <section class="tabla-container">
            <table class="tabla-maestros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Grado</th>
                        <th>Grupo</th>
                        <th>Turno</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Consulta para obtener datos
                        $sql = "SELECT id, grado, grupo, turno FROM Grupos";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Mostrar datos en la tabla
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row["id"] . "</td>
                                        <td>" . $row["grado"] . "</td>
                                        <td>" . $row["grupo"] . "</td>
                                        <td>" . $row["turno"] . "</td>
                                        <td><button class='boton-desasignar' onclick='eliminarGrupo(" . $row["id"] . ")'>Eliminar</button></td>
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
                    window.location.href = 'cerrar_sesion.php';
                }
            });
        }

        function mostrarAgregarGrupo() {
            Swal.fire({
                title: 'Agregar Nuevo Grupo',
                html: `
                    <input type="text" id="grado" class="swal2-input" placeholder="Grado">
                    <input type="text" id="grupo" class="swal2-input" placeholder="Grupo">
                    <input type="text" id="turno" class="swal2-input" placeholder="Turno">
                `,
                showCancelButton: true,
                confirmButtonText: 'Agregar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const grado = Swal.getPopup().querySelector('#grado').value;
                    const grupo = Swal.getPopup().querySelector('#grupo').value;
                    const turno = Swal.getPopup().querySelector('#turno').value;
                    if (!grado || !grupo || !turno) {
                        Swal.showValidationMessage(`Por favor complete todos los campos`);
                    }
                    return { grado: grado, grupo: grupo, turno: turno };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { grado, grupo, turno } = result.value;
                    fetch('agregar_grupo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ grado, grupo, turno })
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              Swal.fire('Éxito', 'Grupo agregado correctamente', 'success').then(() => {
                                  window.location.reload();
                              });
                          } else {
                              Swal.fire('Error', data.message, 'error');
                          }
                      }).catch(error => {
                          console.error('Error:', error);
                          Swal.fire('Error', 'Hubo un problema al agregar el grupo', 'error');
                      });
                }
            });
        }

        function eliminarGrupo(id) {
            Swal.fire({
                title: '¿Está seguro de que desea eliminar este Grupo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('eliminar_grupo.php?id=' + id, {
                        method: 'GET'
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              Swal.fire('Éxito', 'Grupo eliminado correctamente', 'success').then(() => {
                                  window.location.reload();
                              });
                          } else {
                              Swal.fire('Error', data.message, 'error');
                          }
                      }).catch(error => {
                          console.error('Error:', error);
                          Swal.fire('Error', 'Hubo un problema al eliminar el grupo', 'error');
                      });
                }
            });
        }
    </script>
</body>
</html>
