<?php
session_start();

require_once '../clases/Validaciones.php';
require_once '../clases/Imagen.php';
require_once '../clases/Noticia.php';

// Verificar que el usuario esté logueado y tenga ID de usuario
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header('Location: ../vistas/login.php');
    exit();
}

$errores = [];

// 1. Obtener y limpiar los datos del formulario
$titulo = trim(isset($_POST['titulo']) ? $_POST['titulo'] : '');
$contenido = trim(isset($_POST['contenido']) ? $_POST['contenido'] : '');
$foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;

// 2. Validaciones básicas
if (!Validaciones::requerido($titulo)) {
    $errores[] = "El título es obligatorio.";
}
if (!Validaciones::requerido($contenido)) {
    $errores[] = "El contenido es obligatorio.";
}
if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
    $errores[] = "Debe seleccionar una imagen válida.";
}

// 3. Validar imagen (tipo, tamaño, etc.)
$imagen = new Imagen();
if ($foto && !$imagen->validar($foto)) {
    $errores = array_merge($errores, $imagen->getErrores());
}

// 4. Si hay errores, volver al formulario con feedback
if (!empty($errores)) {
    $_SESSION['errores_noticia'] = $errores;
    header('Location: ../vistas/formulario_noticia.php');
    exit();
}

// 5. Generar nombre único y rutas
$ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
$nombreArchivo = uniqid('img_') . '.' . strtolower($ext);

$rutaOriginal = "../imagenes/originales/$nombreArchivo";
$rutaThumb = "../imagenes/thumbs/$nombreArchivo";

$rutaBDOriginal = "imagenes/originales/$nombreArchivo";
$rutaBDThumb = "imagenes/thumbs/$nombreArchivo";

// 6. Asegurar que existan las carpetas
if (!is_dir("../imagenes/originales")) mkdir("../imagenes/originales", 0755, true);
if (!is_dir("../imagenes/thumbs")) mkdir("../imagenes/thumbs", 0755, true);

// 7. Subir imagen y crear miniatura
if (
    !$imagen->guardarOriginal($foto, $rutaOriginal) ||
    !$imagen->crearMiniatura($rutaOriginal, $rutaThumb, 300, 200)
) {
    $_SESSION['errores_noticia'] = ["Error al guardar la imagen o generar la miniatura."];
    header('Location: ../vistas/formulario_noticia.php');
    exit();
}

// 8. Guardar noticia en la base de datos
$noticia = new Noticia();
$noticia->crear(
    Validaciones::limpiar($titulo),
    Validaciones::limpiar($contenido),
    $rutaBDOriginal,
    $rutaBDThumb,
    $_SESSION['id_usuario']
);

// 9. Éxito: Redirigir al index
$_SESSION['mensaje'] = "Noticia publicada correctamente.";
header('Location: ../index.php');
exit();
