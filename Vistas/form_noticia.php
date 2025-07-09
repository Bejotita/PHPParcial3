<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Crear Noticia</title>
    <link rel="stylesheet" href="../css/form_noticia.css" />
</head>
<body>
<h2>Crear nueva noticia</h2>
<form action="../Procesos/procesar_noticia.php" method="post" enctype="multipart/form-data">
    <label>Título:</label><br>
    <input type="text" name="titulo" required><br><br>

    <label>Contenido:</label><br>
    <textarea name="contenido" rows="5" required></textarea><br><br>

    <label>Imagen:</label><br>
    <input type="file" name="imagen" accept="image/png, image/jpeg" required><br><br>

    <button type="submit">Guardar Noticia</button>
</form>
</body>
</html>
