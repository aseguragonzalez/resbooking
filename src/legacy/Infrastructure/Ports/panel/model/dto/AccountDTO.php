<?php

declare(strict_types=1);

/**
 * DTO para la gestión de formularios de cambio de contraseña
 *
 * @author alfonso
 */
class AccountDTO {

    /**
     * Dirección de correo del usuario
     * @var string
     */
    public $Email = "";

    /**
     * Contraseña de acceso actual
     * @var string
     */
    public $Password = "";

    /**
     * Contraseña de acceso nueva
     * @var string
     */
    public $NewPassword = "";

    /**
     * Repetición de la contraseña de acceso nueva
     * @var string
     */
    public $RepeatNewPassword = "";

}
