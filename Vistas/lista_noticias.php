<?php
session_start();

// Verificar si el usuario está logueado
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
    <style>
        /* Estilos básicos solo para presentación */
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px 12px;
            text-align: left;
        }
        img.thumb {
            max-width: 120px;
            height: auto;
        }
        .header {
            text-align: center;
            margin-top: 20px;
        }
        .logout {
            text-align: right;
            margin: 10px 20px;
        }
    </style>
</head>
<body>

<div class="logout">
    Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> |
    <a href="../logout.php">Cerrar sesión</a>
</div>

<h2 class="header">Listado de Noticias</h2>

<table>
    <thead>
    <tr>
        <th>Miniatura</th>
        <th>Título</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($noticias) > 0): ?>
        <?php foreach ($noticias as $noticia): ?>
            <tr>
                <td>
                    <?php if (!empty($noticia['ruta_thumb']) && file_exists('../imagenes/thumbs/' . $noticia['ruta_thumb'])): ?>
                        <img class="thumb" src="../imagenes/thumbs/<?= htmlspecialchars($noticia['ruta_thumb']) ?>" alt="Miniatura">
                    <?php else: ?>
                        Sin imagen
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                <td><?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></td>
                <td>
                    <a href="formulario_noticia.php?id=<?= $noticia['id'] ?>">Editar</a> |
                    <a href="../Procesos/noticia_eliminar.php?id=<?= $noticia['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta noticia?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">No hay noticias registradas.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<p style="text-align:center;">
    <a href="formulario_noticia.php">Crear Nueva Noticia</a>
</p>

</body>
</html>
