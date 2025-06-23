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
    SELECT R.id, R.usuario, R.foto_perfil, G.grado, G.grupo, G.turno
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

// Verificar si se ha enviado el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si la clave ingresada coincide con la clave registrada en la tabla claves
    $claveIngresada = $_POST['current-clave'];
    $claveQuery = "SELECT clave FROM claves WHERE clave = '$claveIngresada' AND usada = 1";
    $claveResult = mysqli_query($conn, $claveQuery);

    if (!$claveResult || mysqli_num_rows($claveResult) == 0) {
        echo "La clave ingresada no coincide con la clave registrada.";
        exit();
    }

    // Actualizar el usuario si se proporciona un nuevo valor
    if (!empty($_POST['new-username'])) {
        $newUsername = $_POST['new-username'];
        $updateUsernameQuery = "UPDATE Registro SET usuario = '$newUsername' WHERE id = $idDocente";
        mysqli_query($conn, $updateUsernameQuery);
    }

    // Actualizar la contraseña si se proporcionan nuevos valores y coinciden
    if (!empty($_POST['new-password']) && !empty($_POST['confirm-password'])) {
        $newPassword = $_POST['new-password'];
        $confirmPassword = $_POST['confirm-password'];
        if ($newPassword === $confirmPassword) {
            $updatePasswordQuery = "UPDATE Registro SET contraseña = '$newPassword' WHERE id = $idDocente";
            mysqli_query($conn, $updatePasswordQuery);
        } else {
            echo "Las contraseñas no coinciden.";
            exit();
        }
    }

    // Procesar la nueva imagen de perfil si se carga
    if (!empty($_FILES['new-profile-image']['name'])) {
        // Eliminar la foto de perfil anterior si existe
        if (!empty($docente['foto_perfil'])) {
            unlink('perfil/' . $docente['foto_perfil']);
        }

        $targetDir = "perfil/";
        $fileName = basename($_FILES["new-profile-image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Permitir solo ciertos formatos de archivo
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Subir archivo al servidor
            if (move_uploaded_file($_FILES["new-profile-image"]["tmp_name"], $targetFilePath)) {
                // Actualizar la ruta de la imagen de perfil en la base de datos
                $updateImageQuery = "UPDATE Registro SET foto_perfil = '$fileName' WHERE id = $idDocente";
                mysqli_query($conn, $updateImageQuery);
            } else {
                echo "Error al subir el archivo.";
                exit();
            }
        } else {
            echo "Solo se permiten archivos de imagen JPG, JPEG, PNG y GIF.";
            exit();
        }
    }

    // Redirigir después de actualizar el perfil
    header("Location: ver_perfil.php");
    exit();
}

mysqli_close($conn);
?>
