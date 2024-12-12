<?php

declare(strict_types=1);

/**
 * Entidad para gestionar la información del proyecto relativa a la
 * impresión de tickets de venta
 *
 * @author alfonso
 */
class ProjectInformation {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public int $projectId = 0;

    /**
     * Título a utilizar en los ticket
     * @var string
     */
    public $Title = "";

    /**
     * Código de identificación fiscal
     * @var string
     */
    public $CIF = "";

    /**
     * Dirección física del proyecto
     * @var string
     */
    public $Address = "";

    /**
     * Número de teléfono del proyecto
     * @var string
     */
    public $Phone = "";

    /**
     * Email de contacto del proyecto
     * @var string
     */
    public $Email = "";
}
