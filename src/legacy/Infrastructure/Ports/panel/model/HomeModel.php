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
 * Model para la pantalla de inicio
 */
class HomeModel extends \PanelModel{

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Título de la página
        $this->Title = "Inicio";
    }

    public function GetBullet($id = 0){
        $count = 0;
        // Filtrar el servicio por id
        $services = array_filter($this->Services, function($item) use ($id){
           return $item->Id == $id;
        });

        if(count($services)>0){
            // Obtener referencia al servicio
            $service = current($services);
            // Obtener nombre del servicio en minúsculas
            $name = strtolower($service->Name);
            // Obtener el valor del bullet en función del servicio
            if($name == "resbooking"){
                $count = $this->GetResbookingBulletValue();
            }
            else if($name == "takeaway"){
                $count = $this->GetTakeawayBulletValue();
            }
        }
        return $count;
    }

    /**
     * Obtiene el número de reservas pendientes para el proyecto especificado
     * @return int
     */
    private function GetResbookingBulletValue(){
        // Cargar dependencias de bbdd
        require_once "model/entities/Booking.php";
        // Filtro de búsqueda
        $filter = ["Project" => $this->Project->Id, "State" => NULL];
        // Cargar la colección de reservas
        $bookings = $this->Dao->GetByFilter("Booking", $filter);
        // Retornar el número de reservas pendientes
        return count($bookings);
    }

    /**
     * Obtiene el número de pedidos pendientes para el proyecto especificado
     * @return int
     */
    private function GetTakeawayBulletValue(){
        // Cargar dependencias de bbdd
        require_once "model/entities/Request.php";
        // Filtro de búsqueda
        $filter = ["Project" => $this->Project->Id, "WorkFlow" => NULL];
        // Cargar la colección de pedidos
        $requests = $this->Dao->GetByFilter("Request", $filter);
        // Retornar el número de pedidos pendientes
        return count($requests);
    }

    /**
     * Establece la tabla de traducción para los códigos de error
     * obtenidos durante procesos de validación de entidades
     */
    protected function SetCodes() {

    }
}
