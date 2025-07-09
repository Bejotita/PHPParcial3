<?php
require_once 'Conexion.php';

class Usuario {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::getInstancia()->getConexion();
    }

    /**
     * Verifica si un usuario ya existe (para evitar duplicados)
     */
    public function existeUsuario($usuario, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = ? AND id <> ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuario, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuario]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Registrar un nuevo usuario, retorna true o mensaje de error
     */
    public function registrar($usuario, $clave, $rol = 'editor') {
        if (empty($usuario) || empty($clave)) {
            return "Usuario o clave vacíos";
        }

        if ($this->existeUsuario($usuario)) {
            return "El nombre de usuario ya existe";
        }

        $hashClave = password_hash($clave, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (usuario, clave, rol, activo) VALUES (?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$usuario, $hashClave, $rol])) {
            return true;
        }
        return "Error al registrar usuario";
    }

    /**
     * Autenticar usuario activo
     */
    public function autenticar($usuario, $clave) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ? AND activo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $datosUsuario = $stmt->fetch();

        if ($datosUsuario && password_verify($clave, $datosUsuario['clave'])) {
            return $datosUsuario;
        }
        return false;
    }

    /**
     * Listar todos los usuarios (activos e inactivos)
     */
    public function listarTodos() {
        $sql = "SELECT id, usuario, rol, activo FROM usuarios";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Actualizar usuario (nombre y rol), evitando duplicados
     */
    public function actualizar($id, $usuario, $rol) {
        if ($this->existeUsuario($usuario, $id)) {
            return "El nombre de usuario ya existe";
        }

        $sql = "UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuario, $rol, $id]);
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function cambiarClave($id, $nuevaClave) {
        $claveHash = password_hash($nuevaClave, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$claveHash, $id]);
    }

    /**
     * Desactivar usuario (activo = 0)
     */
    public function desactivar($id) {
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Activar usuario (activo = 1)
     */
    public function activar($id) {
        $sql = "UPDATE usuarios SET activo = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>
