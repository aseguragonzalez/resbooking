<?php

/**
 * Genera la excepción correspondiente al error detectado
 * @param int $error Código de error
 * @throws RuntimeException Excepción generada
 */
function throw_error($error = 0){
    switch ($error) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.', 2);
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.', 3);
        default:
            throw new RuntimeException('Unknown errors.', 4);
    }
}

/**
 * Proceso de subida de los ficheros
 * @param string $name nombre del fichero
 * @param string $ext extensión del fichero
 * @return int Código
 * @throws RuntimeException
 */
function upload_file($name = "", $ext =""){
    if (!isset($_FILES[$name]['error'])
            || is_array($_FILES[$name]['error'])){
        throw new RuntimeException('Invalid parameters.', 1);
    }

    throw_error($_FILES[$name]['error']);

    if ($_FILES[$name]['size'] > 1000000) {
        throw new RuntimeException('Exceeded filesize limit.', 5);
    }

    if (!move_uploaded_file($_FILES[$name]['tmp_name'],
            sprintf('./input/%s.%s', $name,$ext))){
        throw new RuntimeException('Failed to move uploaded file.' , 6);
    }

    return 0;
}
