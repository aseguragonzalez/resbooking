<?php

declare(strict_types=1);

/**
 * DTO para "leer" la tabla de notificaciones
 *
 * @author alfonso
 */
class NotificationAlias{

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
    public $_To = "";

    /**
     * Asunto de la notificación
     * @var string
     */
    public $_Subject = "";

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
    public $_Date = "";

    /**
     * Número de veces que la notificación ha sido enviada
     * @var int
     */
    public $Dispatched = 0;
}
