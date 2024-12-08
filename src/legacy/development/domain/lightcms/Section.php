<?php

    /**
     * Entidad Section : Contiene la información general sobre una sección
     * del site
     */
    class Section {

        /**
         * Identidad de la entidad
         * @var int Identidad de la sección
         */
        public $Id = 0;

        /**
         * Identidad del proyecto al que pertenece
         * @var int Identidad del proyecto asociado
         */
        public $Project = 0;

        /**
         * Sección padre (si procede)
         * @var int Identidad de la sección padre (si tiene)
         */
        public $Root = NULL;

        /**
         * Nombre por defecto
         * @var string Nombre de la sección
         */
        public $Name = "";

        /**
         * Texto del enlace
         * @var string Enlace de acceso
         */
        public $Link = "";

        /**
         * Autor de la sección
         * @var string Autor de la sección
         */
        public $Author = "";

        /**
         * Conjunto de palabras clave : SEO
         * @var string Colección de términos clave utilizados
         */
        public $Keywords = "";

        /**
         * Descripción de la sección : SEO
         * @var string Descripción del contenido de la sección
         */
        public $Description = "";

        /**
         * Texto de ayuda en la navegación
         * @var string Texto de ayuda utilizado en los menús
         */
        public $Tooltip = "";

        /**
         * Plantilla a utilizar
         * @var string Plantilla utilizada
         */
        public $Template = "";

        /**
         * Posición de la sección en el menú
         * @var int Posición de la sección en el menú
         */
        public $Position = 0;

        /**
         * Estado de la sección : borrador o no
         * @var boolean Indica si se trata de un borrador o no
         */
        public $Draft = true;

        /**
         * Estado de la entidad : Eliminado o activo
         * @var boolean Estado de la sección
         */
        public $State = true;

    }
