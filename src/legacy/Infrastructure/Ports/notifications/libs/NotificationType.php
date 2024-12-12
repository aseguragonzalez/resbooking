<?php

    /**
     * Entidad tipo de notificación
     */
    class NotificationType{

        /**
         * Identidad del tipo de notificación
         * @var int Identidad del tipo de notificación
         */
        public int $id = 0;

        /**
         * Nombre del tipo de notificación
         * @var string Nombre del tipo
         */
        public string $name = "";

        /**
         * Descripción de la notificación
         * @var string Descripción breve sobre el uso del tipo
         */
        public string $description = "";

    }
