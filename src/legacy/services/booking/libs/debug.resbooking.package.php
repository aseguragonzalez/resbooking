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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para Reservas
 *
 * @author alfonso
 */
interface IBookingManagement{

    /**
     * Registro de la reserva
     * @param \Booking $entity Referencia a la entidad
     * @param boolean $saveClient Validación registrar los datos del cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Resultado de la operación
     */
    public function RegisterBooking($entity = NULL,
            $saveClient = FALSE, $sendNotification = TRUE);

    /**
     * Actualización de la información de una reserva
     * @param int $id Identida de la reserva a modificar
     * @param string $propName Nombre de la propiedad
     * que se desea actualizar
     * @return int Código de operación :
     *   0 : La operación de ha ejecutado correctamente.
     *  -1 : No se ha encontrado la reserva por su Id
     *  -2 : Se ha producido un error durante la actualización
     */
    public function SavePropertyBooking($id = 0,
            $propName = "", $propValue = NULL);

    /**
     * Proceso de anulación de la reserva
     * @param int $id Identidad de la reserva
     * @param int $state Identidad del estado de cancelación
     * @return int Código de operación :
     *   0 => La operación se ha ejecutado correctamente
     *  -1 => La reserva no ha sido encontrada
     *  -2 => La reserva no se ha podido actualizar
     *  -3 => La notificación no se ha podido generar
     *  -4 => No se ha encontrado el estado identificado por id
     */
    public function CancelBooking($id = 0, $state = 0);

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @param string $sDate Fecha para la que se solicita la información
     * @return \BookingAgregate
     */
    public function GetAggregate($sDate = "");

    /**
     * Obtiene la informción de una reserva a partir de su identidad
     * @param int $id Identidad de la reserva
     * @return \Booking Referencia encontrada
     */
    public function GetBookingById($id = 0);

    /**
     * Obtiene la colección de reservas filtradas por fecha
     * @param string $sDate Fecha de las reservas
     * @return array Colección de reservas disponibles
     */
    public function GetBookingsByDate($sDate = "");

    /**
     * Obtiene la colección de reservas utilizando el filtro
     * pasado como argumento
     * @param array $filter Filtro de búsqueda
     * @return array Colección de reservas encontradas
     */
    public function GetBookingsByFilter($filter = NULL);

    /**
     * Obtiene una instancia del Management de reservas
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingManagement
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
 * Implementación del gestor de la capa de aplicación para Reservas
 *
 * @author alfonso
 */
class BookingManagement extends \BaseManagement implements \IBookingManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \BookingServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IBookingRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al Management de reservas
     * @var \IBookingManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Cargar Constructor padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = BookingRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = BookingServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @param string $sDate Fecha para la que se solicita la información
     * @return \BookingAgregate
     */
    public function GetAggregate($sDate = ""){

        $this->Aggregate->SetAggregate($sDate);

        return $this->Aggregate;
    }

    /**
     * Obtiene la instancia actual del Management de reservas
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BookingManagement::$_reference == NULL){
           BookingManagement::$_reference =
                   new \BookingManagement($project, $service);
        }
        return BookingManagement::$_reference;
    }

    /**
     * Registro de una reserva
     * @param \Booking $entity Referencia a la entidad
     * @param boolean $saveClient Validación registrar los datos del cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Resultado de la operación
     */
    public function RegisterBooking($entity = NULL,
            $saveClient = FALSE, $sendNotification = TRUE){
        $entity->Project = $this->IdProject;
        $result = $this->Services->Validate($entity);
        if(!is_array($result) && $result == TRUE ){
            $exist = $this->Services->Exist($entity);
            if($exist == FALSE){
                $result = $this->CreateBooking($entity,
                        $saveClient, $sendNotification);
            }
            else{
                $result = [1];
            }
        }
        return $result;
    }

    /**
     * Actualización de la información de una reserva
     * @param int $id Identida de la reserva a modificar
     * @param string $propName Nombre de la propiedad
     * que se desea actualizar
     * @return int Código de operación :
     *   0 : La operación de ha ejecutado correctamente.
     *  -1 : No se ha encontrado la reserva por su Id
     *  -2 : Se ha producido un error durante la actualización
     */
    public function SavePropertyBooking($id = 0,
            $propName = "", $propValue = NULL){
        // Código de operación
        $result = -1;
        // Obtener referencia a la entidad buscada
        $entity = $this->Repository->Read( "Booking", $id);
        // Comprobar que se ha encontrad
        if($entity != NULL){
            // Proceso de actualización de la reserva
            $result = $this->UpdatePropertyBooking($entity, $propName, $propValue);
        }
        return $result;
    }

    /**
     * Proceso de anulación de la reserva
     * @param int $id Identidad de la reserva
     * @param int $state Identidad del estado de cancelación
     * @return int Código de operación :
     *   0 => La operación se ha ejecutado correctamente
     *  -1 => La reserva no ha sido encontrada
     *  -2 => La reserva no se ha podido actualizar
     *  -3 => La notificación no se ha podido generar
     *  -4 => No se ha encontrado el estado identificado por id
     */
    public function CancelBooking($id = 0, $state = 0){
        // Código de operación
        $result = -1;
        // Proceso de validación del estado
        $valState = $this->Services->ValidateState($state);
        // Obtener referencia a la entidad buscada
        $entity = $this->Repository->Read( "Booking", $id);
        // Comprobar que se ha encontrad
        if($entity != NULL && $valState == TRUE){
            // Validar el estado actual de la reserva
            if($entity->State == $state){ return 0; }
            // Actualización
            if($this->UpdatePropertyBooking($entity, "State", $state) == 0){
                // Obtener el asunto de la notificación
                $subject = ConfigurationManager::GetKey("mailCancel");
                // Generar la notificación
                $not = $this->Repository->CreateNotification($entity, $subject);
                // Asignar el resultado de la operación
                $result = ($not == FALSE) ? -3 : 0;
            }
            else{
                $result = -2;
            }
        }
        else if($valState == FALSE){
            $result = -4;
        }
        return $result;
    }

    /**
     * Obtiene la informción de una reserva a partir de la identidad
     * de la misma
     * @param int $id Identidad de la reserva
     * @return \Booking Referencia encontrada
     */
    public function GetBookingById($id = 0){
        return $this->Repository->Read( "Booking", $id);
    }

    /**
     * Obtiene la colección de reservas filtradas por fecha
     * @param string $sDate Fecha de las reservas
     * @return array Colección de reservas disponibles
     */
    public function GetBookingsByDate($sDate = ""){
        // Establecer el filtro de búsqueda
        $filter = [ "Project" => $this->IdProject, "Date" => $sDate ];
        // Obtener todas las reservas
        return $this->Repository->GetByFilter( "Booking", $filter);
    }

    /**
     * Obtiene la colección de reservas utilizando el filtro especificado
     * @param array $filter Filtro de búsqueda
     * @return array Colección de reservas encontradas
     */
    public function GetBookingsByFilter($filter = NULL){
        // Si el filtro no se ha definido, lo definimos
        // y establecemos el proyecto sobre el que se buscan las
        // reservas
        if($filter == NULL){
            $filter = [ "Project" => $this->IdProject ];
        }
        else{
            $filter["Project"] = $this->IdProject;
        }
        // Obtener todas las reservas
        return $this->Repository->GetByFilter( "Booking", $filter);
    }

