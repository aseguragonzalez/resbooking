<?php

declare(strict_types=1);

/**
 * Entidad Log. Representa la cantidad de visitas que recibe un mismo
 * producto.
 */
class Log{

    /**
     * Identidad del log en base de datos
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de visitas recibidas
     * @var int
     */
    public $Count=0;

}
