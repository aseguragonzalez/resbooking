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

// Cargar la referencia al model
require_once "model/SummaryModel.php";
// Cargar la referencia al dto
require_once "model/dto/SummaryDTO.php";

/**
 * Controlador para la gestión de informes
 *
 * @author alfonso
 */
class SummaryController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(TRUE);
    }

    /**
     * Acción para generar el informe de reservas
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Obtener los datos de la petición
            $dto = $this->GetEntity( "SummaryDTO" );
            // Instanciar modelo de datos
            $model = new \SummaryModel();
            // Precesar el resumen de las reservas
            $model->GetSummary($dto);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para la generación del resumen
     * @return string Vista renderizada
     */
    public function Summary(){
        try{
            $dto = $this->GetEntity( "SummaryDTO" );
            // Instanciar modelo de datos
            $model = new \SummaryModel();
            // Procesar el resumen de reservas
            $model->GetSummary($dto);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Summary", $e);
        }
    }
}
