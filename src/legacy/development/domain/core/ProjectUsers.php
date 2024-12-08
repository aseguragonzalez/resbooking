<?php

    /**
     * Relación entre proyectos y usuarios asociados
     */
    class ProjectUsers{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del proyecto
         * @var int Identidad del proyecto
         */
        public $IdProject = 0;

        /**
         * Identidad del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;
    }
