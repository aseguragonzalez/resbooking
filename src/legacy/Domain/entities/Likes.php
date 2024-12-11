<?php

declare(strict_types=1);

/**
 * Entidad Likes. Representa los votos positivos de un producto
 */
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
