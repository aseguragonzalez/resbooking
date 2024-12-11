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
 * Clase base de la capa de aplicación
 *
 * @author alfonso
 */
abstract class BaseManagement{

    /**
     * Identidad del proyecto actual
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio en ejecución
     * @var int
     */
    protected $IdService = 0;

    /**
     * Referencia al respositorio de entidades
     * @var \BaseRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al gestor de servicios de la capa de dominio
     * @var \BaseService
     */
    protected $Service = NULL;

    /**
     * Referencia al agregado actual
     * @var \BaseAggregate;
     */
    protected $Aggregate = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        $this->IdProject = $project;
        $this->IdService = $service;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseAggregate
     */
    public abstract function GetAggregate();

    /**
     * Obtiene la instancia actual del Management del contexto
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseManagement
     */
    public static function GetInstance($project = 0, $service = 0){

    }
}

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
 * Clase base para los agregados
 *
 * @author alfonso
 */
abstract class BaseAggregate{
    /**
     * Referencia al proyecto actual
     * @var \Project
     */
    public $Project = NULL;

    /**
     * Identidad del proyecto actual
     * @var int
     */
    public $IdProject = 0;

    /**
     * Identidad del servicio en ejecución
     * @var int
     */
    public $IdService = 0;

    /**
     * Establecimiento de todas las entidades del agregado
     */
    abstract public function SetAggregate();
}

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
 * Clase base para la capa de servicios de dominio
 *
 * @author alfonso
 */
abstract class BaseServices{

    /**
     * Referencia al agregado actual
     * @var \BaseAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Referencia al respositorio
     * @var \BaseRepository
     */
    protected $Repository = NULL;

    /**
     * Identidad del proyecto
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    protected $IdService = 0;

    /**
     * Constructor
     * @param \BaseAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL){
        if($aggregate != NULL){
            // Asignar el agregado
            $this->Aggregate = $aggregate;
            // Asignar identidad del proyecto
            $this->IdProject = $aggregate->IdProject;
            // Asignar la identidad del servicio
            $this->IdService = $aggregate->IdService;
        }
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = NULL){

    }

    /**
     * Obtiene una entidad desde un array filtrado por id
     * @param array $array Colección de entidades
     * @param int $id Id de la entidad buscada
     * @return object|NULL Referencia encontrada
     */
    public function GetById($array = NULL, $id = 0){
        $items = array_filter($array, function($item) use ($id){
           return $item->Id == $id;
        });
        return (count($items) > 0) ? current($items) : NULL;
    }

