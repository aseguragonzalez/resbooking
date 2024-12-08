<?php

/**
 * Data transfer object para la asignación de nuevas credenciales
 *
 * @author manager
 */
class ChangeDTO {

    /**
     * Email del usuario
     * @var String
     */
    public $Email = "";

    /**
     * Contraseña actual
     * @var String
     */
    public $Pass = "";

    /**
     * Nueva contraseña
     * @var String
     */
    public $NewPass = "";

    /**
     * Repetición de la nueva contraseña
     * @var String
     */
    public $ReNewPass = "";
}
