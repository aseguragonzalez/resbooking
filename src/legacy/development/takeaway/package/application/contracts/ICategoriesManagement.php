<?php

/*
 * Copyright (C) 2015 manager
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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para categorias
 */
interface ICategoriesManagement{

    /**
     * Proceso para cargar en el agregado actual la categoría
     * indicada mediante su identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function GetCategory($id = 0);

    /**
     * Proceso de registro o actualización de la categoría
     * @param \Category $category Referencia a la categoría
     * @return array Códigos de operación
     */
    public function SetCategory($category = NULL);

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveCategory($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Productos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