    /**
     * Filtra una colección los criterios del filtro indicado
     * @param array $array Colección original
     * @param array $filter Colección de criterios para el filtro
     * @return array Colección de elementos que cumplen el filtro
     */
    public function GetListByFilter($array = NULL, $filter = NULL){
        $result = [];
        if($array != NULL && $filter != NULL){
            foreach($array as $item){
                if($this->CompareObject($item, $filter)){
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * Función para filtrar objetos por un array de parámetros
     * @param object $item Referencia al objeto a filtrar
     * @param array $filter Array con los criterios de filtro
     * @return boolean
     */
    private function CompareObject($item = NULL, $filter = NULL){
        foreach($filter as $key => $value){
            $val = $item->{$key};
            $nok = (is_numeric($value) && $val != $value)
                || (is_string($value) && strpos($val, $value) === FALSE);
            if($nok){
                return FALSE;
            }
        }
        return TRUE;
    }
}

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
 * Clase base para los repositorios
 *
 * @author alfonso
 */
abstract class BaseRepository{

    /**
     * Identidad del proyecto del contexto
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio del contexto
     * @var string
     */
    protected $IdService = 0;

    /**
     * Referencia al objeto de acceso a datos
     * @var \IDataAccessObject
     */
    protected $Dao = NULL;

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Gestor de trazas
     */
    protected $Log = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0) {
        $this->IdProject = $project;
        $this->IdService = $service;
        // Obtener nombre de la cadena de conexión
        $connectionString = ConfigurationManager
                ::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString = ConfigurationManager
                ::GetConnectionStr($connectionString);
        // Cargar las referencias
        $injector = Injector::GetInstance();
        // Cargar una instancia del gestor de trazas
        $this->Log = $injector->Resolve( "ILogManager" );
        // Cargar el objeto de acceso a datos
        $this->Dao = $injector->Resolve( "IDataAccessObject" );
        // Configurar el objeto de conexión a datos
        $this->Dao->Configure($oConnString);
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseAggregate
     */
    public abstract function GetAggregate();

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseRepository
     */
    public static function GetInstance($project = 0, $service = 0){

    }

    /**
     * Obtiene una colección con las entidades solicitadas
     * @param string $entityName
     * @return array
     */
    public function Get($entityName = ""){
        return $this->Dao->Get($entityName);
    }

    /**
     * Obtiene una colección filtrada de entidades del tipo solicitado
     * @param string $entityName Nombre del tipo de entidad solicitada
     * @param array $filter Filtro de búsqueda
     * @return array Colección de entidades disponibles
     */
    public function GetByFilter($entityName = "", $filter = NULL){
        return $this->Dao->GetByFilter($entityName, $filter);
    }

    /**
     * Crea un registro de la entidad solicitada
     * @param object $entity Referencia a la entidad a registrar
     * @return object|boolean Referencia a la entidad generada o FALSE
     */
    public function Create($entity = NULL){
        if($entity != NULL){
             $entity->Id = $this->Dao->Create($entity);
            return $entity;
        }
        return FALSE;
    }

    /**
     * Realiza una búsqueda de entidad por su identidad
     * @param string $entityName Nombre de la entidad
     * @param object $identity Identidad de la entidad buscada
     * @return object Referencia a la entidad buscada
     */
    public function Read($entityName = "", $identity = NULL){
        return $this->Dao->Read($identity, $entityName);
    }

    /**
     * Actualización de la entidad pasado como argumento
     * @param object $entity Referencia a la entidad a actualizar
     * @return object|boolean Referencia a la entidad o FALSE
     */
    public function Update($entity = NULL){
        if($entity != NULL){
            $this->Dao->Update($entity);
            return $entity;
        }
        return FALSE;
    }

    /**
     * Eliminación de la entidad por su identidad
     * @param string $entityName Nombre de la entidad
     * @param object $identity Identidad de la entidad
     * @return boolean Resultado de la operacion
     */
    public function Delete($entityName = "", $identity = NULL){
        return $this->Dao->Delete($identity, $entityName);
    }
}

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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para configuración de línea base
 * @author manager
 */
interface IBaseLineManagement {

    /**
     * Proceso para cargar en el agregado la información del Slot
     * de configuración indicado mediante su identidad
     * @param int $id Identidad del registro de configuración
     * @return int Código de operación
     */
    public function GetSlot($id = 0);

    /**
     * Proceso para almacenar la información de un registro de configuración
     * @param \SlotConfiguration $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = NULL);

    /**
     * Proceso para eliminar un registro de configuración
     * @param int $id Identidad del slot
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de gestión de línea base
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}


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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para categorias
 */
interface ICategoriesManagement{

    /**
     * Proceso para cargar en el agregado actual la categoría
     * indicada mediante su identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function GetCategory($id = 0);

    /**
     * Proceso de registro o actualización de la categoría
     * @param \Category $category Referencia a la categoría
     * @return array Códigos de operación
     */
    public function SetCategory($category = NULL);

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveCategory($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Productos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}



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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación de descuentos y ofertas
 *
 * @author manager
 */
interface IDiscountsManagement {

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \DiscountsAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de descuentos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IDiscountsManagement
     */
    public static function GetInstance($project = 0, $service = 0);

    /**
     * Proceso para cargar en el agregado actual el descuento
     * identificado por su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function GetDiscount($id = 0);

    /**
     * Proceso para obtener los descuentos activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts();

    /**
     * Proceso para guardar la información del descuento en el repositorio
     * @param \DiscountDTO $dto Referencia al descuento
     * @return array Códigos de operación
     */
    public function SetDiscount($dto = NULL);

    /**
     * Proceso para dar de baja un descuento mediante su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function RemoveDiscount($id = 0);

    /**
     * Proceso para obtener la colección de eventos asociados a un descuento
     * filtrados por semana y año (opcional) o por estar activos
     * @param int $id Identidad del descuento asociado
     * @return array Colección de eventos registrados
     */
    public function GetDiscountEvents($id = 0, $week = 0, $year = 0);

    /**
     * Proceso para actualizar el estado del evento asociado a un descuento
     * @param \DiscountOnEvent $dto Referencia a la información del evento
     * @return int Código de operación
     */
    public function SetDiscountEvent($dto = NULL);

}

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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación de eventos
 *
 * @author manager
 */
interface IEventsManagement {

    /**
     * Proceso para cargar en el agregado la información del evento
     * indicado mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function GetEvent($id = 0);

    /**
     * Proceso para almacenar la información del evento actual
     * @param \SlotEvent $event Referencia a la entidad
     * @return array Códigos de operación
     */
    public function SetEvent($event = NULL);

    /**
     * Proceso para eliminar un evento del registro
     * mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function RemoveEvent($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de eventos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IEventsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}


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
 * Declaración del contrato(Interface) para la gestión del catálogo y pedidos
 * @author manager
 */
interface IOrderManagement {

    /**
     * Proceso de registro de la solicitud
     * @param \OrderDTO $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetOrder($request = NULL);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IOrderManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}


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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para productos
 */
interface IProductsManagement{

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = NULL);

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0);

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0);

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = NULL);

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Productos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}

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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para solicitudes / pedidos
 */
interface IRequestsManagement{

    /**
     * Proceso para cargar en el agregado actual la solicitud
     * indicada mediante su identidad
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    public function GetRequest($id = 0);

    /**
     * Proceso para cargar en el agregado los solicitudes registradas
     * @param string $date Filtro opcional por fecha
     */
    public function GetRequests($date = "");

    /**
     * Proceso para cargar en el agregado las solicitudes pendientes
     */
    public function GetRequestsPending();

    /**
     * Proceso de registro o actualización de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetRequest($request = NULL);

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveRequest($id = 0);

    /**
     * Proceso para actualizar el estado de la solicitud indicada
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del estado de workflow
     * @return int Código de operación
     */
    public function SetState($id = 0, $state = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}

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
 *
 * @author manager
 */
interface ISlotsOfDeliveryManagement {

    /**
     * Proceso para cargar la información del turno de reparto indicado
     * mediante su identidad en el agregado.
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function GetSlot($id = 0);

    /**
     * Proceso para almacenar la información de un turno de reparto
     * @param \SlotOfDelivery $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = NULL);

    /**
     * Proceso para eliminar el registro de un turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de turnos de reparto
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \ISlotsOfDeliveryManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}


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
 * Interfaz de la capa de aplicación para la gestión de configuraciones
 *
 * @author alfonso
 */
interface IConfigurationManagement {

    /**
     * Procedimiento para cargar en el agregado la información de configuración
     */
    public function GetConfiguration();

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     * @return int Código de operación
     */
    public function SetDeliveryMethod($id = 0);

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de pago seleccionado
     * @param int $id Identidad del método de pago
     * @return int Código de operación
     */
    public function SetPaymentMethod($id = 0);

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el código postal seleccionado
     * @param int $id Identidad del código postal
     * @return int Código de operación
     */
    public function SetPostCode($id = 0);

    /**
     * Procedimiento para establecer la información de proyecto relativa
     * a la impresión de tickets
     * @param \ProjectInfo $info Referencia a la entidad a registrar
     * @return array Códigos de operación
     */
    public function SetProjectInfo($info = NULL);

    /**
     * Obtiene una instancia del Management de gestión de línea base
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IConfigurationManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}


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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de línea base
 *
 * @author manager
 */
class BaseLineManagement extends \BaseManagement implements \IBaseLineManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IBaseLineServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IBaseLineRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IBaseLineManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = BaseLineRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = BaseLineServices::GetInstance($this->Aggregate);
    }

    /**
     * Proceso para cargar en el agregado la información del Slot
     * de configuración indicado mediante su identidad
     * @param int $id Identidad del registro de configuración
     * @return int Código de operación
     */
    public function GetSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById(
                $this->Aggregate->Slots, $id);
        if($slot != NULL){

            $this->Aggregate->Slot = $slot;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información de un registro de configuración
     * @param \SlotConfiguration $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = NULL) {
        $slot->Project = $this->IdProject;
        $result = $this->Services->Validate($slot);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($slot->Id == 0){
                $res = $this->Repository->Create($slot);

                $result[] = ($res != FALSE) ? 0 : -1;

                $slot->Id = ($res != FALSE)? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($slot);

                $result[] = ($res != FALSE) ? 0 : -2;
            }
            // Actualizar la colección de slots
            if($res != FALSE){
                $this->Aggregate->Slots[$slot->Id] = $slot;
            }
        }
        return $result;
    }

    /**
     * Proceso para eliminar un registro de configuración
     * @param int $id Identidad del slot
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0) {

        $slot = $this->Services->GetById($this->Aggregate->Slots, $id);

        if($slot != NULL ){
            $result = $this->Repository->Delete("SlotConfigured", $id);
            if($result == 0){
                unset($slot);
                return 0;
            }
            return -1;
        }
        return -2;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(BaseLineManagement::$_reference == NULL){
            BaseLineManagement::$_reference =
                   new \BaseLineManagement($project, $service);
        }
        return BaseLineManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }
}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para categorías
 */
class CategoriesManagement extends \BaseManagement
    implements \ICategoriesManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ICategoriesServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \ICategoriesRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \ICategoriesManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = CategoriesRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = CategoriesServices::GetInstance($this->Aggregate);
    }

    /**
     * Proceso para cargar en el agregado actual la categoría
     * indicada mediante su identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function GetCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->Aggregate->Categories, $id);
        if($category != NULL){

            $this->Aggregate->Category = $category;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de la categoría
     * @param \Category $category Referencia a la categoría
     * @return array Códigos de operación
     */
    public function SetCategory($category = NULL) {
        $category->Project = $this->IdProject;
        $result = $this->Services->Validate($category);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($category->Id == 0){
                $res = $this->Repository->Create($category);
                $result[] = ($res != FALSE) ? 0 : -1;
                $category->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($category);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Categories[$category->Id] = $category;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->Aggregate->Categories, $id);
        if($category != NULL){
            // Eliminar todas las referencias asociadas a la categoría
            $this->RemoveReferences($id);
            // Establecer el estado
            $category->State = 0;
            // Actualizar
            $res = ($this->Repository->Update($category) != FALSE);

            if($res == TRUE){
                unset($this->Aggregate->Categories[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ICategoriesManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(CategoriesManagement::$_reference == NULL){
            CategoriesManagement::$_reference =
                   new \CategoriesManagement($project, $service);
        }
        return CategoriesManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \CategoriesAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso de baja de todas las referencias asociadas a una categoría
     * @param int $id Identidad de la categoría
     */
    private function RemoveReferences($id = 0){
        // Buscar las subcategorías
        $filter = ["Parent" => $id, "State" => 1];
        // Obtener todas las subcategorias
        $categories = $this->Services->GetListByFilter(
                $this->Aggregate->Categories, $filter);
        // Proces de eliminación de subcategorías
        foreach($categories as $item){
            // Actualizar la categoría actual
            $item->State = 0;
            // Actualizar el estado en bbdd
            $this->Repository->Update($item);
            // Actualizar los productos relacionados
            $products = $this->Repository->GetByFilter("Product",
                ["Category" => $item->Id, "State" => 1]);
            // Actualizar el estado en bbdd
            foreach($products as $prod){
                $prod->State = 0;
                $this->Repository->Update($prod);
            }
        }
    }
}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para descuentos
 */
class DiscountsManagement extends \BaseManagement
    implements \IDiscountsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IDiscountsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IDiscountsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado actual
     * @var \DiscountsAggregate
     */
    public $Aggregate = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IDiscountsManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = DiscountsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = DiscountsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto del contexto
     * @param int $service Identidad del servicio solicitado
     * @return \IDiscountsManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(DiscountsManagement::$_reference == NULL){
            DiscountsManagement::$_reference =
                   new \DiscountsManagement($project, $service);
        }
        return DiscountsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \DiscountsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual el descuento
     * identificado por su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function GetDiscount($id = 0) {
        $result = -1;
        // Obtener referencia al descuento
        $dto = $this->Services->GetById(
                $this->Aggregate->Discounts, $id);

        if($dto == NULL){
            $dto = $this->Repository->GetDiscountById($id);
        }

        // Validar la referencia obtenida
        if($dto != NULL){
            // Asignamos el dto encontrado
            $this->Aggregate->Discount = $dto;
            // código de operación
            $result = 0;
        }

        return $result;
    }

    /**
     * Proceso para obtener los descuentos activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts() {
        // Cargar en el agregado los descuentos
        $this->Aggregate->Discounts = $this->Repository->GetDiscounts();
        // Retornar la colección
        return $this->Aggregate->Discounts;
    }

    /**
     * Proceso para guardar la información del descuento en el repositorio
     * @param \DiscountDTO $dto Referencia al descuento
     * @return array Códigos de operación
     */
    public function SetDiscount($dto = NULL) {
        // Asignar el proyecto
        $dto->Project = $this->IdProject;
        // Asignar el servicio
        $dto->Service = $this->IdService;
        // Validar la información del descuento
        $result = $this->Services->Validate($dto);
        if(!is_array($result) && $result == TRUE ){
            // Obtener referencia a la entidad de bbdd
            $entity = $dto->GetDiscountOn();
            $result = [];
            // Registrar|actualizar el descuento
            if($entity->Id == 0){
                // Crear el registro del descuento
                $res = $this->Repository->Create($entity);
                // Establecer el resultado de la operación
                $result[] = ($res != FALSE) ? 0 : -1;
                $dto->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                // Actualizar el regsitro del descuento
                $res = $this->Repository->Update($entity);
                // Establecer el resultado de la operación
                $result[] = ($res != FALSE) ? 0 : -2;
            }
            // Actualizar las configuraciones asociadas
            if($res != FALSE){
                // Actualizar las configuraciones del descuento
                $res = $this->SetConfiguration($res->Id, $dto->Configuration);
                // Establecer el resultado de la operación
                $result = ($res != FALSE) ? [0] : [-3];
                // Actualizar el dto en la colección
                $this->Aggregate->Discounts[$dto->Id] = $dto;
            }
        }
        return $result;
    }

    /**
     * Proceso para dar de baja un descuento mediante su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function RemoveDiscount($id = 0) {
        // Obtener referencia al dto del descuento
        $dto = $this->Services->GetById(
                $this->Aggregate->Discounts, $id);

        if($dto == NULL){
            $dto = $this->Repository->GetDiscountById($id);
        }

        if($dto != NULL){
            // Eliminar todas las referencias a configuraciones del descuento
            $this->RemoveReferences($dto->Configuration);
            // Eliminar la referencia
            $dto->Configuration = [];
            // obtener referencia al descuento para actualizar el registro
            $entity = $dto->GetDiscountOn();
            // Establecer el estado
            $entity->State = 0;
            // Actualizar
            return ($this->Repository->Update($entity) != FALSE)
                ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso de eliminación de las configuraciones del descuento
     * @param array $configs Colección de configuraciones
     */
    private function RemoveReferences($configs = NULL){
        foreach($configs as $config){
            if($config->Id > 0){
                $this->Repository->Delete(
                        "DiscountOnConfiguration", $config->Id);
            }
        }
    }

    /**
     * Crear o eliminar los registros de configuración indicados
     * @param int $id Identidad del descuento
     * @param array $config Colección de configuraciones
     */
    private function SetConfiguration($id = 0, $config = NULL){
        // filtro para cargar las configuraciones del descuento
        $configFilter = ["DiscountOn" => $id ];
        // Obtener configuraciones
        $configuration = $this->Repository->GetByFilter(
                "DiscountOnConfiguration", $configFilter );
        // Eliminar la configuración actual
        $this->RemoveReferences($configuration);
        // Crear todos los registros nuevos
        foreach($config as $item){
            $item->DiscountOn = $id;
            $item = $this->Repository->Create($item);
        }
        return $id;
    }

    /**
     * Proceso para obtener la colección de eventos asociados a un descuento
     * filtrados por semana y año (opcional) o por estar activos
     * @param int $id Identidad del descuento asociado
     * @return array Colección de eventos registrados
     */
    public function GetDiscountEvents($id = 0, $week = 0, $year = 0){
        // Filtro estándar de búsqueda
        $filter = [
            "Project" => $this->IdProject,
            "Service" => $this->IdService,
            "DiscountOn" => $id
        ];
        // Aplicar el filtro de búsqueda por semana y anyo si se ha especificado
        if($week != 0 && $year != 0){
            $filter["Week"] = $week;
            $filter["Year"] = $year;
        }
        // Búsqueda de eventos
        $events = $this->Repository->GetByFilter("DiscountOnEvent", $filter);
        // Filtrado de eventos por fecha actual
        // (No se han especificado los parametros)
        if($week == 0 || $year == 0){
            $yesterday = new \DateTime("YESTERDAY");
            $events = array_filter($events, function($item) use ($yesterday){
                return (new \DateTime($item->Date)) > $yesterday;
            });
        }
        return $events;
    }

    /**
     * Proceso para actualizar el estado del evento asociado a un descuento
     * @param \DiscountOnEvent $dto Referencia a la información del evento
     * @return int Código de operación
     */
    public function SetDiscountEvent($dto = NULL){
        if($dto == NULL){
            return -1;
        }
        $dto->Project = $this->IdProject;
        $dto->Service = $this->IdService;
        // Filtro para buscar eventos registrados
        $filter = [ "Project" => $dto->Project, "Service" => $dto->Service,
            "DiscountOn" => $dto->DiscountOn, "Date" => "%$dto->Date%",
            "SlotOfDelivery" => $dto->SlotOfDelivery
        ];
        // Resultado de la búsqueda
        $events = $this->Repository->GetByFilter("DiscountOnEvent", $filter);

        if(empty($events)){
            $this->Repository->Create($dto);
        }
        else{
            foreach($events as $event){
                $this->Repository->Delete("DiscountOnEvent", $event->Id);
            }
        }
        return 0;
    }

}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de eventos
 *
 * @author manager
 */
class EventsManagement extends \BaseManagement implements \IEventsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IEventsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IEventsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IEventsManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = EventsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = EventsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IEventsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(EventsManagement::$_reference == NULL){
            EventsManagement::$_reference =
                   new \EventsManagement($project, $service);
        }
        return EventsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado la información del evento
     * indicado mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function GetEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->Aggregate->Events, $id);
        if($event != NULL){

            $this->Aggregate->Category = $event;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información del evento actual
     * @param \SlotEvent $event Referencia a la entidad
     * @return array Códigos de operación
     */
    public function SetEvent($event = NULL) {
        $event->Project = $this->IdProject;
        $result = $this->Services->Validate($event);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($event->Id == 0){
                $res = $this->Repository->Create($event);
                $result[] = ($res != FALSE) ? 0 : -1;
                $event->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($event);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Events[$event->Id] = $event;
            }
        }

        return $result;
    }

    /**
     * Proceso para eliminar un evento del registro
     * mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function RemoveEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->Aggregate->Events, $id);
        if($event != NULL){

            $result = $this->Repository->Delete("SlotEvent", $id);

            if($result == 0){

                unset($event);

                return 0;
            }
            return -1;
        }
        return -2;
    }
}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de eventos
 *
 * @author manager
 */
class OrderManagement extends \BaseManagement implements \IOrderManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IOrderServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IOrderRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IOrderManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = OrderRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = OrderServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IOrderManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(OrderManagement::$_reference == NULL){
            OrderManagement::$_reference =
                   new \OrderManagement($project, $service);
        }
        return OrderManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso de registro de la solicitud
     * @param \OrderDTO $dto Referencia al DTO de la solicitud
     * @return array Códigos de operación
     */
    public function SetOrder($dto = NULL) {
        $subject = "Pedido";
        // Asignar el proyecto
        $dto->Project = $this->IdProject;
        // Asignar campos calculados
        $dto->Total = $this->Services->GetTotal($dto);
        $dto->Amount = $this->Services->GetAmount($dto);
        $dto->Ticket = $this->Services->GetTicket($dto);
        // Validación de los datos
        $result = $this->Services->Validate($dto);

        if(!is_array($result) && $result == TRUE ){
            $result = [];
            // Obtener la referencia a la solicitud
            $request = $dto->GetRequest();
            // Obtener la colección de productos solicitados
            $items = $dto->GetRequestItems();
            // Generar el registro
            $id = $this->Repository->CreateOrder($request, $items);
            // Validar registro del pedido
            if($id > 0){
                $result[] = $this->Repository->CreateNotification($id, $subject);
            }
            else{
                $result[] = $id;
            }
        }
        return $result;
    }
}


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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para productos
 */
class ProductsManagement extends \BaseManagement implements \IProductsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IProductsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IProductsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IBaseLineManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = ProductsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ProductsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ProductsManagement::$_reference == NULL){
            ProductsManagement::$_reference =
                   new \ProductsManagement($project, $service);
        }
        return ProductsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0) {
        $product = $this->Services->GetById(
                $this->Aggregate->Products, $id);
        if($product != NULL){
            $this->Aggregate->Product = $product;

            $this->GetImagesByProduct($id);

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = NULL) {

        $product->Project = $this->IdProject;

        $result = $this->Services->Validate($product);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($product->Id == 0){
                $res = $this->Repository->Create($product);
                $result[] = ($res != FALSE) ? 0 : -1;
                $product->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($product);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Products[$product->Id] = $product;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0) {
        // Obtener referencia
        $product = $this->Services->GetById(
                $this->Aggregate->Products, $id);
        if($product != NULL){
            if($this->RemoveImages($id) == 0){

                $product->State = 0;

                $res = ($this->Repository->Update($product) != FALSE);

                if($res){
                    unset($this->Aggregate->Products[$id]);
                }

                return $res ? 0 : -1;
            }
        }
        return -2;
    }

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0) {
        $image = NULL;

        if(count($this->Aggregate->Images) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $images = $this->Repository->GetByFilter( "Image", $filter );
        }
        else{
            $images = $this->Services->GetById(
                    $this->Aggregate->Images, $id);
        }

        $image = current($images);

        if($image != NULL){
            $image->State = 0;
            return ($this->Repository->Update($image) != FALSE)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = NULL) {
        $date = new \DateTime("NOW");
        $image->Date = $date->format("Y-m-d h:i:s");
        $result = $this->Services->ValidateImage($image);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($image->Id == 0){
                $res = $this->Repository->Create($image);
                $result[] = ($res != FALSE) ? 0 : -1;
            }
            else{
                $res = $this->Repository->Update($image);
                $result[] = ($res != FALSE) ? 0 : -2;
            }
        }
        return $result;
    }

    /**
     * Carga en el agregado la colección de imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function GetImagesByProduct($id = 0){
        $filter = ["Product" => $id, "State"  => 1];
        $this->Aggregate->Images =
                $this->Repository->GetByFilter( "Image", $filter );
    }

    /**
     * Eliminar todas las imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function RemoveImages($id = 0){
        $results = [];
        $this->GetImagesByProduct($id);
        foreach($this->Aggregate->Images as $image){
            $results[] = $this->RemoveImage($image->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }
}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para solicitudes / pedidos
 *
 * @author manager
 */
class RequestsManagement extends \BaseManagement
    implements \IRequestsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IRequestsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IRequestsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IRequestsManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = RequestsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = RequestsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto para el contexto
     * @param int $service Identidad del servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(RequestsManagement::$_reference == NULL){
            RequestsManagement::$_reference =
                   new \RequestsManagement($project, $service);
        }
        return RequestsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \RequestsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual la solicitud
     * indicada mediante su identidad
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    public function GetRequest($id = 0) {
        // Carga las dependencias: Categorías, productos..
        $this->GetRequestDependences();
        // Cargar el registro de la solicitud
        $result = $this->GetRequestById($id);
        // Si la carga ha sido un éxito
        if($result == 0){
            $this->GetItemsByRequest($id);
        }
        return $result;
    }

    /**
     * Proceso para cargar en el agregado los solicitudes registradas
     * @param string $sDate Filtro opcional por fecha
     */
    public function GetRequests($sDate = ""){
        $date = NULL;
        if($sDate != ""){
            try{
                $date = new \DateTime($sDate);
            } catch (Exception $ex) {
                $date = new \DateTime("NOW");
            }
        }
        $this->Aggregate->Requests =
                $this->Repository->GetRequestsByDate($date);
    }

    /**
     * Proceso para cargar en el agregado las solicitudes pendientes
     */
    public function GetRequestsPending(){
        $filter = ["Project" => $this->IdProject, "WorkFlow" => NULL];
        $this->Aggregate->Requests =
                $this->Repository->GetByFilter("Request", $filter);
    }

    /**
     * Proceso de registro o actualización de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetRequest($request = NULL) {

        $result = $this->Services->Validate($request);

        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($request->Id == 0){
                $res = $this->Repository->Create($request);
                $result[] = ($res != FALSE) ? 0 : -1;
                $request->Id = ($res) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($request);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Requests[$request->Id] = $request;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveRequest($id = 0) {
        // Cargamos la información de la solicitud y eliminamos los items
        // asociados a la misma
        if($this->GetRequestById($id) == 0
                && $this->RemoveItemsByRequest($id) == 0){
            // Actualizar el estado
            $this->Aggregate->Request->State = 0;
            // Guardar cambios
            $res = ($this->Repository->Update($this->Aggregate->Request) != FALSE);
            // Modificar la información en el agregado
            if($res && isset($this->Aggregate->Requests[$id])){
                unset($this->Aggregate->Requests[$id]);
            }
            $this->Aggregate->Request = NULL;
            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para actualizar el estado de la solicitud indicada
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del estado de workflow
     * @return int Código de operación
     */
    public function SetState($id = 0, $state = 0) {

        if($this->GetRequestById($id) != 0){
            return -1;
        }

        if(!$this->Services->ValidateChangeState(
                $this->Aggregate->Request->WorkFlow, $state)){
            return -2;
        }

        $this->Aggregate->Request->WorkFlow = $state;

        if($this->Repository->Update($this->Aggregate->Request) == FALSE){
            return -3;
        }

        $this->Aggregate->Requests[$id] = $this->Aggregate->Request;

        return 0;
    }

    /**
     * Carga la información de categorías y productos del proyecto
     */
    private function GetRequestDependences(){
        // Cargamos todos los productos y categorías
        $filter = ["Project" => $this->IdProject ];
        $this->Aggregate->Products =
                $this->Repository->GetByFilter( "Product", $filter );
        $this->Aggregate->Categories =
                $this->Repository->GetByFilter( "Category", $filter );
    }

    /**
     * Carga el registro de la solicitud filtrada por su identidad
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function GetRequestById($id = 0){
        // Buscamos la información en la lista de solicitudes del agregado
        $this->Aggregate->Request =
                $this->Services->GetById($this->Aggregate->Requests, $id);
        // Si no se ha encontrado, buscamos en base de datos
        if($this->Aggregate->Request instanceof \Request == FALSE){
            $this->Aggregate->Request = $this->Repository->Read("Request", $id);
        }
        // Retornamos el código de operación
        return ($this->Aggregate->Request instanceof \Request) ? 0 : -1;
    }

    /**
     * Carga en el agregado la lista de detalles de la
     * solicitud especificada
     * @param int $id Identidad de la solicitud
     */
    private function GetItemsByRequest($id = 0){
        $filter = ["Request" => $id ];

        $this->Aggregate->Items =
                $this->Repository->GetByFilter( "RequestItem", $filter );
    }

    /**
     * Proceso de baja de los registros de detalle del solicitud
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    private function RemoveItemsByRequest($id = 0){
        $results = [];
        $this->GetItemsByRequest($id);
        foreach($this->Aggregate->Items as $item){
            $results[] = $this->RemoveItemById($item->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }

    /**
     * Proceso de baja de un registro de detalle
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function RemoveItemById($id = 0){
        $item = NULL;
        if(count($this->Aggregate->Items) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $item = $this->Repository->GetByFilter( "RequestItem", $filter );
        }
        else{
            $item = $this->Services->GetById($this->Aggregate->Items, $id);
        }
        if($item != NULL){
            return ($this->Repository->Update($item) != FALSE)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para establecer el estado de una solicitud
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del nuevo estado
     * @return \Request Referencia a la solicitud
     */
    public function SetRequestState($id = 0, $state = 0){
        $request = $this->GetById($this->Aggregate->Requests, $id);
        if($request != NULL){
            $request->WorkFlow = $state;
            return $request;
        }
        return NULL;
    }
}

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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryManagement extends \BaseManagement
    implements \ISlotsOfDeliveryManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ISlotsOfDeliveryServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \ISlotsOfDeliveryRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \ISlotsOfDeliveryManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = SlotsOfDeliveryRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = SlotsOfDeliveryServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ISlotsOfDeliveryManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(SlotsOfDeliveryManagement::$_reference == NULL){
            SlotsOfDeliveryManagement::$_reference =
                   new \SlotsOfDeliveryManagement($project, $service);
        }
        return SlotsOfDeliveryManagement::$_reference;
    }

    /**
     * Proceso para cargar la información del turno de reparto indicado
     * mediante su identidad en el agregado.
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function GetSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById(
                $this->Aggregate->Slots, $id);
        if($slot != NULL){

            $this->Aggregate->Slot = $slot;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información de un turno de reparto
     * @param \SlotOfDelivery $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = NULL) {
        $slot->Project = $this->IdProject;
        $result = $this->Services->Validate($slot);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($slot->Id == 0){
                $res = $this->Repository->Create($slot);
                $result[] = ($res != FALSE) ? 0 : -1;
                $slot->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($slot);
                $result[] = ($res != FALSE) ? 0 : -2;
            }
            if($res != FALSE){
                $this->Aggregate->Slots[$slot->Id] = $slot;
            }
        }
        return $result;
    }

    /**
     * Proceso para eliminar el registro de un turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById($this->Aggregate->Slots, $id);
        if($slot != NULL){
            // Establecer el estado
            $slot->State = 0;
            // Actualizar
            $res = ($this->Repository->Update($slot) != FALSE);

            if($res){
                // Eliminar todas las entidades relacionadas
                $this->RemoveRelations($id);

                unset($this->Aggregate->Slots[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * @ignore
     * Cargar toda la información del agregado  para el
     * proyecto y servicio indicado
     */
    private function LoadAggregate(){
        $agg = new \SlotsOfDeliveryAggregate();
        $agg->IdProject = $this->IdProject;
        $agg->IdService = $this->IdService;
        $this->Aggregate = $this->GetFromRepository($agg);
        $this->Aggregate->SetAggregate();
    }

    /**
     * Proceso de carga de los datos de agregado
     * @param \SlotsOfDeliveryAggregate $agg Referencia al agregado a completar
     * @return \SlotsOfDeliveryAggregate
     */
    private function GetFromRepository($agg = NULL){

        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->Repository->
                GetByFilter( "HourOfDay", ["State" => 1] );

        $filter = ["Project" => $this->IdProject];

        $slots = $this->Repository->GetByFilter( "SlotOfDelivery", $filter );

        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }

        return $agg;
    }

