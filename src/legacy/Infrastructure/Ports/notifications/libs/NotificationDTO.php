<?php

/**
 * DTO con la información de la notificación y su configuración
 */
class NotificationDTO{

    /**
     * Identidad del registro de notificación
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Propiedad To ( Destino de la notificación )
     * @var string
     */
    public $_To = "";

    /**
     * Asunto de la notificación
     * @var string
     */
    public $_Subject = "";

    /**
     * Contenido de la notificación
     * @var string
     */
    public $Content = "";

    /**
     * Número de veces que se ha realizado el envío
     * @var int
     */
    public $Dispatched = 0;

    /**
     * Asunto de la notificación
     * @var string
     */
    public $confSubject = "";

    /**
     * Texto utilizado en el asunto de la notificación
     * @var string
     */
    public $confSubjectText = "";

    /**
     * Origen de la nofiticación
     * @var string
     */
    public $_From = "";

    /**
     * Destino de la notificación[Administración]
     * @var string
     */
    public $confTo = "";

    /**
     * Plantilla utilizada en la notificación
     * @var string
     */
    public $confTemplate = "";

    /**
     * Estado de la configuración
     * @var boolean
     */
    public $oConfState = 1;

}
