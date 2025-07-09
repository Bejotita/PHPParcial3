<?php
session_start();
require_once '../clases/Noticia.php';

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $id_usuario = $_SESSION['id_usuario'];

    // Validar datos básicos
    if (empty($titulo) || empty($contenido) || !isset($_FILES['imagen'])) {
        die("Datos incompletos.");
    }

    $imagen = $_FILES['imagen'];

    // Validar imagen
    $permitidos = ['image/jpeg', 'image/png'];
    if (!in_array($imagen['type'], $permitidos)) {
        die("Formato de imagen no permitido.");
    }

    if ($imagen['size'] > 2 * 1024 * 1024) {
        die("La imagen no puede superar los 2MB.");
    }

    // Directorios para guardar imágenes
    $carpeta_imagenes = '../imagenes/';
    $carpeta_thumbs = '../imagenes/thumbs/';

    if (!is_dir($carpeta_imagenes)) mkdir($carpeta_imagenes, 0777, true);
    if (!is_dir($carpeta_thumbs)) mkdir($carpeta_thumbs, 0777, true);

    // Generar nombre único para la imagen
    $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $nombre_imagen = uniqid() . '.' . $ext;
    $ruta_imagen = $carpeta_imagenes . $nombre_imagen;

    // Mover archivo subido
    if (!move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
        die("Error al guardar la imagen.");
    }

    // Crear miniatura proporcional (ejemplo simple)
    $ruta_thumb = $carpeta_thumbs . $nombre_imagen;

    // Función para crear miniatura proporcional
    function crearMiniatura($ruta_origen, $ruta_destino, $ancho_max = 150) {
        list($ancho_orig, $alto_orig, $tipo) = getimagesize($ruta_origen);

        $proporcion = $ancho_max / $ancho_orig;
        $alto_nuevo = $alto_orig * $proporcion;

        $imagen_nueva = imagecreatetruecolor($ancho_max, $alto_nuevo);

        if ($tipo == IMAGETYPE_JPEG) {
            $imagen_origen = imagecreatefromjpeg($ruta_origen);
        } elseif ($tipo == IMAGETYPE_PNG) {
            $imagen_origen = imagecreatefrompng($ruta_origen);
            imagealphablending($imagen_nueva, false);
            imagesavealpha($imagen_nueva, true);
        } else {
            return false;
        }

        imagecopyresampled($imagen_nueva, $imagen_origen, 0, 0, 0, 0, $ancho_max, $alto_nuevo, $ancho_orig, $alto_orig);

        if ($tipo == IMAGETYPE_JPEG) {
            imagejpeg($imagen_nueva, $ruta_destino, 85);
        } elseif ($tipo == IMAGETYPE_PNG) {
            imagepng($imagen_nueva, $ruta_destino);
        }

        imagedestroy($imagen_nueva);
        imagedestroy($imagen_origen);
        return true;
    }

    if (!crearMiniatura($ruta_imagen, $ruta_thumb)) {
        die("Error al crear miniatura.");
    }

    // Guardar noticia en la base de datos
    $noticiaObj = new Noticia();
    if ($noticiaObj->crear($titulo, $contenido, $nombre_imagen, $nombre_imagen, $id_usuario)) {
        header('Location: ../index.php');
        exit();
    } else {
        echo "Error al guardar la noticia.";
    }
} else {
    header('Location: form_noticia.php');
    exit();
}
