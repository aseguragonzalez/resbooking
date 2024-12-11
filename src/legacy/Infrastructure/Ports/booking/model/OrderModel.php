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

class ProductDTO{

    public $Id = "";

    public $Code = "";

    public $Name = "";

    public $Description = "";

    public $Price = "";

    public function __construct($name = ""){
        $this->Id = str_replace(" ", "_" ,trim(strtolower($name)));
        $this->Code = $this->Id."-UN-CODIGO";
        $this->Name = $name;
        $this->Description = "Una descripción para $name";
        $this->Price = rand(1,20)." €/ud";
    }
}

/**
 * Modelo para el catálogo de productoss
 *
 * @author alfonso
 */
class OrderModel extends \ResbookingModel{

    /**
     * Colección de productos
     * @var array
     */
    public $Entities = [];

    /**
     * Constructor
     * @param int $project ID del proyecto actual
     */
    public function __construct($project = 0){
        // Cargar constructor padre
        parent::__construct();
        // Asignar id de proyecto
        $this->Project = $project;

        for($i = 1; $i < 10; $i++){
            $this->Entities[] = new \ProductDTO("Un producto-".$i);
        }

    }
}
