<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de infrastructura de gestión de solicitudes
 * @author alfonso
 */
interface IRequestsRepository {

    /**
     * Carga en el agregado la colección de solicitudes filtradas por fecha.
     * Si no se especifica una fecha, se utiliza la actual
     * @param \DateTime $date Referencia a un objeto de tipo datetime
     * @return array
     */
    public function GetRequestsByDate($date = NULL);
}
