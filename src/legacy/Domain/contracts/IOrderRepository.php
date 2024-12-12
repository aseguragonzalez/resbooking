<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de infrastructura para la gestión de pedidos
 *
 * @author alfonso
 */
interface IOrderRepository {

    /**
     * Proceso de registro de la información de un pedido
     * @param \Request $request Referencia a la información de pedido
     * @param array $items Referencia a la colección de productos seleccionados
     * @return int Código de operación
     */
    public function CreateOrder($request = null, $items = null);

    /**
     * Genera el registro de notificación de un pedido
     * @param int $id Identidad del pedido
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($id = 0, $subject = "");

}
