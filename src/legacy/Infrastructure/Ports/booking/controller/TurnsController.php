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

// Cargar la referencia al modelo
require_once "model/TurnsModel.php";

/**
 * Controlador para la gestión de turnos
 *
 * @author alfonso
 */
class TurnsController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        // Indicamos al constructor que para todas las acciones
        // es necesario que esté contextualizado un proyecto
        parent::__construct(TRUE);
    }

    /**
     * Acción para cargar la configuración de turnos
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo de datos
            $model = new \TurnsModel();
            // Cargar la lista de configuraciones de turnos
            $model->GetTurns();
            // Procesar vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para guardar una nueva configuración de turnos
     * @return string Serialización JSON con el resultado de la operación
     */
    public function SetTurn(){
        try{
            // Obtener información del dto
            $dto = $this->GetEntity( "Configuration" );
            // Instanciar modelo
            $model = new \TurnsModel();
            // Ejecutar cambio
            $result = $model->Save($dto);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $result,
                "Error" => FALSE,
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetTurn" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }


    /**
     * Acción para cargar la vista de configuración de cuotas del turno
     * @return string Vista renderizada
     */
    public function Share(){
        try{
            // Instanciar modelo de datos
            $model = new \TurnsModel();
            // cargar el modelo de cupos
            $model->SetShareModel();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Share", $e);
        }
    }

    /**
     * Acción para el registro de una configuración de cupo de turno.
     * @return string Serialización JSON
     */
    public function SetShare(){
        try{
            // Obtener dto
            $turnShare = $this->GetEntity("TurnShare");
            // Instanciar modelo
            $model = new \TurnsModel();
            // Guardar el cupo de turno
            $result = $model->SetShare($turnShare);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $result,
                "Error" => ($result == -1),
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetShare" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }
}
