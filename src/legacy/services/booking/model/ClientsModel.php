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
 * Modelo para la gestión de clientes
 *
 * @author alfonso
 */
class ClientsModel extends \ResbookingModel{

    /**
     * Indica la opción de menú activa
     * @var string
     */
    public $Activo = "Clientes";

    /**
     * Referencia a la entidad cliente en edición
     * @var \Client
     */
    public $Entity = NULL;

    /**
     * Colección de clientes registradoss
     * @var  array
     */
    public $Entities = [];

    /**
     * Cantidad de clientes registrados
     * @var int
     */
    public $Total = 0;

    /**
     * Flag indicador del estado de error de la última operación
     * @var int
     */
    public $Error = 0;

    /**
     * Mensaje de error de la propiedad Nombre
     * @var string
     */
    public $eName = "";

    /**
     * Clase CSS a aplicar en el mensaje de error del nombre
     * @var string
     */
    public $eNameClass = "";

    /**
     * Mensaje de error de la propiedad Teléfono
     * @var string
     */
    public $ePhone = "";

    /**
     * Clase CSS a aplicar en el mensaje de error del teléfono
     * @var string
     */
    public $ePhoneClass = "";

    /**
     * Mensaje de error de la propiedad Email
     * @var string
     */
    public $eEmail = "";

    /**
     * Clase CSS a aplicar en el mensaje de error del email
     * @var string
     */
    public $eEmailClass = "";

    /**
     * Mensaje de error de la propiedad Comentarios
     * @var string
     */
    public $eComments = "";

    /**
     * Clase CSS a aplicar en el mensaje de error de los comentarios
     * @var string
     */
    public $eCommentsClass = "";

    /**
     * Mensaje resultado de la operación
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS a aplicar en el mensaje de resultado
     * @var string
     */
    public $eResultClass = "";

    /**
     * Colección de errores de validación detectados
     * @var array
     */
    public $Codigos = [];

    /**
     * Colección de Códigos de error
     * @var array
     */
    public $Codes = [];

    /**
     * Constructor
     */
    public function __construct(){

        parent::__construct();

        $this->Title = "Clientes";

        $this->Entity = new \Client();
    }

    /**
     * Carga la colección de clientes registrados
     */
    public function GetClients(){
        $filter = ["Project" => $this->Project];
        $entities = $this->Dao->GetByFilter("ClientDTO", $filter);
        foreach($entities as $item){
            $item->sName = $this->SetText($item->Name);
            $item->sEmail = $item->Email;
            $item->sDate = $this->SetDate($item->UltimaFecha);
            $item->sComments = $this->SetText($item->Comments);
        }
        $this->Entities = $entities;
        $this->Total = count($entities);
    }

    /**
     * Formateo de una cadena fecha a formato largo
     * @param string $sDate Fecha
     * @return string
     */
    private function SetDate($sDate = ""){
        if(!empty($sDate)){
            $date = new \DateTime($sDate);
            return strftime("%A %d de %B de %Y", $date->getTimestamp());
        }
        return "";
    }

    /**
     * Método para acortar el texto si supera la longitud máxima establecida
     * @param string $text texto
     * @param int $maxlength Longitud de corte
     * @return string
     */
    private function SetText($text = "", $maxlength = 25){
        if(strlen($text) > $maxlength){
            $text = substr($text, 0, $maxlength-3)."...";
        }
        return $text;
    }

    /**
     * Obtiene un registro de cliente a partir de su identidad
     * @param int $id Identidad del cliente
     * @return \ClientDTO Referencia al cliente
     */
    public function GetClient($id = 0){
        $states = [ 0 => "Pendiente" , "Reservado", "Llegado", "Sentado",
                "Terminado", "No Show", "Anulado", "ANOTADO"];
        $client = $this->Dao->Read($id, "ClientDTO");
        $client->UltimaFecha = $this->SetDate($client->UltimaFecha);
        $bookings = $this->Dao->GetByFilter("BookingDTO", ["Client" => $client->Id]);
        $data = [];
        foreach($bookings as $item){
            $foo = $item instanceof \BookingDTO;
            if(!$foo){
                continue;
            }
            $obj = new \BookingDTO();
            $obj->Date = $this->SetDate($item->Date);
            $obj->Diners = $item->Diners;
            $obj->StateName = $this->GetStateName($states, $item->State);
            $obj->State = $item->State;
            $data[] = $obj;
        }
        $client->Bookings = array_reverse($data);
        return $client;
    }

