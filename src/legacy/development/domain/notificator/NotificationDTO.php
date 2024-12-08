<?php

    /**
     * DTO con la información de la notificación y su configuración
     */
    class NotificationDTO{

        /**
         * Propiedad Id ( identidad de la notificación asociada )
         * @var int Identidad del registro de notificación
         */
        public $Id = 0;

        /**
         * Propiedad Project ( proyecto asociado )
         * @var int Identidad del proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad Service ( servicio que genera el registro )
         * @var int Identidad del servicio asociado
         */
        public $Service = 0;

        /**
         * Propiedad To ( Destino de la notificación )
         * @var string Destino de la notificación
         */
        public $_To = "";

        /**
         * Propiedad Subject ( Asunto de la notificación [clave] )
         * @var string Asunto de la notificación
         */
        public $_Subject = "";

        /**
         * Propiedad Content ( Contenido de la notificación serializado json)
         * @var string Contenido de la notificación
         */
        public $Content = "";

        /**
         * Propiedad Dispached ( Cantidad de veces que ha sido enviada
         * la notificación)
         * @var int Número de veces que se ha realizado el envío
         */
        public $Dispatched = 0;

        /**
         * Propiedad confSubject ( Asunto de la notificación [clave] )
         * @var string Asunto de la notificación
         */
        public $confSubject = "";

        /**
         * Propiedad confSubjectText ( Asunto de la notificación [texto] )
         * @var string Texto utilizado en el asunto de la notificación
         */
        public $confSubjectText = "";

        /**
         * Propiedad From ( Origen de la notificación )
         * @var string Origen de la nofiticación
         */
        public $_From = "";

        /**
         * Propiedad confTo ( Destinatario de administración )
         * @var string Destino de la notificación[Administración]
         */
        public $confTo = "";

        /**
         * Propiedad confTemplate ( plantilla  )
         * @var string Plantilla utilizada en la notificación
         */
        public $confTemplate = "";

        /**
         * Propiedad State ( Estado del servicio )
         * @var boolean Estado de la configuración
         */
        public $oConfState = 1;

    }
