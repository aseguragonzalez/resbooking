<?php

    /**
     *  Dto con la información resumen de un proyecto
     */
    class ProjectInfo{

        /**
         * Propiedad Id del proyecto
         * @var int Identidad de proyecto
         */
        public $Id = 0;

        /**
         * Propiedad Name del Proyecto
         * @var string Nombre de proyecto
         */
        public $Name = "";

        /**
         * Propiedad Description de Project
         * @var string Descripción del proyecto
         */
        public $Description = "";

        /**
         * Propiedad Path del proyecto
         * @var string Ruta física del proyecto
         */
        public $Path = "";

        /**
         * Propiedad Date del proyecto
         * @var string Fecha de alta del proyecto
         */
        public $Date = null;

        /**
         * Propiedad Id del servicio asociado
         * @var int Identidad del servicio asociado
         */
        public $IdService = 0;

        /**
         * Propiedad Id del usuario asociado
         * @var int Identidad del usuario asociado
         */
        public $IdUser = 0;

        /**
         * Propiedad Username del usuario asociado
         * @var string Nombre del usuario asociado
         */
        public $Username = "";

        /**
         * Propiedad Active del proyecto
         * @var boolean Estado actual del proyecto
         */
        public $Active = true;
    }