    private function GetStateName($states, $id = 0){

        $idState = (isset($id) && $id) != null ? intval($id) : 0;

        if($idState >-1 && $idState <8){
            return $states[$idState];
        }
        return "";
    }

    /**
     * Obtiene un registro de cliente a partir de su nombre
     * @param string $name Nombre del cliente
     * @return array Colección de registros válidos
     */
    public function GetClientByName($name = ""){
        if(!empty($name)){
            $filter = ["Project" => $this->Project,
                "Name" => "%$name%", "Active" => 1 ];
            return $this->Dao->GetByFilter("ClientDTO", $filter);
        }
        return [];
    }

    /**
     * Obtiene un registro de cliente a partir de su email
     * @param string $email Email del cliente
     * @return array Colección de registros válidos
     */
    public function GetClientByEmail($email = ""){
        if(!empty($email)){
            $filter = ["Project" => $this->Project,
                "Email" => "%$email%", "Active" => 1 ];
            return $this->Dao->GetByFilter("ClientDTO", $filter);
        }
        return [];
    }

    /**
     * Obtiene un registro de cliente a partir de su teléfono
     * @param string $sPhone Teléfono del cliente
     * @return array Colección de registros válidos
     */
    public function GetClientByPhone($sPhone = ""){
        $ars = [" ", "-", "(", ")"];
        $arr = ["", "", "", ""];
        $phone = trim(str_replace($ars, $arr, $sPhone));
        if(!empty($phone)){
            $filter = ["Project" => $this->Project,
                "Phone" => "%$phone%", "Active" => 1 ];
            return $this->Dao->GetByFilter("ClientDTO", $filter);
        }
        return [];
    }

    /**
     * Proceso de registro/actualización de la información de cliente
     * @param \Client $client Referencia a la entidad cliente
     * @return int Código de operación
     */
    public function Save($client = NULL){
        $ars = [" ", "-", "(", ")"];
        $arr = ["", "", "", ""];

        if($this->Validate($client)){
            $date = new \DateTime("NOW");
            $client->Project = $this->Project;
            $client->Phone = trim(str_replace($ars, $arr, $client->Phone));
            if($client->Id == 0){
                $client->CreateDate = $date->format("Y-m-d H:i:s");
                $client->UpdateDate = $date->format("Y-m-d H:i:s");
                $client->Id = $this->Dao->Create($client);
            }
            else{
                $clientOld = $this->Dao->Read($client->Id, "Client");
                $client->CreateDate = $clientOld->CreateDate;
                $client->UpdateDate = $date->format("Y-m-d H:i:s");
                $this->Dao->Update($client);
            }
        }
        else{
            $this->Error = 1;
            $this->SetCodes();
            $this->TranslateResultCodes();
        }
        $this->Entity = $client;
    }

    /**
     * Proceso de eliminación del registro de cliente
     * @param int $id Identidad del cliente
     * @return int Código de operación
     */
    public function Delete($id = 0){
        // Eliminamos la asignación de reservas al cliente
        $filter = ["Client" => $id];
        $reservas = $this->Dao->GetByFilter("Booking", $filter);
        foreach($reservas as $item){
            $item->Client = NULL;
            $this->Dao->Update($item);
        }
        $this->Dao->Delete($id, "Client");
        return 0;
    }

    /**
     * Proceso de actualización de la tipificación VIP
     * @param int $id Identidad del cliente
     * @return int Código de operación
     */
    public function SetVip($id = 0){
        $result = -1;
        $client = $this->Dao->Read($id, "Client");
        if($client != NULL){
            $date = new \DateTime("NOW");
            $client->Vip = !$client->Vip;
            $client->UpdateDate = $date->format("Y-m-d H:i:s");
            $this->Dao->Update($client);
            $result = 0;
        }
        return $result;
    }

    /**
     * Proceso de actualización de las notas asociadas a un cliente
     * @param int $id Identidad del cliente
     * @param string $notes Notas asociadas
     * @return int Código de operación
     */
    public function SetNotes($id = 0, $notes = ""){
        $result = -1;
        $client = $this->Dao->Read($id, "Client");
        if($client != NULL){
            $date = new \DateTime("NOW");
            $client->Comments = strip_tags($notes);
            $client->UpdateDate = $date->format("Y-m-d h:i:s");
            $this->Dao->Update($client);
            $result = 0;
        }
        return $result;
    }

