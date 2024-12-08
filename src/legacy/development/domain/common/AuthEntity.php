<?php

    /**
     * Entidad para el proceso de autenticación y autorización
     */
    class AuthEntity{

        /**
         * Propiedad Id del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;

        /**
         * Propiedad Id del servicio
         * @var int Identidad del servicio
         */
        public $IdService = 0;

        /**
         * Propiedad Id del role
         * @var int Idenidad del role
         */
        public $IdRole = 0;

        /**
         * Nombre de usuario
         * @var string Nombre de usuario
         */
        public $Username = "";

        /**
         * Password de acceso
         * @var string Password de acceso
         */
        public $Password = "";

        /**
         * Nombre del role asociado
         * @var string Nombre del Role asociado
         */
        public $Role = "";

        /**
         * Nombre del servicio asociado
         * @var string Nombre del servicio asociado
         */
        public $Service = "";

        /**
         * Id del proyecto asociado
         * @var int Identidad del proyecto
         */
        public $IdProject = 0;

    }
