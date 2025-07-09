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
     * Crea una miniatura redimensionada manteniendo proporciones y transparencia PNG.
     */
    public function crearMiniatura($rutaOriginal, $rutaThumb, $anchoMax = 300, $altoMax = 200) {
        $info = getimagesize($rutaOriginal);
        if (!$info) {
            $this->errores[] = "No se pudo obtener información de la imagen original.";
            return false;
        }

        list($anchoOrig, $altoOrig) = $info;
        $tipoMime = $info['mime'];

        // Calcular proporción para mantener aspecto
        $ratioOrig = $anchoOrig / $altoOrig;
        if ($anchoMax / $altoMax > $ratioOrig) {
            $anchoFinal = (int)($altoMax * $ratioOrig);
            $altoFinal = $altoMax;
        } else {
            $anchoFinal = $anchoMax;
            $altoFinal = (int)($anchoMax / $ratioOrig);
        }

        // Crear recurso imagen original
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

        // Crear lienzo para la miniatura
        $thumb = imagecreatetruecolor($anchoFinal, $altoFinal);

        // Preservar transparencia PNG
        if ($tipoMime === 'image/png') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $colorTransparente = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
            imagefill($thumb, 0, 0, $colorTransparente);
        } else {
            // Fondo blanco para JPEG
            $blanco = imagecolorallocate($thumb, 255, 255, 255);
            imagefill($thumb, 0, 0, $blanco);
        }

        // Redimensionar imagen
        imagecopyresampled(
            $thumb, $imagenOrigen,
            0, 0, 0, 0,
            $anchoFinal, $altoFinal,
            $anchoOrig, $altoOrig
        );

        // Guardar miniatura según tipo
        $guardado = false;
        switch ($tipoMime) {
            case 'image/jpeg':
                $guardado = imagejpeg($thumb, $rutaThumb, 85);
                break;
            case 'image/png':
                $guardado = imagepng($thumb, $rutaThumb, 8);
                break;
        }

        // Liberar recursos
        imagedestroy($thumb);
        imagedestroy($imagenOrigen);

        if (!$guardado) {
            $this->errores[] = "Error al guardar la miniatura.";
            return false;
        }

        return true;
    }
}
?>
