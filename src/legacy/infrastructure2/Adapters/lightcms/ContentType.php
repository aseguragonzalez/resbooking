<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\LightCMS;

class ContentType
{
    /**
     * Identidad de la entidad
     * @var int Identidad de la tipología
     */
    public $Id;

    /**
     * Nombre del tipo de contenido
     * @var string Nombre de la tipología
     */
    public $Name;

    /**
     * Descripción del tipo de contenido
     * @var string Descripción funcional de la tipología
     */
    public $Description;

    /**
     * Plantilla por defecto a utilizar
     * @var string Plantilla a utilizar por defecto
     */
    public $DefaultTemplate;

    /**
     * Estado de la entidad
     * @var boolean Estado de la tipología
     */
    public $State;

}
