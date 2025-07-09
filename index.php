<?php
session_start();

// Verificar si el usuario está logueado
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
    <style>
        /* Estilos básicos para mostrar miniaturas */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
        }
        .logout {
            text-align: right;
            margin-bottom: 10px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .noticia {
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            box-sizing: border-box;
            text-align: center;
        }
        .noticia img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }
        .noticia-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .noticia-date {
            font-size: 0.9em;
            color: #666;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="logout">
    Usuario: <?= htmlspecialchars($_SESSION['usuario']) ?> |
    <a href="logout.php">Cerrar sesión</a>
</div>

<h1 class="header">Noticias Recientes</h1>

<?php if (count($noticias) > 0): ?>
    <div class="grid">
        <?php foreach ($noticias as $noticia): ?>
            <div class="noticia">
                <?php if (!empty($noticia['ruta_thumb']) && file_exists('imagenes/thumbs/' . $noticia['ruta_thumb'])): ?>
                    <img src="Imagenes/thumbs/<?= htmlspecialchars($noticia['ruta_thumb']) ?>" alt="Miniatura">
                <?php else: ?>
                    <img src="Imagenes/no-image.png" alt="Sin imagen">
                <?php endif; ?>
                <div class="noticia-title"><?= htmlspecialchars($noticia['titulo']) ?></div>
                <div class="noticia-date"><?= date('d-m-Y H:i', strtotime($noticia['fecha'])) ?></div>
                <a href="Vistas/formulario_noticia.php?id=<?= $noticia['id'] ?>">Ver / Editar</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="text-align:center;">No hay noticias para mostrar.</p>
<?php endif; ?>

</body>
</html>
