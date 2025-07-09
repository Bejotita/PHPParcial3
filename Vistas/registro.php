<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css"> <!-- Asegúrate de que esta ruta sea correcta -->
</head>
<body>
<div class="registro-container">
    <h2>Registro de Usuario</h2>

    <!-- Formulario -->
    <form action="../Procesos/usuario_crud.php" method="post" onsubmit="return validarRegistro();" autocomplete="off">
        <input type="hidden" name="accion" value="crear">

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="clave">Contraseña:</label>
        <input type="password" id="clave" name="clave" required>

        <label for="clave2">Confirmar Contraseña:</label>
        <input type="password" id="clave2" name="clave2" required>

        <button type="submit">Registrar</button>

        <p class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>
    </form>
</div>

<!-- Validación JavaScript -->
<script>
    function validarRegistro() {
        const clave = document.getElementById('clave').value;
        const clave2 = document.getElementById('clave2').value;

        if (clave.length < 8 || !/[A-Za-z]/.test(clave) || !/[0-9]/.test(clave)) {
            alert("La contraseña debe tener al menos 8 caracteres, incluyendo letras y números.");
            return false;
        }

        if (clave !== clave2) {
            alert("Las contraseñas no coinciden.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>
