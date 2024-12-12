<?php

declare(strict_types=1);

/**
 * Description of RequestItemNotificationDTO
 *
 * @author alfonso
 */
class RequestItemNotificationDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Request=0;

    /**
     * Identidad del producto seleccionado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad solicitada
     * @var int
     */
    public $Count=0;

    /**
     * Observaciones/Opciones del producto
     * @var string
     */
    public $Data = "";

    /**
     * Nombre del producto
     * @var string
     */
    public string $name = "";

    /**
     * Precio del producto asociado
     * @var float
     */
    public $Price = 0;

}
