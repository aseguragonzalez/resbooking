<?php

declare(strict_types=1);

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
    private static $_reference = null;

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
        if(ConfigurationRepository::$_reference == null){
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
