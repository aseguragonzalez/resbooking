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

// Cargar la referencia al model de cuentas de usuario
require_once "model/AccountsModel.php";

/**
 * Controlador para la gestión de cuentas de usuarios
 *
 * @author alfonso
 */
class AccountsController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción para cargar el formulario de recuperación de contraseña
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            if(isset($_SESSION[ "recoveryModel" ])){
                // Recuperar referencia de la sesión
                $model = json_decode($_SESSION[ "recoveryModel" ]);
                // eliminar referencia
                unset($_SESSION[ "recoveryModel" ]);
            }
            else{
                // Instanciar el modelo
                $model = new \AccountsModel();
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para visualizar el formulario de contraseñas
     * @return string Vista Renderizada
     */
    public function ChangePass(){
        try{
            if(isset($_SESSION[ "changeModel" ])){
                // Recuperar referencia de la sesión
                $model = json_decode($_SESSION[ "changeModel" ]);
                // eliminar referencia
                unset($_SESSION[ "changeModel" ]);
            }
            else{
                // Instanciar modelo
                $model = new \AccountsModel();
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("ChangePass", $e);
        }
    }

    /**
     * Acción para modificar contraseña
     * @return string Vista renderizada
     */
    public function Change(){
        try{
            // Recuperar parámetros de la llamada
            $dto = $this->GetEntity( "ChangeDTO" );
            // Instanciar modelo
            $model = new \AccountsModel();
            // Lanzar el proceso de recuperación
            if($model->ChangePass($dto)){
                // Setear el contexto
                $_SESSION[ "changeModel" ] = json_encode($model);
                // redirigir el flujo de ejecución
                return $this->RedirectTo( "ChangePass", "Accounts" );
            }
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Change", $e);
        }
    }

    /**
     * Acción para lanzar la recuperación de contraseña
     * @return string Vista renderizada
     */
    public function Recovery(){
        try{
            // Recuperar parámetros de la llamada
            $dto = $this->GetEntity( "ChangeDTO" );
            // Instanciar modelo
            $model = new AccountsModel();
            // Lanzar el proceso de recuperación
            $model->Recovery($dto->Email);
            // Setear el contexto
            $_SESSION[ "recoveryModel" ] = json_encode($model);
            // Redirigir el flujo
            return $this->RedirectTo( "Index", "Accounts" );
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Recovery", $e);
        }
    }

    /**
     * Acción para iniciar la sessión actual
     * @return string Vista renderizada
     */
    public function Login(){
        try{
            // Instanciar modelo
            $model = new \AccountsModel();
            // Redirigir el flujo
            return $this->RedirectTo("Index", "Home");
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Login", $e);
        }
    }

    /**
     * Acción para cerrar la sessión actual
     * @return string Vista renderizada
     */
    public function Logout(){
        try{
            // eliminar la sesión actual
            session_destroy();
            // Redirigir el flujo
            return $this->RedirectTo( "Index", "Home");
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Logout", $e);
        }
    }

}
