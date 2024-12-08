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
 * Clase para los controles option de un control Select
 *
 * @author alfonso
 */
class SelectControlItem{

    /**
     * Valor del control Option
     * @var string
     */
    public $Value = "";

    /**
     * Texto del control Option
     * @var string
     */
    public $Text = "";

    /**
     * Constructor
     * @param string $text Texto a utilizar
     * @param string $value Valor asociado
     */
    public function __construct($text="", $value = ""){
        $this->Text = $text;
        $this->Value = $value;
    }
}
