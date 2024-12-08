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
