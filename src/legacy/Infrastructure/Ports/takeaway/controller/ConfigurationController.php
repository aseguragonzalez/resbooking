<?php

/*
 * Copyright (C) 2015 manager
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

require "model/ConfigurationModel.php";

/**
 * Controlador para la gestión de configuraciones
 *
 * @author manager
 */
class ConfigurationController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Acción para cargar la información de configuración
     * @return String Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo
            $model = new \ConfigurationModel();
            // Cargar información de la pantalla de configuración
            $model->LoadModel();
            // retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para actualizar la información del proyecto para la impresión
     * de tickets
     * @return string Serialización JSON
     */
    public function SetInformation(){
        try{
            $info = $this->GetEntity("ProjectInformation");
            // Instanciar el modelo
            $model = new \ConfigurationModel();
            // Registrar el evento
            $json = $model->SetProjectInformation($info);
            // Retornar la serialización del resultado
            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetInformation", $e);

            return $this->ReturnJSON($json);
        }
    }

    /**
     * Acción para actualizar la relación del método de entrega
     * @param int $id Identidad del método de entrega
     * @return string Serialización JSON
     */
    public function SetDeliveryMethod($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ConfigurationModel();
            // Registrar el evento
            $json = $model->SetDeliveryMethod(intval($id));
            // Retornar la serialización del resultado
            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetDeliveryMethod", $e);

            return $this->ReturnJSON($json);
        }
    }

    /**
     * Acción para actualizar la relación de la forma de pago
     * @param int $id Identidad de la forma de pago
     * @return string Serialización JSON
     */
    public function SetPaymentMethod($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ConfigurationModel();
            // Registrar el evento
            $json = $model->SetPaymentMethod(intval($id));
            // Retornar la serialización del resultado
            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetPaymentMethod", $e);

            return $this->ReturnJSON($json);
        }
    }

    /**
     * Actualiza la relación del código postal con el proyecto
     * @param int $id Identidad del código postal
     * @return string Serialización JSON
     */
    public function SetPostCode($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ConfigurationModel();
            // Registrar el evento
            $json = $model->SetPostCode(intval($id));
            // Retornar la serialización del resultado
            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetPostCode", $e);

            return $this->ReturnJSON($json);
        }
    }
}
