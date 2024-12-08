<?php

    /**
     * Entidad Service
     */
    class Service{

        /**
         * Propiedad Id de Service
         * @var int Identidad de Servicio
         */
        public $Id = 0;

        /**
         * Propiedad Name de Service
         * @var string Nombre del servicio
         */
        public $Name = "";

        /**
         * Propiedad Path de Service
         * @var string Ruta fisica de la aplicacion cliente
         */
        public $Path = "";

        /**
         * Propiedad Platform de Service
         * @var string Ruta de la plataforma web utilizada
         */
        public $Platform = "";

        /**
         * Propiedad Description de Service
         * @var string Descripcion funcional del servicio
         */
        public $Description = "";

        /**
         * Propiedad Active de Service
         * @var boolean Estado del servicio
         */
        public $Active = true;

    }
