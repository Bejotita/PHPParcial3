<?php
session_start();
require_once '../clases/Usuario.php';
require_once '../clases/Validaciones.php';

// Solo permitir acceso si es admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$usuarioObj = new Usuario();
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$errores = [];
$exito = false;

switch ($accion) {
    case 'crear':
        $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
        $clave   = isset($_POST['clave']) ? $_POST['clave'] : '';
        $rol     = isset($_POST['rol']) ? $_POST['rol'] : 'editor';

        if (!Validaciones::usuarioValido($usuario)) {
            $errores[] = "Usuario inválido.";
        }
        if (!Validaciones::claveSegura($clave)) {
            $errores[] = "Contraseña no segura.";
        }
        if (!Validaciones::rolValido($rol)) {
            $errores[] = "Rol no válido.";
        }

        if (empty($errores)) {
            $exito = $usuarioObj->registrar($usuario, $clave, $rol);
        }
        break;


    case 'actualizar':
        $id      = isset($_POST['id']) ? $_POST['id'] : '';
        $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
        $rol     = isset($_POST['rol']) ? $_POST['rol'] : '';

        if (!Validaciones::usuarioValido($usuario)) {
            $errores[] = "Usuario inválido.";
        }

        if (!Validaciones::rolValido($rol)) {
            $errores[] = "Rol inválido.";
        }

        if (empty($errores)) {
            $exito = $usuarioObj->actualizar($id, $usuario, $rol);
        }
        break;

    case 'cambiar_clave':
        $id    = isset($_POST['id']) ? $_POST['id'] : '';
        $clave = isset($_POST['clave']) ? $_POST['clave'] : '';

        if (!Validaciones::claveSegura($clave)) {
            $errores[] = "La nueva contraseña debe tener mínimo 8 caracteres y contener letras y números.";
        }

        if (empty($errores)) {
            $exito = $usuarioObj->cambiarClave($id, $clave);
        }
        break;

    case 'activar':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $exito = $usuarioObj->activar($id);
        break;

    case 'desactivar':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $exito = $usuarioObj->desactivar($id);
        break;

    default:
        $errores[] = "Acción no válida.";
        break;
}

// Redirigir con resultado
if (!empty($errores)) {
    $_SESSION['errores_usuario'] = $errores;
} elseif ($exito) {
    $_SESSION['mensaje_usuario'] = "Acción '$accion' realizada con éxito.";
} else {
    $_SESSION['errores_usuario'] = ["No se pudo completar la acción '$accion'."];
}

header("Location: ../vistas/gestion_usuarios.php");
exit();
