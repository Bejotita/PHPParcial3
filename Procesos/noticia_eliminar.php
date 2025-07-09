<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: vistas/login.php');
    exit();
}

require_once '../clases/Conexion.php';
require_once '../clases/Noticia.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $noticia = new Noticia();

    // Intentar eliminar (desactivar) la noticia
    if ($noticia->eliminar($id)) {
        $_SESSION['mensaje'] = "Noticia eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la noticia.";
    }
} else {
    $_SESSION['error'] = "ID inválido.";
}

// Redirigir a la lista de noticias
header('Location: ../vistas/lista_noticias.php');
exit();
