<?php

declare(strict_types=1);

/**
 * Description of PaymentMethodDTO
 *
 * @author manager
 */
class PaymentMethodDTO {

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
