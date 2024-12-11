<?php

declare(strict_types=1);

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
