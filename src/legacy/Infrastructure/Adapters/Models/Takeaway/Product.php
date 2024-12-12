<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Product{

    /**
     * Identidad del producto
     * @var int Id
     */
    public int $id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public int $projectId = 0;

    /**
     * Referencia a la categoría
     * @var int Id de la categoría
     */
    public $Category = 0;

    /**
     * Referencia de catalogación del producto
     * @var string Referencia
     */
    public $Reference = "";

    /**
     * Nompre del producto
     * @var string Nombre
     */
    public string $name = "";

    /**
     * Texto del enlace utilizado al cargar la ficha de producto
     * @var string Url friendly
     */
    public $Link = "";

    /**
     * Descripción del producto utilizada en la ficha
     * @var string Descripción
     */
    public string $description = "";

    /**
     * Terminos clave asociados a caracterizar el producto
     * @var string keywords
     */
    public $Keywords = "";

    /**
     * Precio del producto
     * @var float Precio
     */
    public $Price = 0;

    /**
     * Serialización de los atributos que caracterizan el producto
     * @var string Atributos jSon
     */
    public $Attr = "";

    /**
     * Valoración para la ordenación de los productos
     * @var int Orden
     */
    public $Ord = 0;

    /**
     * Estado lógico del producto
     * @var boolean Estado actual
     */
    public bool $state = true;

    /**
     * Estado de visibilidad del producto en el catálogo
     * @var boolean
     */
    public $Visible = 1;
}
