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
 * Implementación para la generación de password aleatorio
 * y cálculo de funciones Hash
 *
 * @author alfonso
 */
class PasswordFactory{

    /**
     * Referencia al generador actual
     * @var \PasswordFactory Referencia a la factoría actual
     */
    protected static $Factory = null;

    /**
     * Cadena de texto con el conjunto de caracteres válidos
     * @var string Alfabeto disponible
     */
    protected $Alphabet = "";

    /**
     * Longitud mínima configurada para el password
     * @var int Longitud mínima de contraseña
     */
    protected $MinLength = 8;

    /**
     * Longitud máxima configurada para el password
     * @var int Longitud máxima de contraseña
     */
    protected $MaxLength = 20;

    /**
     * Longitud por defecto para el password
     * @var int Longitud de contraseña
     */
    protected $Length = 12;

    /**
     * Constructor privado
     * @var string $sfile Nombre del fichero de configuración
     */
    private function __construct($sfile = ""){
        // asignar la ruta de fichero
        $file = ($sfile == "") ? "config.xml": $sfile;

        if(file_exists ($file)){
            // Cargamos el contenido de la configuración desde el xml
            $xml = simplexml_load_file($file);
            $attr1 = $xml->passwordfactory->alphabet->attributes();
            // Configuramos el alfabeto de generación
            $this->Alphabet = (string)$attr1["value"];
            $attr2 = $xml->passwordfactory->minlength->attributes();
            // Configuramos la longitud mínima para el password
            $this->MinLength = (string)$attr2["value"];
            $attr3 = $xml->passwordfactory->maxlength->attributes();
            // Configuramos la longitud máxima para el password
            $this->MaxLength = (string)$attr3["value"];
            $attr4 = $xml->passwordfactory->default->attributes();
            // Configuramos la longitud por defecto
            $this->Length = (string)$attr4["value"];
            // Cargar los datos de configuración
            return;
        }

        throw new \PasswordFactoryException( "config file not found" );
    }

    /**
     * Genera un password aleatorio la longitud indicada
     * @var int $length Longitud del password a generar
     */
    public function GetPassword($length = 12){
        // Validar la longitud de cadena
        if(!is_numeric($length)){
            $length = $this->Length;
        }
        else if($length < $this->MinLength){
            $length = $this->MinLength;
        }
        else if($length > $this->MaxLength){
            $length = $this->MaxLength;
        }
        // Se define una cadena de caractares. Te recomiendo que uses esta.
        $cadena = $this->Alphabet;
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena = strlen($cadena);
        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña, en mi caso 10, pero
        //puedes poner la longitud que quieras
        $longitudPass = $length;
        //Creamos la contraseña
        for($i=1 ; $i <= $longitudPass ; $i++){
            // Definimos numero aleatorio entre 0 y la longitud de la
            // cadena de caracteres-1
            $pos=rand(0,$longitudCadena-1);
            // Agregar un caracter al password
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }

    /**
     * Obtiene la transformación md5 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetMD5($text = ""){
        return hash( "md5", $text );
    }

    /**
     * Obtiene la transformación sha1 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA1($text = ""){
        return hash( "sha1", $text );
    }

    /**
     * Obtiene la transformación sha256 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA256($text = ""){
        return hash( "sha256", $text );
    }

    /**
     * Obtiene la transformación sha512 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA512($text = ""){
        return hash( "sha512", $text );
    }

    /**
     * Obtiene la instancia actual del generador de password
     * @param type $file
     * @return type
     */
    public static function GetInstance($file = ""){
        if(PasswordFactory::$Factory == null){
            PasswordFactory::$Factory = new \PasswordFactory($file);
        }
        return PasswordFactory::$Factory;
    }
}
