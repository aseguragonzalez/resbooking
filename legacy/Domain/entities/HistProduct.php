<?php

declare(strict_types=1);

/**
 * Entidad Histórico. Se utiliza para almacenar cualquier cambio
 * realizado sobre un producto
 */
class HistProduct{

    /**
     * Identidad del historico
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del producto modificado
     * @var int
     */
    public $Product=0;

    /**
     * Serialización JSON del producto
     * @var type
     */
    public $Json="";

    /**
     * Fecha en la que se realiza la modificación
     * @var string
     */
    public $Date=null;

    /**
     * Constructor
     * @param \Product $product Referencia al producto modificado
     */
    public function __construct($product = null){
        if($product != null
                && !is_array($product)
                && is_object($product)){
            $date = new DateTime();
            $this->Product = $product->Id;
            $this->Json = json_encode($product);
            $this->Date = $date->format( 'Y-m-d H:i:s' );
        }
    }
}
