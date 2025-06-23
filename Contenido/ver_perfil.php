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
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../src/styles_Contenido.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Estilos del modal */
        .modal {
            display: none; /* Ocultar el modal por defecto */
            position: fixed;
            z-index: 1; /* Ubicar el modal encima de todo */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Permitir desplazamiento si el contenido es demasiado grande */
            background-color: rgb(0, 0, 0); /* Fondo negro semitransparente */
            background-color: rgba(0, 0, 0, 0.4); /* Fondo negro semitransparente */
        }

        /* Estilo para el modal de editar perfil */
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            max-width: 400px; /* Anchura máxima para mantenerlo compacto */
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type=text], input[type=password], input[type=file] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-save {
            background-color: #4CAF50;
            color: white;
            padding: 12px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background-color: #45a049;
        }

        /* Estilos para cerrar el modal */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Estilos para el formulario */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
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
            <button onclick="cerrarSesion()" class="salir-button">Cerrar Sesión</button>
        </div>
    </div>
</div>

<div class="profile-container">
    <div class="profile-info">
    <img src="perfil/<?php echo htmlspecialchars($docente['foto_perfil'] ? $docente['foto_perfil'] : 'profile_placeholder.png'); ?>" alt="Foto de Perfil" id="profile-image">
        <h1><?php echo htmlspecialchars($docente['nombre'] . ' ' . $docente['apellidoPaterno'] . ' ' . $docente['apellidoMaterno']); ?></h1><br>
        <div><strong>Usuario:</strong> <?php echo htmlspecialchars($docente['usuario']); ?></div>
        <div><strong>Correo:</strong> <?php echo htmlspecialchars($docente['correo']); ?></div>
        <div><strong>Grupo Asignado:</strong> <?php echo htmlspecialchars($docente['grado'] . ' - ' . $docente['grupo'] . ' - ' . $docente['turno']); ?></div>
    </div>
    <div class="button-container">
        <button type="button" class="btn btn-success" onclick="showModal()">Editar</button>
    </div>
</div>

<!-- Modal para editar perfil -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Editar Perfil</h2>
        <form action="actualizar_perfil.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="new-username">Nuevo Usuario:</label>
                <input type="text" id="new-username" name="new-username" required>
            </div>
            <div class="form-group">
                <label for="current-clave">Clave Usada (Al registrarse):</label>
                <input type="password" id="current-clave" name="current-clave" required>
            </div>
            <div class="form-group">
                <label for="current-password">Contraseña Actual:</label>
                <input type="password" id="current-password" name="current-password" required>
            </div>
            <div class="form-group">
                <label for="new-password">Nueva Contraseña:</label>
                <input type="password" id="new-password" name="new-password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <div class="form-group">
                <label for="new-profile-image">Nueva Foto de Perfil:</label>
                <input type="file" id="new-profile-image" name="new-profile-image">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-save">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleUserMenu() {
        const userMenu = document.getElementById('user-menu');
        userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
    }

    function showModal() {
        const modal = document.getElementById('editModal');
        modal.style.display = 'block';
    }

    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.style.display = 'none';
    }

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
</script>
</body>
</html>
