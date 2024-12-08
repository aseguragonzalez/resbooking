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
 * Model para la gestión de informes
 */
class ReportModel extends \ResbookingModel{

    /**
     * Indica la pestaña activa
     * @var string
     */
    public $Activo = "Informes";

    /**
     * Id estado terminado. filtro de entidades
     * @var int
     */
    protected $Terminado = 4;

    /**
     * Ids de estados "cursados"
     * @var array
     */
    protected $ValidStates =  array( null, 1, 2, 3, 4, 7 );

    /**
     * Array de Reservas
     * @var array
     */
    protected $Entities = array();

    /**
     * Array de Reservas en estado terminado
     * @var array
     */
    protected $Terminados = array();

    /**
     * Array de Reservas
     * @var array
     */
    protected $Clients = array();

    /**
     * Array de reports actuales
     * @var array
     */
    public $Reports = array();

    /**
     * Array de meses
     * @var array
     */
    protected $Months = array( "Enero", "Febrero", "Marzo", "Abril",
        "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
        "Noviembre", "Diciembre" );

    /**
     * Array de días de la semana
     * @var array
     */
    public $DaysOfWeek = array( "Lunes", "Martes", "Miercoles", "Jueves",
        "Viernes", "Sabado", "Domingo" );

    /**
     * Array de días de la semana
     * @var array
     */
    public $Days = array( 1 => 0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0 );

    /**
     * Contador para clientes que no repiten
     * @var int
     */
    public $NoRepite = 0;

    /**
     * Contador para clientes que repiten al menos 1 vez
     * @var int
     */
    public $Repite = 0;

