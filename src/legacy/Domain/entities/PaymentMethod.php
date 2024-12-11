<?php

declare(strict_types=1);

/**
 * Medios o formas de pago.
 */
class PaymentMethod{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado al medio de pago
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del medio de mago
     * @var string
     */
    public $Description = "";

    /**
     * Abreviatura del nombre asignado
     * @var string
     */
    public $ShortName = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
