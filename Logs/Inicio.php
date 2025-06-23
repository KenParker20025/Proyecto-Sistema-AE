<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../src/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-box">
        <img class="login-image" src="../img/ProfesorInicio.png" alt="Ícono de bloqueo"> <!-- Ícono en el botón de iniciar sesión -->
        <div class="login-buttons">
        <h1>Bienvenidos al Sistema AE</h1><br><br>
            <button onclick="window.location.href='Ingresar.php'">
                <img src="../img/iniciosesion.png" alt="Ícono de inicio de sesión"> <!-- Ícono en el botón de iniciar sesión -->
                <span>Iniciar Sesión</span>
            </button>
            <div class="register-buttons">
                <button onclick="window.location.href='Registro.php'">
                    <img src="../img/UsProfesor.png" alt="Ícono de registro"> <!-- Ícono en el botón de registro -->
                    <span>Registrarse (Docentes)</span>
                </button>
                <button onclick="solicitarClave()">
                    <img src="../img/UsDirector.png" alt="Ícono de registro"> <!-- Ícono en el botón de registro -->
                    <span>Registrarse (Directores)</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function solicitarClave() {
            Swal.fire({
                title: 'Ingrese la clave secreta para registrar directores',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: (clave) => {
                    // Enviar la clave al servidor para verificarla
                    return fetch('verificar_clave.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ clave: clave }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Error: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si la clave es correcta, redireccionar al formulario de registro de directores
                    if (result.value.status === 'success') {
                        window.location.href = 'RegistroDirectores.php';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Clave incorrecta. No tiene permiso para registrar directores.',
                        });
                    }
                }
            });
        }
    </script>
</body>
</html>
