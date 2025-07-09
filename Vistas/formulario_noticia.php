<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$titulo = '';
$contenido = '';
$errores = isset($_SESSION['errores_noticia']) ? $_SESSION['errores_noticia'] : [];
unset($_SESSION['errores_noticia']);

// Si quieres cargar datos previos para editar, aquí va el código (opcional)
?>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Publicar Noticia</title>
    <link rel="stylesheet" href="../css/formulario_noticia.css" />
</head>
<body>

<header class="form-header">
    <h1>Publicar Noticia</h1>
</header>

<main class="form-container">

    <?php if (!empty($errores)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="../Procesos/noticia_guardar.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario();">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>" required>

        <label for="contenido">Contenido:</label>
        <textarea name="contenido" id="contenido" rows="6" required><?= htmlspecialchars($contenido) ?></textarea>

        <label for="foto">Imagen (JPG o PNG, máx. 2MB):</label>
        <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png" required>

        <div class="form-actions">
            <button type="submit" class="btn-principal">Publicar Noticia</button>
            <a href="../index.php" class="btn-secundario">Volver al inicio</a>
        </div>
    </form>

</main>

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
