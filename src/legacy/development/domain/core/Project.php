<?php

    /**
     * Entidad Project
     */
    class Project{

        /**
         * Propiedad Id de Project
         * @var int Identidad del proyecto
         */
        public $Id = 0;

        /**
         * Propiedad Name de Project
         * @var string Nombre de proyecto
         */
        public $Name = "";

        /**
         * Propiedad Description de Project
         * @var string Descripción del proyecto
         */
        public $Description = "";

        /**
         * Propiedad Path de Project
         * @var string Ruta de acceso al proyecto
         */
        public $Path = "";

        /**
         * Propiedad Date de Project
         * @var string Fecha de alta del proyecto
         */
        public $Date = null;

        /**
         * Propiedad Active de Project
         * @var boolean Estado lógico del proyecto
         */
        public $Active = true;

    }
