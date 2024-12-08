<?php

/**
 * Model para la generación de los informes detallados(Resumen)
 */
class SummaryModel extends \ResbookingModel{

    /**
     * Número de registros en estado reservado
     * @var int
     */
    public $NReservados = 0;

    /**
     * Número de registros en estado llegado
     * @var int
     */
    public $NLlegados = 0;

    /**
     * Número de registros en estado sentado
     * @var int
     */
    public $NSentados = 0;

    /**
     * Número de registros en estado terminado
     * @var int
     */
    public $NTerminados = 0;

    /**
     * Número de registros en estado anulado
     * @var int
     */
    public $NAnulados = 0;

    /**
     * Número de registros en estado NoShow
     * @var int
     */
    public $NNoShow = 0;

    /**
     * Número de registros en estado Cursados
     * @var int
     */
    public $NCursados = 0;

    /**
     * Número de registros en estado Perdidos
     * @var int
     */
    public $NPerdidos = 0;

    /**
     * Número de registros en estado Anotado
     * @var int
     */
    public $NAnotados = 0;

    /**
     * Número de registros sin estado
     * @var int
     */
    public $NPendientes = 0;

    /**
     * Número total de comensales en estado reservado
     * @var int
     */
    public $TotalReservados = 0;

    /**
     * Número total de comensales en estado llegado
     * @var int
     */
    public $TotalLlegados = 0;

    /**
     * Número total de comensales en estado sentado
     * @var int
     */
    public $TotalSentados = 0;

    /**
     * Número total de comensales en estado terminado
     * @var int
     */
    public $TotalTerminados = 0;

    /**
     * Número total de comensales en estado anulado
     * @var int
     */
    public $TotalAnulados = 0;

    /**
     * Número total de comensales en estado NoShow
     * @var int
     */
    public $TotalNoShow = 0;

    /**
     * Número total de comensales cursados
     * @var int
     */
    public $TotalCursados = 0;

    /**
     * Número total de comensales perdidos
     * @var int
     */
    public $TotalPerdidos = 0;

    /**
     * Número total de comensales en estado anotado
     * @var int
     */
    public $TotalAnotados = 0;

    /**
     * Número total de comensales con reserva sin estado
     * @var int
     */
    public $TotalPendientes = 0;

    /**
     * Número de registros totales
     */
    public $NTotal = 0;

    /**
     * Fecha de búsqueda
     */
    public $Fecha = "";

    /**
     * Mes en curso
     */
    public $Current = "";

    /**
     * Mes en curso
     */
    public $Month = "";

    /**
     * Anyo en curso
     */
    public $Year = "";

    /**
     * Mes anterior
     */
    public $Prev = "";

    /**
     * Mes posterior
     */
    public $Next = "";

    /**
     * Año anterior
     */
    public $PrevYear= "";

    /**
     * Siguiente año
     */
    public $NextYear = "";

    /**
     * Colección de estados disponibles
     */
    public $States = [];

    /**
     * Colección de años
     */
    public $Years = [];

    /**
     * Colección de reservas
     * @var array
     */
    public $Entities = [];

    /**
     * Colección de reservas en estado reservado
     * @var array
     */
    public $Reservados = [];

    /**
     * Colección de reservas en estado llegado
     * @var array
     */
    public $Llegados = [];

    /**
     * Colección de reservas en estado sentado
     * @var array
     */
    public $Sentados = [];

    /**
     * Colección de reservas en estado terminado
     * @var array
     */
    public $Terminados = [];

    /**
     * Colección de reservas en estado anulado
     * @var array
     */
    public $Anulados = [];

    /**
     * Colección de reservas en estado NoShow
     * @var array
     */
    public $NoShows = [];

    /**
     * Colección de reservas en estado Anotado
     * @var array
     */
    public $Anotados = [];

    /**
     * Colección de reservas sin estado asignado
     * @var array
     */
    public $Pendientes = [];

    /**
     * @ignore
     * Constructor de la clase
     */
    public function __construct(){
        // LLamada al constructor
        parent::__construct();
        // Cargar los estados posibles
        $this->States = $this->Dao->Get( "State" );
    }

    /**
     * Método que obtiene la lista de reservas para una fecha
     */
    public function GetSummary($dto = NULL){
        // Obtener parámetro de fecha
        $date = $this->SetFecha($dto);
        // establecer el filtro
        $filter = [ "Project" => $this->Project,
            "Date" => $date, "BookingSource" => 1 ];
        // Obtener todas las reservas
        $this->Entities = $this->Dao->GetByFilter( "Booking", $filter);
         // Contar, ordenar, filtar... las reservas
        $this->CountEntities();
    }