    /**
     * Generar el registro de la reserva y de la actividad en base de datos
     * @param \Booking $entity Referencia a la reserva
     * @param boolean $saveClient Indica si se deben guardar los datos de cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Códigos de operación
     */
    private function CreateBooking($entity = NULL,
            $saveClient = FALSE, $sendNotification = TRUE){
        $log = $this->Services->GetActivity($entity);
        $this->Repository->Create($log);
        $entity->Client = $this->Repository->GetClient($entity, $saveClient);
        $booking = $this->Repository->Create($entity);
        if($booking != FALSE ){
            $this->Aggregate->Booking = $booking;

            $subject = ConfigurationManager::GetKey("mailSubject");

            if(class_exists("ZapperDAL")){
                $zapper = new \ZapperDAL();
                $required = $zapper->RequiredPrePay($booking->Project,
                        ($booking->Offer == null) ? 0 : $booking->Offer,
                        $booking->Diners, $booking->Date);
                if($required){
                    $booking = $zapper->RegisterZapperBooking($booking);
                    $this->Aggregate->Booking = $booking;
                    $subject = "zapper-booking";
                }
            }

            if($sendNotification){
                $this->Repository->CreateNotification($booking, $subject);
            }

            return [1];
        }
        else{
            return [0];
        }
    }

