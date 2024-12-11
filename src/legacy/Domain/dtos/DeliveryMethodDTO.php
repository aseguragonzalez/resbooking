<?php

declare(strict_types=1);

/**
 * Description of DeliveryMethodDTO
 *
 * @author manager
 */
class DeliveryMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del método de entrega
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del método de entrega
     * @var string
     */
    public $Description = "";

    /**
     * Términos generales (opcional)
     * @var string
     */
    public $Terms = "";

    /**
     * Nombre del icono a utilizar(si procede)
     * @var type
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio actual
     * @var int
     */
    public $Service = 0;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}