    /**
     * Contador para clientes asiduos
     * @var int
     */
    public $Asiduo = 0;

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        // LLamada al constructor
        parent::__construct();
        // Título de la página
        $this->Title = "Informes";
    }

    /**
     * Método que obtiene la lista de reservas para una fecha
     */
    public function GetReport(){
        // Filtrar reservas
        $this->FilterEntities();
        // Contar, ordenar, filtar... las reservas
        $this->SetReport();
        // Obtener los datos de clientes
        $this->SetDataOfClients();
        // Obtener los datos de la ocupación
        $this->SetDataOfDays();

        usort($this->Reports, "ReportModel::OrderInverseByDate");
    }

    /**
     * @ignore
     * Carga la lista de reservas y las filtra por el estado y origen
     */
    private function FilterEntities(){
        $filter = array("Project" => $this->Project , "BookingSource" => 1);
        // Obtener todas las reservas
        $this->Entities = $this->Dao->GetByFilter( "Booking", $filter);
        // filtrar
        foreach($this->Entities as $item){
            // Incluir reserva filtrada
            $this->Terminados[] = $item;
        }
    }

    /**
     * @ignore
     * Agrupación de las reservas por días de la semana. Se suma el número
     * de comensales de cada reserva al día de la semana que corresponde
     */
    private function SetAgruparComensalesPorDia(){
        foreach($this->Terminados as $item){
            // Obtener clientes registrados
            $keys = array_keys($this->Days);
            // Obtener la fecha de la reserva
            $date = new DateTime($item->Date);
            // Obtener el día de la semana
            $dayOfWeek = $date->format( "N" );
            // Comprobar si el cliente actual ya está registrado
            if(in_array($dayOfWeek, $keys)){
                $this->Days[$dayOfWeek] += $item->Diners;
            }
            else{
                $this->Days[$dayOfWeek] = $item->Diners;
            }
        }
    }

    /**
     * @ignore
     * Calcula los porcentajes de repetición por día de la semana
     */
    private function CalcularRepeticionesPorDia(){
        $count = 0;

        foreach( $this->Days as $value){
            $count += $value;
        }

        if($count > 0){
            foreach( $this->Days as $key => $value){
                $valor = (($this->Days[$key] / $count) * 100);
                $this->Days[$key] = number_format($valor, 2);
            }
        }
    }

    /**
     * @ignore
     * Método para las estadísticas por días de la semana
     */
    private function SetDataOfDays(){
        // Agrupar las reservas de clientes por día de la semana
        $this->SetAgruparComensalesPorDia();
        // Contabilizar las repeticiones
        $this->CalcularRepeticionesPorDia();
        // Serializar el resultado
        $this->Days = json_encode($this->Days);
    }

    /**
     * @ignore
     * Agrupa las reservas por cliente
     */
    private function AgruparRepeticionesPorCliente(){
        foreach($this->Terminados as $item){
            // Obtener clientes registrados
            $keys = array_keys($this->Clients);
            // Comprobar si el cliente actual ya está registrado
            if(in_array($item->Client, $keys)){
                $this->Clients[$item->Client] += 1;
            }
            else{
                $this->Clients[$item->Client] = 1;
            }
        }
    }

    /**
     * @ignore
     * Calcula la asiduidad de cada cliente registrado
     */
    private function CalcularAsiduidadPorCliente(){
        foreach($this->Clients as $value){
            if ($value == 1){
                $this->NoRepite ++;
            }
            elseif($value == 2){
                $this->Repite++;
            }
            elseif($value > 2){
                $this->Asiduo++;
            }
        }

        $count = count($this->Clients);

        if($count > 0 ){
            $this->NoRepite = number_format(($this->NoRepite / $count)*100,2);
            $this->Repite = number_format(($this->Repite / $count)*100,2);
            $this->Asiduo = number_format(($this->Asiduo / $count)*100,2);
        }
    }

    /**
     * @ignore
     * Método para las estadísticas de los clientes
     */
    private function SetDataOfClients(){
        // Agrupar por cliente
        $this->AgruparRepeticionesPorCliente();
        // Caluclar repeticiones
        $this->CalcularAsiduidadPorCliente();
    }

    /**
     * @ignore
     * Configuración mínima de las propiedades del DTO
     * @param \DateTime $date Referencia a la fecha
     * @return \ReportDTO Referencia al DTO generado
     */
    private function SetReportDTO($date = NULL){
        if($date != NULL){
            $month = $date->format( "m" );
            $year = $date->format( "Y" );
            $dto = new ReportDTO();
            $dto->Mes = substr($this->Months[($month - 1)],0,3);
            $dto->Anyo = $year;
            $dto->Month = $month;
            $dto->Year = $year;
            return $dto;
        }
        return new ReportDTO();
    }

    /**
     * @ignore
     * Configura los datos del dto para la vista
     * @param \DateTime $date Referencia al objeto fecha
     * @param string $array_key Clave para el array de dtos
     * @param \Booking $item Referencia a la reserva
     */
    private function SetDtoInfo($date = NULL, $array_key = "", $item = NULL){
        $keys = array_keys($this->Reports);
        // evaluar si ya existe el dto
        if(in_array($array_key, $keys)){
            // Comprobar si es cursado o no
            if(in_array($item->State , $this->ValidStates)){
                $this->Reports[$array_key]->Cursadas += $item->Diners;
            }
            else{
                $this->Reports[$array_key]->Perdidas += $item->Diners;
            }
        }
        else{
            $dto = $this->SetReportDTO($date);

            if(in_array($item->State , $this->ValidStates)){
                $dto->Cursadas += $item->Diners;
            }
            else{
                $dto->Perdidas += $item->Diners;
            }

            $this->Reports[$array_key] = $dto;
        }
    }

    /**
     * @ignore
     * Método para generar el report
     */
    private function SetReport(){

        foreach($this->Terminados as $item){
            // Obtener la fecha de la reserva
            $date = new DateTime($item->Date);
            // Obtener la clave en el array de reports
            $array_key = $date->format( "Y-m" );
            // Configurar dto
            $this->SetDtoInfo($date, $array_key, $item);
        }
        usort($this->Reports, "ReportModel::OrderByDate");
    }

    /**
     * Filtro para ordenar registros ReportDTO por la fecha
     * @param \ReportDTO $a Referencia al operando 1 de la comparación
     * @param \ReportDTO $b Referencia al operando 2 de la comparación
     * @return int estado e la comparación
     */
    public static function OrderByDate($a, $b){
        $returnValue = 0;

        if($a->Year > $b->Year){
            $returnValue = 1;
        }
        elseif ($a->Year < $b->Year){
            $returnValue = -1;
        }
        else{
            if($a->Month > $b->Month){
                $returnValue = 1;
            }
            elseif ($a->Month < $b->Month){
                $returnValue = -1;
            }
        }

        return $returnValue;
    }

    /**
     * Filtro para ordenar registros ReportDTO por la fecha
     * @param \ReportDTO $a Referencia al operando 1 de la comparación
     * @param \ReportDTO $b Referencia al operando 2 de la comparación
     * @return int estado e la comparación
     */
    public static function OrderInverseByDate($a, $b){
        $returnValue = 0;

        if($a->Year < $b->Year){
            $returnValue = 1;
        }
        elseif ($a->Year > $b->Year){
            $returnValue = -1;
        }
        else{
            if($a->Month < $b->Month){
                $returnValue = 1;
            }
            elseif ($a->Month > $b->Month){
                $returnValue = -1;
            }
        }

        return $returnValue;
    }
}
