<?php

    /**
     * DTO para el cambio de Contraseñas
     */
    class ChangeDTO{

        /**
         * Email de usuario
         * @var string
         */
        public $Email = "";

        /**
         * Password actual del usuario
         * @var string
         */
        public $Pass = "";

        /**
         * Nueva password a asignar
         * @var string
         */
        public $NewPass = "";

        /**
         * Repetición de la nueva password
         * @var string
         */
        public $ReNewPass = "";
    }
