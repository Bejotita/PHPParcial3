<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Si deseas usarlo también para editar, podrías cargar valores aquí
$titulo = '';
$contenido = '';
$errores = isset($_SESSION['errores_noticia']) ? $_SESSION['errores_noticia'] : [];
unset($_SESSION['errores_noticia']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Noticia</title>
</head>
<body>
<h2>Publicar Noticia</h2>

<?php if (!empty($errores)): ?>
    <div style="color: red;">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="../Procesos/noticia_guardar.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario();">
    <label for="titulo">Título:</label><br>
    <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>" required><br><br>

    <label for="contenido">Contenido:</label><br>
    <textarea name="contenido" id="contenido" rows="6" required><?= htmlspecialchars($contenido) ?></textarea><br><br>

    <label for="foto">Imagen (JPG o PNG, máx. 2MB):</label><br>
    <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png" required><br><br>

    <input type="submit" value="Publicar Noticia">
</form>

<br>
<a href="../index.php">Volver al inicio</a>

<script>
    function validarFormulario() {
        const archivo = document.getElementById('foto').files[0];
        if (!archivo) {
            alert("Debe seleccionar una imagen.");
            return false;
        }

        const tipo = archivo.type;
        const tamano = archivo.size;

        if (!['image/jpeg', 'image/png'].includes(tipo)) {
            alert("Formato de imagen no permitido. Solo JPG o PNG.");
            return false;
        }

        if (tamano > 2 * 1024 * 1024) {
            alert("La imagen no puede superar los 2MB.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>
