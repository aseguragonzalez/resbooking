<?php

    /**
     * Entidad Notificación
     */
    class Notification{

        /**
         * Propiedad Id de la notificación
         * @var int Identidad de la notificación
         */
        public $Id = 0;

        /**
         * Propiedad Project
         * @var int proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad Service
         * @var int servicio que genera el registro
         */
        public $Service = 0;

        /**
         * Propiedad To
         * @var string Destino de la notificación
         */
        public $To = "";

        /**
         * Propiedad Subject
         * @var string Asunto de la notificación
         */
        public $Subject = "";

        /**
         * Propiedad Header
         * @var string Cabecera del e-mail
         */
        public $Header = "";

        /**
         * Propiedad Content
         * @var string Contenido de la notificación
         */
        public $Content = "";

        /**
         * Propiedad Date
         * @var string  Fecha en la que se genera la notificación
         */
        public $Date = "";

        /**
         * Propiedad Dispatched
         * @var int  Número de veces que la notificación ha sido enviada
         */
        public $Dispatched = 0;

    }
