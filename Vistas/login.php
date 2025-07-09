<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: vistas/lista_noticias.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Login - Administración Noticias</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <?php
    if (isset($_SESSION['errores_login'])) {
        foreach ($_SESSION['errores_login'] as $error) {
            echo "<p style='color:red; text-align:center;'>$error</p>";
        }
        unset($_SESSION['errores_login']);
    }
    ?>
    <form action="../Procesos/login_procesar.php" method="post" autocomplete="off">
        <label for="usuario">Usuario:</label><br>
        <input type="text" id="usuario" name="usuario" required autofocus><br><br>

        <label for="clave">Contraseña:</label><br>
        <input type="password" id="clave" name="clave" required><br><br>

        <button type="submit">Entrar</button>
        <p style="text-align:center; margin-top: 10px;">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>

    </form>
</div>
</body>
</html>
