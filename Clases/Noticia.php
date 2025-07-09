<?php
require_once 'Conexion.php';

class Noticia {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::getInstancia()->getConexion();
    }

    // Crear una nueva noticia
    public function crear($titulo, $contenido, $rutaImagen, $rutaThumb, $idUsuario) {
        $sql = "INSERT INTO noticias (titulo, contenido, ruta_imagen, ruta_thumb, fecha, id_usuario, activa)
                VALUES (:titulo, :contenido, :ruta_imagen, :ruta_thumb, NOW(), :id_usuario, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titulo'      => $titulo,
            ':contenido'   => $contenido,
            ':ruta_imagen' => $rutaImagen,
            ':ruta_thumb'  => $rutaThumb,
            ':id_usuario'  => $idUsuario
        ]);
    }

    // Listar todas las noticias activas
    public function listarTodas() {
        $sql = "SELECT n.*, u.usuario 
                FROM noticias n
                INNER JOIN usuarios u ON n.id_usuario = u.id
                WHERE n.activa = 1
                ORDER BY n.fecha DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una noticia por su ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM noticias WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Eliminar (desactivar) una noticia
    public function eliminar($id) {
        $sql = "UPDATE noticias SET activa = 0 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Editar una noticia (sin modificar imágenes)
    public function actualizar($id, $titulo, $contenido) {
        $sql = "UPDATE noticias SET titulo = ?, contenido = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titulo, $contenido, $id]);
    }

    // Contar cuántas noticias activas hay
    public function contarActivas() {
        $sql = "SELECT COUNT(*) as total FROM noticias WHERE activa = 1";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function actualizarConImagen($id, $titulo, $contenido, $rutaImagen, $rutaThumb) {
        $sql = "UPDATE noticias 
            SET titulo = ?, contenido = ?, ruta_imagen = ?, ruta_thumb = ?
            WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titulo, $contenido, $rutaImagen, $rutaThumb, $id]);
    }

}
