<?php
session_start();
require_once '../clases/Usuario.php';
require_once '../clases/Validaciones.php';

$errores = [];

// Verificar que se haya enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $clave   = isset($_POST['clave']) ? $_POST['clave'] : '';

    // Validaciones básicas
    if (!Validaciones::requerido($usuario)) {
        $errores[] = "El campo usuario es obligatorio.";
    }

    if (!Validaciones::requerido($clave)) {
        $errores[] = "El campo contraseña es obligatorio.";
    }

    // Si no hay errores, proceder con la autenticación
    if (empty($errores)) {
        $usuarioObj = new Usuario();
        $datos = $usuarioObj->autenticar($usuario, $clave);

        if ($datos) {
            // Autenticación exitosa
            $_SESSION['usuario'] = $datos['usuario'];
            $_SESSION['rol'] = $datos['rol'];
            $_SESSION['id_usuario'] = $datos['id'];

            header("Location: ../index.php");
            exit();
        } else {
            $errores[] = "Usuario o contraseña incorrectos, o cuenta inactiva.";
        }
    }
} else {
    $errores[] = "Acceso no válido.";
}

// Si hubo errores, los guardamos en sesión para mostrarlos
$_SESSION['errores_login'] = $errores;
header("Location: ../vistas/login.php");
exit();
