<?php

declare(strict_types=1);

/**
 * DTO Resumen del informe de reservas
 */
class ReportDTO{

    /**
     * Mes
     * @var string
     */
    public $Mes = "";

    /**
     * Anyo
     * @var string
     */
    public $Anyo = "";

    /**
     * Cantidad de reservas cursadas
     * @var int
     */
    public $Cursadas = 0;

    /**
     * Cantidad de reservas perdidas
     * @var int
     */
    public $Perdidas = 0;

    /**
     * Mes del informe
     * @var int
     */
    public $Month = 0;

    /**
     * Año del informe
     * @var int
     */
    public $Year = 0;
}
