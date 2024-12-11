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

// Cargar la referencia al modelo para el inicio y selección de proyecto
require_once "model/HomeModel.php" ;

/**
 * Controlador para la sección pública y selección de proyecto
 *
 * @author alfonso
 */
class HomeController extends \PanelController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(FALSE);
    }

    /**
     * Acción para cargar el formulario de inicio.
     * @return string vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Configurar lista de proyectos
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para la selección de proyecto
     * @param int $id Identidad del proyecto
     * @return string Vista renderizada
     */
    public function SetProject($id = 0){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Establecer el proyecto actual
            $model->SetCurrent($id);
            // Configurar el proyecto seleccionado
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("SetProject", $e);
        }
    }

    /**
     * Acción para obtener el ticket actualizado
     * @return string Serialización JSON del resultado
     */
    public function GetTicket(){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $model->Ticket,
                "Error" => FALSE,
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetTicket" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para obtener el valor del bullet utilizando el id de servicio
     * @param int $id Identidad del servicio
     * @return string Serialización JSON del resultado
     */
    public function GetBullet($id = 0){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Obtener el contador del servicio
            $bullet = $model->GetBullet($id);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $bullet,
                "Error" => FALSE,
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetBullet" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para cargar la política de privacidad
     * @return string Vista renderizada
     */
    public function Privacity(){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Privacity", $e);
        }
    }
}