    /**
     * Configuración de las fechas a utilizar en el filtro y el calendario
     */
    private function SetFecha($dto = null){
        // Validación inicial del parámetro
        if($dto == null
                || !is_numeric($dto->month)
                || $dto->month <= 0
                || !is_numeric($dto->year)
                || $dto->year <= 0) {
                $dto = new \SummaryDTO();
                $dto->year = intval(date( "Y" ));
                $dto->month = intval(date( "m" ));
        }
        // Establecer la fecha del filtro
        $date = "%".$dto->year."-".$dto->month."%";
        $this->Prev = ($dto->month > 1) ? ($dto->month - 1) : 12;
        $this->Next = ($dto->month < 12) ? ($dto->month + 1) : 1;
        $this->PrevYear =  ($dto->month > 1)  ? $dto->year : $dto->year -1;
        $this->NextYear =  ($dto->month < 12)  ? $dto->year : $dto->year + 1;
        $this->Year = $dto->year;
        $this->Current = $dto->month;
        $this->Month = $this->GetMonthName($dto->month);
        $this->FormatearFechas();
        return $date;
    }

    /**
     * Formateado de las fechas en las propiedades del modelo
     */
    private function FormatearFechas(){
        if($this->Prev < 10) {
            $this->Prev = "0".$this->Prev;
        }
        if($this->Next < 10) {
            $this->Next = "0".$this->Next;
        }

        $current = intval(date( "Y"));

        for($i = 2013; $i < $current; $i++){
            $this->Years[] = $i;
        }
    }

    /**
     * Obtener el nombre del mes por su número de mes
     */
    private function GetMonthName($id = 1){
        // Array de meses
        $months = [ "Enero", "Febrero", "Marzo", "Abril", "Mayo",
            "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
            "Noviembre", "Diciembre" ];
        return $months[$id-1];
    }

    /**
     * Realiza la cuenta de las distintas reservas según el estado en el que se encuentran
     */
    private function CountEntities(){

        $this->NTotal = count($this->Entities);
        // contar los estados reservados
        foreach($this->Entities as $item){
            $date = new DateTime($item->Date);
            $item->Date = $date->format( "d-m-Y" );
            // Validar si es estado reservado
            $this->FiltrarRegistro($item);
        }

        $this->NCursados += $this->NTerminados;
        $this->TotalCursados += $this->TotalTerminados;

        $this->NPerdidos += $this->NAnulados;
        $this->NPerdidos += $this->NNoShow;
        $this->TotalPerdidos += $this->TotalAnulados;
        $this->TotalPerdidos += $this->TotalNoShow;

        usort($this->Reservados, "SummaryModel::OrderByDate");
        usort($this->Llegados, "SummaryModel::OrderByDate");
        usort($this->Sentados, "SummaryModel::OrderByDate");
        usort($this->Terminados, "SummaryModel::OrderByDate");
        usort($this->Anulados, "SummaryModel::OrderByDate");
        usort($this->NoShows, "SummaryModel::OrderByDate");
        usort($this->Anotados, "SummaryModel::OrderByDate");
    }

    /**
     * Contabilizar los datos de las reservas
     * @param \Booking $item Referencia a la reserva
     */
    private function FiltrarRegistro($item = NULL){
        switch($item->State){
            case 1:
                $this->NReservados++;
                $this->TotalReservados += $item->Diners;
                $this->Reservados[] = $item;
                break;
            case 2:
                $this->NLlegados++;
                $this->TotalLlegados += $item->Diners;
                $this->Llegados[] = $item;
                break;
            case 3:
                $this->NSentados++;
                $this->TotalSentados += $item->Diners;
                $this->Sentados[] = $item;
                break;
            case 4:
                $this->NTerminados++;
                $this->TotalTerminados += $item->Diners;
                $this->Terminados[] = $item;
                break;
            case 5:
                $this->NNoShow++;
                $this->TotalNoShow += $item->Diners;
                $this->NoShows[] = $item;
                break;
            case 6:
                $this->NAnulados++;
                $this->TotalAnulados += $item->Diners;
                $this->Anulados[] = $item;
                break;
            case 7:
                $this->NAnotados++;
                $this->TotalAnotados += $item->Diners;
                $this->Anotados[] = $item;
                break;
            case null:
                $this->NPendientes++;
                $this->TotalPendientes += $item->Diners;
                $this->Pendientes[] = $item;
                break;
        }
    }

    /**
     * Comparador de objetos por la propiedad Date (Fecha)
     * @param Object $a Referencia al primer objeto
     * @param Object $b Referencia al segundo objeto
     * @return boolean Resultado de la comparación
     */
    public static function OrderByDate($a, $b){
        return strcmp($a->Date, $b->Date);
    }
}
