<?php
class Imagen {
    private $errores = [];

    public function getErrores() {
        return $this->errores;
    }

    // Validar imagen (tipo, tamaño)
    public function validar($imagen, $tamanioMaximo = 2097152) {
        $this->errores = [];

        if (!isset($imagen['tmp_name']) || $imagen['error'] !== UPLOAD_ERR_OK) {
            $this->errores[] = 'Error al subir la imagen.';
            return false;
        }

        $info = getimagesize($imagen['tmp_name']);
        if ($info === false) {
            $this->errores[] = 'El archivo no es una imagen válida.';
            return false;
        }

        $tipoMime = $info['mime'];
        if (!in_array($tipoMime, ['image/jpeg', 'image/png'])) {
            $this->errores[] = 'Solo se permiten imágenes JPG o PNG.';
            return false;
        }

        if ($imagen['size'] > $tamanioMaximo) {
            $this->errores[] = 'La imagen excede el tamaño máximo permitido (2 MB).';
            return false;
        }

        return true;
    }

    // Guardar imagen original
    public function guardarOriginal($imagen, $rutaDestino) {
        return move_uploaded_file($imagen['tmp_name'], $rutaDestino);
    }

    // Crear miniatura/redimensionada
    public function crearMiniatura($rutaOriginal, $rutaThumb, $anchoFinal = 300, $altoFinal = 200) {
        $info = getimagesize($rutaOriginal);
        $ancho = $info[0];
        $alto = $info[1];
        $tipoMime = $info['mime'];

        // Crear imagen desde el archivo original
        switch ($tipoMime) {
            case 'image/jpeg':
                $imagenOrigen = imagecreatefromjpeg($rutaOriginal);
                break;
            case 'image/png':
                $imagenOrigen = imagecreatefrompng($rutaOriginal);
                break;
            default:
                $this->errores[] = 'Tipo de imagen no soportado para miniatura.';
                return false;
        }

        // Crear imagen redimensionada
        $thumb = imagecreatetruecolor($anchoFinal, $altoFinal);
        imagecopyresampled($thumb, $imagenOrigen, 0, 0, 0, 0, $anchoFinal, $altoFinal, $ancho, $alto);

        // Guardar miniatura
        switch ($tipoMime) {
            case 'image/jpeg':
                imagejpeg($thumb, $rutaThumb, 85);
                break;
            case 'image/png':
                imagepng($thumb, $rutaThumb, 8);
                break;
        }

        imagedestroy($thumb);
        imagedestroy($imagenOrigen);

        return true;
    }
}
