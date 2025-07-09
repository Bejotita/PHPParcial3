<?php
session_start();

require_once '../clases/Validaciones.php';
require_once '../clases/Imagen.php';
require_once '../clases/Noticia.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header('Location: ../vistas/login.php');
    exit();
}

$errores = [];

$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$titulo = trim(isset($_POST['titulo']) ? $_POST['titulo'] : '');
$contenido = trim(isset($_POST['contenido']) ? $_POST['contenido'] : '');
$foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;

// Validar campos obligatorios
if (!Validaciones::requerido($titulo)) {
    $errores[] = "El título es obligatorio.";
}
if (!Validaciones::requerido($contenido)) {
    $errores[] = "El contenido es obligatorio.";
}

$imagen = new Imagen();
$actualizarImagen = false; // bandera para saber si actualizamos imagen

// Si no es edición, la imagen es obligatoria
if (!$id) {
    if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debe seleccionar una imagen válida.";
    } else {
        if (!$imagen->validar($foto)) {
            $errores = array_merge($errores, $imagen->getErrores());
        } else {
            $actualizarImagen = true;
        }
    }
} else {
    // Si es edición, la imagen es opcional
    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        if (!$imagen->validar($foto)) {
            $errores = array_merge($errores, $imagen->getErrores());
        } else {
            $actualizarImagen = true;
        }
    }
}

// Si hay errores, regresar al formulario
if (!empty($errores)) {
    $_SESSION['errores_noticia'] = $errores;
    header('Location: ../vistas/formulario_noticia.php' . ($id ? "?id=$id" : ''));
    exit();
}

$noticia = new Noticia();

if ($actualizarImagen) {
    // Guardar imagen
    $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('img_') . '.' . strtolower($ext);

    $rutaOriginal = "../imagenes/originales/$nombreArchivo";
    $rutaThumb = "../imagenes/thumbs/$nombreArchivo";

    $rutaBDOriginal = "imagenes/originales/$nombreArchivo";
    $rutaBDThumb = "imagenes/thumbs/$nombreArchivo";

    if (!is_dir("../imagenes/originales")) mkdir("../imagenes/originales", 0755, true);
    if (!is_dir("../imagenes/thumbs")) mkdir("../imagenes/thumbs", 0755, true);

    if (
        !$imagen->guardarOriginal($foto, $rutaOriginal) ||
        !$imagen->crearMiniatura($rutaOriginal, $rutaThumb, 300, 200)
    ) {
        $_SESSION['errores_noticia'] = ["Error al guardar la imagen o generar la miniatura."];
        header('Location: ../vistas/formulario_noticia.php' . ($id ? "?id=$id" : ''));
        exit();
    }
}

if ($id) {
    // Edición
    if ($actualizarImagen) {
        // Actualizar título, contenido y rutas de imagen
        $resultado = $noticia->actualizarConImagen($id, $titulo, $contenido, $rutaBDOriginal, $rutaBDThumb);
    } else {
        // Actualizar solo título y contenido
        $resultado = $noticia->actualizar($id, $titulo, $contenido);
    }
} else {
    // Crear nuevo registro
    $resultado = $noticia->crear(
        Validaciones::limpiar($titulo),
        Validaciones::limpiar($contenido),
        isset($rutaBDOriginal) ? $rutaBDOriginal : null,
        isset($rutaBDThumb) ? $rutaBDThumb : null,
        $_SESSION['id_usuario']
    );
}

if ($resultado) {
    $_SESSION['mensaje'] = $id ? "Noticia actualizada correctamente." : "Noticia publicada correctamente.";
    header('Location: ../index.php');
    exit();
} else {
    $_SESSION['errores_noticia'] = ["Error al guardar la noticia en la base de datos."];
    header('Location: ../vistas/formulario_noticia.php' . ($id ? "?id=$id" : ''));
    exit();
}
