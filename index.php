<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: vistas/login.php');
    exit();
}

require_once 'clases/Conexion.php';
require_once 'clases/Noticia.php';

$noticiaObj = new Noticia();

// Verificar si se solicita eliminar una noticia desde GET
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    if ($noticiaObj->eliminar($idEliminar)) {
        $_SESSION['mensaje'] = "Noticia eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la noticia.";
    }
    // Redirigir para evitar reenvío
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

$noticias = $noticiaObj->listarTodas();

// Capturar mensajes flash
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Inicio - Noticias</title>
    <link rel="stylesheet" href="css/index.css"> <!-- Enlace al CSS externo -->
</head>
<body>

<header class="header-container">
    <div class="usuario-info">
        Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)
    </div>
    <nav class="botones-derecha">
        <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'editor'): ?>
            <a class="boton" href="Vistas/formulario_noticia.php">Crear Nueva Noticia</a>
        <?php endif; ?>
        <a class="boton" href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="contenido">
    <h1 class="header">Noticias Recientes</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-exito"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (count($noticias) > 0): ?>
        <div class="grid">
            <?php foreach ($noticias as $noticia): ?>
                <article class="noticia">
                    <?php
                    // Corregir file_exists para ruta completa y para la etiqueta img solo usar ruta de BD
                    if (!empty($noticia['ruta_thumb']) && file_exists($noticia['ruta_thumb'])):
                        ?>
                        <img src="<?= htmlspecialchars($noticia['ruta_thumb']) ?>" alt="Miniatura">
                    <?php else: ?>
                        <img src="Imagenes/no-image.png" alt="Sin imagen">
                    <?php endif; ?>

                    <h2 class="noticia-title"><?= htmlspecialchars($noticia['titulo']) ?></h2>
                    <time class="noticia-date"><?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></time>

                    <div class="botones-noticia">
                        <a href="Vistas/ver_noticia.php?id=<?= $noticia['id'] ?>" class="btn-ver">Ver</a>
                        <a href="Vistas/formulario_noticia.php?id=<?= $noticia['id'] ?>" class="btn-ver">Editar</a>
                        <a href="Vistas/lista_noticias.php?eliminar=<?= $noticia['id'] ?>" class="btn-eliminar">Eliminar</a>
                    </div>

                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center;">No hay noticias para mostrar.</p>
    <?php endif; ?>
</main>

</body>
</html>
