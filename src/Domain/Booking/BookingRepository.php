<?php

declare(strict_types=1);

namespace App\Domain\Booking;

interface BookingRepository
{
    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingRepository
     */
    public static function GetInstance($project = 0, $service = 0);

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseAggregate
     */
    public function GetAggregate($project = 0, $service = 0);

    /**
     * Obtiene la referencia a la entidad cliente de la reserva
     * @param \Booking $entity Referencia a la reserva actual
     * @param boolean $advertising Flag para indicar si el cliente quiere publicidad
     * @return int Identidad del cliente
     */
    public function GetClient($entity = null, $advertising = false);

    /**
     * Genera el registro de notificación de una reserva
     * @param \Booking $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($entity = null, $subject = "");
}
