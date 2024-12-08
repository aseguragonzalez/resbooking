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
 * Entidad Usuario
 *
 * @author alfonso
 */
class User{

    /**
     * Identidad del usuario
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre de usuario (e-mail)
     * @var string
     */
    public $Username = "";

    /**
     * Contraseña de acceso
     * @var string
     */
    public $Password = "";

    /**
     * Estado del usuario
     * @var boolean
     */
    public $Active = TRUE;
}
