<?php

/**
 * Model para los formularios públicos
 */
class HomeModel extends \CoreModel{

    /**
     * @ignore
     * Parámetro para activar el menú
     * @var String
     */
    public $MenuActivo = "Home";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

}
