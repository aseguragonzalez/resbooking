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
 * de aplicación para productos
 */
interface IProductsManagement{

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = NULL);

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0);

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0);

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = NULL);

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Productos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
