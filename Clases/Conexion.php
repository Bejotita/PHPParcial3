<?php
class Conexion {
    private static $instancia = null;
    private $pdo;

    private $host = 'localhost';
    private $db = 'sistema_noticias';
    private $usuario = 'root';
    private $clave = '';

    // Constructor privado
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8",
                $this->usuario,
                $this->clave,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Obtener la instancia única
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    // Obtener la conexión PDO
    public function getConexion() {
        return $this->pdo;
    }
}
?>
