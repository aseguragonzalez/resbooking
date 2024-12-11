<?php

declare(strict_types=1);

/**
 * Entidad categoría
 */
class Category{

    /**
     * Identidad de la categoría
     * @var int
     */
    public $Id=0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Identidad de la categoría padre si existe
     * @var int
     */
    public $Parent = NULL;

    /**
     * Código asociado a la categoría
     * @var string
     */
    public $Code = "";

    /**
     * Nombre o denominación de la categoría
     * @var string
     */
    public $Name = "";

    /**
     * Descripción informativa de la categoría
     * @var string
     */
    public $Description = "";

    /**
     * Definición de los atributos que caracterizan a una categoría
     * @var xml
     */
    public $Xml = "";

    /**
     * Estado lógico de la categoría
     * @var boolean
     */
    public $State = 1;

    /**
     * Link de búsqueda para tener un URL friendly
     * @var string
     */
    public $Link = "";
}
