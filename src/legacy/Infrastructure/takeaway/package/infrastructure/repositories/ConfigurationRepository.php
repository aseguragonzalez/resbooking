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
 * Description of ConfigurationRepository
 *
 * @author alfonso
 */
class ConfigurationRepository extends \BaseRepository implements \IConfigurationRepository{

    /**
     * Referencia a la clase base
     * @var \IConfigurationRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IConfigurationRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(ConfigurationRepository::$_reference == NULL){
            ConfigurationRepository::$_reference =
                    new \ConfigurationRepository($project, $service);
        }
        return ConfigurationRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \ConfigurationAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \ConfigurationAggregate($this->IdProject, $this->IdService);
        // Cargar tablas maestras
        $agg->PostCodes = $this->Dao->Get("PostCode");
        $agg->DeliveryMethods = $this->Dao->Get("DeliveryMethod");
        $agg->PaymentMethods = $this->Dao->Get("PaymentMethod");
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject, "Service" => $this->IdService];
        // Cargar configuraciones
        $agg->AvailablePostCodes = $this->Dao->GetByFilter("PostCodeDTO", $filter);
        $agg->AvailableDeliveryMethods =
                $this->Dao->GetByFilter("DeliveryMethodDTO", $filter);
        $agg->AvailablePaymentMethods =
                $this->Dao->GetByFilter("PaymentMethodDTO", $filter);

        $projectsInfo = $this->Dao->GetByFilter("ProjectInformation",
            ["Project" => $this->IdProject]);

        if(count($projectsInfo)>0){
            $info = $projectsInfo[0] ;
            $info instanceof \ProjectInformation;
            $agg->ProjectInfo = $info;
        }

        return $agg;
    }
}
