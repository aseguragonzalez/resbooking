<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class DiscountOn{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public $Service = 0;

    /**
     * Porcentaje de descuento
     * @var int
     */
    public $Value = 0;

    /**
     * Valor mínimo aplicable
     * @var int
     */
    public $Min = 0;

    /**
     * Valor máximo aplicable
     * @var int
     */
    public $Max = 0;

    /**
     * Fecha de inicio del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de fin del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $End = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
