<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Request{

    /**
     * Identidad del registro de solicitud
     * @var type
     */
    public $Id=0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Ticket generado para la solicitud
     * @var string
     */
    public $Ticket="";

    /**
     * Nombre del solicitante
     * @var string
     */
    public $Name="";

    /**
     * Email de contacto del solicitante
     * @var string
     */
    public $Email="";

    /**
     * Dirección física del solicitante
     * @var string
     */
    public $Address="";

    /**
     * Dirección IP desde donde se realiza la solicitud
     * @var string
     */
    public $IP="";

    /**
     * Fecha en la que se realiza la solicitud
     * @var string
     */
    public $Date=null;

    /**
     * Estado de workflow de la solicitud
     * @var int?
     */
    public $WorkFlow=null;

    /**
     * Estado lógico de la solicitud
     * @var boolean
     */
    public $State=1;

    /**
     * Referencia al descuento asociado
     * @var int?
     */
    public $Discount = NULL;

    /**
     * Referencia al método de entrega seleccionado
     * @var int
     */
    public $DeliveryMethod = 0;

    /**
     * Referencia al método de pago seleccionado
     * @var int
     */
    public $PaymentMethod = 0;

    /**
     * Fecha de entrega seleccionada
     * @var Fecha de entrega seleccionada
     */
    public $DeliveryDate = NULL;

    /**
     * Referencia a la hora de entrega seleccionada
     * @var int
     */
    public $DeliveryTime = 0;

    /**
     * Flag sobre la política de publicidad
     * @var bool
     */
    public $Advertising = FALSE;

    /**
     * Teléfono de contacto
     * @var string
     */
    public $Phone = "";

    /**
     * Código postal
     * @var string
     */
    public $PostCode = "";

    /**
     * Importe del pedido
     * @var float
     */
    public $Amount = 0;

    /**
     * Importe total aplicado el descuento(si procede)
     * @var float
     */
    public $Total = 0;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new \DateTime();
        $this->Date = $date->format( 'Y-m-d H:i:s' );
    }
}