    /**
     * Elimina todos los registros relacionados con el turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return boolean
     */
    private function RemoveRelations($id = 0){

        $filter = [ "SlotOfDelivery" => $id ];

        $slotsEvents = $this->Repository->GetByFilter( "SlotEvent", $filter );

        foreach($slotsEvents as $item){
            $this->Repository->Delete( "SlotEvent", $item->Id );
        }

        $slotsConfigured =
                $this->Repository->GetByFilter( "SlotConfigured", $filter );

        foreach($slotsConfigured as $item){
            $this->Repository->Delete( "SlotConfigured", $item->Id );
        }

        $discountsOnConfiguration =
                $this->Repository->GetByFilter( "DiscountOnConfiguration",
                        $filter );

        foreach($discountsOnConfiguration as $item){
            $this->Repository->Delete( "DiscountOnConfiguration", $item->Id );
        }

        return TRUE;
    }
}

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
 * Implementación del contrato(Interface) para el gestor de configuraciones
 * de la capa de aplicación
 *
 * @author alfonso
 */
class ConfigurationManagement extends \BaseManagement
    implements \IConfigurationManagement {

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IConfigurationServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IConfigurationRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IConfigurationManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = ConfigurationRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = new \ConfigurationAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ConfigurationServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ConfigurationManagement::$_reference == NULL){
            ConfigurationManagement::$_reference =
                   new \ConfigurationManagement($project, $service);
        }
        return ConfigurationManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \ConfigurationAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     * @return int Código de operación
     */
    public function SetDeliveryMethod($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "DeliveryMethod" => $id,
            "Service" => $this->IdService];
        $register =
                $this->Repository->GetByFilter("ServiceDeliveryMethod", $filter);
        if(empty($register)){
            $entity = new \ServiceDeliveryMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->DeliveryMethod = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServiceDeliveryMethod", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de pago seleccionado
     * @param int $id Identidad del método de pago
     * @return int Código de operación
     */
    public function SetPaymentMethod($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "PaymentMethod" => $id,
            "Service" => $this->IdService];
        $register =
                $this->Repository->GetByFilter("ServicePaymentMethod", $filter);
        if(empty($register)){
            $entity = new \ServicePaymentMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->PaymentMethod = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServicePaymentMethod", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el código postal seleccionado
     * @param int $id Identidad del código postal
     * @return int Código de operación
     */
    public function SetPostCode($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "Code" => $id,
            "Service" => $this->IdService];
        $register =
                $this->Repository->GetByFilter("ServicePostCode", $filter);
        if(empty($register)){
            $entity = new \ServicePostCode();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->Code = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServicePostCode", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la información de proyecto relativa
     * a la impresión de tickets
     * @param \ProjectInfo $info Referencia a la entidad a registrar
     * @return array Códigos de operación
     */
    public function SetProjectInfo($info = NULL){
        $info->Project = $this->IdProject;
        $result = $this->Services->ValidateInfo($info);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($info->Id == 0){
                $res = $this->Repository->Create($info);
                $result = ($res != FALSE) ? [] : [-1];
                $info->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($info);
                $result = ($res != FALSE) ? [] : [-2];
            }

            if($res != FALSE){
                $this->Aggregate->ProjectInfo = $info;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para cargar en el agregado la información de configuración
     */
    public function GetConfiguration() {
        // Cargar el agregado
        $this->Aggregate =
                $this->Repository->GetAggregate($this->IdProject, $this->IdService);

        $this->Aggregate->SetAggregate();
    }

}

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
 *
 * @author alfonso
 */
interface IBaseLineRepository {
    //put your code here
}


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
 *
 * @author alfonso
 */
interface ICategoriesRepository {
    //put your code here
}


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
 * Interfaz de la capa de infrastructura para la gestión de descuentos
 *
 * @author alfonso
 */
interface IDiscountsRepository {

    /**
     * Proceso para obtener la colección de descuentos registrados activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts();

    /**
     * Proceso para obtener la información de un descuento filtrado por su Id
     * @param int $id Identidad del descuento
     * @return \DiscountDTO Referencia al DTO
     */
    public function GetDiscountById($id = 0);
}


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
 *
 * @author alfonso
 */
interface IEventsRepository {
    //put your code here
}


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
 * Interfaz de la capa de infrastructura para la gestión de pedidos
 *
 * @author alfonso
 */
interface IOrderRepository {

    /**
     * Proceso de registro de la información de un pedido
     * @param \Request $request Referencia a la información de pedido
     * @param array $items Referencia a la colección de productos seleccionados
     * @return int Código de operación
     */
    public function CreateOrder($request = NULL, $items = NULL);

    /**
     * Genera el registro de notificación de un pedido
     * @param int $id Identidad del pedido
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($id = 0, $subject = "");

}


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
 *
 * @author alfonso
 */
interface IProductsRepository {
    //put your code here
}


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
 * Interfaz de la capa de infrastructura de gestión de solicitudes
 * @author alfonso
 */
interface IRequestsRepository {

    /**
     * Carga en el agregado la colección de solicitudes filtradas por fecha.
     * Si no se especifica una fecha, se utiliza la actual
     * @param \DateTime $date Referencia a un objeto de tipo datetime
     * @return array
     */
    public function GetRequestsByDate($date = NULL);
}


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
 *
 * @author alfonso
 */
interface ISlotsOfDeliveryRepository {
    //put your code here
}


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
 *
 * @author alfonso
 */
interface IConfigurationRepository {
    //put your code here
}


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
 * Implementación del repositorio para la gestión de línea base
 *
 * @author alfonso
 */
class BaseLineRepository extends \BaseRepository implements \IBaseLineRepository{

    /**
     * Referencia a la clase base
     * @var \IBaseLineRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IBaseLineRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BaseLineRepository::$_reference == NULL){
            BaseLineRepository::$_reference =
                    new \BaseLineRepository($project, $service);
        }
        return BaseLineRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseLineAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \BaseLineAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Obtener turnos configurados
        $slots = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los turnos de reparto configurados
        foreach($slots as $item){
            $agg->Slots[$item->Id] = $item;
        }
        return $agg;
    }

}


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
 * Implementación de la interfaz para el repositorio de categorías
 *
 * @author alfonso
 */
class CategoriesRepository extends \BaseRepository
    implements \ICategoriesRepository{

    /**
     * Referencia a la clase base
     * @var \ICategoriesRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \ICategoriesRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(CategoriesRepository::$_reference == NULL){
            CategoriesRepository::$_reference =
                    new \CategoriesRepository($project, $service);
        }
        return CategoriesRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \CategoriesAggregate
     */
    public function GetAggregate() {
        $agg = new \CategoriesAggregate($this->IdProject, $this->IdService);
        // Cargar referencia al proyecto
        $agg->Project = $this->Dao->Read($this->IdProject, "Project");

        $filter = ["Project" => $this->IdProject, "State"  => 1];
        $categories = $this->Dao->GetByFilter( "Category", $filter );
        foreach($categories as $cat){
            $agg->Categories[$cat->Id] = $cat;
        }
        return $agg;
    }
}


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
 * Implementación de la interfaz para el repositorio de descuentos
 *
 * @author alfonso
 */
class DiscountsRepository extends \BaseRepository
    implements \IDiscountsRepository{

    /**
     * Referencia a la clase base
     * @var \IDiscountsRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IDiscountsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(DiscountsRepository::$_reference == NULL){
            DiscountsRepository::$_reference =
                    new \DiscountsRepository($project, $service);
        }
        return DiscountsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \DiscountsAggregate
     */
    public function GetAggregate($project = 0, $service = 0) {
        // Instanciar referencia al agregado
        $agg = new \DiscountsAggregate($this->IdProject, $this->IdService);
        // Cargar los días de la semana
        $agg->DaysOfWeek =  $this->Dao->Get("DayOfWeek");
        // filtro de búsqueda
        $filtro = ["Project" => $this->IdProject, "State" => 1];
        // Cargar los turnos de reparto
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter( "SlotOfDelivery", $filtro);

        return $agg;
    }

    /**
     * Proceso para obtener la colección de descuentos registrados activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts() {
        // array de resultados
        $result = [];
        // Filtro para la búsqueda de descuentos
        $filter=["Project"=>$this->IdProject,"Service"=>$this->IdService,"State"=>1];
        // Buscar descuentos
        $discounts = $this->Dao->GetByFilter( "DiscountOn", $filter );

        foreach($discounts as $discount){
            // filtro para cargar las configuraciones del descuento
            $configFilter = ["DiscountOn" => $discount->Id];
            // Obtener configuraciones
            $configuration =
                    $this->Dao->GetByFilter("DiscountOnConfiguration", $configFilter );
            // Agregar el dto correspondiente
            $result[$discount->Id] = new \DiscountDTO($discount, $configuration);
        }

        return $result;
    }

    /**
     * Proceso para obtener la información de un descuento filtrado por su Id
     * @param int $id Identidad del descuento
     * @return \DiscountDTO Referencia al DTO
     */
    public function GetDiscountById($id = 0){
        // Obtener la información del descuento
        $discount = $this->Dao->Read($id, "DiscountOn");
        if($discount == NULL){
            return NULL;
        }
        $filter = ["DiscountOn" => $id];
        $configuration = $this->Dao->GetByFilter("DiscountOnConfiguration", $filter);
        return new \DiscountDTO($discount, $configuration);
    }

}


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
 * Description of EventsRepository
 *
 * @author alfonso
 */
class EventsRepository extends \BaseRepository implements \IEventsRepository{

    /**
     * Referencia a la clase base
     * @var \IEventsRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IEventsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(EventsRepository::$_reference == NULL){
            EventsRepository::$_reference =
                    new \EventsRepository($project, $service);
        }
        return EventsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \EventsAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->Events = $this->Dao->GetByFilter("SlotEvent", $filter);
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Cargar turnos configurados
        $agg->BaseLine = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los días de la semana disponibles
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");

        return $agg;
    }
}


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
 * Implementación de la interfaz para la realización de pedidos
 *
 * @author alfonso
 */
class OrderRepository extends \BaseRepository implements \IOrderRepository{

    /**
     * Referencia a la clase base
     * @var \IOrderRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IOrderRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(OrderRepository::$_reference == NULL){
            OrderRepository::$_reference =
                    new \OrderRepository($project, $service);
        }
        return OrderRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \OrderAggregate
     */
    public function GetAggregate($project = 0, $service = 0) {
        // Instanciar agregado
        $agg = new \OrderAggregate($project, $service);
        // Tablas maestras
        $agg->HoursOfDay = $this->Dao->Get("HourOfDay");
        // Información del proyecto
        $agg->Project = $this->Dao->Read($project, "Project");
        // filtrado por proyecto y servicio
        $filter = ["Project" => $project, "Service" => $service];
        $agg->PaymentMethods = $this->Dao->GetByFilter("PaymentMethodDTO", $filter);
        $agg->DeliveryMethods = $this->Dao->GetByFilter("DeliveryMethodDTO", $filter);
        $agg->Slots = $this->Dao->GetByFilter("SlotDTO", $filter);
        $agg->PostCodes = $this->Dao->GetByFilter("PostCodeDTO", $filter);
        // filtrado por proyecto
        $filterP = ["Project" => $project, "State" => 1 ];
        $agg->Events = $this->Dao->GetByFilter("SlotEvent", $filterP);
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter( "SlotOfDelivery", $filterP );
        $agg->Categories = $this->Dao->GetByFilter("Category", $filterP);
        $agg->Products = $this->Dao->GetByFilter("Product", $filterP);
        foreach($agg->Products as $item){
            $item->Images = $this->Dao->GetByFilter("Image", ["Product" => $item->Id]);
        }
        $agg->Discounts = $this->Dao->GetByFilter("DiscountOn", $filterP);
        foreach($agg->Discounts as $item){
            $item->Configuration = $this->Dao->GetByFilter(
                    "DiscountOnConfiguration", ["DiscountOn" => $item->Id]);
        }
        return $agg;
    }

    /**
     * Proceso de registro de la información de un pedido
     * @param \Request $request Referencia a la información de pedido
     * @param array $items Referencia a la colección de productos seleccionados
     * @return int Código de operación
     */
    public function CreateOrder($request = NULL, $items = NULL){
        // Validación de los parámetros
        if($request == NULL || $request instanceof \Request == FALSE){
            return -101;
        }
        if(!is_array($items) || $items == NULL ){
            return -102;
        }
        // Registrar la solicitud y productos
        if(($r = $this->Create($request)) != FALSE){
            $r instanceof \Request;
            foreach($items as $item){
                $item instanceof \RequestItem;
                $item->Request = $r->Id;
                $this->Create($item);
            }
            return $r->Id;
        }
        return -103;
    }

    /**
     * Genera el registro de notificación de un pedido
     * @param int $id Identidad del pedido
     * @param string $subject Asunto de la notificación
     * @return int Código de operación
     */
    public function CreateNotification($id = 0, $subject = ""){
        // Obtener la información del pedido
        $dto = $this->Dao->Read($id, "RequestNotificationDTO");
        // Comprobar datos leídos
        if($dto instanceof \RequestNotificationDTO != FALSE){
            // Obtener los productos asociados al pedido
            $dto->Items = $this->Dao->GetByFilter(
                    "RequestItemNotificationDTO", ["Request"=>$id]);
            // Establecer el formato de fecha
            $date = new DateTime($dto->DeliveryDate);
            $dto->DeliveryDate = strftime(
                    "%A %d de %B del %Y", $date->getTimestamp());
            // Comprobar descuento
            if(empty($dto->Discount)){
                $dto->Discount = "Sin descuento";
            }

            return $this->RegisterNotification($dto, $subject);
        }
        return -104;
    }

    /**
     * Crea el registro de la notificación con la información de
     * la reserva y la tipología indicada.
     * @param \RequestNotificationDTO $entity Referencia a la notificación
     * @param string $subject Asunto de la notificación
     * @return int Código de operación
     */
    private function RegisterNotification($entity = NULL, $subject = ""){
       if($entity != NULL && is_object($entity)){
           $date = new \DateTime( "NOW" );
           $dto = new \Notification();
           $dto->Project = $this->IdProject;
           $dto->Service = $this->IdService;
           $dto->To = $entity->Email;
           $dto->Subject = $subject;
           $dto->Content = json_encode($entity);
           $dto->Date = $date->format( "y-m-d h:i:s" );
           $this->Create( $dto );
           $dto->To = "";
           $this->Create( $dto );
           return 0;
       }
       return -105;
    }
}


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
 * Implementación de la interfaz para el repositorio de productos
 *
 * @author alfonso
 */
class ProductsRepository extends \BaseRepository implements \IProductsRepository{

    /**
     * Referencia a la clase base
     * @var \IProductsRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(ProductsRepository::$_reference == NULL){
            ProductsRepository::$_reference =
                    new \ProductsRepository($project, $service);
        }
        return ProductsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $agg = new \ProductsAggregate($this->IdProject, $this->IdService);

        $filter = ["Project" => $this->IdProject, "State"  => 1];

        $products = $this->Dao->GetByFilter( "Product", $filter );

        foreach($products as $item){
            $agg->Products[$item->Id] = $item;
        }

        $agg->Categories = $this->Dao->GetByFilter( "Category", $filter );

        return $agg;
    }
}


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
 * Implementación de la interfaz para el repositorio de solicitudes
 *
 * @author alfonso
 */
class RequestsRepository extends \BaseRepository implements \IRequestsRepository{

    /**
     * Referencia a la clase base
     * @var \IRequestsRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IRequestsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(RequestsRepository::$_reference == NULL){
            RequestsRepository::$_reference =
                    new \RequestsRepository($project, $service);
        }
        return RequestsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \RequestsAggregate
     */
    public function GetAggregate($projec = 0, $service = 0) {
        // Instanciar agregado de solicitudes
        $agg = new \RequestsAggregate($this->IdProject, $this->IdService);

        $agg->HoursOfDay = $this->Dao->Get("HourOfDay");

        $agg->States = $this->Dao->GetByFilter( "WorkFlow", ["State" => 1]);

        $agg->Discounts = $this->Dao->GetByFilter("DiscountOn",
                    ["Project" => $this->IdProject, "State" => 1]);

        $agg->Requests = $this->GetRequestsByDate();

        $projectsInfo = $this->Dao->GetByFilter("ProjectInformation",
            ["Project" => $this->IdProject]);

        if(count($projectsInfo)>0){
            $info = $projectsInfo[0] ;
            $info instanceof \ProjectInformation;
            $agg->ProjectInformation = $info;
        }

        return $agg;
    }

    /**
     * Carga en el agregado la colección de solicitudes filtradas por fecha.
     * Si no se especifica una fecha, se utiliza la actual
     * @param \DateTime $date Referencia a un objeto de tipo datetime
     * @return array
     */
    public function GetRequestsByDate($date = NULL){
        $array = [];
        if($date == NULL || !($date instanceof DateTime)){
            $date = new \DateTime("NOW");
        }
        $sDate = $date->format("Y-m-d");
        $filter = ["Project" => $this->IdProject, "DeliveryDate" => $sDate ];
        $requests = $this->Dao->GetByFilter( "Request", $filter );
        foreach ($requests as $item){
            $array[$item->Id] = $item;
        }
        return $array;
    }
}


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
 * Implementación de la interfaz para el repositorio de turnos de reparto
 *
 * @author alfonso
 */
class SlotsOfDeliveryRepository extends \BaseRepository
    implements \ISlotsOfDeliveryRepository {

    /**
     * Referencia a la clase base
     * @var \ISlotsOfDeliveryRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \ISlotsOfDeliveryRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(SlotsOfDeliveryRepository::$_reference == NULL){
            SlotsOfDeliveryRepository::$_reference =
                    new \SlotsOfDeliveryRepository($project, $service);
        }
        return SlotsOfDeliveryRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \SlotsOfDeliveryAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \SlotsOfDeliveryAggregate($this->IdProject, $this->IdService);
        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->Dao->GetByFilter( "HourOfDay", ["State" => 1] );
        // filtro por proyecto
        $filter = ["Project" => $this->IdProject];
        $slots = $this->Dao->GetByFilter( "SlotOfDelivery", $filter );
        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }
        return $agg;
    }
}


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
 * Description of ConfigurationRepository
 *
 * @author alfonso
 */
class ConfigurationRepository extends \BaseRepository implements \IConfigurationRepository{

    /**
     * Referencia a la clase base
     * @var \IConfigurationRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IConfigurationRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(ConfigurationRepository::$_reference == NULL){
            ConfigurationRepository::$_reference =
                    new \ConfigurationRepository($project, $service);
        }
        return ConfigurationRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \ConfigurationAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \ConfigurationAggregate($this->IdProject, $this->IdService);
        // Cargar tablas maestras
        $agg->PostCodes = $this->Dao->Get("PostCode");
        $agg->DeliveryMethods = $this->Dao->Get("DeliveryMethod");
        $agg->PaymentMethods = $this->Dao->Get("PaymentMethod");
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject, "Service" => $this->IdService];
        // Cargar configuraciones
        $agg->AvailablePostCodes = $this->Dao->GetByFilter("PostCodeDTO", $filter);
        $agg->AvailableDeliveryMethods =
                $this->Dao->GetByFilter("DeliveryMethodDTO", $filter);
        $agg->AvailablePaymentMethods =
                $this->Dao->GetByFilter("PaymentMethodDTO", $filter);

        $projectsInfo = $this->Dao->GetByFilter("ProjectInformation",
            ["Project" => $this->IdProject]);

        if(count($projectsInfo)>0){
            $info = $projectsInfo[0] ;
            $info instanceof \ProjectInformation;
            $agg->ProjectInfo = $info;
        }

        return $agg;
    }
}


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
 * Interfaz de la capa de servicios para la gestión de línea base
 *
 * @author alfonso
 */
interface IBaseLineServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseLineAggregate Referencia al agregado actual
     * @return \IBaseLineServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la entidad
     * @param \SlotConfigured $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);
}


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
 * Interfaz de la capa de servicios para la gestión de categorías
 */
interface ICategoriesServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \ICategoriesServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de categorías
     * @param \Category $entity Referencia a la categoría a validar
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);
}

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
 * Interfaz de la capa de servicios para la gestion de descuentos
 *
 * @author manager
 */
interface IDiscountsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \DiscountsAggregate Referencia al agregado actual
     * @return \IDiscountsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la información del descuento
     * contenida en el DTO
     * @param \DiscountDTO $dto Referencia a la información de descuento
     * @return TRUE|array Colección de códigos de validación
     */
    public function Validate($dto = NULL);

}

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
 * Interfaz de la capa de servicio para la gestión de eventos
 *
 * @author manager
 */
interface IEventsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \EventsAggregate Referencia al agregado actual
     * @return \IEventsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la entidad
     * @param \SlotEvent $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);
}


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
 * Interfaz de la capa de servicios para la gestión de solicitudes
 *
 * @author manager
 */
interface IOrderServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \OrderAggregate Referencia al agregado actual
     * @return \IOrderServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la solicitud
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso para el cálculo del importe total(Aplicado descuento si procede)
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe Total
     */
    public function GetTotal($entity = NULL);

    /**
     * Proceso para el cálculo del importe sin descuentos
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe
     */
    public function GetAmount($entity = NULL);

    /**
     * Proceso para la generación del Ticket de pedido
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return string Ticket del pedido
     */
    public function GetTicket($entity = NULL);
}

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
 * Interfaz de la capa de servicios para la gestión de productos
 */
interface IProductsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ProductsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Obtiene el link de un producto a partir de su nombre
     * @param string $name Nombre del producto
     * @return string
     * @throws Exception Excepción generada si el nombre
     * de producto no es válido
     */
    public function GetLinkProduct($name = "");

    /**
     * Proceso de validación del producto
     * @param \Product $entity
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso de validacion de la imagen
     * @param \Image $image Referencia al objeto imagen a crear
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function ValidateImage($image = NULL);
}

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
 * Interfaz de la capa de servicio para la gestión de solicitudes/pedidos
 *
 * @author manager
 */
interface IRequestsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IRequestsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return boolean
     */
    public function Validate($request = NULL);

    /**
     * Proceso de validación en el cambio de estado de una solicitud
     * @param int $current Identidad del estado actual
     * @param int $next Identidad del estado próximo
     * @return boolean
     */
    public function ValidateChangeState($current = 0, $next = 0);
}

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
 * Interfaz de la capa de servicios para la gestión de turnos de reparto
 *
 * @author manager
 */
interface ISlotsOfDeliveryServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \SlotsOfDeliveryAggregate Referencia al agregado actual
     * @return \ISlotsOfDeliveryServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la entidad
     * @param \SlotOfDelivery $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

}

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
 * Interfaz de la capa de servicios para la gestión de la configuración
 * del proyecto
 *
 * @author alfonso
 */
