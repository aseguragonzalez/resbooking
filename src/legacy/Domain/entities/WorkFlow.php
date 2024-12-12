<?php

declare(strict_types=1);

/**
 * Estado del flujo de procesado de solicitud
 */
class WorkFlow{

    /**
     * Identidad del estado de workflow
     * @var int
     */
    public int $id = 0;

    /**
     * Nombre del estado
     * @var string
     */
    public $Name="";

    /**
     * Descripción funcional del estado
     * @var string
     */
    public $Description="";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;
}
