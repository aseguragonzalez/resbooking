<?php

declare(strict_types=1);

/**
 * DTO para la información de solicitudes
 */
class RequestDTO{

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia a la solicitud
     * @var \Request
     */
    public $Request = null;

    /**
     * Colección de productos asociados a la solicitud
     * @var array
     */
    public $Items = [];

}
