<?php

    /**
     * Entidad configuración de notificaciones
     */
    class NotificationConfig{

        /**
         * Propiedad Id de la notificación
         * @var int Identidad del registro de configuración
         */
        public int $id = 0;

        /**
         * Propiedad Project ( proyecto asociado )
         * @var int Identidad del proyecto asociado
         */
        public int $projectId = 0;

        /**
         * Propiedad Service ( servicio que genera el registro )
         * @var int Identidad del servicio asociado
         */
        public int $serviceId = 0;

        /**
         * Propiedad Subject ( Asunto de la notificación )
         * @var string Tipificación del Asunto de la notificación
         */
        public $Subject = "";

        /**
         * Propiedad Text ( Asunto de la notificación a visualizar)
         * @var string Texto utilizado en el asunto de la notificación
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
         * @var string Plantilla a utilizar (html)
         */
        public $Template = "";

        /**
         * Propiedad State ( Estado del servicio )
         * @var boolean Estado de la notificación
         */
        public bool $state = true;

    }
