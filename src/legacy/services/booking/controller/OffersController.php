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

// Cargar la referencia al model de ofertas
require_once "model/OffersModel.php";

/**
 * Controlador para la gestión de ofertas
 *
 * @author Alfonso Segura
 */
class OffersController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción Principal. Se encarga de proporcionar la lista de
     * ofertas activas(vivas) que estén registradas en el proyecto
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Validar el contexto de proyecto
            $this->ValidateProject();
            // Instanciar modelo de datos
            $model = new \OffersModel();
            // Obtener todas las ofertas registradas
            $model->CargarOfertas();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción de registro. Se encarga de realizar el proceso de
     * creación o actualización de una oferta y su configuración
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Validar el contexto de proyecto
            $this->ValidateProject();
            // Cargar la info de la oferta
            $entity = $this->GetEntity( "Offer" );
            // Instanciar modelo de datos
            $model = new \OffersModel();
            // Guardar la información de la oferta
            $model->GuardarOferta($entity);
            // Cargar el grid de ofertas
            $model->CargarOfertas();
            // retornar la vista
            return $this->Partial( "Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Acción de eliminación. Inicia el proceso de eliminación para
     * el registro de oferta seleccionado.
     * @return string Vista renderizada
     */
    public function Delete($id = 0){
        try{
            // Validar el contexto de proyecto
            $this->ValidateProject();
            // Instanciar model
            $model = new \OffersModel();
            // Eliminación de la oferta
            $model->EliminarOferta($id);
            // Cargar el grid de ofertas
            $model->CargarOfertas();
            // retornar la vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Delete", $e);
        }
    }

    /**
     * Acción listar ofertas. Obtiene la colección de ofertas diponibles
     * @param int $id Identidad del proyecto
     * @return string Serialización JSON
     */
    public function GetOffers($id = 0){
        try{
            // Instanciar modelo
            $model = new \OffersModel();
            // Obtener la colección de configuraciones
            $offers = $model->ObtenerOfertas($id);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $offers,
                "Error" => FALSE,
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetOffers" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para cargar la vista de configuración de la oferta
     * @param int $id Identidad de la oferta
     * @return string Vista renderizada
     */
    public function Configuration($id = 0){
        try{
            // Validar el contexto de proyecto
            $this->ValidateProject();
            // Instanciar modelo de datos
            $model = new \OffersModel();
            // Obtener todas las ofertas registradas
            $model->CargarOferta($id);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Configuration", $e);
        }
    }

    /**
     * Acción para obtener la configuración de turnos de una oferta
     * @param int $id Identidad de la oferta
     * @return string Serialización JSON
     */
    public function GetConfig($id = 0){
        try{
            // Instanciar configuraciones
            $model = new \OffersModel();
            // Obtener la colección de configuraciones
            $configs = $model->ObtenerConfiguraciones($id);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $configs,
                "Error" => FALSE,
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetConfig" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para el registro de una configuración de turno.
     * @return string Serialización JSON
     */
    public function SetConfig(){
        try{
            // Obtener dto
            $config = $this->GetEntity("OfferConfig");
            // Instanciar modelo
            $model = new \OffersModel();
            // Guardar la configuración
            $result = $model->GuardarConfiguracion($config);
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
            $obj = $this->ProcessJSONError("SetConfig" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para cargar la vista de configuración de cuotas de la oferta
     * @param int $id Identidad de la oferta
     * @return string Vista renderizada
     */
    public function Share($id = 0){
        try{
            // Validar el contexto de proyecto
            $this->ValidateProject();
            // Instanciar modelo de datos
            $model = new \OffersModel();
            // Cargar el modelo de cupos
            $model->CargarModeloCupos($id);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Share", $e);
        }
    }

    /**
     * Acción para el registro de una configuración de cupo de oferta
     * @return string Serialización JSON
     */
    public function SetShare(){
        try{
            // Obtener dto
            $offerShare = $this->GetEntity("OfferShare");
            // Instanciar modelo
            $model = new \OffersModel();
            // Guardar el cupo establecido
            $result = $model->GuardarCuotaOferta($offerShare);
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

    /**
     * Override para obtener el valor del checkbox sobre publicación web
     * @param string $entityName Nombre de la entidad
     * @return Object
     */
    public function GetEntity($entityName = "") {
        $entity = parent::GetEntity($entityName);
        if($entityName == "Offer"){
            $entity->Web = filter_input(INPUT_POST,
                    "Web", FILTER_VALIDATE_BOOLEAN);
        }
        return $entity;
    }
}
