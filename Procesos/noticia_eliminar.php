<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../vistas/login.php'); // Corregido: ../ para redireccionar bien
    exit();
}

require_once '../clases/Conexion.php';
require_once '../clases/Noticia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    $noticia = new Noticia();

    // Intentar eliminar (desactivar) la noticia
    if ($noticia->eliminar($id)) {
        $_SESSION['mensaje'] = "Noticia eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Ocurrió un error al intentar eliminar la noticia.";
    }
} else {
    $_SESSION['error'] = "ID inválido o acceso no permitido.";
}

// Redirigir a la lista de noticias
header('Location: ../vistas/lista_noticias.php');
exit();
