<?php

declare(strict_types=1);

/**
 * Excepción para el control de selección de proyecto. Se utiliza para identificar
 * cuando se accede a un recurso sin que exista un proyecto activo.
 *
 * @author manager
 */
class ProjectException extends \Exception{

    public function __construct($message, $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}
