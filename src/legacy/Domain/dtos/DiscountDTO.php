<?php

declare(strict_types=1);

/**
 * DTO resumen para la gestión de descuentos. Contiene la información
 * del registro de descuento y su configuración
 *
 * @author manager
 */
class DiscountDTO extends \DiscountOn{

    /**
     * Colección de configuraciones disponibles para el descuento
     * @var array
     */
    public $Configuration = [];

    /**
     * Constructor
     * @param \DiscountOn $discountOn Referencia al descuento
     * @param \DiscountOnConfiugration $configuration
     * Colección de configuraciones
     */
    public function __construct($discountOn = NULL,
            $configuration = NULL){
        if($discountOn != NULL){
            $this->Id = $discountOn->Id;
            $this->Value = $discountOn->Value;
            $this->Project = $discountOn->Project;
            $this->Service = $discountOn->Service;
            $this->Max = $discountOn->Max;
            $this->Min = $discountOn->Min;
            $this->Start = $discountOn->Start;
            $this->End = $discountOn->End;
            $this->State = $discountOn->State;
        }

        if(is_array($configuration)){
            $this->Configuration = $configuration;
        }
    }

    /**
     * Obtiene una referencia a la entidad descuento
     * @return \DiscountOn Instancia del descuento
     */
    public function GetDiscountOn(){
        $discount = new \DiscountOn();
        $discount->Id = $this->Id;
        $discount->Value = $this->Value;
        $discount->Project = $this->Project;
        $discount->Service = $this->Service;
        $discount->Max = $this->Max;
        $discount->Min = $this->Min;
        $discount->Start = $this->Start;
        $discount->End = $this->End;
        $discount->State = $this->State;
        return $discount;
    }
}
