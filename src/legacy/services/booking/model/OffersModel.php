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
 * Model para la gestión de ofertas
 *
 * @author Alfonso
 */
class OffersModel extends \ResbookingModel{

    /**
     * Indica la pestaña activa del menú principal
     * @var string
     */
    public $Activo = "Configuración";

    /**
     * Flag para indicar error en la última operación
     * @var int
     */
    public $Error = 0;

    /**
     * Colección de días de la semana
     * @var array
     */
    public $DaysH = [];

    /**
     * Colección de días de la semana
     * @var array
     */
    public $Days = [];

    /**
     * Colección de turnos disponibles
     * @var array
     */
    public $Turns = [];

    /**
     * Colección de slots disponibles
     * @var array
     */
    public $Slots = [];

    /**
     * Referencia a la entidad en edición
     * @var \Offer
     */
    public $Entity = NULL;

    /**
     * Colección de Ofertas registradas
     * @var array
     */
    public $Entities = [];

    /**
     * Colección de configuraciones de cuota serializadas JSON
     * @var string
     */
    public $OffersShare = "[]";

    /**
     * Mensaje de error para el campo Title
     * @var string
     */
    public $eTitle = "";

    /**
     * Clase CSS utilizado en el mensaje de error Title
     * @var string
     */
    public $eTitleClass = "";

    /**
     * Mensaje de error para el campo Description
     * @var string
     */
    public $eDesc = "";

    /**
     * Clase CSS utilizado en el mensaje de error Description
     * @var string
     */
    public $eDescClass = "";

    /**
     * Mensaje de error para el campo Terms
     * @var string
     */
    public $eTerms = "";

    /**
     * Clase CSS utilizado en el mensaje de error Terms
     * @var string
     */
    public $eTermsClass = "";

    /**
     * Mensaje general con el resultado de la operación
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS utilizado en el mensaje de Resultados
     * @var string
     */
    public $eResultClass = "";

    /**
     * Mensaje para la vista principal sobre el último resultado
     * @var string
     */
    public $eGenResult = "";

    /**
     * Clase CSS utilizada en el mensaje de la vista principal
     * @var type
     */
    public $eGenResultClass = "";

    /**
     * Mensaje de error para los datos de periodo
     * @var string
     */
    public $ePeriodo = "";

    /**
     * Clase CSS utilizado en el mensaje de error del periodo
     * @var string
     */
    public $ePeriodoClass = "";

