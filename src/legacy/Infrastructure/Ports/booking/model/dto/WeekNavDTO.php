<?php

declare(strict_types=1);

/**
 * DTO para la gestionar las opciones de navegación en un cuadro semanal
 *
 * @author alfonso
 */
class WeekNavDTO {

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Número de la semana previa
     * @var int
     */
    public $Prev = 0;

    /**
     * Año sobre el que se consulta
     * @var int
     */
    public $PrevYear = 2014;

    /**
     * Número de la semana siguiente
     * @var type
     */
    public $Next = 0;

    /**
     * Año sobre el que se consulta
     * @var type
     */
    public $NextYear = 2014;

    /**
     * Semana actual
     * @var int
     */
    public $Current = 0;

    /**
     * Año en el que se realiza la consulta
     * @var int
     */
    public $CurrentYear = 2014;

    /**
     * Fecha del Lunes
     * @var string
     */
    public $FechaLunes = "";

    /**
     * Fecha par ael domingo
     * @var string
     */
    public $FechaDomingo = "";

    /**
     *
     * @var int
     */
    protected $MaxWeek = 52;

    /**
     * Configura el DTO con los parámetros indicados
     * @param array $days Colección de días configurados
     * @param int $year Anyo en el que se desea configurar
     * @param int $week Semana en la que se desea configurar
     */
    public function SetWeekInfo($days = [], $year = 0, $week = 0){
        $this->DaysOfWeek = $days;
        $this->SetCurrentYearWeek($week, $year);
        $this->SetWeekAndYear();
        $this->SetControlDates();
    }

    /**
     * Establece los valores solicitados de año y semana
     * @param int $week Semana sobre la que se solicita el calendario
     * @param int $year Año sobre el que se ejecuta la consulta
     */
    private function SetCurrentYearWeek($week = 0, $year = 0){
        $date = new DateTime( "NOW" );
        $strDate = "-12-31";

        if($week == 0){
            $this->Current = intval($date->format("W"));
        }
        else{
            $this->Current = $week;
        }

        if($year == 0){
            $date2 = new DateTime($date->format("Y") . $strDate);
            $this->MaxWeek = $date2->format("W");
            $this->CurrentYear = intval($date->format("Y"));
        }
        else{
            $maxDate = new \DateTime($year . $strDate);
            $this->MaxWeek = $maxDate->format("W");
            $this->CurrentYear = $year;
        }
    }

    /**
     * Establece el valor de la semana y año para los controles de navegación
     */
    private function SetWeekAndYear(){
       if($this->Current > $this->MaxWeek){
           $this->Current = 1;
           $this->CurrentYear = $this->CurrentYear +1;
       }

       if($this->Current < 1){
           $this->CurrentYear = $this->CurrentYear -1;
           $date = new \DateTime($this->CurrentYear."-12-31");
           $this->Current = $date->format("W");
           $this->MaxWeek = $this->Current;
       }

       $this->Next = ($this->Current == $this->MaxWeek) ? 1 : $this->Current +1;
       $this->NextYear = ($this->Current == $this->MaxWeek)
               ? $this->CurrentYear+1
               : $this->CurrentYear;

        $this->PrevYear = ($this->Current == 1)
               ? $this->CurrentYear-1
               : $this->CurrentYear;

        if($this->Current == 1){
            $date = new \DateTime($this->PrevYear."-12-31");
            $this->Prev = $date->format("W");
        }
        else{
            $this->Prev = $this->Current -1;
        }
    }

    /**
     * Establece las fechas para los controles y títulos del modelo
     */
    private function SetControlDates(){
       $d = new DateTime();
       foreach($this->DaysOfWeek as $day){
           $d->setISODate($this->CurrentYear, $this->Current, $day->DayOfWeek);
           $day->Date = $d->format("Y-m-d");
           $day->sDate = strftime("%e %b", $d->getTimestamp());
           if($day->DayOfWeek == 1){
               $this->FechaLunes = $d->format("d/m/Y");
           }
           if($day->DayOfWeek == 7){
               $this->FechaDomingo = $d->format("d/m/Y");
           }
       }
    }
}
