<?php

declare(strict_types=1);

/**
 * DTO para obtener la información del código postal asociado
 *
 * @author alfonso
 */
class PostCodeDTO{

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Referencia al proyecto asociado
     * @var int
     */
    public int $projectId = 0;

    /**
     * Referencia al servicio asociado
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Referencia al código postal
     * @var int
     */
    public int $code = 0;

    /**
     * Código postal
     * @var string
     */
    public $PostCode = "";

    /**
     * Flag indicación si incluye el código postal completo
     * @var boolean
     */
    public $Full = false;

    /**
     * Descripción
     * @var string
     */
    public $Description;
}
