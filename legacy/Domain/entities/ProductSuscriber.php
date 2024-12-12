<?php

declare(strict_types=1);

/**
 * Registro de suscripción de usuario a las noticias
 * de un producto
 */
class ProductSuscriber{

     /**
     * Identidad del registro de suscripción
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del producto al que se asocia el usuario
     * @var int
     */
    public $Product=0;

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
     * Dirección IP desde donde se genera la suscripción
     * @var type
     */
    public $IP="";

    /**
     * Fecha en la que se genera la suscripción
     * @var string
     */
    public $CreateDate = null;

    /**
     * Fecha en la que se solicita la baja
     * @var string
     */
    public $DeleteDate = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime();
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
        $this->DeleteDate = $date->format( 'Y-m-d H:i:s' );
    }

}
