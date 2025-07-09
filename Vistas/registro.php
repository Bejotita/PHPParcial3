<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/registro.css">
</head>
<body>
<div class="registro-container">
    <h2>Registrar nuevo usuario</h2>
    <form action="../Procesos/usuario_crud.php" method="post" onsubmit="return validarRegistro();" autocomplete="off">
        <input type="hidden" name="accion" value="crear">

        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" id="clave" name="clave" required><br><br>

        <label>Confirmar contraseña:</label><br>
        <input type="password" id="clave2" name="clave2" required><br><br>

        <button type="submit">Registrar</button>
        <p style="text-align: center; margin-top: 10px;">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>

    </form>
</div>

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
