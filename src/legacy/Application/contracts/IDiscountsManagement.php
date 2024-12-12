<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación de descuentos y ofertas
 *
 * @author manager
 */
interface IDiscountsManagement {

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \DiscountsAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de descuentos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IDiscountsManagement
     */
    public static function GetInstance($project = 0, $service = 0);

    /**
     * Proceso para cargar en el agregado actual el descuento
     * identificado por su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function GetDiscount($id = 0);

    /**
     * Proceso para obtener los descuentos activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts();

    /**
     * Proceso para guardar la información del descuento en el repositorio
     * @param \DiscountDTO $dto Referencia al descuento
     * @return array Códigos de operación
     */
    public function SetDiscount($dto = null);

    /**
     * Proceso para dar de baja un descuento mediante su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function RemoveDiscount($id = 0);

    /**
     * Proceso para obtener la colección de eventos asociados a un descuento
     * filtrados por semana y año (opcional) o por estar activos
     * @param int $id Identidad del descuento asociado
     * @return array Colección de eventos registrados
     */
    public function GetDiscountEvents($id = 0, $week = 0, $year = 0);

    /**
     * Proceso para actualizar el estado del evento asociado a un descuento
     * @param \DiscountOnEvent $dto Referencia a la información del evento
     * @return int Código de operación
     */
    public function SetDiscountEvent($dto = null);

}
