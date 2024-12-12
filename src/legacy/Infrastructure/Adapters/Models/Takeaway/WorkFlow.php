<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

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
