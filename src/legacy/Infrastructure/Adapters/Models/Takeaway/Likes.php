<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Likes{

    /**
     * Identidad del Like
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de votos positivos
     * @var int
     */
    public $Count=0;

}
