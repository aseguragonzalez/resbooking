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
 * Interfaz de la capa de servicios para la gestión de la configuración
 * del proyecto
 *
 * @author alfonso
 */
interface IConfigurationServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ConfigurationAggregate Referencia al agregado actual
     * @return \IConfigurationServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la información del proyecto para la impresión
     * de tickets
     * @param \ProjectInfo $dto Referencia a la información del proyecto
     * @return TRUE|array Colección de códigos de validación
     */
    public function ValidateInfo($dto = NULL);
}
