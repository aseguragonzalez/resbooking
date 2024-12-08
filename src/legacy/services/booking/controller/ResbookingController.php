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
 * Clase base para los controladores de la aplicación
 *
 * @author alfonso
 */
class ResbookingController extends \SaasController{

    /**
     * Constructor
     */
    public function __construct($projectRequired = FALSE){

        parent::__construct();

        if($projectRequired == TRUE){
            $this->ValidateProject();
        }
    }

    /**
     * Validación del contexto de proyecto
     * @throws Exception
     */
    protected function ValidateProject(){
        if($this->Project == 0){
            throw new \ProjectException("Proyecto no seleccionado");
        }
    }

    /**
     * Procesado de las excepciones capturadas por el controlador
     * @param string $method Nombre del método que origina el error
     * @param \Exception $e Referencia a la excepción capturada
     * @return string Vista redenrizada
     */
    public function ProcessError($method = "", $e = null) {

        if($e instanceof \ProjectException){
            header("Location: /Home/Index");
            exit();
        }

        // Crear traza de error
        $this->Log->LogErrorTrace($method, $e);
        // Instanciar Modelo
        $model = new \SaasModel();
        // Renderizado de la vista de error
        return $this->Partial( "../shared/_error", $model);
    }

    /**
     * Procesado de errores en operaciones asíncronas con JSON
     * @param string $method Método donde se produce la captura de error
     * @param \Exception $e Referencia a la excepción capturada
     * @return \JsonResultDTO
     */
    protected function ProcessJSONError($method = "", $e = null){
        // Crear traza de error
        $this->Log->LogErrorTrace($method, $e);

        $dto = new \JsonResultDTO();
        $dto->Result = FALSE;
        $dto->Error = $e->getMessage();
        $dto->Code = 200;
        $dto->Exception = $e;
        return $dto;
    }
}