    /**
     * Códigos de error generados en la ejecución
     * @var array
     */
    protected $Codigos = [];

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        $this->Title = "Configuración::Ofertas";
        $this->Entity = new \Offer();
        $this->SetCodes();
    }

    /**
     * Obtener la lista de ofertas registradas
     * @return void
     */
    public function CargarOfertas(){
        $filter = ["Project" => $this->Project , "Active" => TRUE];
        $this->Entities = $this->Dao->GetByFilter( "Offer", $filter );
        foreach($this->Entities as $item){
            $item->sWeb = ($item->Web == 1)
                    ? "glyphicon-eye-open" : "glyphicon-eye-close";
            $item->sStart = $this->ConfigurarFecha($item->Start);
            $item->sEnd = $this->ConfigurarFecha($item->End);
            $item->DescText = $this->CortarTexto($item->Description, 35);
            $item->TermsText = $this->CortarTexto($item->Terms, 15);
            $item->sTitle = $this->CortarTexto($item->Title, 15);
        }
    }

    /**
     * Cargar los datos del modelo con la información de la oferta especificada
     * @param int $id Identidad de la oferta
     */
    public function CargarOferta($id = 0){
        $this->CargarDiasDeLaSemana();
        $this->CargarTurnosConfigurados();
        $this->Entity = $this->Dao->Read($id, "Offer");
    }

    /**
     * Configura el modelo para la vista de gestión de cupos
     * @param type $id
     */
    public function CargarModeloCupos($id = 0){
        $this->CargarDiasDeLaSemana();
        $this->Entity = $this->Dao->Read($id, "Offer");
        $this->Slots = $this->Dao->Get("Slot");
        $filter = ["Project" => $this->Project, "Offer" => $id];
        $offersShare = $this->Dao->GetByFilter("OfferShare", $filter);
        $this->OffersShare = json_encode($offersShare);
    }

    /**
     * Obtiene la lista de ofertas activas filtradas por proyecto
     * @param int Identidad del proyecto padre
     * @return array colección de ofertas
     */
    public function ObtenerOfertas($id = 0){
        $filter = ["Project" => $id , "Active" => TRUE , "Web" => 1];
        $entities = $this->Dao->GetByFilter( "Offer", $filter );
        foreach($entities as $item){
            $item->Start = $this->ConfigurarFecha($item->Start);
            $item->End = $this->ConfigurarFecha($item->End);
        }
        return $entities;
    }

    /**
     * Método para cargar la información de los días de la semana
     * @return void
     */
    private function CargarDiasDeLaSemana(){
        $this->Days = $this->Dao->Get("Day");
        foreach($this->Days as $day){
            $day->ShortName = substr($day->Name, 0, 2);
            unset($day->Id);
        }
        $this->DaysH = $this->Days;
    }

    /**
     * Metodo para cargar la colección de turnos configurados en el proyecto
     * @return void
     */
    private function CargarTurnosConfigurados(){
        $filter = ["Project" => $this->Project];
        $dtos = $this->Dao->GetByFilter("TurnDTO", $filter);
        foreach($dtos as $dto){
            if(isset($this->Turns[$dto->Id])){
                $this->Turns[$dto->Id]->Days[] = $dto->DOW;
            }
            else{
                $dto->Days = [];
                $dto->Days[] = $dto->DOW;
                $this->Turns[$dto->Id] = $dto;
            }
        }
        foreach ($this->Turns as $turn){
            $turn->Start = substr($turn->Start,0,5);
            $turn->Days = json_encode($turn->Days);
        }
    }

    /**
     * Método para formatear las fechas de oferta en el grid
     * @param string $fechaTexto fecha a formatear
     * @return string cadena de fecha procesada
     */
    private function ConfigurarFecha($fechaTexto = ""){
        // Obtener la fecha de la reserva
        $date = new DateTime($fechaTexto);
        // Obtener la clave en el array de reports
        return (intval($date->format( "Y" )) >= 2014 )
                ? $date->format( "d-m-Y" ) : "-" ;
    }

    /**
     * Método para recortar el texto en función de la longitud máxima
     * @param string $texto Texto a recortar
     * @param int $longitudMax Longitud máxima
     * @return string texto recortado
     */
    private function CortarTexto($texto = "", $longitudMax = 50){
        return (strlen($texto) > $longitudMax)
                    ? substr($texto, 0, $longitudMax)."..."
                    : $texto;
    }

    /**
     * Proceso para el registro o actualización de una oferta
     * @param \Offer $offer Referencia a la oferta a registrar
     * @return boolean Resultado de la operación
     */
    public function GuardarOferta($offer = NULL){
        $this->Entity = $offer;
        if($offer != NULL && $this->Validate($offer)){
            $offer->Project = $this->Project;
            $date = new \DateTime("NOW");
            $sdate = $date->format("Y-m-d H:i:s");
            if($offer->Id == 0) {
                $offer->CreateDate = $sdate;
                $offer->Id = $this->Dao->Create( $offer );
            }
            else{
                $off = $this->Dao->Read($offer->Id, "Offer");
                $offer->CreateDate = $off->CreateDate;
                $offer->UpdateDate = $sdate;
                $this->Dao->Update($offer);
            }
            $this->eGenResult = "La oferta ha sido guardada con éxito.";
            $this->eGenResultClass = "alert-success";
            return TRUE;
        }
        $this->TranslateResultCodes();
        $this->Error = 1;
        return FALSE;
    }

    /**
     * Eliminar una oferta registrada (borrado lógico)
     * @param int $id Identidad de la oferta a eliminar
     * @return boolean Resultado de la operación
     */
    public function EliminarOferta($id = 0){
        if(is_numeric($id)){
            $o = $this->Dao->Read($id, "Offer");
            if($o->Active != FALSE){
                $o->Active = FALSE;
                $this->Dao->Update( $o );
                $this->eGenResult = "La oferta ha sido eliminada con éxito.";
                $this->eGenResultClass = "alert-success";
                return TRUE;
            }
        }
        $this->eGenResult = "La oferta no ha podido ser eliminada.";
        $this->eGenResultClass = "alert-danger";
        return FALSE;
    }

    /**
     * Proceso para actualizar la cuota de una oferat
     * @param \OfferShare $offerShare Referencia a la cuota
     * @return int Código de operación
     */
    public function GuardarCuotaOferta($offerShare = NULL){
        if($offerShare == NULL){
            return -1;
        }

        $offerShare->Turn = NULL;
        // Asignar proyecto
        $offerShare->Project = $this->Project;
        // filtro de búsqueda
        $filter = ["Project" => $offerShare->Project,
            "Offer" => $offerShare->Offer,
            "DayOfWeek" => $offerShare->DayOfWeek,
            "Slot" => $offerShare->Slot,
            "Turn" => $offerShare->Turn];
        $register = $this->Dao->GetByFilter("OfferShare", $filter);
        if(count($register) == 0){
            $this->Dao->Create($offerShare);
        }
        else{
            $os = current($register);
            $os->Share = $offerShare->Share;
            $this->Dao->Update($os);
        }
        return 0;
    }

    /**
     * Proceso para actualizar el estado de una configuración
     * @param \OfferConfig $config Referencia a la configuración
     * @return int Código de operación
     */
    public function GuardarConfiguracion($config = NULL){
        if($config != NULL){
            if($config->Id == 0){
                return $this->Dao->Create($config);
            }
            $filtro = ["Offer" => $config->Offer, "Turn" => $config->Turn,
                "Day" => $config->Day];
            $configs = $this->Dao->GetByFilter("OfferConfig", $filtro);
            foreach($configs as $item){
                $this->Dao->Delete($item->Id, "OfferConfig");
            }
            return 0;
        }
        return -1;
    }

    /**
     * Obtener la colección de configuraciones para la oferta indicada
     * @param int $id Identidad de la oferta
     * @return array Colección de configuraciones
     */
    public function ObtenerConfiguraciones($id = 0){
        return $this->Dao->GetByFilter("OfferConfig",["Offer" => $id]);
    }

    /**
     * Proceso de validación de la entidad
     * @param \Offer $entity Referencia a la entidad a validar
     * @return boolean Resultado de la operación
     */
    private function Validate($entity = NULL){
        if($entity == NULL){
            $this->Codigos[] = -1;
        }
        else{
            $this->ValidateIdentity($entity->Id);
            $this->ValidateTitle($entity->Title);
            $this->ValidateDescription($entity->Description);
            $this->ValidateTerms($entity->Terms);
            $this->ValidateDates($entity);
        }
        return count($this->Codigos) == 0;
    }

    /**
     * Proceso de validación de la identidad
     * @param int $id Identidad a validar
     * @return void
     */
    private function ValidateIdentity($id = 0){
        if(!is_numeric($id)){
            $this->Codigos[] = -2;
        }
        else if($id < 0){
            $this->Codigos[] = -3;
        }
    }

    /**
     * Proceso de validación del título de oferta
     * @param string $titulo Título de la oferta
     * @return void
     */
    private function ValidateTitle($titulo = ""){
        if(empty($titulo)){
            $this->Codigos[] = -4;
        }
        else if(!is_string($titulo)){
            $this->Codigos[] = -5;
        }
        else if(strlen($titulo) > 100){
            $this->Codigos[] = -6;
        }
    }

    /**
     * Proceso de validación de la propiedad Description
     * @param string $descripcion Descripción de la oferta
     * @return void
     */
    private function ValidateDescription($descripcion = ""){
        if(!empty($descripcion)){
            if(!is_string($descripcion)){
                $this->Codigos[] = -7;
            }
            else if(strlen($descripcion) > 1000){
                $this->Codigos[] = -8;
            }
        }
    }

    /**
     * Proceso de validación de los términos de la oferta
     * @param string $terms Términos de la oferta
     * @return void
     */
    private function ValidateTerms($terms = ""){
        if(!empty($terms)){
            if(!is_string($terms)){
                $this->Codigos[] = -9;
            }
            else if(strlen($terms) > 500){
                $this->Codigos[] = -10;
            }
        }
    }

    /**
     * Proceso de validación de las fechas de inicio y fin
     * @param \Offer $entity Referencia a la entidad a validar
     * @return void
     */
    private function ValidateDates($entity = NULL){
        $startD = FALSE;
        $endD = FALSE;

        if(isset($entity->Start) && $entity->Start != ""){
            $date = new DateTime($entity->Start);
            $entity->Start = $date->format( "Y-m-d");
            $startD = TRUE;
        }

        if(isset($entity->End) && $entity->End != ""){
            $date = new DateTime($entity->End);
            $entity->End = $date->format( "Y-m-d");
            $endD = TRUE;
        }

        if($startD && $endD){
            if(strcmp($entity->End, $entity->Start) < 1){
                $this->Codigos[] = -11;
            }
        }
    }

    /**
     * Establece el array de "traducción" de códigos de error
     * @return void
     */
    private function SetCodes(){
       $this->Codes = [
           0 => [ "name" => "eResult", "msg" => "La oferta se ha guardado correctamente" ],
           -1 => [ "name" => "eResult", "msg" => "No se ha recuperado la oferta" ],
           -2 => [ "name" => "eResult", "msg" => "El tipo de dato del Id no es correcto." ],
           -3 => [ "name" => "eResult", "msg" => "El Id no puede ser menor que 1" ],
           -4 => [ "name" => "eTitle", "msg" => "Debe especificar un título." ],
           -5 => [ "name" => "eTitle", "msg" => "El tipo de dato no es correcto." ],
           -6 => [ "name" => "eTitle", "msg" => "La longitud del título supera el "
               . "máximo de caracteres (100)." ],
           -7 => [ "name" => "eDesc", "msg" => "El tipo de dato no es correcto." ],
           -8 => [ "name" => "eDesc", "msg" => "La longitud de la descripción "
               . "debe ser menor que 1000 caractéres" ],
           -9 => [ "name" => "eTerms", "msg" => "El tipo de dato no es correcto." ],
           -10 => [ "name" => "eTerms", "msg" => "La longitud de los términos "
               . "debe ser menor que 500 catacteres" ],
           -11 => [ "name" => "ePeriodo", "msg" => "La fecha de fin debe ser "
               . "posterior a la de inicio" ]
       ];
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
}
