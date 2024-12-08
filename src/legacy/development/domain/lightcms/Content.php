<?php

    /**
     * Entidad Content
     */
    class Content{

        /**
         * Identidad de la entidad
         * @var int Identidad del Contenido
         */
        public $Id = 0;

        /**
         * Identidad de la sección padre
         * @var int Identidad de la sección padre
         */
        public $Section = 0;

        /**
         * Identidad del tipo de contenido
         * @var int Identidad de la tipología de contenido
         */
        public $Type = 0;

        /**
         * Título del contenido
         * @var string Título para el contenido
         */
        public $Title = "";

        /**
         * Colección de palabras clave : SEO
         * @var string Colección de palabras clave asociadas
         */
        public $Keywords  = "";

        /**
         * Descripción del contenido : SEO
         * @var string Descripción breve sobre el contenido
         */
        public $Description  = "";

        /**
         * Texto del enlace a utilizar
         * @var string Texto a utilizar en el enlace al contenido
         */
        public $LinkText = "";

        /**
         * Enlace para filtrar el contenido : SEO
         * @var string Enlace al contenido
         */
        public $Link = "";

        /**
         * Indica si el enlace es a una entidad distinta
         * @var boolean Indicador del tipo de enlace (externo - interno)
         */
        public $ExtLink = 0;

        /**
         * Contenido html
         * @var Contenido de la sección
         */
        public $Content = "";

        /**
         * Autor del contenido
         * @var string Autor del contenido
         */
        public $Author = "";

        /**
         * Plantilla a utilizar
         * @var string Plantilla a utilizar en la renderización
         */
        public $Template = "";

        /**
         * Estado de publicación : borrador o no
         * @var boolean Indica si se trata  un borrador
         */
        public $Draft = true;

        /**
         * Estado de la entidad : eliminado | activo
         * @var boolean Estado de la entidad
         */
        public $State = true;

    }
