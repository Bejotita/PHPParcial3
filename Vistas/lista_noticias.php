<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../clases/Conexion.php';
require_once '../clases/Noticia.php';

$noticiaObj = new Noticia();
$noticias = $noticiaObj->listarTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Listado de Noticias</title>
    <link rel="stylesheet" href="../css/lista_noticias.css" />
</head>
<body>

<header class="header-container">
    <div class="usuario-info">
        Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)
    </div>
    <nav>
        <a href="formulario_noticia.php" class="btn-crear">Crear Nueva Noticia</a>
        <a href="../logout.php" class="btn-cerrar">Cerrar sesión</a>
    </nav>
</header>

<main>
    <h1 class="titulo-principal">Listado de Noticias</h1>

    <?php if (count($noticias) > 0): ?>
        <table class="tabla-noticias">
            <thead>
            <tr>
                <th>Miniatura</th>
                <th>Título</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($noticias as $noticia): ?>
                <tr>
                    <td class="td-imagen">
                        <?php if (!empty($noticia['ruta_thumb']) && file_exists('../imagenes/thumbs/' . $noticia['ruta_thumb'])): ?>
                            <img src="../imagenes/thumbs/<?= htmlspecialchars($noticia['ruta_thumb']) ?>" alt="Miniatura" class="thumb">
                        <?php else: ?>
                            <span class="sin-imagen">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></td>
                    <td class="td-acciones">
                        <a href="formulario_noticia.php?id=<?= $noticia['id'] ?>" class="btn-editar">Editar</a>
                        <form action="../Procesos/noticia_eliminar.php" method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?');">
                            <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-noticias">No hay noticias registradas.</p>
    <?php endif; ?>

</main>

</body>
</html>
