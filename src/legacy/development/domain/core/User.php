<?php

    /**
     * Entidad User
     */
    class User{

        /**
         * Propiedad Id de User
         * @var int Identidad del usuario
         */
        public $Id = 0;

        /**
         * Propiedad Username de User
         * @var string Nombre de usuario (e-mail)
         */
        public $Username = "";

        /**
         * Propiedad Password de User
         * @var string Contraseña de acceso
         */
        public $Password = "";

        /**
         * Propiedad Active de User
         * @var boolean Estado del usuario
         */
        public $Active = true;

    }
