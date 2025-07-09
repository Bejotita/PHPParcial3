<?php
session_start();
require_once '../clases/Conexion.php';
require_once '../clases/Noticia.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de noticia inválido.";
    header('Location: ../index.php');
    exit();
}

$id = intval($_GET['id']);
$noticiaObj = new Noticia();
$noticia = $noticiaObj->obtenerPorId($id);

if (!$noticia) {
    $_SESSION['error'] = "Noticia no encontrada.";
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="../css/ver_noticia.css" />
</head>
<body>

<main class="noticia-completa">
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <p><em>Publicado el <?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></em></p>

    <?php if (!empty($noticia['ruta_imagen']) && file_exists('../' . $noticia['ruta_imagen'])): ?>
        <img src="../<?= htmlspecialchars($noticia['ruta_imagen']) ?>" alt="Imagen noticia" style="max-width:100%;height:auto;">
    <?php endif; ?>

    <div class="contenido-noticia">
        <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>
    </div>

    <p><a href="../index.php">Volver al listado</a></p>
</main>

</body>
</html>
