<?php

declare(strict_types=1);

/**
 * Description of DateNavDTO
 *
 * @author manager
 */
class DateNavDTO {

    protected $NextDate = null;

    public $NextText = "";

    public $Next = "";

    protected $CurrentDate = null;

    public $CurrentText = "";

    public $Current = "";

    protected $PrevDate = null;

    public $PrevText = "";

    public $Prev = "";

    public function __construct($sDate = "") {
        if(empty($sDate) || !$this->ValidateDate($sDate)){
           $sDate = "NOW";
        }
        // Instanciar las fechas
        $this->CurrentDate = new \DateTime($sDate);
        $this->Current = $this->CurrentDate->format("Y-m-d");
        $this->CurrentText = strftime("%A %e de %B del %Y",
                $this->CurrentDate->getTimestamp());

        $this->NextDate = new \DateTime($sDate);
        $this->NextDate->add(new DateInterval('P1D'));
        $this->Next = $this->NextDate->format("Y-m-d");
        $this->NextText = strftime("%A %e de %B del %Y",
                $this->NextDate->getTimestamp());

        $this->PrevDate = new \DateTime($sDate);
        $this->PrevDate->sub(new DateInterval('P1D'));
        $this->Prev = $this->PrevDate->format("Y-m-d");
        $this->PrevText = strftime("%A %e de %B del %Y",
                $this->PrevDate->getTimestamp());
    }

    /**
     * Validación de la fecha
     * @param string $date Fecha con formato Y-m-d
     * @return boolean
     */
    private function ValidateDate($date = ""){
        try{
            $d = new DateTime($date);
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }
}