    /**
     * Proceso de validación de la entidad
     * @param \Client $entity Referencia al cliente
     * @return boolean Resultado de la validación
     */
    private function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateIdentity($entity->Id);
            $this->ValidateName($entity->Name);
            $this->ValidatePhone($entity->Phone);
            $this->ValidateEmail($entity->Id, $entity->Email);
            $this->ValidateComments($entity->Comments);
        }
        else{
            $this->Codigos[] = -10;
        }
        return count($this->Codigos) == 0;
    }

    /**
     * Proceso de validación de la identidad
     * @param int $id Identidad del cliente
     */
    private function ValidateIdentity($id = 0){
        if($id < 0){
            $this->Codigos[] = -1;
        }
    }

    /**
     * Proceso de validación del nombre
     * @param string $name Nombre del cliente
     */
    private function ValidateName($name = ""){
        if(empty($name)){
            $this->Codigos[] = -2;
        }
        else if(strlen($name) > 100){
            $this->Codigos[] = -3;
        }
    }

    /**
     * Proceso de validación del teléfono
     * @param string $phone Teléfono del cliente
     */
    private function ValidatePhone($phone = ""){
        if(empty($phone)){
            $this->Codigos[] = -4;
        }
        else if(strlen($phone) > 45){
            $this->Codigos[] = -5;
        }
    }

    /**
     * Proceso de validación del email
     * @param int $id Identidad del cliente
     * @param string $email Email de cliente
     */
    private function ValidateEmail($id = 0, $email = ""){
        if(empty($email)){
            $this->Codigos[] = -6;
        }
        else if(strlen($email) > 100){
            $this->Codigos[] = -7;
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL)==FALSE){
            $this->Codigos[] = -11;
        }
        else if($id == 0){
            $clients = $this->Dao->GetByFilter("Client", ["Email" => $email]);
            if(count($clients) > 0){
                $this->Codigos[] = -8;
            }
        }
        else if($id > 0){
            $clients = $this->Dao->GetByFilter("Client", ["Email" => $email]);
            $counts = count($clients);
            if( $counts > 1 || ($counts == 1 && $clients[0]->Id != $id)){
                $this->Codigos[] = -8;
            }
        }
    }

    /**
     * Proceso de validación de los comentarios
     * @param string $comments Comentarios
     */
    private function ValidateComments($comments = ""){
        if(strlen($comments) > 500){
            $this->Codigos[] = -9;
        }
    }

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @return void
     */
    private function TranslateResultCodes(){
        foreach ($this->Codigos as $code){
            if(!isset($this->Codes[$code])){
                continue;
            }
            $codeInfo = $this->Codes[$code];
            $class = ($code == 0) ? "has-success" : "has-error";
            $this->{$codeInfo["name"]} = $codeInfo["msg"];
            $this->{$codeInfo["name"]."Class"} = $class;
        }
    }

    /**
     * Establece el array de "traducción" de códigos de error
     * @return void
     */
    private function SetCodes(){
       $this->Codes = [
           0 => [ "name" => "eResult", "msg" => "Se ha registrado al cliente con éxito" ],
           -1 => [ "name" => "eResult", "msg" => "El Id no puede ser negativo." ],
           -2 => [ "name" => "eName", "msg" => "El campo nombre es obligatorio" ],
           -3 => [ "name" => "eName", "msg" => "La longitud del nombre supera "
               . "el máximo de caracteres permitido(100)" ],
           -4 => [ "name" => "ePhone", "msg" => "Debe especificar un teléfono." ],
           -5 => [ "name" => "ePhone", "msg" => "La longitud del teléfono supera"
               . " el máximo de caracteres permitidos (45)." ],
           -6 => [ "name" => "eEmail", "msg" => "Debe especificar una dirección"],
           -7 => [ "name" => "eEmail", "msg" => "La longitud del título supera el "
               . "máximo de caracteres (100)."  ],
           -8 => [ "name" => "eEmail", "msg" => "Ya existe un cliente con la "
               . "misma dirección de e-mail" ],
           -9 => [ "name" => "eComments", "msg" => "La longitud de los comentarios"
               . " supera el máximo de caracteres (500)." ],
           -10 => [ "name" => "eResult", "msg" => "No se ha podido recuperar la información" ],
           -11 => [ "name" => "eEmail", "msg" => "Dirección de e-mail no válida" ]
       ];
    }
}
