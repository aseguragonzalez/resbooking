<?php

declare(strict_types=1);

/**
 * Modelo para la gestión de clientes
 *
 * @author manager
 */
class SuscribersModel extends \SaasModel{

    /**
     * Pestaña del menú activo
     * @var type
     */
    public $Activo = "Clientes";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
       parent::__construct();
    }

    public function GetSuscribers(){

    }

    public function Delete($id = 0){

    }
}
