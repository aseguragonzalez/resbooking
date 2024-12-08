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
 * DTO para gestionar las opciones de navegación en un calentario
 *
 * @author alfonso
 */
class DateNavDTO {

    /**
     * Fecha de búsqueda
     * @var string
     */
    public $Fecha = "";

    /**
     * Fecha del pie de tabla
     * @var string
     */
    public $FechaPie = "";

    /**
     * Texto a visualizar en la fecha
     * @var string
     */
    public $FechaTexto = "";

    /**
     * Fecha previa
     * @var string
     */
    public $PrevFecha = "";

    /**
     * Fecha posterior
     * @var string
     */
    public $PostFecha = "";

    /**
     * Constructor
     * @param string $sDate Fecha de configuración
     */
    public function __construct($sDate = ""){
        $this->SetDate($sDate);
    }

    /**
     * Configuración de las fechas a utilizar en el filtro y el calendario
     * @param string $sdate Fecha
     * @return string
     */
    public function SetDate($sdate = ""){
        // Iniciar las fechas
        $fechas = $this->GetFechas($sdate);
        $sDate = $fechas["sDate"];
        $date = $fechas["date"];
        $prevDate = $fechas["prev"];
        $nextDate = $fechas["next"];
        // Cálculos fecha previa y posterior
        $intervalo = new DateInterval('P1D');
        $intervalo2 = new DateInterval('P1D');
        $prevDate->sub($intervalo);
        $nextDate->add($intervalo2);
        // Formato para las fechas
        $formato = "Y-m-d";
        // Guardar la fecha en el modelo
        $this->Fecha = $date->format( $formato );
        $this->FechaPie =
                strftime("%A %d de %B de %Y", $date->getTimestamp());
        $this->FechaTexto =
                strftime("%A %d de %B", $date->getTimestamp());
        $this->PrevFecha = $prevDate->format($formato);
        $this->PostFecha = $nextDate->format($formato);
        // retornar cadena de fecha
        return $sDate;
    }

    /**
     * Obtiene el conjunto de objetos DateTime para las fechas del formulario
     * @param string $sdate Fecha actual
     * @return array Colección de fechas
     */
    private function GetFechas($sdate = ""){
        // Formato para las fechas
        $formato = "Y-m-d";
        // Establecer fecha
        $sDate = ($sdate == "") ? "NOW" : $sdate;
        // Iniciar conversiones
        try{
            $date = new DateTime($sDate);
            $prevDate = new DateTime($sDate);
            $nextDate = new DateTime($sDate);
        }
        catch(Exception $e){
            // En el caso de no ser una fecha válida
            $sDate = (new DateTime("NOW"))->format( $formato );
            $date = new DateTime($sDate);
            $prevDate = new DateTime($sDate);
            $nextDate = new DateTime($sDate);
        }

        return array(
            "sDate" => $sDate,
            "date" => $date,
            "prev" => $prevDate,
            "next" => $nextDate);
    }

}
