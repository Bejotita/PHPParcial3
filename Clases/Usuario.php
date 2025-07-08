<?php
require_once 'Conexion.php';

class Usuario {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::getInstancia()->getConexion();
    }

    // Crear un nuevo usuario
    public function registrar($usuario, $clave, $rol = 'editor') {
        $claveHash = password_hash($clave, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (usuario, clave, rol, activo) VALUES (?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuario, $claveHash, $rol]);
    }

    // Autenticar usuario
    public function autenticar($usuario, $clave) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ? AND activo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $usuarioEncontrado = $stmt->fetch();

        if ($usuarioEncontrado && password_verify($clave, $usuarioEncontrado['clave'])) {
            return $usuarioEncontrado;
        }
        return false;
    }

    // Obtener todos los usuarios activos/inactivos
    public function listarTodos() {
        $sql = "SELECT id, usuario, rol, activo FROM usuarios";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Actualizar información de un usuario
    public function actualizar($id, $usuario, $rol) {
        $sql = "UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$usuario, $rol, $id]);
    }

    // Cambiar contraseña
    public function cambiarClave($id, $nuevaClave) {
        $claveHash = password_hash($nuevaClave, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$claveHash, $id]);
    }

    // Desactivar usuario (no se elimina)
    public function desactivar($id) {
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Activar usuario
    public function activar($id) {
        $sql = "UPDATE usuarios SET activo = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Obtener un usuario por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
