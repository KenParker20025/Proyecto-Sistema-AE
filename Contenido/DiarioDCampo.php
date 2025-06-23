<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: ../Logs/inicio.php");
    exit();
}

require '../ConexionBD/db.php'; // Ruta de tu archivo de conexión a la base de datos


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

function obtenerArchivos() {
    global $conn;
    $sql = "SELECT * FROM d_campo";
    $result = $conn->query($sql);
    $archivos = [];
    while ($row = $result->fetch_assoc()) {
        $archivos[] = $row;
    }
    return $archivos;
}

$archivos = obtenerArchivos(); // Obtener los archivos de la base de datos

// Función para eliminar un archivo físico
function eliminarArchivoFisico($ruta) {
    if (file_exists($ruta)) {
        unlink($ruta); // Eliminar el archivo físico
    }
}

// Función para eliminar un archivo
function eliminarArchivo($id) {
    global $conn;
    $sql = "SELECT ruta_archivo FROM d_campo WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $archivo = $result->fetch_assoc();
    $stmt->close();

    // Eliminar el archivo físico
    eliminarArchivoFisico($archivo['ruta_archivo']);

    // Eliminar el registro de la base de datos
    $sql = "DELETE FROM d_campo WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Función para obtener los detalles de un archivo por su ID
function obtenerDetallesArchivo($id) {
    global $conn;
    $sql = "SELECT * FROM d_campo WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $archivo = $result->fetch_assoc();
    $stmt->close();
    return $archivo;
}

// Función para editar un archivo
function editarArchivo($id, $nombre, $fecha, $nuevoNombreArchivo) {
    global $conn;
    $sql = "UPDATE d_campo SET nombre = ?, ruta_archivo = ?, fecha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $nuevoNombreArchivo, $fecha, $id);
    $stmt->execute();
    $stmt->close();
}

// Función para agregar un nuevo archivo
function agregarArchivo($nombre, $ruta, $autor, $fecha) {
    global $conn;
    $sql = "INSERT INTO d_campo (nombre, ruta_archivo, autor, fecha) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $ruta, $autor, $fecha);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['eliminar_id'])) {
        eliminarArchivo($_POST['eliminar_id']);
        // Redireccionar para evitar envío de formulario al actualizar la página
        header("Location: DiarioDCampo.php");
        exit();
    } elseif (isset($_POST['editar_id'])) {
        $id = $_POST['editar_id'];
        $nuevoNombre = $_POST['nombre'];
        $fecha = $_POST['fecha'];
    
        // Obtener detalles del archivo antes de editar
        $archivoAnterior = obtenerDetallesArchivo($id);
        $rutaArchivoAnterior = $archivoAnterior['ruta_archivo'];
        $nombreArchivoAnterior = basename($rutaArchivoAnterior);
        $extension = pathinfo($nombreArchivoAnterior, PATHINFO_EXTENSION);
    
        // Generar nuevo nombre de archivo
        $nombreArchivoNuevo = $nuevoNombre . '_' . time() . '.' . $extension;
        $rutaArchivoNuevo = 'uploads/' . $nombreArchivoNuevo;
    
        // Renombrar archivo en la carpeta uploads
        rename($rutaArchivoAnterior, $rutaArchivoNuevo);
    
        // Actualizar nombre en la base de datos
        editarArchivo($id, $nuevoNombre, $fecha, $rutaArchivoNuevo);
    
        // Redireccionar para evitar envío de formulario al actualizar la página
        header("Location: DiarioDCampo.php");
        exit();   
    } elseif (isset($_POST['nombreArchivo'])) {
        $nombre = $_POST['nombreArchivo'];
        $fecha = $_POST['fecha'];
        $autor = $_SESSION['usuario'];
        
        // Manejo del archivo subido
        $archivo = $_FILES['archivo'];
        $nombreOriginal = basename($archivo['name']); // Obtener el nombre original del archivo
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION); // Obtener la extensión del archivo
        $nombreArchivo = $nombre . '_' . time() . '.' . $extension; // Nuevo nombre del archivo
        
        $rutaArchivo = 'uploads/' . $nombreArchivo; // Ruta completa del archivo
        
        // Verificar y mover el archivo subido
        if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
            agregarArchivo($nombre, $rutaArchivo, $autor, $fecha); // Usar el nombre original para la base de datos
            // Alerta de éxito al subir el archivo
            echo '<script>alert("Archivo subido correctamente.");</script>';
            // Redireccionar para evitar reenvío de formulario al actualizar la página
            header("Location: DiarioDCampo.php");
            exit();
        } else {
            // Alerta de error al subir el archivo
            echo '<script>alert("Error al subir el archivo. Inténtelo de nuevo.");</script>';
        }
    }
}

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diario de Campo</title>
    <link rel="stylesheet" href="../src/styles_Contenido.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <div id="diario-wrapper">
        <div class="diario-container">
            <h1>Diario de Campo</h1>
            <div class="diario-content">
                <table class="table">
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
                        <?php foreach ($archivos as $archivo) : ?>
                            <tr>
                                <td><?php echo $archivo['id']; ?></td>
                                <td><?php echo $archivo['nombre']; ?></td>
                                <td><?php echo $archivo['autor']; ?></td>
                                <td><?php echo $archivo['fecha']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalVisualizar<?php echo $archivo['id']; ?>">Visualizar</button>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar<?php echo $archivo['id']; ?>">Editar</button>
                                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacion(<?php echo $archivo['id']; ?>)">Eliminar</button>
                                    <form id="formEliminar<?php echo $archivo['id']; ?>" method="post" style="display: none;">
                                        <input type="hidden" name="eliminar_id" value="<?php echo $archivo['id']; ?>">
                                    </form>
                                </td>
                            </tr>
                            <!-- Modal para visualizar archivo -->
                            <div class="modal fade" id="modalVisualizar<?php echo $archivo['id']; ?>">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <!-- Encabezado del Modal -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">Visualizar Archivo</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <!-- Contenido del Modal -->
                                        <div class="modal-body">
                                            <?php
                                            $extension = pathinfo($archivo['ruta_archivo'], PATHINFO_EXTENSION);
                                            if ($extension === 'pdf') {
                                                echo '<embed src="' . $archivo['ruta_archivo'] . '" type="application/pdf" width="100%" height="500px" />';
                                            } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                                echo '<img src="' . $archivo['ruta_archivo'] . '" alt="' . $archivo['nombre'] . '" style="width: 100%;" />';
                                            } elseif ($extension === 'pptx') {
                                                echo '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($archivo['ruta_archivo']) . '" width="100%" height="500px" frameborder="0"></iframe>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal para editar archivo -->
                            <div class="modal fade" id="modalEditar<?php echo $archivo['id']; ?>">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <!-- Encabezado del Modal -->
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Archivo</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <!-- Contenido del Modal -->
                                        <div class="modal-body">
                                            <form method="post">
                                                <input type="hidden" name="editar_id" value="<?php echo $archivo['id']; ?>">
                                                <div class="form-group">
                                                    <label for="nombreArchivo">Nombre del archivo:</label>
                                                    <input type="text" class="form-control" id="nombreArchivo" name="nombre" value="<?php echo $archivo['nombre']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="fecha">Fecha:</label>
                                                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $archivo['fecha']; ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Botón para agregar nuevo diario de campo -->
    <button id="add-diario-button" class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarDiario">Agregar Nuevo Diario de Campo</button>

    <!-- Modal para agregar nuevo diario de campo -->
    <div class="modal fade" id="modalAgregarDiario">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nuevo Diario de Campo</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Contenido del Modal -->
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nombreArchivo">Nombre del archivo:</label>
                            <input type="text" class="form-control" id="nombreArchivo" name="nombreArchivo" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="form-group">
                            <label for="archivo">Seleccionar archivo, Máximo de 2mb:</label>
                            <input type="file" class="form-control-file" id="archivo" name="archivo" accept=".pdf, .jpg, .jpeg, .png, .pptx" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Subir Archivo</button>
                    </form>
                </div>
            </div>
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

        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Está seguro de que desea eliminar este archivo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formEliminar' + id).submit();
                }
            });
        }
        
    </script>
    <script src="../src/scripts_Contenido.js"></script>
</body>
</html>
