<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Suscriber{

    /**
     * Identidad del suscriptor
     * @var int
     */
    public $Id=0;

    /**
     * Nombre del suscriptor
     * @var string
     */
    public $SuscriberName="";

    /**
     * Dirección de email del suscriptor
     * @var string
     */
    public $Email="";

    /**
     * Dirección IP desde donde se genera el registro
     * @var string
     */
    public $IP="";

    /**
     * Fecha de creación del registro
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Estado de la suscripción
     * @var boolean
     */
    public $Active=0;

    /**
     * Fecha de baja del registro
     * @var string
     */
    public $DeleteDate=NULL;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime("NOW");
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
    }

}
