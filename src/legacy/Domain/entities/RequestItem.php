<?php

declare(strict_types=1);

/**
 * Registro de producto en una solicitud de información
 */
class RequestItem{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id=0;

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Request=0;

    /**
     * Identidad del producto seleccionado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad solicitada
     * @var int
     */
    public $Count=0;

    /**
     * Observaciones/Opciones del producto
     * @var string
     */
    public $Data = "";
}
