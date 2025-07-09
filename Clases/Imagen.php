<?php
class Imagen {
    private $errores = [];

    public function getErrores() {
        return $this->errores;
    }

    /**
     * Valida que la imagen sea JPG o PNG y no supere el tamaño permitido.
     */
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

    /**
     * Guarda la imagen original en el destino indicado.
     */
    public function guardarOriginal($imagen, $rutaDestino) {
        return move_uploaded_file($imagen['tmp_name'], $rutaDestino);
    }

    /**
     * Crea una miniatura redimensionada, manteniendo proporciones
     * y transparencia en caso de PNG.
     */
    public function crearMiniatura($rutaOriginal, $rutaThumb, $anchoFinal = 300, $altoFinal = 200) {
        $info = getimagesize($rutaOriginal);
        if (!$info) {
            $this->errores[] = "No se pudo obtener información de la imagen original.";
            return false;
        }

        list($ancho, $alto) = $info;
        $tipoMime = $info['mime'];

        // Crear recurso de imagen según el tipo
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

        if (!$imagenOrigen) {
            $this->errores[] = "Error al crear la imagen origen.";
            return false;
        }

        // Calcular nueva dimensión proporcional
        $ratio = min($anchoFinal / $ancho, $altoFinal / $alto);
        $nuevoAncho = max(1, (int)($ancho * $ratio));
        $nuevoAlto = max(1, (int)($alto * $ratio));

        $thumb = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        // Preservar transparencia si es PNG
        if ($tipoMime == 'image/png') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        imagecopyresampled($thumb, $imagenOrigen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

        // Guardar imagen redimensionada
        $resultado = false;
        switch ($tipoMime) {
            case 'image/jpeg':
                $resultado = imagejpeg($thumb, $rutaThumb, 85);
                break;
            case 'image/png':
                $resultado = imagepng($thumb, $rutaThumb, 8);
                break;
        }

        // Liberar recursos
        imagedestroy($thumb);
        imagedestroy($imagenOrigen);

        if (!$resultado) {
            $this->errores[] = "Error al guardar la miniatura.";
            return false;
        }

        return true;
    }
}
?>
