<?php

declare(strict_types=1);

/**
 * Description of ServiceDTO
 *
 * @author alfonso
 */
class ServiceDTO {

    /**
     * Identidad de Servicio
     * @var int
     */
    public int $id = 0;

    /**
     * Nombre del servicio
     * @var string
     */
    public string $name = "";

    /**
     * Ruta fisica de la aplicacion cliente
     * @var string
     */
    public $Path = "";

    /**
     * Ruta de la plataforma web utilizada
     * @var string
     */
    public $Platform = "";

    /**
     * Descripcion funcional del servicio
     * @var string
     */
    public string $description = "";

    /**
     * Identidad del proyecto
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del Usuario
     * @var int
     */
    public $User = 0;

}
