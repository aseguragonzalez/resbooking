<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
