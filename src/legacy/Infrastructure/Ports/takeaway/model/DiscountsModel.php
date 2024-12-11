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
 * Modelo para la gestión de descuentos
 *
 * @author manager
 */
class DiscountsModel extends \TakeawayModel{

    /**
     * Indica si se ha producido algún error durante la última operación
     * @var boolean
     */
    public $Error = FALSE;

    /**
     * Referencia al descuento en edición
     * @var \DiscountDTO
     */
    public $Entity = NULL;

    /**
     * Colección de descuentos disponibles
     * @var array
     */
    public $Entities = [];

    /**
     * Serialización de la colección de eventos/excepciones del descuento
     * @var string
     */
    public $Events = "[]";

    /**
     * Colección de días de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de Turnos de reparto
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Mensaje de error en el valor del descuento
     * @var String
     */
    public $eValue = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de
     * @var String
     */
    public $eValueClass = "";

    /**
     * Mensaje de error en el valor máximo aplicable
     * @var String
     */
    public $eMaxValue = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de
     * @var String
     */
    public $eMaxValueClass = "";

    /**
     * Mensaje de error en el valor mínimo aplicable
     * @var String
     */
    public $eMinValue = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de
     * @var String
     */
    public $eMinValueClass = "";

    /**
     * Mensaje de error en la fecha de inicio
     * @var String
     */
    public $eStart = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de
     * @var String
     */
    public $eStartClass = "";

    /**
     * Mensaje de error en la fecha de finalización
     * @var String
     */
    public $eEnd = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de
     * @var String
     */
    public $eEndClass = "";

    /**
     * Mensaje de resultado del formulario principal
     * @var string
     */
    public $eFormResult = "";

    /**
     * Clase CSS del mensaje de resultado del formulario principal
     * @var type
     */
    public $eFormResultClass = "";

    /**
     * DTO para la navegación semanal
     * @var \WeekNavDTO
     */
    public $WeekNavDTO = NULL;

    /**
     * Referencia al Management de descuentos
     * @var \IDiscountsManagement
     */
    protected $Management = NULL;

