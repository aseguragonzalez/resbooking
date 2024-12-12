<?php

declare(strict_types=1);

/**
 * DTO resumen con la informacion de un producto
 */
class ProductDTO{

    /**
     * Identidad del producto
     * @var int
     */
    public int $id = 0;

    /**
     * Referencia al producto
     * @var \Product
     */
    public $Product = null;

    /**
     * Coleccion de imagenes asociadas al producto
     * @var array
     */
    public $Gallery = [];

    /**
     *
     * @var type
     */
    public $Likes = [];

    /**
     * Coleccion de registros de actividad
     * @var type
     */
    public $Logs = [];

    /**
     * Coleccion de comentarios asociados al producto
     * @var array
     */
    public $Comments = [];

    /**
     * Constructor de la clase
     * @param int Identidad del producto
     */
    public function __construct($id = 0){
        $this->Id = $id;
    }
}
