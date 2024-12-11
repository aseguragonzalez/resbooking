<?php

declare(strict_types=1);

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
