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
 * Controlador para el envío de notificaciones
 *
 * @author alfonso
 */
class ContactMailController extends \Controller{

    /**
     * Constructor por defecto
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción por defecto : envio de notificación
     */
    public function Send(){
        try{
            // Instanciar modelo
            $model = new \ContactMailModel();
            // Obtener toda la información de la petición
            $data = $this->GetMailData();
            // Enviar mail
            $model->Send($data);
            // Establecer resultado
            $_SESSION["eResult"] =
                    "Su solicitud ha sido procesada correctamente.";

            $_SESSION["eResultClass"] = "has-success";
            // Redirigir la petición
            return $this->Redirect();
        }
        catch(Exception $e){
            // Generar traza de error
            $this->Log->LogErrorTrace( "Send" , $e);
            // Relanzar el error
            throw $e;
        }
    }

    /**
     * Método para obtener los parámetros de la llamada
     */
    private function GetMailData(){
        $array = array();
        if(isset($_POST)){
            foreach($_POST as $key => $value){
                // Eliminamos posibles tags html y php.
                $array[$key] = strip_tags($value);
            }
        }
        return $array;
    }

    /**
     * Método para redirigir el flujo de la petición
     */
    private function Redirect(){

        $url = ConfigurationManager::GetKey( "path" );

        if(isset($_SERVER["HTTP_REFERER"])
                && $_SERVER["HTTP_REFERER"] != ""){
            $url = $_SERVER["HTTP_REFERER"];
        }

        return "<script type='text/javascript'>"
            . "window.location='$url'</script>";
    }

}
