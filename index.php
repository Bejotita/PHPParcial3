<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: vistas/login.php');
    exit();
}

require_once 'clases/Conexion.php';
require_once 'clases/Noticia.php';

$noticiaObj = new Noticia();
$noticias = $noticiaObj->listarTodas();
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
        Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= $_SESSION['rol'] ?>)
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

    <?php if (count($noticias) > 0): ?>
        <div class="grid">
            <?php foreach ($noticias as $noticia): ?>
                <article class="noticia">
                    <?php if (!empty($noticia['ruta_thumb']) && file_exists('imagenes/thumbs/' . $noticia['ruta_thumb'])): ?>
                        <img src="imagenes/thumbs/<?= htmlspecialchars($noticia['ruta_thumb']) ?>" alt="Miniatura">
                    <?php else: ?>
                        <img src="imagenes/no-image.png" alt="Sin imagen">
                    <?php endif; ?>

                    <h2 class="noticia-title"><?= htmlspecialchars($noticia['titulo']) ?></h2>
                    <time class="noticia-date"><?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></time>

                    <div class="botones-noticia">
                        <a href="vistas/formulario_noticia.php?id=<?= $noticia['id'] ?>" class="btn-ver">Ver / Editar</a>

                        <form action="Procesos/noticia_eliminar.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?');" class="form-eliminar">
                            <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
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
