<?php

declare(strict_types=1);

/**
 * Entidad Notificación
 *
 * @author alfonso
 */
class Notification{

    /**
     * Identidad de la notificación
     * @var int
     */
    public $Id = 0;

    /**
     * proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Servicio que genera el registro
     * @var int
     */
    public $Service = 0;

    /**
     * Destino de la notificación
     * @var string
     */
    public $To = "";

    /**
     * Asunto de la notificación
     * @var string
     */
    public $Subject = "";

    /**
     * Cabecera del e-mail
     * @var string
     */
    public $Header = "";

    /**
     * Contenido de la notificación
     * @var string
     */
    public $Content = "";

    /**
     * Fecha en la que se genera la notificación
     * @var string
     */
    public $Date = "";

    /**
     * Número de veces que la notificación ha sido enviada
     * @var int
     */
    public $Dispatched = 0;
}
