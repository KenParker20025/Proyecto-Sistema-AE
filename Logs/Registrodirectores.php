<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../src/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <div class="registro-container">
    <div class="title-container">
        <h2 class="title">Registro de Directores</h2>
        </div>
        <form action="Procesar_registroDirectores.php" method="post">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre">
            </div>
            <div class="form-group">
                <label for="apellidoPaterno">Apellido Paterno:</label>
                <input type="text" id="apellidoPaterno" name="apellidoPaterno" placeholder="Ingrese su apellido paterno">
            </div>
            <div class="form-group">
                <label for="apellidoMaterno">Apellido Materno:</label>
                <input type="text" id="apellidoMaterno" name="apellidoMaterno" placeholder="Ingrese su apellido materno">
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" placeholder="Ingrese su correo electrónico">
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario">
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" placeholder="Ingrese su contraseña">
            </div>
            <div class="form-group">
                <button type="button" onclick="registrar()">Aceptar</button>
            </div>
        </form>
    </div>
</div> 
    <script>
    function registrar() {
        var nombre = document.getElementById('nombre').value;
        var apellidoPaterno = document.getElementById('apellidoPaterno').value;
        var apellidoMaterno = document.getElementById('apellidoMaterno').value;
        var correo = document.getElementById('correo').value;
        var usuario = document.getElementById('usuario').value;
        var contraseña = document.getElementById('contraseña').value;
        
        // Validar que los campos no estén vacíos
        if (nombre === '' || apellidoPaterno === '' || apellidoMaterno === '' || correo === '' || usuario === '' || contraseña === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, complete todos los campos.'
            });
            return; // Detener la ejecución si hay campos vacíos
        }
        
        // Hacer la petición al servidor y mostrar las alertas de SweetAlert2
        fetch('Procesar_registroDirectores.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'nombre=' + nombre + '&apellidoPaterno=' + apellidoPaterno + '&apellidoMaterno=' + apellidoMaterno + '&correo=' + correo + '&usuario=' + usuario + '&contraseña=' + contraseña
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar mensaje de acuerdo a la respuesta del servidor
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro exitoso!',
                    text: data.message
                });
                // Limpiar los campos del formulario después de un registro exitoso
                document.getElementById('nombre').value = '';
                document.getElementById('apellidoPaterno').value = '';
                document.getElementById('apellidoMaterno').value = '';
                document.getElementById('correo').value = '';
                document.getElementById('usuario').value = '';
                document.getElementById('contraseña').value = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al procesar la solicitud. Por favor, inténtelo de nuevo.'
            });
        });
    }
</script>
</body>
</html>
