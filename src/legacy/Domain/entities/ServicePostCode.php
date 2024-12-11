<?php

declare(strict_types=1);

/**
 * Entidad relacional para asociar códigos postales a
 * un proyecto y servicio
 *
 * @author manager
 */
class ServicePostCode {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Referencia al servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Referencia al código postal
     * @var int
     */
    public $Code = 0;

    /**
     * Flag indicación si incluye el código postal completo
     * @var boolean
     */
    public $Full = FALSE;
}
