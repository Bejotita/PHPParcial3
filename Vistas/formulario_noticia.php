<?php
session_start();
require_once '../clases/Noticia.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$noticiaObj = new Noticia();

$titulo = '';
$contenido = '';
$id = null;
$errores = isset($_SESSION['errores_noticia']) ? $_SESSION['errores_noticia'] : [];
unset($_SESSION['errores_noticia']);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $noticia = $noticiaObj->obtenerPorId($id);
    if ($noticia) {
        $titulo = $noticia['titulo'];
        $contenido = $noticia['contenido'];
    } else {
        $_SESSION['error'] = "Noticia no encontrada.";
        header('Location: ../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title><?= $id ? "Editar Noticia" : "Publicar Noticia" ?></title>
    <link rel="stylesheet" href="../css/formulario_noticia.css" />
</head>
<body>

<header class="form-header">
    <h1><?= $id ? "Editar Noticia" : "Publicar Noticia" ?></h1>
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
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>" required>

        <label for="contenido">Contenido:</label>
        <textarea name="contenido" id="contenido" rows="6" required><?= htmlspecialchars($contenido) ?></textarea>

        <label for="foto">Imagen (JPG o PNG, máx. 2MB):</label>
        <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png" <?= $id ? '' : 'required' ?>>

        <div class="form-actions">
            <button type="submit" class="btn-principal"><?= $id ? "Actualizar Noticia" : "Publicar Noticia" ?></button>
            <a href="../index.php" class="btn-secundario">Volver al inicio</a>
        </div>
    </form>

</main>

<script>
    function validarFormulario() {
        const archivo = document.getElementById('foto').files[0];
        // Si es creación (no hay id), la imagen es obligatoria
        if (<?= $id ? 'false' : 'true' ?> && !archivo) {
            alert("Debe seleccionar una imagen.");
            return false;
        }

        if (archivo) {
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
        }

        return true;
    }
</script>

</body>
</html>
