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
 * Controlador para el gestor de contenidos LightCMS ( Cliente )
 *
 * @author alfonso
 */
class ClientCMSController extends \SaasController{

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    /**
     * Acción para cargar una sección
     * @param string Nombre de la sección a cargar
     * @return string Contenido de la vista rederizada
     * @throws Exception
     */
    public function Section($sectionName = ""){
        try{
            // Instanciar modelo
            $model = new ClientCMSModel();
            // Cargar la información de la sección
            $model->LoadSection($sectionName);
            // Renderizar la vista
            return $this->RenderPartialView($model);
        }
        catch(Exception $e){
            // Generar traza de error
            $this->Log->LogErrorTrace( "Section", $e);
            // Relanzar el error
            throw $e;
        }
    }

    /**
     * Acción para cargar un contenido específico
     * @param string Nombre de la sección
     * @param string Propiedad Link del contenido (Utilizado para filtrar)
     * @return string Contenido de la vista renderizado
     * @throws Exception
     */
    public function Content($sectionName = "", $linkText = ""){
        try{
            // Instanciar modelo
            $model = new ClientCMSModel();
            // Cargar los datos del contenido especificado
            $model->LoadContent($sectionName, $linkText);
            // Renderizar la vista
            return $this->RenderPartialView($model);
        }
        catch(Exception $e){
            // Generar traza de error
            $this->Log->LogErrorTrace( "Content", $e);
            // Relanzar el error
            throw $e;
        }
    }

    /**
     * Procesar la vista parametrizando el nombre de la vista y del modelo
     * @param \SaasModel Referencia al modelo de datos
     * @return string Vista renderizada
     */
    public function RenderPartialView($model = null){
        // Construir path para encontrar la vista correspondiente
        $filePath = "view/".$model->TypeName."/".$model->Template;
        // Renderizar vista parcial
        return $this->RenderView($filePath, $model);
    }
}