    /**
     * Referencia al agregado de descuentos
     * @var \DiscountsManagement
     */
    public $Aggregate = NULL;

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Descuentos",
                "Descuentos",
                "DiscountsManagement");
        $this->SetModel();
    }

    /**
     * Carga el modelo con la colección de descuentos disponibles
     */
    public function GetDiscounts(){
        $this->Entities = [];
        $this->Management->GetDiscounts();
        $this->Aggregate = $this->Management->GetAggregate();
        foreach($this->Aggregate->Discounts as $item){
            $this->Entities[$item->Id] = $item;
        }
        // formatear la colección de descuentos
        foreach($this->Entities as $item){
            $item->Configuration = json_encode($item->Configuration);
            $item->sStart = $this->SetDate($item->Start);
            $item->sEnd = $this->SetDate($item->End);
            $item->Start = $this->SetFormatDate($item->Start);
            $item->End = $this->SetFormatDate($item->End);
        }
    }

    /**
     * Almacena la información de la entidad y configura el modelo
     * para visualiar los resultados de la operación
     * @param \DiscountDTO $entity Referencia a la entidad
     */
    public function Save($entity = NULL){
        $this->Error = TRUE;
        // Adaptar la configuración del descuento
        $entity = $this->SetDiscountsOnConfiguration($entity);

        // Procedimiento para almacenar el descuento
        $result = $this->Management->SetDiscount($entity);

        if(is_array($result) == FALSE){
            throw new Exception("Save: SetDiscount: "
                    . "Códigos de operación inválidos");
        }

        $this->Entity = $entity;

        if(count($result) != 1 || $result[0] != 0){
            $this->TranslateResultCodes(_OP_CREATE_, $result);
            $this->Entity->Configuration = json_encode($entity->Configuration);
        }
        else{
            $this->Error = FALSE;
            $this->Entities[$entity->Id] = $entity;
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * Elimina el descuento identificado por su identidad y configura
     * el modelo para visualizar los resultados de la operación
     * @param int $id Identidad del descuento
     */
    public function Delete($id = 0){
        // Procedimiento de eliminación
        $result = $this->Management->RemoveDiscount($id);

        if($result != 0){
            $this->TranslateResultCodes(_OP_DELETE_, [$result]);
        }
        else{
            unset($this->Entities[$id]);
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * Configura el modelo para la gestión de excepciones del descuento
     * @param int $id Identidad del descuento
     * @param int $year Año solicitado
     * @param int $week Semana solicitada
     */
    public function GetEvents($id = 0, $year = 0, $week = 0){
        $this->WeekNavDTO->SetWeekInfo($this->DaysOfWeek, $year, $week);
        if($this->Management->GetDiscount($id)== 0){
            $this->Aggregate = $this->Management->GetAggregate();
            $this->Entity = $this->Aggregate->Discount;
            $events = $this->Management->GetDiscountEvents($id,
                    $this->WeekNavDTO->Current, $this->WeekNavDTO->CurrentYear);
            $this->Events = json_encode($events);
        }
    }

    /**
     * Procedimiento para actualizar el registro de eventos
     * @param \DiscountOnEvent $dto
     * @return \JsonResultDTO
     */
    public function SetEvent($dto = NULL){
        // Configurar evento
        $result = $this->Management->SetDiscountEvent($dto);

        $json = new \JsonResultDTO();

        if(is_numeric($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new Exception("Códigos de operación inválidos");
        }

        if($result!= 0){
            $json->Result = FALSE;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }

        return $json;
    }

    /**
     * Instancia todas las configuraciones seleccionadas en el formulario
     * @param \DiscountDTO $entity Referencia al dto de descuento
     */
    private function SetDiscountsOnConfiguration($entity = NULL){
        $dtos = [];
        if($entity != NULL){
            $configs = json_decode($entity->Configuration);
            foreach ($configs as $config){
                $dto = new \DiscountOnConfiguration();
                $dto->DayOfWeek = $config->DayOfWeek;
                $dto->SlotOfDelivery = $config->SlotOfDelivery;
                $dto->DiscountOn = $config->DiscountOn;
                $dto->Id = $config->Id;
                $dtos[] = $dto;
            }
            $entity->Configuration = $dtos;
        }
        return $entity;
    }

    /**
     * Convertir la fecha a formato texto (largo)
     * @param string $sdate Fecha de base de datos formato Y-m-d
     * @return string Fecha en formato largo
     */
    private function SetDate($sdate = ""){
        if($sdate != ""){
            // Obtener la instancia para la fecha
            $date = new \DateTime($sdate);
            // Parsear a formato texto
            $sdate = strftime("%A %d de %B del %Y", $date->getTimestamp());
        }
        return $sdate;
    }

    /**
     * Establece el formato yyyy-mm-dd para las fechas de inicio y fin
     * @param string $sdate Fecha en formato yyyy-mm-dd hh:ii:ss
     * @return string
     */
    private function SetFormatDate($sdate = ""){
        if($sdate != ""){
            // Obtener la instancia para la fecha
            $date = new \DateTime($sdate);
            // Parsear a formato texto
            $sdate = $date->format("Y-m-d");
        }
        return $sdate;
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->Entity = new \DiscountDTO();
        $this->WeekNavDTO = new \WeekNavDTO();
        $this->Entity->Configuration = "[]";
        $this->DaysOfWeek = $this->Aggregate->DaysOfWeek;
        $this->SlotsOfDelivery = $this->Aggregate->SlotsOfDelivery;
    }

    /**
     * Configuración de los códigos de resultados
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Obtiene los mensajes de error al "leer" una categoría desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [-1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado el descuento" ]
            ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" una categoría
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "El descuento no ha sido encontrado" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de una categoría en el repositorio principal
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -2 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -3 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -4 => ["name" => "eResult",
                    "msg" => "La referencia no es válida" ],
            -5 => ["name" => "eValue",
                    "msg" => "Debe introducir un valor para el descuento" ],
            -6 => ["name" => "eValue",
                    "msg" => "El tipo de dato no es correcto" ],
            -7 => ["name" => "eValue",
                    "msg" => "El descuento no puede ser menor que 0" ],
            -8 => ["name" => "eMaxValue",
                    "msg" => "Debe introducir un valor máximo aplicable" ],
            -9 => ["name" => "eMaxValue",
                    "msg" => "El tipo de dato no es correcto" ],
            -10 => ["name" => "eMaxValue",
                    "msg" => "El valor máximo aplicable no puede ser menor igual que 0" ],
            -11 => ["name" => "eMinValue",
                    "msg" => "Debe introducir un valor mínimo aplicable" ],
            -12 => ["name" => "eMinValue",
                    "msg" => "El tipo de dato no es correcto" ],
            -13 => ["name" => "eMinValue",
                    "msg" => "El valor mínimo aplicable no puede ser menor que 0" ],
            -14 => ["name" => "eFormResult",
                    "msg" => "El mínimo aplicable debe ser menor que el máximo aplicable" ],
            -15 => ["name" => "eStart",
                    "msg" => "Debe introducir una fecha de inicio" ],
            -16 => ["name" => "eStart",
                    "msg" => "La fecha de inicio debe ser posterior a la actual" ],
            -17 => ["name" => "eStart",
                    "msg" => "El formato no es correcto." ],
            -18 => ["name" => "eEnd",
                    "msg" => "Debe introducir una fecha de fin" ],
            -19 => ["name" => "eEnd",
                    "msg" => "La fecha debe ser posterior a la actual" ],
            -20 => ["name" => "eEnd",
                    "msg" => "El formato no es correcto" ],
            -21 => ["name" => "eFormResult",
                    "msg" => "La fecha de inicio no puede ser posterior a la de fin" ],
            -22 => ["name" => "eFormResult",
                    "msg" => "El formato de fechas no es correcto" ]
        ];
    }
}