interface IConfigurationServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ConfigurationAggregate Referencia al agregado actual
     * @return \IConfigurationServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la información del proyecto para la impresión
     * de tickets
     * @param \ProjectInfo $dto Referencia a la información del proyecto
     * @return TRUE|array Colección de códigos de validación
     */
    public function ValidateInfo($dto = NULL);
}


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
 * Capa de servicio para la configuración de línea base
 *
 * @author manager
 */
class BaseLineServices extends \BaseServices implements \IBaseLineServices{

    /**
     * Referencia
     * @var \IBaseLineServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IBaseLineRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \BaseLineAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \BaseLineAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = BaseLineRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \IBaseLineServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(BaseLineServices::$_reference == NULL){
            BaseLineServices::$_reference = new \BaseLineServices($aggregate);
        }
        return BaseLineServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotConfigured $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDayOfWeek($entity->DayOfWeek);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        else if($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del slot asociado a la configuración
     * @param int $slot Identidad del slot asociado
     */
    private function ValidateSlot($slot = 0){
        if(empty($slot)){
            $this->Result[] = -6;
        }
        else if($slot < 1){
            $this->Result[] = -7;
        }
        else{
            $s = $this->GetById(
                    $this->Aggregate->AvailableSlotsOfDelivery, $slot);
            if($s == NULL){
                $this->Result[] = -8;
            }
        }
    }

    /**
     * Proceso de validación del día de la semana seleccionado
     * @param int $dayOfWeek Día de la semana asociado
     */
    private function ValidateDayOfWeek($dayOfWeek = 0){
        if(empty($dayOfWeek)){
            $this->Result[] = -9;
        }
        else if($dayOfWeek < 1){
            $this->Result[] = -10;
        }
        else{
            $s = $this->GetById(
                    $this->Aggregate->DaysOfWeek, $dayOfWeek);
            if($s == NULL){
                $this->Result[] = -11;
            }
        }
    }
}

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
 * Capa de servicios para la gestión de categorías
 */
class CategoriesServices extends \BaseServices implements \ICategoriesServices{

    /**
     * Referencia
     * @var \ICategoriesServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \ICategoriesRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \CategoriesAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \CategoriesAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = CategoriesRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \ICategoriesServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(CategoriesServices::$_reference == NULL){
            CategoriesServices::$_reference = new \CategoriesServices($aggregate);
        }
        return CategoriesServices::$_reference;
    }

    /**
     * Proceso de validación de categorías
     * @param \Category $entity Referencia a la categoría a validar
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateCode($entity->Id, $entity->Code);
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateDescription($entity->Description);
            $this->ValidateXml($entity->Xml);
            $this->ValidateParent($entity->Parent);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del código de usuario
     * @param string $code Código de categoría
     */
    private function ValidateCode($id = 0, $code  = ""){
        // Validar código
        if(empty($code)){
            $this->Result[] = -4;
        }
        elseif(strlen($code) > 10){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateExistsCode($id, $code);
        }
    }

