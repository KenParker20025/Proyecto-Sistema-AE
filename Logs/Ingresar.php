<?php
session_start(); // Iniciar sesión para mantener el estado del usuario

// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener las credenciales de inicio de sesión
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    
    // Conectar a la base de datos (ajusta los detalles según tu configuración)
    require '../ConexionBD/db.php'; // Usar la conexión centralizada

    // Preparar la consulta SQL según el tipo de usuario
    if (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] === 'profesor') {
        $sql = "SELECT * FROM registro WHERE usuario = ? AND contraseña = ?";
    } elseif (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] === 'director') {
        $sql = "SELECT * FROM directores WHERE usuario = ? AND contraseña = ?";
    }
    
    // Verificar si se definió correctamente $sql
    if (isset($sql) && !empty($sql)) {
        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $contraseña);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Verificar si se encontró un usuario con las credenciales proporcionadas
        if ($result->num_rows > 0) {
            // El usuario y la contraseña son correctos, iniciar sesión y redireccionar
            $usuario_data = $result->fetch_assoc();
            $_SESSION['tipo_usuario'] = $_POST['tipo_usuario']; // Guardar el tipo de usuario en la sesión
            $_SESSION['nombre_usuario'] = $usuario_data['nombre']; // Guardar el nombre del usuario en la sesión
            $_SESSION['usuario'] = $usuario_data['usuario']; // Guardar el apodo del usuario en la sesión
            $_SESSION['id_usuario'] = $usuario_data['id']; // Guardar el ID del usuario en la sesión

            
            if ($_POST['tipo_usuario'] === 'profesor') {
                header('Location: ../Contenido/inicioD.php');
            } else {
                header('Location: ../Admin/Admin.php');
            }
            exit();
        } else {
            // Las credenciales son incorrectas, mostrar un mensaje de error
            echo '<script>alert("Usuario o contraseña incorrectos. Por favor, inténtelo de nuevo.");</script>';
        }
    } else {
        // La variable $sql no está definida o está vacía, mostrar un mensaje de error
        echo '<script>alert("Error interno: consulta SQL no válida.");</script>';
    }
    
    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../src/styles.css">
        
</head>
<body>
    <div class="login-container">
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'profesor')">Soy Docente</button>
            <button class="tablinks" onclick="openTab(event, 'director')">Soy Director</button>
        </div>

        <div id="profesor" class="tab-content">
            <img src="../img/UsProfesor.png" alt="Imagen de Profesor" class="profile-image"> <!-- Imagen de Profesor dentro de la pestaña -->
            <form action="" method="post">
                <input type="hidden" name="tipo_usuario" value="profesor"> <!-- Campo oculto que indica el tipo de usuario -->
                <div class="form-group">
                    <label for="usuarioProfesor">Ingresar Usuario:</label>
                    <input type="text" id="usuarioProfesor" name="usuario" placeholder="Usuario">
                </div>
                <div class="form-group">
                    <label for="contraseñaProfesor">Ingresar Contraseña:</label>
                    <input type="password" id="contraseñaProfesor" name="contraseña" placeholder="Contraseña">
                </div>
                <div class="form-group">
                    <button type="submit">Ingresar</button>
                </div>
            </form>
        </div>

        <div id="director" class="tab-content">
            <img src="../img/UsDirector.png" alt="Imagen de Director" class="profile-image"> <!-- Imagen de Director dentro de la pestaña -->
            <form action="" method="post">
                <input type="hidden" name="tipo_usuario" value="director"> <!-- Campo oculto que indica el tipo de usuario -->
                <div class="form-group">
                    <label for="usuarioDirector">Ingresar Usuario:</label>
                    <input type="text" id="usuarioDirector" name="usuario" placeholder="Usuario">
                </div>
                <div class="form-group">
                    <label for="contraseñaDirector">Ingresar Contraseña:</label>
                    <input type="password" id="contraseñaDirector" name="contraseña" placeholder="Contraseña">
                </div>
                <div class="form-group">
                    <button type="submit">Ingresar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tablinks;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>
