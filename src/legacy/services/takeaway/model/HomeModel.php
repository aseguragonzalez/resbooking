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

/**
 * Model para la pantalla de inicio
 */
class HomeModel extends \SaasModel{

    /**
     * Opción del menú activa
     * @var string
     */
    public $Activo = "";

    /**
     * Colección de proyectos válidos para el usuario
     * @var array
     */
    public $Projects = [];

    /**
     * Cadena aleatoria para forzar no cache en dependencias
     * @var string
     */
    public $Random = "";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Título de la página
        $this->Title = "Inicio";
        $date = new \DateTime("NOW");
        $this->Random = $date->format("YmdHis");
    }

    /**
     * Cargar todos los proyectos del usuario y establece el proyecto de
     * contexto si sólo existe uno
     * @return boolean Resultado de la carga
     */
    public function LoadProjects(){
        // filtro de busqueda
        $filter = [ "Username" => $this->Username,
            "IdService" =>  $this->Service];
        // Filtrar la lista de proyectos
        $this->Projects =
                $this->Dao->GetByFilter( "ProjectInfo" , $filter);
        // Comprobamos el número de proyectos actuales
        if(count($this->Projects) == 1){
            return $this->SetProject($this->Projects[0]);
        }
        // Resultado por defecto
        return FALSE;
    }

    /**
     * Establece el proyecto seleccionado por su id
     * @param int $idProyecto Identidad del proyecto
     * @return boolean Resultado de la configuración de proyecto
     */
    public function SetCurrent($idProyecto = 0){
        // Definir el filtro de búsqueda
        $filter = [ "Username" => $this->Username,
            "IdService" => $this->Service, "Id" => $idProyecto];
        // Filtrar el proyecto seleccionado
        $projects = $this->Dao->GetByFilter( "ProjectInfo" , $filter );
        // Comprobar si se ha encontrado algún proyecto
        if(count($projects) > 0){
            return $this->SetProject($projects[0]);
        }
        // resultado por defecto
        return FALSE;
    }

    /**
     * Establece los datos del proyecto en sesión
     * @param \Project $project Referencia al proyecto a "setear"
     * @return boolean Resultado de la operación
     */
    private function SetProject($project = NULL){
        // Setear el contexto con los datos de proyecto
        if($project != null){
            $_SESSION["projectId"] = $project->Id;
            $_SESSION["projectName"] = $project->Name;
            $_SESSION["projectPath"] = $project->Path;
            return TRUE;
        }
        // resultado por defecto
        return FALSE;
    }

}
