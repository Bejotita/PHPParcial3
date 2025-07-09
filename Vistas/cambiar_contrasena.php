<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: vistas/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../css/cambiar_contrasena.css">
</head>
<body>
    <div class="form-container">
        <h2>Cambiar Contraseña</h2>
        <form action="../Procesos/usuario_crud.php" method="POST">
            <input type="hidden" name="accion" value="cambiar_clave">
            <label for="actual">Contraseña actual:</label>
            <input type="password" id="actual" name="actual" required>

            <label for="nueva">Nueva contraseña:</label>
            <input type="password" id="nueva" name="nueva" required>

            <label for="confirmar">Confirmar contraseña:</label>
            <input type="password" id="confirmar" name="confirmar" required>
            <button type="submit">Cambiar contraseña</button>
        </form>
        <a href="../index.php">Volver al inicio</a>
    </div>
    <script>
        function validarRegistro() {
            const clave = document.getElementById('nueva').value;
            const clave2 = document.getElementById('confirmar').value;

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

