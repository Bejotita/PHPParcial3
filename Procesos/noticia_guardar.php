<?php
session_start();
require_once '../clases/Validaciones.php';
require_once '../clases/Imagen.php';
require_once '../clases/Noticia.php';

// Asegurar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header('Location: ../vistas/login.php');
    exit();
}

$errores = [];

// Validar datos recibidos
$titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
$contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
$foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;

if (!Validaciones::requerido($titulo)) {
    $errores[] = "El título es obligatorio.";
}
if (!Validaciones::requerido($contenido)) {
    $errores[] = "El contenido es obligatorio.";
}
if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
    $errores[] = "Debe seleccionar una imagen.";
}

$imagen = new Imagen();

if ($foto && !$imagen->validar($foto)) {
    $errores = array_merge($errores, $imagen->getErrores());
}

// Si hay errores, regresar al formulario
if (!empty($errores)) {
    $_SESSION['errores_noticia'] = $errores;
    header('Location: ../vistas/formulario_noticia.php');
    exit();
}

// Generar nombre único
$ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
$nombreArchivo = uniqid('img_') . '.' . $ext;

$rutaOriginal = "../imagenes/originales/" . $nombreArchivo;
$rutaThumb = "../imagenes/thumbs/" . $nombreArchivo;

// Crear carpetas si no existen
if (!is_dir("../imagenes/originales")) mkdir("../imagenes/originales", 0755, true);
if (!is_dir("../imagenes/thumbs")) mkdir("../imagenes/thumbs", 0755, true);

// Guardar imagen y miniatura
$imagen->guardarOriginal($foto, $rutaOriginal);
$imagen->crearMiniatura($rutaOriginal, $rutaThumb, 300, 200);

// Guardar en base de datos
$noticia = new Noticia();
$noticia->crear(
    Validaciones::limpiar($titulo),
    Validaciones::limpiar($contenido),
    "imagenes/originales/" . $nombreArchivo,
    "imagenes/thumbs/" . $nombreArchivo,
    $_SESSION['id_usuario']
);

// Redirigir al inicio
header('Location: ../index.php');
exit();
