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
    public int $id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad de la categoría padre si existe
     * @var int
     */
    public $Parent = null;

    /**
     * Código asociado a la categoría
     * @var string
     */
    public string $code = "";

    /**
     * Nombre o denominación de la categoría
     * @var string
     */
    public string $name = "";

    /**
     * Descripción informativa de la categoría
     * @var string
     */
    public string $description = "";

    /**
     * Definición de los atributos que caracterizan a una categoría
     * @var xml
     */
    public $Xml = "";

    /**
     * Estado lógico de la categoría
     * @var boolean
     */
    public bool $state = true;

    /**
     * Link de búsqueda para tener un URL friendly
     * @var string
     */
    public $Link = "";
}