    /**
     * @ignore
     * Validación clave Unique del código de categoría
     * @param int $id Identidad de la entidad
     * @param string $code Código de categoría
     */
    private function ValidateExistsCode($id = 0, $code = ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Code" => $code, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->Aggregate->Categories, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * @ignore
     * Proceso de validación del nombre de la categoría
     * @param string $name Nombre de la categoría
     */
    private function ValidateName($id = 0,$name = ""){
        if(empty($name)){
            $this->Result[] = -7;
        }
        elseif(strlen($name) > 100){
            $this->Result[] = -8;
        }
        else{
            $this->ValidateExistsName($id, $name);
        }
    }

    /**
     * Validación clave Unique del nombre de categoría
     * @param string $name Nombre de la categoría
     */
    private function ValidateExistsName($id = 0, $name= ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];
        // buscar algún item con el mismo nombre
        $items = $this->GetListByFilter(
                    $this->Aggregate->Categories, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
            && count($items) > 0 && $items[0]->Id != $id){
            $this->Result[] = -9;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la descripción de categoría
     * @param string $desc Descripción de la categoría
     */
    private function ValidateDescription($desc = ""){
        if(empty($desc)){
            $this->Result[] = -10;
        }
        elseif(strlen($desc) > 500){
            $this->Result[] = -11;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la definición Xml de la categoría
     * @param string $xml Xml de descripción de la categoría
     * @return boolean
     */
    private function ValidateXml($xml = ""){
        if(empty($xml)){
            $this->Result[] = -12;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la categoría padre
     * @param int $id Referencia a la categoría padre
     */
    private function ValidateParent($id = 0){
        if(is_numeric($id) && $id > 0){
            $filter = [ "Project" => $this->IdProject,
                "Id" => $id, "State" => 1 ];
            $items = $this->GetListByFilter(
                    $this->Aggregate->Categories, $filter);
            if(empty($items) || count($items) == 0){
                $this->Result[] = -13;
            }
        }
    }
}

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
 * Capa de servicios para la gestion de descuentos
 *
 * @author manager
 */
class DiscountsServices extends \BaseServices implements \IDiscountsServices{

    /**
     * Referencia
     * @var \IDiscountsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IDiscountsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \CategoriesAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \DiscountsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = DiscountsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     * @return \IDiscountsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(DiscountsServices::$_reference == NULL){
            DiscountsServices::$_reference = new \DiscountsServices($aggregate);
        }
        return DiscountsServices::$_reference;
    }

    /**
     * Proceso de validación de la información del descuento
     * contenida en el DTO
     * @param \DiscountDTO $dto Referencia a la información de descuento
     * @return TRUE|array Colección de códigos de validación
     */
    public function Validate($dto = NULL){
        if($dto != NULL){
            $this->ValidateValue($dto->Value);
            $this->ValidateMaxValue($dto->Max);
            $this->ValidateMinValue($dto->Min);
            $this->ValidateMaxMinValue($dto->Max,
                    $dto->Min, $dto->Value);
            $this->ValidateStart($dto->Start);
            $this->ValidateEnd($dto->End);
            $this->ValidateStartEnd($dto->Start, $dto->End);
            $this->ValidateConfiguration($dto->Configuration);
        }
        else{
            $this->Result[] = -4;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del descuento seleccionado
     * @param int $value Descuento a aplicar
     */
    private function ValidateValue($value){
        if(empty($value)){
            $this->Result[] = -5;
        }
        else if(!is_numeric($value)){
            $this->Result[] = -6;
        }
        else if(intval($value) < 0){
            $this->Result[] = -7;
        }
    }

    /**
     * Proceso de validación del valor máximo aplicable
     * @param int $max Valor máximo al que se puede aplicar el descuento
     */
    private function ValidateMaxValue($max){
        if(empty($max)){
            $this->Result[] = -8;
        }
        else if(!is_numeric($max)){
            $this->Result[] = -9;
        }
        else if(intval($max) <= 0){
            $this->Result[] = -10;
        }
    }

    /**
     * Proceso de validación del valor mínimo aplicable
     * @param int $min Valor mínimo al que se puede aplicar el descuento
     */
    private function ValidateMinValue($min){
        if(empty($min)){
            $this->Result[] = -11;
        }
        else if(!is_numeric($min)){
            $this->Result[] = -12;
        }
        else if(intval($min) < 0){
            $this->Result[] = -13;
        }
    }

    /**
     * Proceso de validación de la coherencia entre los márgenes de valores
     * establecidos para el descuento
     * @param int $max
     * @param int $min
     */
    private function ValidateMaxMinValue($max = 0, $min = 0){
        if($max != 0 && ($min >= $max)){
            $this->Result[] = -14;
        }
    }

    /**
     * Proceso de validación de la fecha de inicio de descuento
     * @param string $start Fecha de inicio
     */
    private function ValidateStart($start = ""){
        // Referencia al día anterior
        $yesterday = new DateTime("YESTERDAY");

        try{
            if(empty($start)){
                $this->Result[] = -15;
                return;
            }

            return ;

            $date = new DateTime($start);
            //  No se valida si el inicio es anterior
            /*
            if($date < $yesterday){
                $this->Result[] = -16;
            }
            */
        }
        catch(Exception $e){
            $this->Result[] = -17;
        }
    }

    /**
     * Proceso de validación de la fecha de fin de descuento
     * @param string $end Fecha fin de descuento
     */
    private function ValidateEnd($end = ""){
        // Referencia al día anterior
        $yesterday = new DateTime("YESTERDAY");

        try{
            if(empty($end)){
                $this->Result[] = -18;
                return;
            }

            $date = new DateTime($end);

            if($date < $yesterday){
                $this->Result[] = -19;
            }
        }
        catch(Exception $e){
            $this->Result[] = -20;
        }
    }

    /**
     * Proceso de validación de la coherencia entre las fechas del descuento
     * @param string $start Fecha de inicio del descuento
     * @param string $end Fecha fin del descuento
     */
    private function ValidateStartEnd($start = "", $end = ""){

        if(empty($start) || empty($end)) {
            return;
        }

        try{

            $dStart = new DateTime($start);
            $dEnd = new DateTime($end);
            if($dEnd < $dStart){
                $this->Result[] = -21;
            }
        }
        catch(Exception $e){
            $this->Result[] = -22;
        }
    }

    /**
     * Proceso de validación de las configuraciones del descuento
     * @param array $config
     */
    private function ValidateConfiguration($config = NULL){
        if($config == NULL){
            return;
        }

        if(!is_array($config)){
            $this->Result[] = -23;
            return;
        }

        foreach($config as $item){

        }
    }

    /**
     * Validación de la configuración del descuento
     * @param \DiscountOnConfiguration $config
     * @return boolean
     */
    private function ValidateConfig($config = NULL){
        if($config == NULL ||
                $config instanceof \DiscountOnConfiguration == FALSE){
            return FALSE;
        }

        if(empty($config->DayOfWeek)){
            return FALSE;
        }

        $slot = $this->GetById($this->Aggregate->SlotsOfDelivery,
                $config->SlotOfDelivery);

        if($slot == NULL){
            return FALSE;
        }

        return TRUE;
    }

}


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
 * Capa de servicio para la gestión de eventos
 *
 * @author manager
 */
class EventsServices extends \BaseServices implements \IEventsServices{

    /**
     * Referencia
     * @var \IEventsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IEventsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \EventsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \EventsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = EventsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \EventsAggregate Referencia al agregado actual
     * @return \IEventsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(EventsServices::$_reference == NULL){
            EventsServices::$_reference = new \EventsServices($aggregate);
        }
        return EventsServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotEvent $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDate($entity->Date);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        else if($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del slot
     * @param int $slot Identidad del slot asociado
     */
    private function ValidateSlot($slot = 0){
        if(empty($slot)){
            $this->Result[] = -6;
        }
        else if($slot < 1){
            $this->Result[] = -7;
        }
        else{
            $s = $this->GetById(
                    $this->Aggregate->AvailableSlotsOfDelivery, $slot);
            if($s == NULL){
                $this->Result[] = -8;
            }
        }
    }

    /**
     * Proceso de validación de la fecha asociada al evento
     * @param string $sDate Fecha del evento
     */
    private function ValidateDate($sDate = ""){
        try{
            if(empty($sDate)){
                $this->Result[] = -9;
                return;
            }

            $yesterday = new \DateTime("YESTERDAY");

            $date = new \DateTime($sDate);

            if($date <= $yesterday){
                $this->Result[] = -10;
            }
        }
        catch(Exception $e){
            $this->Result[] = -11;
        }
    }
}


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
 * Implementación de la capa de servicios para la gestión de solicitudes
 *
 * @author manager
 */
class OrderServices extends \BaseServices implements \IOrderServices{

    /**
     * Referencia
     * @var \IOrderServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IOrderRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \OrderAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \OrderAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = OrderRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \OrderAggregate Referencia al agregado actual
     * @return \IOrderServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(OrderServices::$_reference == NULL){
            OrderServices::$_reference = new \OrderServices($aggregate);
        }
        return OrderServices::$_reference;
    }

    /**
     * Proceso para el cálculo del importe sin descuentos
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe
     */
    public function GetAmount($entity = NULL) {
        // Obtener la colección de productos
        $items = $entity->GetRequestItems();
        // Calcular importe
        return $this->CalculateAmount($items);
    }

    /**
     * Proceso para el cálculo del importe total(Aplicado descuento si procede)
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe Total
     */
    public function GetTotal($entity = NULL) {
        // Calcular total sin descuento
        $total = $this->GetAmount($entity);
        // Obtener referencia al descuento
        if(isset($entity->Discount) && $entity->Discount > 0){
            $discounts = $this->GetListByFilter(
                    $this->Aggregate->Discounts, ["Id" => $entity->Discount]);
            if(!empty($discounts)){
                $discount = current($discounts);
                $discount instanceof \DiscountOn;
                $total = $total * (1 - ($discount->Value/100));
            }
        }
        return number_format($total, 2);
    }

    /**
     * Proceso para la generación del Ticket de pedido
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return string Ticket del pedido
     */
    public function GetTicket($entity = NULL) {

        $sProject = "$this->IdProject";
        if($this->IdProject < 10){
            $sProject = "00$this->IdProject-";
        }
        elseif($this->IdProject < 100){
            $sProject = "0$this->IdProject-";
        }
        else{
            $sProject = "$this->IdProject-";
        }

        $requests = $this->Repository->GetByFilter(
                "Request", ["Project" => $this->IdProject]);
        $current = count($requests);

        do{
            $current++;

            $ticket = $sProject.$this->SetTicket($current);

            $requests = $this->Repository->GetByFilter(
                    "Request", ["Ticket" => $ticket]);
        }while (count($requests) > 0);

        return $ticket;
    }

    private function SetTicket($current){

        if($current < 10){
            $sCurrent = "0000$current";
        }
        elseif($current < 100){
            $sCurrent = "000$current";
        }
        elseif($current < 1000){
            $sCurrent = "00$current";
        }
        elseif($current < 10000){
            $sCurrent = "0$current";
        }
        else{
            $sCurrent = "$current";
        }
        return $sCurrent;
    }

    /**
     * Proceso de validación de la solicitud
     * @param \OrderDTO $entity Referencia al pedido
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateTicket($entity->Ticket);
            $this->ValidateName($entity->Name);
            $this->ValidateEmail($entity->Email);
            $this->ValidatePhone($entity->Phone);
            $this->ValidateAddress($entity->Address);
            $this->ValidateDiscount($entity->Discount);
            $this->ValidateDeliveryMethod($entity->DeliveryMethod);
            $this->ValidatePaymentMethod($entity->PaymentMethod);
            $this->ValidateDeliveryDate($entity->DeliveryDate);
            $this->ValidateDeliveryTime($entity->DeliveryTime);
            $this->ValidateItems($entity->Items);
            $this->ValidateRequest($entity);
            $this->ValidatePostCode($entity->PostCode,$entity->DeliveryMethod );
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }



    /**
     * Calcula el importe neto del pedido utilizando la
     * lista de productos asociados
     * @param array $items Colección de productos asociados
     * @return float
     */
    private function CalculateAmount($items = NULL){
        $amount = 0;
        foreach($items as $item){
            $product = $this->GetById($this->Aggregate->Products,
                    $item->Product);
            if($product != NULL){
                $amount += $item->Count * $product->Price;
            }
        }
        return number_format($amount, 2);
    }

    /**
     * Validación del proyecto seleccionad
     * @param int $id
     */
    private function ValidateProject($id = 0){
        if(empty($id) || $id == 0){
            $this->Result[] = -4;
        }
    }

    /**
     * Proceso de validación del ticket de solicitud
     * @param string $ticket ticket de solicitud
     */
    private function ValidateTicket($ticket = ""){
        if(empty($ticket)){
            $this->Result[] = -5;
        }
        else if(strlen($ticket) > 45){
            $this->Result[] = -6;
        }
    }

    /**
     * Validación del nombre de cliente
     * @param string $name Nombre del cliente
     */
    private function ValidateName($name = ""){
        if(empty($name)){
            $this->Result[] = -7;
        }
        else if(strlen($name) > 200){
            $this->Result[] = -8;
        }
    }

    /**
     * Validación del e-mail de contacto
     * @param string $email Dirección de email de contacto
     */
    private function ValidateEmail($email = ""){
        if(empty($email)){
            $this->Result[] = -9;
        }
        else if(strlen($email) > 100){
            $this->Result[] = -10;
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE){
            $this->Result[] = -11;
        }
    }

    /**
     * Validación de la dirección de entrega
     * @param string $addr Dirección de entrega del pedido
     */
    private function ValidateAddress($addr = ""){
        if(empty($addr)){
            $this->Result[] = -12;
        }
        else if(strlen($addr) > 500){
            $this->Result[] = -13;
        }
    }

    /**
     * Validación del descuento asociado (si corresponde)
     * @param int $id Identidad del descuento
     * @return void
     */
    private function ValidateDiscount($id = NULL){

        if($id == NULL){
            return;
        }

        if($id == 0){
            $this->Result[] = -14;
        }
        else{
            $discounts = array_filter($this->Aggregate->Discounts,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($discounts) == 0){
                $this->Result[] = -15;
            }
        }
    }

    /**
     * Validación del método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     */
    private function ValidateDeliveryMethod($id = 0){
        if($id == 0){
            $this->Result[] = -16;
        }
        else{
            $list = array_filter($this->Aggregate->DeliveryMethods,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -17;
            }
        }
    }

    /**
     * Validación del método de pago seleccionado
     * @param int $id Identidad del método de pago
     */
    private function ValidatePaymentMethod($id = 0){
        if($id == 0){
            $this->Result[] = -18;
        }
        else{
            $list = array_filter($this->Aggregate->PaymentMethods,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -19;
            }
        }
    }

    /**
     * Validación de la hora de entrega seleccionada
     * @param int $id Identidad de la hora de entrega
     * @param int $dayOfWeek Identidad del día de la semana
     */
    private function ValidateDeliveryTime($id = 0, $dayOfWeek = 0){
        if($id == 0){
            $this->Result[] = -20;
        }
        else{
            $list = array_filter($this->Aggregate->HoursOfDay,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -21;
            }
        }
    }

    /**
     * Proceso de validación del teléfono de contacto
     * @param string $phone Teléfono asociado
     */
    private function ValidatePhone($phone = ""){
        if(empty($phone)){
            $this->Result[] = -22;
        }
        else if(strlen($phone) > 15){
            $this->Result[] = -23;
        }
    }

    /**
     * Validación de los productos seleccionados
     * @param array $items colección de productos asociados al pedido
     */
    private function ValidateItems($items = NULL){
        if($items == NULL || !is_array($items) || count($items)==0){
            $this->Result[] = -24;
        }
        else{
            $error = FALSE;
            foreach($items as $item){
                $product = $this->GetById($this->Aggregate->Products,
                    $item->Product);
                if($product == NULL){
                    $error = TRUE;
                }
            }
            if($error == TRUE){
                $this->Result[] = -31;
            }
        }
    }

    /**
     * Validación si ya existe un registro de pedido
     * @param \OrderDTO $request Referencia al DTO de pedido
     */
    private function ValidateRequest($request = NULL){
        $filter = [
            "DeliveryDate" => $request->DeliveryDate,
            "DeliveryTime" => $request->DeliveryTime,
            "Email" => $request->Email,
            "Name" => $request->Name,
            "Phone" => $request->Phone,
            "Project" => $request->Project
        ];

        $requests = $this->Repository->GetByFilter("Request", $filter);

        if(is_array($requests) && count($requests) != 0){
            $this->Result[] = -25;
        }
    }

    /**
     * Proceso de validación del código postal asociado
     * @param string $postcode Código postal para el pedido
     * @param int $delivery Tipo de método de entrega
     */
    private function ValidatePostCode($postcode = "", $delivery = 0){
        // Comprobación si el método de entrega no es a domicilio
        if($delivery != 2){
            return;
        }

        if(empty($postcode)){
            $this->Result[] = -26;
        }
        else if(strlen($postcode) > 6){
            $this->Result[] = -27;
        }
        else if(!is_numeric($postcode)){
            $this->Result[] = -28;
        }
        else{
            $postcodes = array_filter($this->Aggregate->PostCodes,
                    function($item) use ($postcode){
                        return $item->Code == $postcode;
                    });
            if(count($postcodes) != 1){
                $this->Result[] = -35;
            }
        }
    }

    /**
     * Proceso de validación del importe neto del pedido
     * @param float $amount Importe del pedido
     * @param array $items Colección de productos asociados
     */
    private function ValidateAmount($amount = 0, $items = NULL){
        if($amount <= 0){
            $this->Result[] = -29;
        }
        else if($items != NULL && is_array($items) && count($items) !=0){
            $temp = $this->CalculateAmount($items);
            if($temp != $amount){
                $this->Result[] = -30;
            }
        }


    }

    /**
     * Proceso de validación del importe TOTAL del pedido
     * @param float $total Importe total del pedido
     * @param float $amount Importe neto del pedido
     * @param int $discount Referencia al descuento aplicable
     */
    private function ValidateTotal($total = 0, $amount = 0, $discount = NULL){
        if($total == 0){
            $this->Result[] = -32;
        }
        else if(($discount == NULL || $discount == 0)
                && ($total != $amount)){
            $this->Result[] = -33;
        }
        else {
            $disc = $this->GetById($this->Aggregate->Discounts, $discount);
            $temp = 0;
            if($disc != NULL){
                $temp = $amount * (1 - ($disc->Value/100));
            }
            if($temp != $total){
                $this->Result[] = -34;
            }
        }
    }

    /**
     * Validación de la fecha de entrega
     * @param \DateTime $date Referencia a la fecha de entrega
     */
    private function ValidateDeliveryDate($date = NULL){

    }
}

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
 * Implementación de la capa de servicios para la gestión de productos
 */
class ProductsServices extends \BaseServices implements \IProductsServices{

    /**
     * Referencia
     * @var \IProductsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IProductsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \ProductsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \ProductsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = ProductsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ProductsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(ProductsServices::$_reference == NULL){
            ProductsServices::$_reference = new \ProductsServices($aggregate);
        }
        return ProductsServices::$_reference;
    }

    /**
     * Obtiene el link de un producto a partir de su nombre
     * @param string $name Nombre del producto
     * @return string
     * @throws Exception Excepción generada si el nombre
     * de producto no es válido
     */
    public function GetLinkProduct($name = ""){
        if(empty($name)){
            return str_replace(" ", "-",
                    strtolower(urlencode(trim($name))));
        }
        throw new Exception("GetLinkProduct: El nombre de producto "
                . "no puede ser una cadena vacía");
    }

    /**
     * Proceso de validación del producto
     * @param \Product $entity
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateDesc($entity->Description);
            $this->ValidateKeywords($entity->Keywords);
            $this->ValidateCategory($entity->Category);
            $this->ValidateReference($entity->Id, $entity->Reference);
            $this->ValidatePrice($entity->Price);
            $this->ValidateOrd($entity->Ord);
            $this->ValidateAttr($entity->Attr);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validacion de la imagen
     * @param \Image $image Referencia al objeto imagen a crear
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function ValidateImage($image = NULL){
        if($image != NULL){
            $this->ValidateImageProduct($image->Product);
            $this->ValidateImageName($image->Id, $image->Name);
            $this->ValidateImageDescription($image->Description);
            $this->ValidateImagePath($image->Path);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del nombre de producto
     * @param int $id Identidad del producto
     * @param string $name Nombre del producto
     */
    private function ValidateName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -4;
        }
        elseif(strlen($name) > 100){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateExistsName($id, $name);
        }
    }

    /**
     * Validación del nombre de producto por si es único o no
     * @param string $name Nombre del producto
     */
    private function ValidateExistsName($id = 0, $name = ""){
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];

        $items = $this->GetListByFilter(
                    $this->Aggregate->Products, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * Proceso de validación de la descripción
     * @param string $description Descripción del producto
     */
    private function ValidateDesc($description = ""){
        if(empty($description)){
            $this->Result[] = -7;
        }
        elseif(strlen($description) > 140){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación de los keywords de producto
     * @param string $keywords Keywords introducidos
     */
    private function ValidateKeywords($keywords = ""){
        if(empty($keywords)){
            $this->Result[] = -9;
        }
        elseif(strlen($keywords) > 140){
            $this->Result[] = -10;
        }
    }

    /**
     * Proceso de validación de la categoría
     * @param int $category Identidad de la categoría seleccionada
     */
    private function ValidateCategory($category = 0){
        $filter = [ "Project" => $this->IdProject,
            "Id" => $category, "State" => 1 ];
        $items = $this->GetListByFilter(
                    $this->Aggregate->Categories, $filter);
        if(empty($items) || count($items) == 0 ){
            $this->Result[] = -11;
        }
    }

    /**
     * Proceso de validación de la referencia de producto
     * @param int $id Identidad del producto
     * @param string $reference Referencia asignada al producto
     */
    private function ValidateReference($id = 0, $reference = ""){
        if(empty($reference)){
            $this->Result[] = -12;
        }
        elseif(strlen($reference) > 20){
            $this->Result[] = -13;
        }
        else{
            $this->ValidateExistsReference($id, $reference);
        }
    }

    /**
     * Validación clave Unique de la referencia de producto
     * @param int $id Identidad del producto
     * @param string $reference Referencia asignada al producto
     */
    private function ValidateExistsReference($id = 0, $reference = ""){
        $filter = [ "Project" => $this->IdProject,
            "Reference" => $reference, "State" => 1 ];

        $items = $this->GetListByFilter(
                    $this->Aggregate->Products, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -14;
        }
    }

    /**
     * Proceso de validación del precio de producto
     * @param string $price Precio asignado al producto
     */
    private function ValidatePrice($price = ""){
        if(empty($price)){
            $this->Result[] = -15;
        }
        elseif(!is_numeric($price)){
            $this->Result[] = -16;
        }
    }

    /**
     * Proceso de validación del orden de producto
     * @param string $ord Criterio de orden asignado al producto
     */
    private function ValidateOrd($ord = ""){
        if(empty($ord)){
            $this->Result[] = -17;
        }
        elseif(!is_numeric($ord)){
            $this->Result[] = -18;
        }
    }

    /**
     * Proceso de validación de los atributos del producto
     * @param string $attr Cadena de atributos (Serialización JSON)
     */
    private function ValidateAttr($attr = ""){
        if(empty($attr)){
            $this->Result[] = -19;
        }
    }

    /**
     * Proceso de validación para el atributo producto
     * @param int $id Identidad del producto
     */
    private function ValidateImageProduct($id = 0){
        $product = $this->GetById($this->Aggregate->Products, $id);
        if($product == NULL){
            $this->Result[] = -10;
        }
    }

    /**
     * Proceso de validación del nombre de la imágen
     * @param int $id Identidad de la imagen
     * @param string $name Nombre de la imagen
     */
    private function ValidateImageName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -4;
        }
        elseif(strlen($name) > 45){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateImageExistsName($id, $name);
        }
    }

    /**
     * Proceso de validación del nombre. Comprueba si ya
     * existe una imagen con el mismo nombre
     * @param int $id Identidad de la imagen
     * @param string $name Nombre de la imagen
     */
    private function ValidateImageExistsName($id = 0, $name = ""){
        $filter = [ "Name" => $name, "State" => 1 ];
        $items = $this->GetListByFilter(
                    $this->Aggregate->Images, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * Proceso de validación de la descripción de la imagen
     * @param type $description
     */
    private function ValidateImageDescription($description = ""){
        if(empty($description)){
            $this->Result[] = -7;
        }
        elseif(strlen($description) > 200){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación del path de fichero
     * @param type $path
     */
    private function ValidateImagePath($path = ""){
        if(empty($path)){
            $this->Result[] = -9;
        }
    }
}

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
 * Capa de servicio para la gestión de solicitudes/pedidos
 *
 * @author manager
 */
class RequestsServices extends \BaseServices implements \IRequestsServices{

    /**
     * Referencia
     * @var \IRequestsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IRequestsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \RequestsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \RequestsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = RequestsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(RequestsServices::$_reference == NULL){
            RequestsServices::$_reference = new \RequestsServices($aggregate);
        }
        return RequestsServices::$_reference;
    }

    /**
     * Proceso de validación de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return boolean
     */
    public function Validate($request = NULL){
        return TRUE;
    }

    /**
     * Proceso de validación en el cambio de estado de una solicitud
     * @param int $current Identidad del estado actual
     * @param int $next Identidad del estado próximo
     * @return boolean
     */
    public function ValidateChangeState($current = 0, $next = 0){
        return TRUE;
    }
}

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
 * Capa de servicios para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryServices extends \BaseServices
    implements \ISlotsOfDeliveryServices{

    /**
     * Referencia
     * @var \ISlotsOfDeliveryServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \ISlotsOfDeliveryRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \SlotsOfDeliveryAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \SlotsOfDeliveryAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = SlotsOfDeliveryRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(SlotsOfDeliveryServices::$_reference == NULL){
            SlotsOfDeliveryServices::$_reference =
                    new \SlotsOfDeliveryServices($aggregate);
        }
        return SlotsOfDeliveryServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotOfDelivery $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateStart($entity->Id, $entity->Start);
            $this->ValidateEnd($entity->Id, $entity->End);
            $this->ValidateStartEnd($entity->Start, $entity->End);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        else if($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del nombre para el turno de reparto
     * @param int $id Identidad del turno si existe
     * @param string $name Nombre asignado al turno
     */
    private function ValidateName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -6;
        }
        elseif(strlen($name) > 45){
            $this->Result[] = -7;
        }
        else{
            $this->ValidateExistName($id, $name);
        }
    }

    /**
     * Proceso de validación para comprobar si el nombre ya está registrado
     * @param int $id Identidad del turno de reparto
     * @param string $name Nombre asignado al turno
     */
    private function ValidateExistName($id = 0, $name = ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter($this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación de la hora de inicio del turno
     * @param int $id Identidad del turno si ya existe
     * @param int $start Identidad de la hora de inicio del turno
     */
    private function ValidateStart($id = 0, $start = 0){
        if(empty($start)){
            $this->Result[] = -9;
        }
        elseif(!$this->ValidateExistHour($start)){
            $this->Result[] = -10;
        }
        else{
            $this->ValidateExistStart($id, $start);
        }
    }

    /**
     * Proceso de validación para comprobar si ya existe un turno activo con
     * la misma hora de inicio
     * @param int $id Identidad del registro de turno si ya existe
     * @param int $start Identidad de la hora de inicio del turno
     */
    private function ValidateExistStart($id = 0, $start = 0){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Start" => $start, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -12;
        }
    }

    /**
     * Proceso de validación de la hora de finalización del turno de reparto
     * @param int $id Identidad del turno de reparto
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateEnd($id = 0, $end = 0){
        if(empty($end)){
            $this->Result[] = -13;
        }
        elseif(!$this->ValidateExistHour($end)){
            $this->Result[] = -14;
        }
        else{
            $this->ValidateExistEnd($id, $end);
        }
    }

    /**
     * Proceso de validación para comprobar si ya existe un turno activo con
     * la misma hora de finalización
     * @param int $id Identidad del turno si ya existe
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateExistEnd($id = 0, $end = 0){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "End" => $end, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -16;
        }
    }

    /**
     * Proceso de validación sobre el orden de las horas deliminates del turno
     * @param int $start Identidad de la hora de inicio del turno
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateStartEnd($start = 0, $end = 0){
        if(empty($start) || empty($end)){
            $this->Result[] = -17;
        }
        else if(!$this->CompareHour($start, $end)){
            $this->Result[] = -18;
        }
    }

    /**
     * Proceso de validación en el que se comprueba que existe
     * el registro de hora identificado
     * @param int $id Identidad del registro de hora
     */
    private function ValidateExistHour($id = 0){
        $hour = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($id){
            return $item->Id == $id;
        });
        return count($hour) == 1;
    }

    /**
     * Proceso de validación para comprobar que la hora de inicio es menor
     * que la hora de finalización
     * @param int $iStart Identidad de la hora de inicio
     * @param int $iEnd Identidad de la hora de finalización
     * @return boolean
     */
    private function CompareHour($iStart = 0, $iEnd = 0){
        $sHours = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($iStart){return $item->Id == $iStart;});
        $start = current($sHours);

        $eHours = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($iEnd){return $item->Id == $iEnd;});
        $end = current($eHours);

        // Partimos las cadenas con format [hh:mm] por ":"
        $aStart = explode(":", $start->Text);
        $aEnd = explode(":", $end->Text);
        // Obtener horas
        $hStart = intval($aStart[0]);
        $hEnd = intval($aEnd[0]);
        // Proceso de comparación
        if($hStart > $hEnd){
            return FALSE;
        }
        else if($hStart == $hEnd){
            // comparar minutos
            if(intval($aStart[1]) >= intval($aEnd[1])){
                return FALSE;
            }
        }
        return TRUE;
    }
}


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
 * Capa de servicio para la configuración del proyecto
 *
 * @author alfonso
 */
class ConfigurationServices  extends \BaseServices
    implements \IConfigurationServices{

    /**
     * Referencia
     * @var \IConfigurationServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IConfigurationRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \ConfigurationAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \ConfigurationAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = ConfigurationRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Proceso de validación de la información del proyecto para la impresión
     * de tickets
     * @param \ProjectInformation $dto Referencia a la información del proyecto
     * @return TRUE|array Colección de códigos de validación
     */
    public function ValidateInfo($dto = NULL){
        if($dto != NULL){
            $this->ValidateTitleInfo($dto->Title);
            $this->ValidateCIFInfo($dto->CIF);
            $this->ValidateAddressInfo($dto->Address);
            $this->ValidatePhoneInfo($dto->Phone);
            $this->ValidateEmailInfo($dto->Email);
        }
        else{
            $this->Result[] = -1;
        }

        return count($this->Result) == 0 ? TRUE : $this->Result;
    }

    public function ValidateTitleInfo($title = ""){
        if(empty($title)){
            $this->Result[] = -2;
        }
    }

    public function ValidateCIFInfo($cif = ""){
        if(empty($cif)){
            $this->Result[] = -3;
        }
        else if(strlen($cif) > 15){
            $this->Result[] = -4;
        }
    }

    public function ValidateAddressInfo($address = ""){
        if(empty($address)){
            $this->Result[] = -5;
        }
    }

    public function ValidatePhoneInfo($phone = ""){
        if(empty($phone)){
            $this->Result[] = -6;
        }
        else if(strlen($phone) > 15){
            $this->Result[] = -7;
        }
    }

    public function ValidateEmailInfo($email = ""){
        if(empty($email)){
            $this->Result[] = -8;
        }
        else if(strlen($email) > 200){
            $this->Result[] = -9;
        }
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \IConfigurationServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(ConfigurationServices::$_reference == NULL){
            ConfigurationServices::$_reference = new \ConfigurationServices($aggregate);
        }
        return ConfigurationServices::$_reference;
    }
}


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
 * Agregado para la configuración de línea base
 *
 * @author manager
 */
class BaseLineAggregate extends \BaseAggregate{

    /**
     * Referencia al Slot actual
     * @var \SlotConfigured
     */
    public $Slot = NULL;

    /**
     * Coleccion de Slots configurados
     * @var array
     */
    public $Slots = [];

    /**
     * Coleccion de turnos de reparto
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Coleccion de turnos de reparto activos
     * @var array
     */
    public $AvailableSlotsOfDelivery = [];

    /**
     * Coleccion de dias de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotConfigured();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {
        $this->AvailableSlotsOfDelivery =
                array_filter($this->SlotsOfDelivery,function($item){
                   return $item->State == 1;
                });
    }
}


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
 * Agregado para la gestión de Categorias
 */
class CategoriesAggregate extends \BaseAggregate{

    /**
     * Referencia a la categoría cargada
     * @var \Category
     */
    public $Category = NULL;

    /**
     * Colección de categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Category = new \Category();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }
}

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
 * Agregado para la gestion de descuentos
 *
 * @author manager
 */
class DiscountsAggregate extends \BaseAggregate{

    /**
     * Referencia al descuento en edición
     * @var \DiscountDTO
     */
    public $Discount = NULL;

    /**
     * Colección de DTOs de descuentos activos
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de días de la semana registrados
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de turnos de reparto establecidos
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Discount = new \DiscountDTO();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}


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
 * Agregado para la gestión de eventos
 *
 * @author manager
 */
class EventsAggregate extends \BaseAggregate{

    /**
     * Colección de días de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Referencia al evento actual
     * @var \SlotEvent
     */
    public $Event = NULL;

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de slots configurados
     * @var array
     */
    public $BaseLine = [];

    /**
     * Colección de turnos de reparto registrados
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Colección de turnos de reparto activos
     * @var array
     */
    public $AvailableSlotsOfDelivery = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Event = new \SlotEvent();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {
        $this->AvailableSlotsOfDelivery =
                array_filter($this->SlotsOfDelivery,function($item){
                   return $item->State == 1;
                });
    }
}


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
 * Agregado para la gestión de solicitudes
 * @author manager
 */
class OrderAggregate extends \BaseAggregate{

    /**
     * Colección de categorías disponibles
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de productos disponibles
     * @var array
     */
    public $Products = [];

    /**
     * Colección de Slots
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de horas disponibles
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Métodos de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Métodos de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de turnos de reparto existentes
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Colección de códigos postales asociados
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de descuentos configurados
     * @var array
     */
    public $Discounts = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}


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
 * Agregado para la gestión de productos
 */
class ProductsAggregate extends \BaseAggregate{

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de las categorías disponibles
     * @var array
     */
    public $AvailableCategories = [];

    /**
     * Referencia al producto cargado
     * @var \Product
     */
    public $Product = NULL;

    /**
     * Colección de imagenes del producto seleccionado
     * @var array
     */
    public $Images = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Product = new \Product();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {
        $this->AvailableCategories = array_filter($this->Categories,
                function ($item) {
                    return $item->State == 1;
            });
    }
}



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
 * Agregado para la gestión de solicitudes/pedidos
 */
class RequestsAggregate extends \BaseAggregate{

    /**
     * Colección de horas del día
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Colección de descuentos disponibles
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de estados del flujo de trabajo registrados
     * @var array
     */
    public $States = [];

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Referencia a la solicitud cargada
     * @var \Request
     */
    public $Request = NULL;

    /**
     * Referencia a la información del proyecto para impresión
     * @var \ProjectInformation
     */
    public $ProjectInformation = NULL;

    /**
     * Colección de Productos parametrizados en la solicitud
     * @var array
     */
    public $Items = [];

    /**
     * Colección de solicitudes disponibles
     * @var array
     */
    public $Requests = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Request = new \Request();
        $this->ProjectInformation = new \ProjectInformation();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }
}

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
 * Argegado para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryAggregate extends \BaseAggregate{

    /**
     * Colección de horas disponibles en base de datos
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Referencia al Turno de reparto actual
     * @var \SlotOfDelivery
     */
    public $Slot = NULL;

    /**
     * Colección de turnos de reparto registrados
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de turnos activos
     * @var array
     */
    public $AvailableSlots = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotOfDelivery();
    }

    /**
     * Configuración de agregados
     */
    public function SetAggregate() {
        $this->AvailableSlots =
                array_filter($this->Slots, function($item){
                   return $item->State == 1;
                });
    }
}


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
 * Agregado para la configuración de proyecto
 *
 * @author alfonso
 */
class ConfigurationAggregate extends \BaseAggregate{

    /**
     * Referencia a la entidad de registro de información de proyecto
     * @var \ProjectInformation
     */
    public $ProjectInfo = NULL;

    /**
     * Colección de formas de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de formas de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Colección de códigos postales disponibles
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de formas de entrega configurados
     * @var array
     */
    public $AvailableDeliveryMethods = [];

    /**
     * Colección de formas de pago configurados
     * @var array
     */
    public $AvailablePaymentMethods = [];

    /**
     * Colección de códigos postales configurados
     * @var array
     */
    public $AvailablePostCodes = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->ProjectInfo = new \ProjectInformation();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {

    }
}

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
 * Entidad categoría
 */
class Category{

    /**
     * Identidad de la categoría
     * @var int
     */
    public $Id=0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Identidad de la categoría padre si existe
     * @var int
     */
    public $Parent = NULL;

    /**
     * Código asociado a la categoría
     * @var string
     */
    public $Code = "";

    /**
     * Nombre o denominación de la categoría
     * @var string
     */
    public $Name = "";

    /**
     * Descripción informativa de la categoría
     * @var string
     */
    public $Description = "";

    /**
     * Definición de los atributos que caracterizan a una categoría
     * @var xml
     */
    public $Xml = "";

    /**
     * Estado lógico de la categoría
     * @var boolean
     */
    public $State = 1;

    /**
     * Link de búsqueda para tener un URL friendly
     * @var string
     */
    public $Link = "";
}

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
 * Entidad comentario. Representa el comentario de un usuario
 * respecto al producto con el que está relacionado
 */
class Comment{

    /**
     * Identidad del comentario
     * @var int
     */
    public $Id=0;

    /**
     * Referencia al producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Título del comentario
     * @var string
     */
    public $Title="";

    /**
     * Texto del comentario
     * @var string
     */
    public $Text="";

    /**
     * Autor / Usuario que realiza el comentario
     * @var string
     */
    public $Author="";

    /**
     * Fecha en la que se realiza el comentario
     * @var string
     */
    public $Date=null;

    /**
     * Cantidad de votos positivos
     * @var int
     */
    public $Likes=0;

    /**
     * Cantidad de votos negativos
     * @var int
     */
    public $Unlikes=0;

    /**
     * Estado del comentario
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct() {
        $date = new DateTime();
        $this->Date = $date->format( 'Y-m-d H:i:s' );
    }
}

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
 * Día de la semana
 */
class DayOfWeek{

    /**
     * Identidad del resgitro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del día
     * @var type
     */
    public $Name = "";

    /**
     * Abreviatura del día
     * @var type
     */
    public $ShortName = "";

    /**
     * Nombre del icono utilizado (si es necesario)
     * @var string
     */
    public $IcoName = "";

    /**
     * Número de día de la semana [1 - 7]
     * @var int
     */
    public $DayOfWeek = 0;
}

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
 * Métodos de entrega del pedido
 */
class DeliveryMethod {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del método de entrega
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del método de entrega
     * @var string
     */
    public $Description = "";

    /**
     * Términos generales (opcional)
     * @var string
     */
    public $Terms = "";

    /**
     * Nombre del icono a utilizar(si procede)
     * @var type
     */
    public $IcoName = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}

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
 * Descuento especificado sobre el precio de un producto para el proyecto y
 * servicio especificado. El descuento es aplicable cuando un precio
 * está entre el valor mínimo ( x >= MinValue ) y el valor máximo ( x < MaxValue)
 */
class DiscountOn{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public $Service = 0;

    /**
     * Porcentaje de descuento
     * @var int
     */
    public $Value = 0;

    /**
     * Valor mínimo aplicable
     * @var int
     */
    public $Min = 0;

    /**
     * Valor máximo aplicable
     * @var int
     */
    public $Max = 0;

    /**
     * Fecha de inicio del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de fin del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $End = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}

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
 * Configuración del día de la semana y franja horaria para el cual
 * un descuento es válido.
 */
class DiscountOnConfiguration{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del descuento asociado
     * @var int
     */
    public $DiscountOn = 0;

    /**
     * Identidad del día de la semana en que es válido
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad de la franja horaria en que es válido
     * @var int
     */
    public $SlotOfDelivery = 0;
}

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
 * Entidad para registrar un evento de apertura o cierre sobre un descuento
 *
 * @author alfonso
 */
class DiscountOnEvent {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Identidad del descuento asociado
     * @var int
     */
    public $DiscountOn = 0;

    /**
     * Identidad de la franja de reparto asociada
     * @var int
     */
    public $SlotOfDelivery = 0;

    /**
     * Fecha del evento
     * @var string
     */
    public $Date = "";

    /**
     * Anyo del evento
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del anyo
     * @var int
     */
    public $Week = 0;

    /**
     * Día de la semana asociado
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Estado del descuento: Abierto o cerrado
     * @var int
     */
    public $State = 0;
}

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
 * Entidad Histórico. Se utiliza para almacenar cualquier cambio
 * realizado sobre un producto
 */
class HistProduct{

    /**
     * Identidad del historico
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto modificado
     * @var int
     */
    public $Product=0;

    /**
     * Serialización JSON del producto
     * @var type
     */
    public $Json="";

    /**
     * Fecha en la que se realiza la modificación
     * @var string
     */
    public $Date=null;

    /**
     * Constructor
     * @param \Product $product Referencia al producto modificado
     */
    public function __construct($product = null){
        if($product != null
                && !is_array($product)
                && is_object($product)){
            $date = new DateTime();
            $this->Product = $product->Id;
            $this->Json = json_encode($product);
            $this->Date = $date->format( 'Y-m-d H:i:s' );
        }
    }
}

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
 * Hora del día
 */
class HourOfDay{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Texto a visualizar para la hora, p.e. : "11:00"
     * @var string
     */
    public $Text = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}

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
 * Entidad Imagen. Representa una imagen asociada a un producto
 */
class Image{

    /**
     * Identidad de la imagen
     * @var int Id
     */
    public $Id = 0;

    /**
     * Identidad del producto padre
     * @var int Id  del producto al que está asociado
     */
    public $Product = 0;

    /**
     * Nombre asignado a la imagen
     * @var string Nombre de producto
     */
    public $Name = "";

    /**
     * Descripción de la imagen
     * @var string Descripción
     */
    public $Description = "";

    /**
     * Ruta de acceso al fichero de imagen
     * @var string Ruta física
     */
    public $Path = "";

    /**
     * Fecha asociada a la imagen
     * @var string Fecha de imagen
     */
    public $Date = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}

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
 * Entidad Likes. Representa los votos positivos de un producto
 */
class Likes{

    /**
     * Identidad del Like
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de votos positivos
     * @var int
     */
    public $Count=0;

}

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
 * Entidad Log. Representa la cantidad de visitas que recibe un mismo
 * producto.
 */
class Log{

    /**
     * Identidad del log en base de datos
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de visitas recibidas
     * @var int
     */
    public $Count=0;

}

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
 * Entidad para el seguimiento de solicitudes http del website
 */
class PageLog{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id=0;

    /**
     * Dirección IP desde donde se realiza la solicitud
     * @var string
     */
    public $IP="";

    /**
     * Url solicitada
     * @var string
     */
    public $Url="";

    /**
     * Fecha de la última actualización
     * @var string
     */
    public $Date=null;

    /**
     * Cantidad de veces que se ha realizado la misma solicitud
     * @var int
     */
    public $Count=0;
}

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
 * Medios o formas de pago.
 */
class PaymentMethod{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado al medio de pago
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del medio de mago
     * @var string
     */
    public $Description = "";

    /**
     * Abreviatura del nombre asignado
     * @var string
     */
    public $ShortName = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}

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
 * Entidad para el registro de códigos postales en la tabla maestra
 *
 * @author alfonso
 */
class PostCode {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Código postal
     * @var string
     */
    public $Code = "";

    /**
     * Descripción y/o Notas
     * @var string
     */
    public $Description = "";

}

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
 * Entidad Producto
 */
class Product{

    /**
     * Identidad del producto
     * @var int Id
     */
    public $Id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Referencia a la categoría
     * @var int Id de la categoría
     */
    public $Category = 0;

    /**
     * Referencia de catalogación del producto
     * @var string Referencia
     */
    public $Reference = "";

    /**
     * Nompre del producto
     * @var string Nombre
     */
    public $Name = "";

    /**
     * Texto del enlace utilizado al cargar la ficha de producto
     * @var string Url friendly
     */
    public $Link = "";

    /**
     * Descripción del producto utilizada en la ficha
     * @var string Descripción
     */
    public $Description = "";

    /**
     * Terminos clave asociados a caracterizar el producto
     * @var string keywords
     */
    public $Keywords = "";

    /**
     * Precio del producto
     * @var float Precio
     */
    public $Price = 0;

    /**
     * Serialización de los atributos que caracterizan el producto
     * @var string Atributos jSon
     */
    public $Attr = "";

    /**
     * Valoración para la ordenación de los productos
     * @var int Orden
     */
    public $Ord = 0;

    /**
     * Estado lógico del producto
     * @var boolean Estado actual
     */
    public $State = 1;

    /**
     * Estado de visibilidad del producto en el catálogo
     * @var boolean
     */
    public $Visible = 1;
}

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
 * Registro de suscripción de usuario a las noticias
 * de un producto
 */
class ProductSuscriber{

     /**
     * Identidad del registro de suscripción
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto al que se asocia el usuario
     * @var int
     */
    public $Product=0;

    /**
     * Nombre del suscriptor
     * @var string
     */
    public $SuscriberName="";

    /**
     * Dirección de email del suscriptor
     * @var string
     */
    public $Email="";

    /**
     * Dirección IP desde donde se genera la suscripción
     * @var type
     */
    public $IP="";

    /**
     * Fecha en la que se genera la suscripción
     * @var string
     */
    public $CreateDate = null;

    /**
     * Fecha en la que se solicita la baja
     * @var string
     */
    public $DeleteDate = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime();
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
        $this->DeleteDate = $date->format( 'Y-m-d H:i:s' );
    }

}

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
 * Solicitud de información de productos.
 */
class Request{

    /**
     * Identidad del registro de solicitud
     * @var type
     */
    public $Id=0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Ticket generado para la solicitud
     * @var string
     */
    public $Ticket="";

    /**
     * Nombre del solicitante
     * @var string
     */
    public $Name="";

    /**
     * Email de contacto del solicitante
     * @var string
     */
    public $Email="";

    /**
     * Dirección física del solicitante
     * @var string
     */
    public $Address="";

    /**
     * Dirección IP desde donde se realiza la solicitud
     * @var string
     */
    public $IP="";

    /**
     * Fecha en la que se realiza la solicitud
     * @var string
     */
    public $Date=null;

    /**
     * Estado de workflow de la solicitud
     * @var int?
     */
    public $WorkFlow=null;

    /**
     * Estado lógico de la solicitud
     * @var boolean
     */
    public $State=1;

    /**
     * Referencia al descuento asociado
     * @var int?
     */
    public $Discount = NULL;

    /**
     * Referencia al método de entrega seleccionado
     * @var int
     */
    public $DeliveryMethod = 0;

    /**
     * Referencia al método de pago seleccionado
     * @var int
     */
    public $PaymentMethod = 0;

    /**
     * Fecha de entrega seleccionada
     * @var Fecha de entrega seleccionada
     */
    public $DeliveryDate = NULL;

    /**
     * Referencia a la hora de entrega seleccionada
     * @var int
     */
    public $DeliveryTime = 0;

    /**
     * Flag sobre la política de publicidad
     * @var bool
     */
    public $Advertising = FALSE;

    /**
     * Teléfono de contacto
     * @var string
     */
    public $Phone = "";

    /**
     * Código postal
     * @var string
     */
    public $PostCode = "";

    /**
     * Importe del pedido
     * @var float
     */
    public $Amount = 0;

    /**
     * Importe total aplicado el descuento(si procede)
     * @var float
     */
    public $Total = 0;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new \DateTime();
        $this->Date = $date->format( 'Y-m-d H:i:s' );
    }
}

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
 * Registro de producto en una solicitud de información
 */
class RequestItem{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id=0;

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Request=0;

    /**
     * Identidad del producto seleccionado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad solicitada
     * @var int
     */
    public $Count=0;

    /**
     * Observaciones/Opciones del producto
     * @var string
     */
    public $Data = "";
}

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
 * Registro de configuración del método de recogida|entrega para el servicio
 * y proyecto especificado
 */
class ServiceDeliveryMethod{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public $Service = 0;

    /**
     * Identidad del método de entrega|recogida
     * @var int
     */
    public $DeliveryMethod = 0;
}

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
 * Registro de configuración del método de pago para el servicio
 * y proyecto especificado
 */
class ServicePaymentMethod{

    /**
     * Identidad del registro de configuración
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del Servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Identidad del método|forma de pago
     * @var int
     */
    public $PaymentMethod = 0;
}

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
 * Entidad relacional para asociar códigos postales a
 * un proyecto y servicio
 *
 * @author manager
 */
class ServicePostCode {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Referencia al servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Referencia al código postal
     * @var int
     */
    public $Code = 0;

    /**
     * Flag indicación si incluye el código postal completo
     * @var boolean
     */
    public $Full = FALSE;
}


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
 * Configuración de la franja de servicio de un proyecto
 */
class SlotConfigured{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad de la franja horaria configurada
     * @var int
     */
    public $SlotOfDelivery = 0;
}



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
 * Evento en la franja horaria de servicio. Permite "abrir" una franja
 * de servicio no configurada en una fecha específica o cerrar una
 * franja configurada en una fecha dada.
 */
class SlotEvent{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad de la franja horaria configurada
     * @var int
     */
    public $SlotOfDelivery = 0;

    /**
     * Fecha del evento en formato yyyy-mm-dd
     * @var string
     */
    public $Date = "";

    /**
     * Tipo de evento Apertura o cierre.
     * @var boolean
     */
    public $Open = 0;
}

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
 * Franja horaria del servicio para un proyecto
 */
class SlotOfDelivery {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Nombre asignado a la franja horaria
     * @var string
     */
    public $Name = "";

    /**
     * Hora de inicio de la franja horaria
     * @var string
     */
    public $Start = "";

    /**
     * Hora de finalización de la franja horaria
     * @var string
     */
    public $End = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Estado del registro
     * @var boolean
     */
    public $State = 1;
}

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
 * Suscriptor a la lista de noticias de la web
 */
class Suscriber{

    /**
     * Identidad del suscriptor
     * @var int
     */
    public $Id=0;

    /**
     * Nombre del suscriptor
     * @var string
     */
    public $SuscriberName="";

    /**
     * Dirección de email del suscriptor
     * @var string
     */
    public $Email="";

    /**
     * Dirección IP desde donde se genera el registro
     * @var string
     */
    public $IP="";

    /**
     * Fecha de creación del registro
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Estado de la suscripción
     * @var boolean
     */
    public $Active=0;

    /**
     * Fecha de baja del registro
     * @var string
     */
    public $DeleteDate=NULL;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime("NOW");
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
    }

}

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
 * Estado del flujo de procesado de solicitud
 */
class WorkFlow{

    /**
     * Identidad del estado de workflow
     * @var int
     */
    public $Id=0;

    /**
     * Nombre del estado
     * @var string
     */
    public $Name="";

    /**
     * Descripción funcional del estado
     * @var string
     */
    public $Description="";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;
}

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
 * Entidad para gestionar la información del proyecto relativa a la
 * impresión de tickets de venta
 *
 * @author alfonso
 */
class ProjectInformation {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Título a utilizar en los ticket
     * @var string
     */
    public $Title = "";

    /**
     * Código de identificación fiscal
     * @var string
     */
    public $CIF = "";

    /**
     * Dirección física del proyecto
     * @var string
     */
    public $Address = "";

    /**
     * Número de teléfono del proyecto
     * @var string
     */
    public $Phone = "";

    /**
     * Email de contacto del proyecto
     * @var string
     */
    public $Email = "";
}


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
 * DTO para agregar Likes y UnLikes de un comentario
 */
class CommentLikeDTO{

    /**
     * Identidad del comentario asociado
     * @var integer
     */
    public $Comment = 0;

    /**
     * Cantiddad de Likes que tiene
     * @var int
     */
    public $Likes = 0;

    /**
     * Cantidad de unlikes que tiene el comentario
     * @var int
     */
    public $Unlikes = 0;

}

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
 * Description of DeliveryMethodDTO
 *
 * @author manager
 */
class DeliveryMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del método de entrega
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del método de entrega
     * @var string
     */
    public $Description = "";

    /**
     * Términos generales (opcional)
     * @var string
     */
    public $Terms = "";

    /**
     * Nombre del icono a utilizar(si procede)
     * @var type
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio actual
     * @var int
     */
    public $Service = 0;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}

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
 * DTO resumen para la gestión de descuentos. Contiene la información
 * del registro de descuento y su configuración
 *
 * @author manager
 */
class DiscountDTO extends \DiscountOn{

    /**
     * Colección de configuraciones disponibles para el descuento
     * @var array
     */
    public $Configuration = [];

    /**
     * Constructor
     * @param \DiscountOn $discountOn Referencia al descuento
     * @param \DiscountOnConfiugration $configuration
     * Colección de configuraciones
     */
    public function __construct($discountOn = NULL,
            $configuration = NULL){
        if($discountOn != NULL){
            $this->Id = $discountOn->Id;
            $this->Value = $discountOn->Value;
            $this->Project = $discountOn->Project;
            $this->Service = $discountOn->Service;
            $this->Max = $discountOn->Max;
            $this->Min = $discountOn->Min;
            $this->Start = $discountOn->Start;
            $this->End = $discountOn->End;
            $this->State = $discountOn->State;
        }

        if(is_array($configuration)){
            $this->Configuration = $configuration;
        }
    }

    /**
     * Obtiene una referencia a la entidad descuento
     * @return \DiscountOn Instancia del descuento
     */
    public function GetDiscountOn(){
        $discount = new \DiscountOn();
        $discount->Id = $this->Id;
        $discount->Value = $this->Value;
        $discount->Project = $this->Project;
        $discount->Service = $this->Service;
        $discount->Max = $this->Max;
        $discount->Min = $this->Min;
        $discount->Start = $this->Start;
        $discount->End = $this->End;
        $discount->State = $this->State;
        return $discount;
    }
}


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
 * Description of OrderDTO
 *
 * @author manager
 */
class OrderDTO extends \Request {

    /**
     * Colección de productos asociados al pedido
     * @var array
     */
    public $Items = [];

    /**
     * Constructor
     * @param \Request $request
     */
    public function __construct($request = NULL) {

        parent::__construct();

        $this->SetRequest($request);
    }

    /**
     * Obtiene una instancia de la solicitud con la información del DTO
     * @return \Request
     */
    public function GetRequest(){
        $request = new \Request();
        $request->Address = $this->Address;
        $request->Advertising = ($this->Advertising == TRUE);
        $request->Date = $this->Date;
        $request->DeliveryDate = $this->DeliveryDate;
        $request->DeliveryMethod = $this->DeliveryMethod;
        $request->DeliveryTime = $this->DeliveryTime;
        $request->Discount = $this->Discount;
        $request->Email = $this->Email;
        $request->IP = $this->IP;
        $request->Name = $this->Name;
        $request->PaymentMethod = $this->PaymentMethod;
        $request->Phone = $this->Phone;
        $request->Project = $this->Project;
        $request->Ticket = $this->Ticket;
        $request->State = $this->State;
        $request->WorkFlow = $this->WorkFlow;
        $request->Id = $this->Id;
        $request->Total = $this->Total;
        $request->Amount = $this->Amount;
        $request->PostCode = $this->PostCode;
        return $request;
    }

    /**
     * Obtiene la colección de productos asociados a la solicitud
     * @param int $id Identidad de la solicitud
     * @return \RequestItem
     */
    public function GetRequestItems($id = 0){
        $items = [];
        if(is_array($this->Items)){
            foreach($this->Items as $item){
                $o = new \RequestItem();
                $o->Id = $item->Id;
                $o->Request = $item->Request;
                $o->Product = $item->Product;
                $o->Data = $item->Data;
                $o->Count = $item->Count;
                if($id > 0){
                    $o->Request = $id;
                }
                $items[] = $o;
            }
        }
        return $items;
    }

    /**
     * Establece las propiedades heredadas de una solicitud
     * @param \Request $request
     */
    public function SetRequest($request = NULL){
        if($request != NULL && is_object($request)){
            $this->Address = $request->Address;
            $this->Advertising = ($request->Advertising == TRUE);
            $this->Date = $request->Date;
            $this->DeliveryDate = $request->DeliveryDate;
            $this->DeliveryMethod = $request->DeliveryMethod;
            $this->DeliveryTime = $request->DeliveryTime;
            $this->Discount = $request->Discount;
            $this->Email = $request->Email;
            $this->IP = $request->IP;
            $this->Name = $request->Name;
            $this->PaymentMethod = $request->PaymentMethod;
            $this->Phone = $request->Phone;
            $this->Project = $request->Project;
            $this->Ticket = $request->Ticket;
            $this->State = $request->State;
            $this->WorkFlow = $request->WorkFlow;
            $this->Id = $request->Id;
            $this->Amount= $request->Amount;
            $this->Total = $request->Total;
            $this->PostCode = $request->PostCode;
        }
    }

}


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
 * Description of PaymentMethodDTO
 *
 * @author manager
 */
class PaymentMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado al medio de pago
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del medio de mago
     * @var string
     */
    public $Description = "";

    /**
     * Abreviatura del nombre asignado
     * @var string
     */
    public $ShortName = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio actual
     * @var int
     */
    public $Service = 0;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}


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
 * DTO para obtener la información del código postal asociado
 *
 * @author alfonso
 */
class PostCodeDTO{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Referencia al servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Referencia al código postal
     * @var int
     */
    public $Code = 0;

    /**
     * Código postal
     * @var string
     */
    public $PostCode = "";

    /**
     * Flag indicación si incluye el código postal completo
     * @var boolean
     */
    public $Full = FALSE;

    /**
     * Descripción
     * @var string
     */
    public $Description;
}


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
 * DTO resumen con la informacion de un producto
 */
class ProductDTO{

    /**
     * Identidad del producto
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al producto
     * @var \Product
     */
    public $Product = NULL;

    /**
     * Coleccion de imagenes asociadas al producto
     * @var array
     */
    public $Gallery = [];

    /**
     *
     * @var type
     */
    public $Likes = [];

    /**
     * Coleccion de registros de actividad
     * @var type
     */
    public $Logs = [];

    /**
     * Coleccion de comentarios asociados al producto
     * @var array
     */
    public $Comments = [];

    /**
     * Constructor de la clase
     * @param int Identidad del producto
     */
    public function __construct($id = 0){
        $this->Id = $id;
    }
}

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
 * DTO para la información de solicitudes
 */
class RequestDTO{

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia a la solicitud
     * @var \Request
     */
    public $Request = null;

    /**
     * Colección de productos asociados a la solicitud
     * @var array
     */
    public $Items = [];

}

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
 * DTO para las notificaciones de pedidos
 *
 * @author alfonso
 */
class RequestNotificationDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * Dirección del cliente
     * @var string
     */
    public $Address = "";

    /**
     * Correo electrónico del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono de contacto
     * @var string
     */
    public $Phone = "";

    /**
     * Ticket de la solicitud
     * @var string
     */
    public $Ticket = "";

    /**
     * Importe sin descuento
     * @var float
     */
    public $Amount = "";

    /**
     * Importe con descuento
     * @var float
     */
    public $Total = "";

    /**
     * Descuento asociado al pedido
     * @var string
     */
    public $Discount = "";

    /**
     * Método de pago seleccionado
     * @var string
     */
    public $PaymentMethod = "";

    /**
     * Método de entrega seleccionado
     * @var string
     */
    public $DeliveryMethod = "";

    /**
     * Hora de entrega seleccionada
     * @var string
     */
    public $DeliveryTime = "";

    /**
     * Fecha de entrega seleccionada
     * @var string
     */
    public $DeliveryDate = "";

    /**
     * Colección de productos del pedido
     * @var array
     */
    public $Items = [];
}


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
 * Description of RequestItemNotificationDTO
 *
 * @author alfonso
 */
class RequestItemNotificationDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id=0;

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Request=0;

    /**
     * Identidad del producto seleccionado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad solicitada
     * @var int
     */
    public $Count=0;

    /**
     * Observaciones/Opciones del producto
     * @var string
     */
    public $Data = "";

    /**
     * Nombre del producto
     * @var string
     */
    public $Name = "";

    /**
     * Precio del producto asociado
     * @var float
     */
    public $Price = 0;

}


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
 * Description of SlotDTO
 *
 * @author manager
 */
class SlotDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado a la franja horaria
     * @var string
     */
    public $Name = "";

    /**
     * Hora de inicio de la franja horaria
     * @var string
     */
    public $Start = "";

    /**
     * Hora de finalización de la franja horaria
     * @var string
     */
    public $End = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Estado del registro
     * @var boolean
     */
    public $State = 1;

}
 ?>
