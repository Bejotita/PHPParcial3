<?php
class Validaciones {
    // Verifica que un campo no esté vacío
    public static function requerido($campo) {
        return isset($campo) && trim($campo) !== '';
    }

    // Valida la longitud mínima y máxima
    public static function longitud($campo, $min = 1, $max = 255) {
        $long = strlen(trim($campo));
        return $long >= $min && $long <= $max;
    }

    // Valida usuario alfanumérico (sin espacios ni caracteres raros)
    public static function usuarioValido($usuario) {
        return preg_match('/^[a-zA-Z0-9_]{4,20}$/', $usuario);
    }

    // Valida contraseñas (mínimo 8, con letras y números)
    public static function claveSegura($clave) {
        return preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/', $clave);
    }

    // Valida roles válidos (admin/editor)
    public static function rolValido($rol) {
        return in_array($rol, ['admin', 'editor']);
    }

    // Sanitiza texto plano (opcional)
    public static function limpiar($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }
}