    /**
     * Procedimiento para actualizar una propiedad una reserva creando
     * un registro de histórico
     * @param \Booking $entity Referencia a la reserva a actualizar
     * @param string $propertyName Nombre de la propiedad que se actualiza
     * @param object $propertyValue Valor asignado a la propiedad
     * @return int Código de operación :
     *   0 => La operación de ha ejecutado correctamente
     *  -1 => La referencia a la reserva es null
     *  -2 => No se ha definido la propiedad a actualizar
     */
    private function UpdatePropertyBooking($entity = NULL,
            $propertyName = "", $propertyValue = NULL){
        $return = -1;
        if($entity != NULL){
            if($propertyName != ""){
                // Crear Histórico
                $hist = new \BookingBck($entity);
                // Guardar el histórico
                $this->Repository->Create($hist);
                // Actualizar la propiedad
                $entity->{$propertyName} = $propertyValue;
                // Actualización de la reserva
                $this->Repository->Update($entity);
                // Resultado de la operación
                $return = 0;
            }
            else{
                $return = -2;
            }
        }
        return $return;
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
 * Interfaz de la capa de infraestructura para reservas
 *
 * @author alfonso
 */
interface IBookingRepository {

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingRepository
     */
    public static function GetInstance($project = 0, $service = 0);

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseAggregate
     */
    public function GetAggregate($project = 0, $service = 0);

    /**
     * Obtiene la referencia a la entidad cliente de la reserva
     * @param \Booking $entity Referencia a la reserva actual
     * @param boolean $advertising Flag para indicar si el cliente quiere publicidad
     * @return int Identidad del cliente
     */
    public function GetClient($entity = NULL, $advertising = FALSE);

    /**
     * Genera el registro de notificación de una reserva
     * @param \Booking $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($entity = NULL, $subject = "");
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
 * Implementación para la capa de infraestructura en la gestión de reservas
 *
 * @author alfonso
 */
class BookingRepository extends \BaseRepository implements \IBookingRepository{

    /**
     * Referencia a la clase base
     * @var \BookingRepository
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
     * @return \BookingRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BookingRepository::$_reference == NULL){
            BookingRepository::$_reference =
                    new \BookingRepository($project, $service);
        }
        return BookingRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseAggregate
     */
    public function GetAggregate($project = 0, $service = 0) {
        // Instanciar agregado
        $agg = new \BookingAggregate($project, $service);
        // Información del proyecto
        $agg->Project = $this->Read("Project", $project);
        // Tablas maestras
        $agg->States = $this->Get("State");
        $agg->Turns = $this->Get("Turn");
        $agg->Slots = $this->Get("Slot");
        $agg->BookingSources = $this->Get("BookingSource");
        // Información filtrada por proyecto
        $filter = [ "Project" => $project ];

        $configs = $this->GetByFilter("ConfigurationService" , $filter);
        $agg->Configuration = (empty($configs))
                ? new \ConfigurationService() : $configs[0];

        $agg->Places = $this->GetByFilter("Place" , $filter);
        $agg->Blocks = $this->GetByFilter("Block" , $filter);
        $agg->Configurations = $this->GetByFilter("Configuration", $filter);
        $agg->Offers = $this->GetByFilter( "Offer" , $filter);
        $agg->OffersEvents = $this->GetByFilter("OfferEvent" , $filter);
        foreach($agg->Offers as $offer){
            $filtroOferta = ["Offer" => $offer->Id];
            $offer->Config =  $this->GetByFilter("OfferConfig", $filtroOferta);
        }

        $agg->OffersShare = $this->GetByFilter("OfferShareDTO", $filter);

        $agg->TurnsShare = $this->GetByFilter("TurnShareDTO", $filter);

        $agg->SetAggregate();

        return $agg;
    }

    /**
     * Obtiene la referencia a la entidad cliente de la reserva
     * @param \Booking $entity Referencia a la reserva actual
     * @param boolean $advertising Flag para indicar si el cliente quiere publicidad
     * @return int Identidad del cliente
     */
    public function GetClient($entity = NULL, $advertising = FALSE){
        $filter = ["Project" => $entity->Project ];
        // Buscar el registro de cliente
        if(empty($entity->Email)){
            $filter["Phone"] = "%$entity->Phone%";
        }
        else{
            $filter["Email"] = "%$entity->Email%";
        }
        $clients = $this->Dao->GetByFilter( "Client", $filter);
        $client = (empty($clients)) ? NULL : $clients[0];
        // Crear el registro si no existe
        if($client == NULL){
            $client = new \Client();
            $client->Project = $entity->Project;
            $client->Name = $entity->ClientName;
            $client->Email = $entity->Email;
            $client->Phone = $entity->Phone;
            $client->Advertising = $advertising;
            $client->Id = $this->Dao->Create($client);
        }
        else{
            if($client->Advertising == FALSE){
                $client->Advertising = $advertising;
                $this->Dao->Update($client);
            }
        }
        return $client->Id;
    }

    /**
     * Genera el registro de notificación de una reserva
     * @param \Booking $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($entity = NULL, $subject = ""){
        if($entity != NULL) {
            $bookingDTO = $this->Read("BookingNotificationDTO", $entity->Id);
            if($bookingDTO != NULL){
                $date = new \DateTime($bookingDTO->Date);
                $bookingDTO->ClientName = $bookingDTO->Name;
                $bookingDTO->Date = strftime("%A %d de %B del %Y",$date->getTimestamp());
                $bookingDTO->Turn = $bookingDTO->Start;
                $bookingDTO->Offer = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Title : "Sin oferta";
                $bookingDTO->OfferTerms = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Terms : "";
                $bookingDTO->OfferDesc = (!empty($bookingDTO->Title))
                        ? $bookingDTO->Description : "";
                return $this->RegisterNotification($bookingDTO, $subject);
            }
        }
        return FALSE;
    }

    /**
     * Crea el registro de la notificación con la información de
     * la reserva y la tipología indicada.
     * @param \BookingNotificationDTO $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    private function RegisterNotification($entity = NULL, $subject = ""){
        if($entity != NULL && is_object($entity)){
            $date = new \DateTime( "NOW" );
            $dto = new \Notification();
            $dto->Project = $this->IdProject;
            $dto->Service = $this->IdService;
            $dto->To = $entity->Email;
            $dto->Subject = $subject;
            $dto->Date = $date->format( "y-m-d h:i:s" );

            $entity->Ticket = $this->GetTicket($dto->To, $entity);
            $dto->Content = json_encode($entity);
            $this->Dao->Create( $dto );

            $dto->To = "";
            $entity->Ticket = $this->GetTicket("", $entity);
            $dto->Content = json_encode($entity);
            $this->Dao->Create( $dto );
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Obtiene un ticket de validación para las notificaciones
     * @param string $user Destinatario de Ticket
     * @param \BookingNotificationDTO $dto Referencia a la reserva
     * @return string Ticket generado
     */
    private function GetTicket($user = "", $dto = ""){
        // Establecer el destinatario de la notificación
        if($user == ""){ $user = "admin"; }
        // Array de parámetros del ticket
        $arr = ["User" => $user, "Project" => $dto->Project, "Id" => $dto->Id ];
        // Serialización de la información del ticket
        $text = json_encode($arr);
        // cifrado del ti
        return $this->fnEncrypt($text, "resbooking2015");
    }

    /**
     * Método para cifrar el texto pasado como argumento con la clave especificada
     * @param string $sValue Texto plano
     * @param string $sSecretKey clave de cifrado
     * @return string
     */
    private function fnEncrypt($sValue, $sSecretKey){
        return base64_encode($sValue);
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
 * Interfaz para el gestor de servicios del dominio
 *
 * @author alfonso
 */
interface IBookingServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Comprobación sobre la existencia de la reserva solicitada
     * @param \Booking $entity Referencia a la reserva a registrar
     * @return boolean Resultado de la comprobación. TRUE si la reserva
     * ya está registrada. FALSE en caso contrario
     */
    public function Exist($entity = NULL);

    /**
     * Obtiene una instancia para el registro de actividad
     * @param \Booking $entity Referencia a la reserva
     * @return \Log
     */
    public function GetActivity($entity = NULL);

    /**
     * Proceso de validación de la entidad Reserva
     * @param \Booking $entity Referencia a los datos de reserva
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso de validación del estado de la reserva
     * @param int $id Identidad del estado a validar
     * @return boolean Resultado de la validación del estado
     */
    public function ValidateState($id = 0);
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
 * Capa de servicios para la gestión de entidades reserva
 *
 * @author alfonso
 */
class BookingServices extends \BaseServices implements \IBookingServices{

    /**
     * Referencia
     * @var \BookingServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IBookingRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \BookingAggregate
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

        parent::__construct($aggregate);

        $this->Repository = BookingRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = NULL) {
        if(BookingServices::$_reference == NULL){
            BookingServices::$_reference =
                    new \BookingServices($aggregate);
        }
        return BookingServices::$_reference;
    }

    /**
     * Comprobación sobre la existencia de la reserva solicitada
     * @param \Booking $entity Referencia a la reserva a registrar
     * @return boolean Resultado de la comprobación. TRUE si la reserva
     * ya está registrada. FALSE en caso contrario
     */
    public function Exist($entity = NULL){
        $filter = [ "Project" => $entity->Project, "Turn" => $entity->Turn,
                "Date" => $entity->Date, "Diners" => $entity->Diners,
                "Email" => "%".$entity->Email."%", "Phone" => "%".$entity->Phone."%",
                "Offer" => $entity->Offer, "Place" => $entity->Place ];
        $reservas = $this->Repository->GetByFilter( "Booking" , $filter );
        return !empty($reservas);
    }

    /**
     * Obtiene una instancia para el registro de actividad
     * @param \Booking $entity Referencia a la reserva
     * @return \Log
     */
    public function GetActivity($entity = NULL){
        $info = [ "REQUEST" => $_REQUEST, "Entity" => $entity];
        $date = new \DateTime( "NOW" );
        $log = new \Log();
        $log->Booking = $entity->Id;
        $log->Address = $_SERVER["REMOTE_ADDR"];
        $log->Information = json_encode($info);
        $log->Date = $date->format( "Y-m-d" );
        return $log;
    }

    /**
     * Proceso de validación de la entidad Reserva
     * @param \Booking $entity Referencia a los datos de reserva
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        $this->Result = [];
        $this->ValidateClientName($entity->ClientName);
        $this->ValidateDate($entity->Date);
        $this->ValidateDiners($entity->Diners);
        $this->ValidateEmail($entity->Email);
        $this->ValidatePhone($entity->Phone);
        $this->ValidatePlace($entity->Place);
        $this->ValidateTurn($entity->Turn, $entity->Date);
        $this->ValidateOffer($entity->Offer,
                $entity->Turn, $entity->Date);
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del estado de la reserva
     * @param int $id Identidad del estado a validar
     * @return boolean Resultado de la validación del estado
     */
    public function ValidateState($id = 0){
        // Referencia al estado de reserva
        $state = $this->GetById($this->Aggregate->States, $id);
        // Validación
        return ($state != NULL);
    }

    /**
     * Validación del nombre del cliente
     * @param string $name Nombre del cliente
     */
    private function ValidateClientName($name=""){
        if(empty($name)){
            $this->Result[] = -1;
        }
        else if(!is_string($name)){
            $this->Result[] = -2;
        }
        else if(strlen($name) > 100){
            $this->Result[] = -3;
        }
    }

    /**
     * Proceso de validación de e-mail
     * @param string $email email del cliente
     */
    private function ValidateEmail($email = ""){
        if(empty($email)){
            //$this->Result[] = -4;
        }
        else if(strlen($email) > 100){
            $this->Result[] = -6;
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del número de teléfono
     * @param string $phone Teléfono del cliente
     */
    private function ValidatePhone($phone = ""){
        if(empty($phone)){
            $this->Result[] = -7;
        }
        else if(!is_string($phone)){
            $this->Result[] = -8;
        }
        else if(strlen($phone) > 15){
            $this->Result[] = -9;
        }
    }

    /**
     * Proceso de validación del número de comensales
     * @param int $diners Cantidad de comensales
     */
    private function ValidateDiners($diners = 0){
        if(empty($diners)){
            $this->Result[] = -10;
        }
        else if(is_numeric($diners)=== FALSE){
            $this->Result[] = -11;
        }
        else if($diners > $this->Aggregate->MaxDiners){
            $this->Result[] = -12;
        }
        else if($diners < $this->Aggregate->MinDiners){
            $this->Result[] = -13;
        }
    }

    /**
     * Proceso de validación de la fecha de reserva
     * @param string $sDate Fecha de la validación
     */
    private function ValidateDate($sDate = ""){
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]"
                . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";

        if(empty($sDate)){
            $this->Result[] = -14;
        }
        else if(preg_match($regex, $sDate) != 1){
            $this->Result[] = -15;
        }
        else{
            try{
                $date = new \DateTime($sDate);
                $yesterday = new \DateTime( "YESTERDAY" );
                if($date <=$yesterday){
                    $this->Result[] = -16;
                }
            }catch(Exception $e){
                $this->Result[] = -15;
            }
        }
    }

    /**
     * Validación del Espacio. Comprueba que el espacio está
     * asociado al proyecto actual
     * @param int $place Identidad del Espacio|Lugar
     */
    private function ValidatePlace($place = 0){
        if(empty($place)){
            $this->Result[] = -17;
        }
        else if(!is_numeric($place)){
            $this->Result[] = -18;
        }
        else{
            $filter = ["Project" =>
                $this->Aggregate->IdProject,"Id" => $place ];
            $places = $this->GetListByFilter(
                    $this->Aggregate->Places, $filter);
            if(empty($places)){
                $this->Result[] = -19;
            }
        }
    }

    /**
     * Validación del Turno. Comprueba que el turno está asociado
     * al proyecto actual para la fecha dada(date)
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     */
    private function ValidateTurn($turn = 0, $sDate = ""){
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]"
                . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";
        if(empty($turn)){
            $this->Result[] = -20;
        }
        else if(!is_numeric($turn)){
            $this->Result[] = -21;
        }
        else if(!empty($sDate) && preg_match($regex, $sDate) == 1){
            if($this->TurnIsBlock($turn, $sDate)){
                $this->Result[] = -22;
            }
            else if(!$this->TurnIsOpen($turn, $sDate)
                    && !$this->TurnIsConfig($turn, $sDate)){
                $this->Result[] = -23;
            }
            else if(!$this->TurnIsAlive($turn, $sDate)){
                $this->Result[] = -27;
            }
            else if(!$this->ValidateTurnShare($turn, $sDate)){
                $this->Result[] = -28;
            }
        }
    }

    /**
     * Filtro para validar los turnos activos por la hora de reserva
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean resultado de la validación
     */
    private function TurnIsAlive($id = 0, $sDate = ""){
        // Comprobar si es necesario validar el turno
        $date = new \DateTime($sDate);
        $current = new \DateTime("NOW");
        $validar = (intval($date->format("d")) == intval($current->format("d")))
                && (intval($date->format("m")) == intval($current->format("m")))
                && (intval($date->format("Y")) == intval($current->format("Y")));
        if($validar == FALSE){ return TRUE;}
        // Proceso de validación del turno
        $turn = $this->GetById($this->Aggregate->Turns, $id);
        if($turn != NULL && $turn instanceof \Turn){
            $start = substr($turn->Start, 0, 5);
            $startParts = explode(":", $start);
            $H = intval($current->format("H"));
            $h = intval($startParts[0]);
            if( $H < $h){
                return TRUE;
            }
            else if($H == $h){
                $M = intval($current->format("i")) + 20;
                $m = intval($startParts[1]);
                return $M < $m;
            }
        }
        return FALSE;
    }

    /**
     * Comprueba si el turno está bloqueado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsBlock($turn = 0, $sDate =""){
        $blocksFilter = ["Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 1];
        $blocks = $this->GetListByFilter(
                $this->Aggregate->Blocks, $blocksFilter);
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está "abierto" en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsOpen($turn = 0, $sDate =""){
        $blocksFilter = [ "Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 0];
        $blocks = $this->GetListByFilter(
                $this->Aggregate->Blocks, $blocksFilter);
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está configurado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsConfig($turn = 0, $sDate =""){
        $date = new \DateTime($sDate);
        $dayOfWeek = $date->format( "N" );
        $filter = ["Project" => $this->IdProject,
            "Day" => $dayOfWeek, "Turn" => $turn ];
        $configs = $this->GetListByFilter(
                $this->Aggregate->Configurations, $filter);
        return !empty($configs);
    }

    /**
     * Proceso para validar la cuota del turno
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la validación
     */
    private function ValidateTurnShare($id = 0, $sDate = ""){
        $filter = [ "Project" => $this->IdProject,
            "Turn" => $id, "Date" => $sDate ];
        $shares = $this->GetListByFilter($this->Aggregate->TurnsShare, $filter);
        $filterShares = array_filter($shares, function($item){
           return $item->DinersFree <= 0;
        });
        return empty($filterShares);
    }

    /**
     * Proceso de validación de la oferta seleccionada
     * @param int $offer Identidad de la oferta seleccionada
     * @param int $turn Identidad del turno seleccionado
     * @param string $sDate Fecha de la reserva
     */
    private function ValidateOffer($offer = 0, $turn = 0, $sDate = "" ){
        if($offer > 0){
            $off = $this->GetById($this->Aggregate->Offers, $offer);
            if($off == NULL){
                $this->Result[] = -24;
            }
            // Comprobamos si la oferta está abierta
            else if($this->OfferIsOpen($offer, $turn, $sDate) == TRUE){
                return;
            }
            // Comprobamos si la oferta está cerrada
            else if($this->OfferIsClose($offer, $turn, $sDate) == TRUE){
                $this->Result[] = -26;
            }
            else if(!$this->ValidateOfferDates($off, $sDate)){
                $this->Result[] = -25;
            }
            else if(!$this->ValidateOfferConfig($off, $turn, $sDate)){
                $this->Result[] = -26;
            }
            else if(!$this->ValidateOfferShare($off, $turn, $sDate)){
                $this->Result[] = -29;
            }
        }
    }

    /**
     * Proceso de validación de oferta de configuración
     * @param \Offer $offer Referencia a la oferta seleccionada
     * @param int $idturn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean
     */
    private function ValidateOfferConfig($offer = NULL, $idturn = 0, $sDate = ""){
        if($offer != NULL) {
            $date = new \DateTime($sDate);
            $dayOfWeek = $date->format("N");
            $filter = [ "Turn" => $idturn, "Day" => $dayOfWeek ];
            $configs = json_decode($offer->Config);
            if($configs == FALSE){
                $configs = [];
            }
            return !empty($this->GetListByFilter($configs, $filter));
        }
        return FALSE;
    }

    /**
     * Validación de las fechas de la oferta
     * @param \Offer $offer Referencia al objeto oferta
     * @param string $sDate Referencia a la fecha
     * @return boolean Resultado de la comprobación
     */
    private function ValidateOfferDates($offer = NULL, $sDate = ""){
        // Instanciar fecha
        $date = new \DateTime($sDate);

        $start = (isset($offer->Start)
                && $offer->Start != ""
                && $offer->Start != "0000-00-00 00:00:00")
                ? new DateTime($offer->Start) : NULL;

        $end = (isset($offer->End)
                && $offer->End != ""
                && $offer->End != "0000-00-00 00:00:00")
                ? new DateTime($offer->End) : NULL;

        $cmp_ok_1 = ($start == NULL
                || ($start != NULL && $date >= $start));

        $cmp_ok_2 = ($end == NULL
                || ($end != NULL && $date <= $end));

        return ($cmp_ok_1 && $cmp_ok_2);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Abierta"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsOpen($id = 0, $turn = 0, $sDate = ""){
        $filter = ["Project" => $this->IdProject, "Offer" => $id,
                "Turn" => $turn, "Date" => $sDate, "State" => 1];
        $events = $this->GetListByFilter(
                $this->Aggregate->AvailableOffersEvents, $filter);
        return empty($events);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Cerrada"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsClose($id = 0, $turn = 0, $sDate = ""){
       $filter = ["Project" => $this->IdProject, "Offer" => $id,
                "Turn" => $turn, "Date" => $sDate, "State" => 0];
        $events = $this->GetListByFilter(
                $this->Aggregate->AvailableOffersEvents, $filter);
        return empty($events);
    }

    /**
     * Proceso de validación del cupo de oferta
     * @param int $id Identidad de la oferta
     * @param int $idTurn Identidad del turno
     * @param string $sDate Fecha de reserva
     * @return boolean Resultado de la validación
     */
    private function ValidateOfferShare($id = 0, $idTurn = 0, $sDate = ""){
        $turn = $this->GetById($this->Aggregate->Turns, $idTurn);
        $filterShares = [];
        if($turn != NULL){
            $filter = [ "Project" => $this->IdProject, "Offer" => $id,
                "Slot" => $turn->Slot, "Date" => $sDate ];
            $shares = $this->GetListByFilter($this->Aggregate->OffersShare, $filter);
            $filterShares = array_filter($shares, function($item){
               return $item->DinersFree <= 0;
            });
        }
        return empty($filterShares);
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
 * Agregado para la gestión de Reserva
 *
 * @author alfonso
 */
class BookingAggregate extends \BaseAggregate{

    /**
     * Colección de fuentes de reserva registradas
     * @var array
     */
    public $BookingSources = [];

    /**
     * Colección de estados posibles de reserva
     * @var array
     */
    public $States = [];

    /**
     * Colección de turnos registrados
     * @var array
     */
    public $Turns = [];

    /**
     * Colección de cupos de turnos
     * @var array
     */
    public $TurnsShare = [];

    /**
     * Colección de Slots|Franjas registradas
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de Espacios registrados en el proyecto
     * @var array
     */
    public $Places = [];

    /**
     * Colección de Ofertas registradas en el proyecto
     * @var array
     */
    public $Offers = [];

    /**
     * Colección de eventos de ofertas actuales
     * @var array
     */
    public $OffersEvents = [];

    /**
     * Colección de cuotas asociadas a ofertas
     * @var array
     */
    public $OffersShare = [];

    /**
     * Colección de Bloqueos registrados en el proyecto
     * @var array
     */
    public $Blocks = [];

    /**
     * Colección de configuraciones de turnos registradas en el proyecto
     * @var array
     */
    public $Configurations = [];

    /**
     * Espacios disponibles para la reserva
     * @var array
     */
    public $AvailablePlaces = [];

    /**
     * Colección de ofertas disponibles
     * @var array
     */
    public $AvailableOffers = [];

    /**
     * Colección de eventos de ofertas disponibles
     * @var array
     */
    public $AvailableOffersEvents = [];

    /**
     * Colección de bloqueos disponibles
     * @var array
     */
    public $AvailableBlocks = [];

    /**
     * Colección de turnos configurados
     * @var array
     */
    public $AvailableTurns = [];

    /**
     * Referencia a la entidad cliente
     * @var \Client
     */
    public $Client = NULL;

    /**
     * Referencia a la entidad reserva
     * @var \Booking
     */
    public $Booking = NULL;

    /**
     * Referencia a la configuración del servicio
     * @var \ConfigurationService
     */
    public $Configuration = NULL;

    /**
     * Cantidad mínima de comensales
     * @var int
     */
    public $MinDiners = 1;

    /**
     * Cantidad máxima de comensales
     * @var int
     */
    public $MaxDiners = 25;

    /**
     * Fecha de solicitud
     * @var \DateTime
     */
    public $Date = NULL;

    /**
     * Constructor
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
        $this->Booking = new \Booking();
        $this->Client = new \Client();
        $this->Configuration = new \ConfigurationService();
    }

    /**
     * Configuración de las propiedades filtrando por fecha
     * @param string $sDate
     */
    public function SetAggregate($sDate = ""){
        $this->Date = ($sDate != "") ?
                new \DateTime($sDate) : new \DateTime("NOW");
        $this->MaxDiners = $this->Configuration->MaxDiners;
        $this->MinDiners = $this->Configuration->MinDiners;
        $this->FilterAvailablePlaces();
        $this->FilterAvailableBlocks();
        $this->FilterAvailableOffersEvents();
        $this->FilterAvailableOffers();
        $this->FilterAvailableTurns();

        $yesterday = new \DateTime("YESTERDAY");
        $arr = [];
        foreach($this->OffersShare as $item){
            $date = new \DateTime($item->Date);
            if($date <= $yesterday){
                continue;
            }
            $arr[] = $item;
        }

        $this->OffersShare = $arr;
        $arr = [];
        foreach($this->TurnsShare as $item){
            $date = new \DateTime($item->Date);
            if($date <= $yesterday){
                continue;
            }
            $arr[] = $item;
        }
        $this->TurnsShare = $arr;
    }

    /**
     * Filtra los espacios activos en la colección de espacios disponibles
     */
    private function FilterAvailablePlaces(){
        $this->AvailablePlaces = array_filter($this->Places, function($item){
           return  $item->Active == TRUE;
        });
    }

    /**
     * Filtra los bloqueos activos desde el día anterior(AYER)
     */
    private function FilterAvailableBlocks(){
        $yesterday = new \DateTime( "YESTERDAY" );
        $this->AvailableBlocks = array_filter($this->Blocks,
                function($item) use ($yesterday){
            $dateBlocked = new \DateTime($item->Date);
            return $dateBlocked >= $yesterday;
        });
    }

    /**
     * Filtra los eventos de ofertas activos desde el día anterior(AYER)
     */
    private function FilterAvailableOffersEvents(){
        $yesterday = new \DateTime( "YESTERDAY" );
        $this->AvailableOffersEvents = array_filter($this->OffersEvents,
                function($item) use ($yesterday){
            $date = new \DateTime($item->Date);
            return $date >= $yesterday;
        });
    }

    /**
     * Filtra las ofertas activas válidas
     */
    private function FilterAvailableOffers(){
        $this->AvailableOffers = [];
        $yesterday = new \DateTime("YESTERDAY");
        foreach($this->Offers as $offer){
            if($offer->Active != 1){
                continue;
            }
            $end = ($offer->End == "0000-00-00 00:00:00" )
                    ? NULL : new \DateTime($offer->End);
            if($end > $yesterday || $end == NULL){
                $this->AvailableOffers[] = $offer;
            }
        }
    }

    /**
     * Filtra y configura los turnos establecidos para el proyecto
     */
    private function FilterAvailableTurns(){
        $this->AvailableTurns = array();
        foreach($this->Turns as $turn){
            $t = $this->SetTurnData($turn);
            if($t != NULL){
                $this->AvailableTurns[] = $t;
            }
        }
    }

    /**
     * Establece los datos del turno para agregarlo a la lista de turnos
     * disponibles.
     * @param \Turn $turn Referencia al turno a configurar
     * @return \Turn Referencia al turno a agregar o NULL si
     * no tiene configuraciones válidas
     */
    private function SetTurnData($turn = NULL){
        $configs = $this->GetConfigByTurn($turn->Id);
        if(!empty($configs)){
            $days = [];
            foreach($configs as $item){
                $days[] = $item->Day;
            }
            $turn->Days = $days;
            $turn->Start = substr($turn->Start, 0, 5);
            $turn->End = substr($turn->End, 0, 5);
            return $turn;
        }
        return NULL;
    }

    /**
     * Obtiene las configuraciones para un turno especificado
     * @param int $id Identidad del turno
     * @return array Colección de configuraciones registradas
     */
    private function GetConfigByTurn($id = 0){
        $configs = array_filter($this->Configurations,
                function ($item) use ($id) {
            return ($item->Turn == $id);
        });
        return (empty($configs)) ? [] : $configs;
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
 * Configuración de días bloqueados
 *
 * @author alfonso
 */
class Block{

    /**
     * Identidad
     * @var int Identidad del bloqueo
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) al Proyecto
     * @var int Identidad del proyecto asociado
     */
    public $Project = 0;

    /**
     * Referencia externa(fk) al Turno
     * @var int Identidad del turno asociado
     */
    public $Turn = 0;

    /**
     * Fecha del día que se bloquea
     * @var string Fecha del bloqueo
     */
    public $Date = NULL;

    /**
     * Tipo de registro: Bloqueo = TRUE| Apertura = FALSE
     * @var boolean
     */
    public $Block = TRUE;

    /**
     * Anyo del bloqueo
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del bloqueo
     * @var int
     */
    public $Week = 0;
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
 * Información de la reserva
 *
 * @author alfonso
 */
class Booking{

    /**
     * Identidad
     * @var int Identidad de la reserva
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) al Proyecto
     * @var int Identidad del proyecto
     */
    public $Project = 0;

    /**
     * Referencia externa (fk) al Turno de comida
     * @var int Identidad del turno
     */
    public $Turn = 0;

    /**
     * Referencia externa (fk) al cliente registrado
     * @var int Identidad del cliente (si ha sido registrado)
     */
    public $Client = NULL;

    /**
     * Fecha para la reserva
     * @var string Fecha de la reserva
     */
    public $Date = NULL;

    /**
     * Comensales
     * @var int Comensales para la reserva
     */
    public $Diners = 1;

    /**
     * Usuario que reserva
     * @var string Nombre al que se hace la reserva
     */
    public $ClientName = "";

    /**
     * E-mail de contacto
     * @var string Email de contacto para la reserva
     */
    public $Email = "";

    /**
     * Teléfono de contacto
     * @var string Teléfono de contacto para la reserva
     */
    public $Phone = "";

    /**
     * Fecha de creación del registro
     * @var string Fecha de creación del registro
     */
    public $CreateDate = NULL;

    /**
     * Fecha de creación del registro
     * @var int Estado de workflow de gestión de la reserva
     */
    public $State = null;

    /**
     * Referencia a la oferta seleccionada
     * @var int Identidad de la ofera asociada a la reserva
     */
    public $Offer = null;

    /**
     * Referencia al lugar seleccionado
     * @var int Identidad del "Lugar" asociado a la reserva
     */
    public $Place = null;

    /**
     * Comentarios del cliente sobre la reserva
     * @var string Comentarios de la reserva
     */
    public $Comment = "-";

    /**
     * Identidad del origen de reserva
     * @var int
     */
    public $BookingSource = NULL;

    /**
     * Notas asociadas a la reserva
     * @var string
     */
    public $Notes = "";

    /**
     * Información del pre-pedido
     * @var string
     */
    public $PreOrder = "";

    /**
     * Información para la gestión de mesas
     * @var string
     */
    public $sTable = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime( "NOW" );
        $this->Date = $date->format( "Y-m-d H:i:s" );
        $this->CreateDate = $date->format( "Y-m-d H:i:s" );
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
 * Entidad Backup de las reservas para cuando se modifican datos
 *
 * @author alfonso
 */
class BookingBck{

    /**
     * Propiedad Id del objeto copia
     * @var int Identidad del registro de copia
     */
    public $Id = 0;

    /**
     * Propiedad Ref del objeto copia
     * @var int Identidad de la reserva asociada
     */
    public $Ref = null;

    /**
     * Propiedad Data del objeto copia
     * @var string Serialización json de la información de reserva original
     */
    public $Data = "[]";

    /**
     * Propiedad Date del objeto copia
     * @var string Fecha en la que se realiza la copia
     */
    public $Date = null;

    /**
     * Constructor de la clase
     */
    public function __construct($o = null){
        $dt = new DateTime();
        $this->Date = $dt->format('Y-m-d H:i:s');
        if($o != null){
            $this->Ref = $o->Id;
            $this->Data = json_encode($o);
        }
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
 * Entidad origen de datos
 *
 * @author alfonso
 */
class BookingSource{

    /**
     * Identidad del origen de reserva
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del origen de reserva
     * @var string
     */
    public $SourceName = "";

    /**
     * Descripción del origen de datos
     * @var string
     */
    public $Description = "";
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
 * Información de cliente
 *
 * @author alfonso
 */
class Client{

    /**
    * Identidad del cliente
    * @var int
    */
   public $Id = 0;

   /**
    * Identidad del proyecto asociado
    * @var int
    */
   public $Project = 0;

   /**
    * Nombre del cliente
    * @var string
    */
   public $Name = "";

   /**
    * E-mail del cliente
    * @var string
    */
   public $Email = "";

   /**
    * Teléfono del cliente
    * @var string
    */
   public $Phone = "";

   /**
    * Fecha del regsitro
    * @var string
    */
   public $CreateDate = NULL;

   /**
    * Fecha de última actualización
    * @var string
    */
   public $UpdateDate = NULL;

   /**
    * Estado del cliente
    * @var boolean
    */
   public $State = 1;

   /**
    * Tipificación del cliente como VIP
    * @var boolean
    */
   public $Vip = FALSE;

   /**
    * Comentarios asociados al regsitro de cliente
    * @var string
    */
   public $Comments = "";

   /**
    * Flag para indicar si el cliente acepta recibir publicidad
    * @var string
    */
   public $Advertising = FALSE;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new \DateTime( "NOW" );
        $this->CreateDate = $date->format( "Y-m-d H:i:s" );
        $this->UpdateDate = $date->format( "Y-m-d H:i:s" );
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
 * Comentario sobre la reserva
 *
 * @author alfonso
 */
class Comment{

    /**
     * Identidad del comentario
     * @var int Identidad del comentario
     */
    public $Id = 0;

    /**
     * Referencia a la reserva
     * @var int Identidad de la reserva asociada
     */
    public $Booking = 0;

    /**
     * Comentario
     * @var string Comentario
     */
    public $Text = "";

    /**
     * Fecha del comentario
     * @var string Fecha en la que se realiza el comentario
     */
    public $Date = "";

    /**
     * Usuario que crea el comentario
     * @var string Nombre de usuario
     */
    public $UserName = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime( "NOW" );
        $this->Date = $date->format( "Y-m-d H:i:s" );
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
 * Configuración de turnos. Almacena la información
 * sobre el máximo número de reservas para un turno y día de la semana
 *
 * @author alfonso
 */
class Configuration{

    /**
     * Propiedad Id
     * @var int Identidad de la configuración
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) al Proyecto
     * @var int Identidad del proyecto
     */
    public $Project = 0;

    /**
     * Referencia externa(fk) al Turno
     * @var int Identidad del turno
     */
    public $Turn = 0;

    /**
     * Referencia externa(fk) al día de la semana
     * @var int Identidad del día de la semana
     */
    public $Day = 0;

    /**
     * Número de reservas
     * @var int Cantidad de comensales posibles
     */
    public $Count = 0;
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
 * Entidad con los parámetros de configuración del proyecto
 *
 * @author alfonso
 */
class ConfigurationService {

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
     * Identidad del servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Mínimo número de comensales
     * @var int
     */
    public $MinDiners = 1;

    /**
     * Máximo número de comensales
     * @var int
     */
    public $MaxDiners = 25;

    /**
     * Flag para indicar si están activados los recordatorios
     * @var boolean
     */
    public $Reminders = FALSE;

    /**
     * Ventana de tiempo previa para el envío de recordatorio [en horas]
     * @var int
     */
    public $TimeSpan = 1;

    /**
     * Filtro de tiempo para generar recordatorios [en horas]
     * @var int
     */
    public $TimeFilter = 24;

    /**
     * Mínimo número de comensales para enviar un recordatorio
     * @var int
     */
    public $Diners = 1;

    /**
     * Flag para indicar la suscripción al servicio de publicidad
     * en el formulario de reservas
     * @var boolean
     */
    public $Advertising = FALSE;

    /**
     * Flag para indicar la suscripción al servicio de pre-pedidos
     * @var boolean
     */
    public $PreOrder = FALSE;
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
 * Día de la semana
 *
 * @author alfonso
 */
class Day{

    /**
     * Propiedad Id
     * @var int Identidad del registro
     */
    public $Id = 0;

    /**
     * Nombre del día de la semana
     * @var string Nombre del día de la semana
     */
    public $Name = "";

    /**
     * Número del día de la semana [1-7]
     * @var int Índice de día de la semana
     */
    public $DayOfWeek = 1;
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
 * Registro de actividad
 *
 * @author alfonso
 */
class Log{

    /**
     * Propiedad Id
     * @var int Identidad del registro
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) a la reserva
     * @var int Identidad de la reserva
     */
    public $Booking = 0;

    /**
     * Dirección Ip de la petición
     * @var string Dirección de red del cliente
     */
    public $Address = "";

    /**
     * Fecha en la que se realiza el registro
     * @var string Fecha en la que se realiza el registro
     */
    public $Date = NULL;

    /**
     * Información sobre la petición serializada en json
     * @var string Serialización JSON de la información a loguear
     */
    public $Information = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime("NOW");
        $this->Date = $date->format( "Y-m-d H:i:s" );
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
 * Oferta disponible para las reservas
 *
 * @author alfonso
 */
class Offer{

    /**
     * Identidad de la oferta
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Título de la oferta
     * @var string
     */
    public $Title = 0;

    /**
     * Descripción de la oferta
     * @var string
     */
    public $Description = "";

    /**
     * Términos de la oferta
     * @var string
     */
    public $Terms = "";

    /**
     * Fecha de inicio de validez de la oferta
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de fin de validez de la oferta
     * @var string
     */
    public $End = "";

    /**
     *  Estado de la oferta
     * @var boolean
     */
    public $Active = TRUE;

    /**
     * Fecha de registro de la oferta
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Fecha de actualización de la oferta
     * @var string
     */
    public $UpdateDate = NULL;

    /**
     * Flag para indicar si la oferta es visible en la web
     * @var boolean
     */
    public $Web = FALSE;
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
 * Configuración de dias oferta
 *
 * @author alfonso
 */
class OfferConfig{

    /**
     * Identidad del registro de configuración
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad de la oferta a la que pertenece
     * @var int
     */
    public $Offer = 0;

    /**
     *Identidad del día de la semana
     * @var int
     */
    public $Day = 0;

    /**
     * Identidad del slot asociado (franja horaria)
     * @var int
     */
    public $Slot = 0;

    /**
     * Turno asociado a la configuración
     * @var int
     */
    public $Turn = 0;
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
 * Entidad para el registro de eventos asociados a una oferta
 *
 * @author alfonso
 */
class OfferEvent {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = 0;

    /**
     * Identidad de la oferta asociada
     * @var int
     */
    public $Offer = 0;

    /**
     * Identidad de la configuración de línea base(si existe)
     * @var int
     */
    public $Config = "";

    /**
     * Anyo del evento
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del anyo en que es válida
     * @var int
     */
    public $Week = 0;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Fecha del evento
     * @var string
     */
    public $Date = "";

    /**
     * Tipo de evento de oferta: válida o no
     * @var boolean
     */
    public $State = FALSE;

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
 * Entidad para la gestión de cuotas por oferta
 *
 * @author alfonso
 */
class OfferShare {

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
     * Identidad de la oferta asociada
     * @var int
     */
    public $Offer = 0;

    /**
     * Identidad del día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad del slot asociado
     * @var int
     */
    public $Slot = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = NULL;

    /**
     * Cuota asignada a la oferta
     * @var int
     */
    public $Share = 0;
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
 * Lugar o estancia
 *
 * @author alfonso
 */
class Place{

    /**
     * Identidad del lugar
     * @var int Identidad del lugar
     */
    public $Id = 0;

    /**
     * Proyecto al que pertenece
     * @var int Identidad del proyecto asociado
     */
    public $Project = 0;

    /**
     * Nombre del lugar
     * @var string Nombre del Lugar
     */
    public $Name = 0;

    /**
     * Descripción del lugar
     * @var string Descripción del lugar
     */
    public $Description = "";

    /**
     * Numero de plazas
     * @var int Cantidad de comensales que pueden ser servidos
     */
    public $Size = 0;

    /**
     * Estado lógico del registro
     * @var boolean Estado del lugar
     */
    public $Active = true;

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
 * Entidad Slot
 *
 * @author alfonso
 */
class Slot{

    /**
     * Propiedad Id del slot
     * @var int Identidad del Slot o franja horaria
     */
    public $Id = 0;

    /**
     * Nombre de la franja horaria
     * @var string Nombre del slot o franja horaria
     */
    public $Name = "";

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
 * Estado de la reserva (WORKFLOW)
 *
 * @author alfonso
 */
class State{

    /**
     * Identidad del estado
     * @var int Identidad del estado
     */
    public $Id = 0;

    /**
     * Nombre del estado
     * @var string Nombre del estado
     */
    public $Name = 0;

    /**
     * Descripción del estado
     * @var string Descripción del estado
     */
    public $Description = "";

    /**
     * Nivel en el workflow
     * @var int Nivel o profundidad del estado
     */
    public $Level = 0;

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
 * Entidad Turno
 *
 * @author alfonso
 */
class Turn{

    /**
     * Propiedad Id
     * @var int Identidad del turno
     */
    public $Id = 0;

    /**
     * Referencia externa a la franja horaria
     * @var int Identidad del Slot asociado
     */
    public $Slot = 0;

    /**
     * Hora de inicio del turno
     * @var string Hora de inicio del turno
     */
    public $Start = "";

    /**
     * Hora de fin del turno
     * @var string Hora de finalización del turno
     */
    public $End = "";

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
 * Entidad para la configuración de cupos por turno
 *
 * @author alfonso
 */
class TurnShare {

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
     * Identidad del turno asociado
     * @var type
     */
    public $Turn = 0;

    /**
     * Identidad del día de la semana asociado
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Cupo de comensales configurado
     * @var int
     */
    public $Share = 0;
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
 * DTO para la gestión de bloqueo
 *
 * @author alfonso
 */
class BlockDTO{

    /**
     * Identidad del bloqueo
     * @var int
     */
    public $Id = 0;

    /**
     * Año del bloqueo
     * @var int
     */
    public $Year = 2014;

    /**
     * Semana del año
     * @var int
     */
    public $Week = 1;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 1;

    /**
     * Identidad del turno
     * @var int
     */
    public $Turn = 0;

    /**
     * Fecha
     * @var string
     */
    public $Date = "";

    /**
     * Estado del bloqueo
     * @var boolean
     */
    public $Block = FALSE;

    /**
     * Constructor
     */
    public function __construct($year = 2014, $week=1,
            $day = 1, $turn = 0, $date = "", $block = FALSE, $id = 0){
        $this->Id = $id;
        $this->Year = $year;
        $this->Week = $week;
        $this->DayOfWeek = $day;
        $this->Turn = $turn;
        $this->Block = $block;
        $this->Date = $date;
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
 * DTO para el listado de reservas
 *
 * @author alfonso
 */
class BookingDTO {
    // Propiedades de la reserva
    public $Id = 0;
    public $Project = 0;
    public $Turn = 0;
    public $Client = NULL;
    public $Date = NULL;
    public $Diners = 1;
    public $ClientName = "";
    public $Email = "";
    public $Phone = "";
    public $CreateDate = NULL;
    public $State = null;
    public $Offer = null;
    public $Place = null;
    public $Comment = "-";
    public $BookingSource = NULL;
    public $Notes = "";
    public $PreOrder = "";
    // Propiedades del turno
    public $TurnSlot = 0;
    public $TurnStart = "";
    public $TurnEnd = "";
    // Propiedades del "espacio"
    public $PlaceName = 0;
    public $PlaceDescription = "";
    public $PlaceSize = 0;
    // Propiedades de la oferta
    public $OfferTitle = 0;
    public $OfferDescription = "";
    public $OfferTerms = "";
    public $OfferStart = "";
    public $OfferEnd = "";
    // Propiedades origen reserva
    public $SourceName = "";
    public $SourceDescription = "";
    // Campos calculados
    public $ClientCount = 0;
    public $sTable = "";
    public $ClientComments = "";
    public $ZapperState = "";
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
 * DTO para visualizar la información del registro de clientes
 *
 * @author alfonso
 */
class ClientDTO {

    /**
     * Identidad del cliente
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * E-mail del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono del cliente
     * @var string
     */
    public $Phone = "";

    /**
     * Fecha del regsitro
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Fecha de última actualización
     * @var string
     */
    public $UpdateDate = NULL;

    /**
     * Estado del cliente
     * @var boolean
     */
    public $State = 1;

    /**
     * Tipificación del cliente como VIP
     * @var boolean
     */
    public $Vip = FALSE;

    /**
     * Comentarios asociados al regsitro de cliente
     * @var string
     */
    public $Comments = "";

    /**
     * Número total de reservas realizadas
     * @var int
     */
    public $Total = 0;

    /**
     * Total de reservas sin estado(pendientes)
     * @var int
     */
    public $Estado_0 = 0;

    /**
     * Total de reservas en el primer estado
     * @var int
     */
    public $Estado_1 = 0;

    /**
     * Total de reservas en el segundo estado
     * @var int
     */
    public $Estado_2 = 0;

    /**
     * Total de reservas en el tercer estado
     * @var int
     */
    public $Estado_3 = 0;

    /**
     * Total de reservas en el cuarto estado
     * @var int
     */
    public $Estado_4 = 0;

    /**
     * Total de reservas en el quinto estado
     * @var int
     */
    public $Estado_5 = 0;

    /**
     * Total de reservas en el sexto estado
     * @var int
     */
    public $Estado_6 = 0;

    /**
     * Total de reservas en el séptimo estado
     * @var int
     */
    public $Estado_7 = 0;

    /**
     * Total de reservas sin estado(pendientes)
     * @var strin
     */
    public $UltimaFecha = "";

    /**
     * Flag para indicar si el cliente cede sus datos para comunicaciones
     * @var boolean
     */
    public $Advertising = FALSE;

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
 * DTO para cargar la información de reservas
 *
 * @author alfonso
 */
class RequestDTO {
    // Propiedades de la reserva
    public $Id = 0;
    public $Project = 0;
    public $Turn = 0;
    public $Client = NULL;
    public $Date = NULL;
    public $Diners = 1;
    public $ClientName = "";
    public $Email = "";
    public $Phone = "";
    public $CreateDate = NULL;
    public $State = null;
    public $Offer = null;
    public $Place = null;
    public $Comment = "-";
    public $BookingSource = NULL;
    // Propiedades del turno
    public $TurnId = 0;
    public $TurnSlot = 0;
    public $TurnStart = "";
    public $TurnEnd = "";
    // Propiedades del "espacio"
    public $PlaceId = 0;
    public $PlaceProject = 0;
    public $PlaceName = 0;
    public $PlaceDescription = "";
    public $PlaceSize = 0;
    public $PlaceActive = true;
    // Propiedades de la oferta
    public $OfferId = 0;
    public $OfferProject = 0;
    public $OfferTitle = 0;
    public $OfferDescription = "";
    public $OfferTerms = "";
    public $OfferStart = "";
    public $OfferEnd = "";
    public $OfferActive = true;
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
 * DTO para un registro de turno configurado. Permite obtener la
 * información de un turno junto a los parámetros de configuración
 * del proyecto
 *
 * @author alfonso
 */
class TurnDTO {

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno
     * @var int
     */
    public $Id = 0;

    /**
     * Día de la semana asociado a la configuración
     * @var int
     */
    public $DOW = 0;

    /**
     * Franja horaria del turno
     * @var int
     */
    public $Slot = 0;

    /**
     * Hora de inicio del turno
     * @var string
     */
    public $Start = "";

    /**
     * Hora de finalización del turno
     * @var string
     */
    public $End = "";
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
 * Description of BookingNotificationDTO
 *
 * @author alfonso
 */
class BookingNotificationDTO {

    /**
     * Identidad de la reserva
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project= 0;

    /**
     * Hora de la reserva
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de la reserva
     * @var string
     */
    public $Date = "";

    /**
     * Número de comensales
     * @var int
     */
    public $Diners = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * Email del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono del cliente
     * @var string
     */
    public $Phone = "";

    /**
     * Estado de la reserva
     * @var int
     */
    public $State = NULL;

    /**
     * Lugar de la reserva
     * @var string
     */
    public $Place = "";

    /**
     * Título de la oferta
     * @var string
     */
    public $Title = "";

    /**
     * Descripción de la oferta
     * @var string
     */
    public $Description = "";

    /**
     * Términos y condiciones de la oferta
     * @var string
     */
    public $Terms = "";

    /**
     * Comentarios del cliente
     * @var string
     */
    public $Comment = "";

    /**
     * Notas de la reserva
     * @var string
     */
    public $Notes = "";

    /**
     * Información del pre-pedido
     * @var string
     */
    public $PreOrder = "";

    /**
     * Contenido en base64 del QR
     * @var string
     */
    public $QrContent = "";
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
 * DTO para la gestión de cuotas de ofertas
 *
 * @author alfonso
 */
class OfferShareDTO {

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad de la oferta asociada
     * @var int
     */
    public $Offer = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = NULL;

    /**
     * Identidad del slot asociado
     * @var int
     */
    public $Slot = 0;

    /**
     * Fecha de registro
     * @var string
     */
    public $Date = "";

    /**
     * Cuota configurada
     * @var int
     */
    public $Share = 0;

    /**
     * Reservas actuales
     * @var int
     */
    public $BookingsTotal = 0;

    /**
     * Reservas pendientes
     * @var int
     */
    public $BookingsFree = 0;

    /**
     * Comensales actuales
     * @var int
     */
    public $DinersTotal = 0;

    /**
     * Comensales pendientes
     * @var int
     */
    public $DinersFree = 0;
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
 * DTO para la gestión de cupos por turno
 *
 * @author alfonso
 */
class TurnShareDTO {

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = NULL;

    /**
     * Fecha de registro
     * @var string
     */
    public $Date = "";

    /**
     * Cuota configurada
     * @var int
     */
    public $Share = 0;

    /**
     * Reservas actuales
     * @var int
     */
    public $BookingsTotal = 0;

    /**
     * Reservas pendientes
     * @var int
     */
    public $BookingsFree = 0;

    /**
     * Comensales actuales
     * @var int
     */
    public $DinersTotal = 0;

    /**
     * Comensales pendientes
     * @var int
     */
    public $DinersFree = 0;

} ?>
