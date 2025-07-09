<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../clases/Usuario.php';
require_once '../clases/Validaciones.php';

$usuarioObj = new Usuario();
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$errores = [];
$exito = false;

//file_put_contents(__DIR__ . "/debug.txt", print_r($_POST, true));
switch ($accion) {
    case 'crear':
        $usuario = isset($_POST['usuario']) ? Validaciones::limpiar($_POST['usuario']) : '';
        $clave   = isset($_POST['clave']) ? Validaciones::limpiar($_POST['clave']) : '';
        $rol     = isset($_POST['rol']) ? Validaciones::limpiar($_POST['rol']) : 'editor';

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
        $id      = isset($_POST['id']) ? ($_POST['id']) : '';
        $usuario = isset($_POST['usuario']) ? Validaciones::limpiar($_POST['usuario']) : '';
        $rol     = isset($_POST['rol']) ? Validaciones::limpiar($_POST['rol']) : '';

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
        $id    = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : '';
        $usuario = isset($_SESSION['usuario']) ? Validaciones::limpiar($_SESSION['usuario']) : '';
        $clave_nueva = isset($_POST['nueva']) ? Validaciones::limpiar($_POST['nueva']) : '';
        $clave_actual = isset($_POST['actual']) ? Validaciones::limpiar($_POST['actual']) : '';
        $clave_confirmar = isset($_POST['confirmar']) ? Validaciones::limpiar($_POST['confirmar']) : '';
        $autenticacion = $usuarioObj->autenticar($usuario, $clave_actual);

        if (!$autenticacion) {
            $errores[] = "La contraseña actual es incorrecta.";
        }

        if ($clave_nueva !== $clave_confirmar) {
            $errores[] = "Las contraseñas no coinciden.";
        }

        if (!Validaciones::claveSegura($clave_nueva)) {
            $errores[] = "La nueva contraseña debe tener mínimo 8 caracteres y contener letras y números.";
        }

        if (empty($errores)) {
            $exito = $usuarioObj->cambiarClave($id, $clave_nueva);
        }
        break;

    case 'activar':
        $id = isset($_POST['id_usuario']) ? $_POST['id_usuariox'] : '';
        $exito = $usuarioObj->activar($id);
        break;

    case 'desactivar':
        $id = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : '';
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

header("Location: ../Vistas/login.php");
exit();
