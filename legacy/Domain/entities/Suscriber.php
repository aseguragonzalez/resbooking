<?php

declare(strict_types=1);

/**
 * Suscriptor a la lista de noticias de la web
 */
class Suscriber{

    /**
     * Identidad del suscriptor
     * @var int
     */
    public int $id = 0;

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
    public $CreateDate = null;

    /**
     * Estado de la suscripción
     * @var boolean
     */
    public $Active=0;

    /**
     * Fecha de baja del registro
     * @var string
     */
    public $DeleteDate=null;

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
