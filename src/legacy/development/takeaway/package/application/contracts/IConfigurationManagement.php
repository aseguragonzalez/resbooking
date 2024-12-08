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
 * Interfaz de la capa de aplicación para la gestión de configuraciones
 *
 * @author alfonso
 */
interface IConfigurationManagement {

    /**
     * Procedimiento para cargar en el agregado la información de configuración
     */
    public function GetConfiguration();

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     * @return int Código de operación
     */
    public function SetDeliveryMethod($id = 0);

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de pago seleccionado
     * @param int $id Identidad del método de pago
     * @return int Código de operación
     */
    public function SetPaymentMethod($id = 0);

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el código postal seleccionado
     * @param int $id Identidad del código postal
     * @return int Código de operación
     */
    public function SetPostCode($id = 0);

    /**
     * Procedimiento para establecer la información de proyecto relativa
     * a la impresión de tickets
     * @param \ProjectInfo $info Referencia a la entidad a registrar
     * @return array Códigos de operación
     */
    public function SetProjectInfo($info = NULL);

    /**
     * Obtiene una instancia del Management de gestión de línea base
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IConfigurationManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
