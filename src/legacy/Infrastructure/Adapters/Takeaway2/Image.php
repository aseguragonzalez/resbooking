<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Image{

    /**
     * Identidad de la imagen
     * @var int Id
     */
    public $Id = 0;

    /**
     * Identidad del producto padre
     * @var int Id  del producto al que está asociado
     */
    public $Product = 0;

    /**
     * Nombre asignado a la imagen
     * @var string Nombre de producto
     */
    public $Name = "";

    /**
     * Descripción de la imagen
     * @var string Descripción
     */
    public $Description = "";

    /**
     * Ruta de acceso al fichero de imagen
     * @var string Ruta física
     */
    public $Path = "";

    /**
     * Fecha asociada a la imagen
     * @var string Fecha de imagen
     */
    public $Date = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
