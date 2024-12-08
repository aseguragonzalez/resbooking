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

// Cargar dependencias
require_once( "model/BookModel.php" );

/**
 * Controlador para el registro de reservas
 *
 * @author alfonso
 */
class BookController extends \SaasController{

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    /**
     * Cargar formulario de reservas
     * @param int $id Id de proyecto
     * @return string Vista renderizada
     */
    public function Index($id = 0){
        try{
            // Instanciar el modelo
            $model = new \BookModel($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Crear traza de error
            $this->Log->LogErrorTrace("Index", $e);
            // Renderizado de la vista de error
            return $this->Partial("error", new \SaasModel());
        }
    }

    /**
     * Guarda la información de la reserva
     * @param int $id Identidad del proyecto
     * @return string Vista renderizada
     */
    public function Save($id = 0){
        try{
            // Obtener datos de la solicitud
            $entity = $this->GetEntity( "Booking" );
            // Instanciar el modelo
            $model = new \BookModel($id);
            // Ejecutar operacion de registro
            $result = $model->Save($entity, isset($_POST["Legal"]));
            // Evaluar el resultado
            $view = ($result) ? "Saved" : "Index";
            // Asignar la entidad que se ha recibido en la peticion sin procesar
            $model->Entity = $this->GetEntity("Booking");
            // Retornar la vista del formulario
            return $this->Partial( $view, $model );
        }
        catch(Exception $e){
            // Crear traza de error
            $this->Log->LogErrorTrace( "Save", $e);
            // Renderizado de la vista de error
            return $this->Partial("error", new \SaasModel());
        }
    }
}
