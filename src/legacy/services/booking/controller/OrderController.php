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
require_once( "model/OrderModel.php" );

/**
 * Controlador para el acceso al catálogo de productos
 *
 * @author alfonso
 */
class OrderController extends \SaasController{

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    /**
     * Acción para obtener el formulario de prepedidos
     * @param int $id Identidad del proyecto
     */
    public function Index($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \OrderModel($id);
            // Objeto a retornar
            $resultDTO = [
                "Error" => FALSE,
                "Result" => TRUE,
                "Content" => $this->PartialView($model),
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetPreOrder" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

}
