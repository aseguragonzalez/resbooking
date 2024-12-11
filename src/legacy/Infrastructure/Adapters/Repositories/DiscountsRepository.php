<?php

declare(strict_types=1);

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
