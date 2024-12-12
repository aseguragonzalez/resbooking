<?php

/**
 * Entidad configuración de notificaciones
 */
class NotificationConfigDTO{

    /**
     * Propiedad Id de la notificación
     * @var int identidad de la notificación
     */
    public int $id = 0;

    /**
     * Propiedad Project ( proyecto asociado )
     * @var int Identidad del proyecto asociado
     */
    public int $projectId = 0;

    /**
     * Propiedad ProjectName ( Nombre del proyecto asociado )
     * @var string Nombre del proyecto
     */
    public $ProjectName = "";

    /**
     * Propiedad Service ( servicio que genera el registro )
     * @var int Identidad del servicio asociado
     */
    public int $serviceId = 0;

    /**
     * Propiedad ServiceName ( Nombre del servicio que genera el registro )
     * @var string Nombre del servicio asociado
     */
    public $ServiceName = "";

    /**
     * Propiedad Subject ( Asunto de la notificación )
     * @var string Tipología del asunto de notificación
     */
    public $Subject = "";

    /**
     * Propiedad Text ( Asunto de la notificación a visualizar)
     * @var string Texto del asunto de la notificación
     */
    public $Text = "";

    /**
     * Propiedad From ( Origen de la notificación )
     * @var string Origen de la notificación
     */
    public $From = "";

    /**
     * Propiedad To ( Destino de la notificación )
     * @var string Destino de la notificación
     */
    public $To = "";

    /**
     * Propiedad Template ( Ruta de la plantilla a utilizar )
     * @var string Plantilla utilizada en la notificación
     */
    public $Template = "";

    /**
     * Propiedad State ( Estado del servicio )
     * @var boolean Estado de la notificación
     */
    public bool $state = true;

}
