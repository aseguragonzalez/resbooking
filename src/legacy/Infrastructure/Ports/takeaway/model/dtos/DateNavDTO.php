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
 * Description of DateNavDTO
 *
 * @author manager
 */
class DateNavDTO {

    protected $NextDate = NULL;

    public $NextText = "";

    public $Next = "";

    protected $CurrentDate = NULL;

    public $CurrentText = "";

    public $Current = "";

    protected $PrevDate = NULL;

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
            return FALSE;
        }
        return TRUE;
    }
}
