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
 * Agregado para la configuración de proyecto
 *
 * @author alfonso
 */
class ConfigurationAggregate extends \BaseAggregate{

    /**
     * Referencia a la entidad de registro de información de proyecto
     * @var \ProjectInformation
     */
    public $ProjectInfo = NULL;

    /**
     * Colección de formas de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de formas de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Colección de códigos postales disponibles
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de formas de entrega configurados
     * @var array
     */
    public $AvailableDeliveryMethods = [];

    /**
     * Colección de formas de pago configurados
     * @var array
     */
    public $AvailablePaymentMethods = [];

    /**
     * Colección de códigos postales configurados
     * @var array
     */
    public $AvailablePostCodes = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->ProjectInfo = new \ProjectInformation();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {

    }
}
