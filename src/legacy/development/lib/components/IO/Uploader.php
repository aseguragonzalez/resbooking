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
 * Clase para la gestión de subidas de ficheros en peticiones post
 *
 * @author alfonso
 */
class Uploader{

    /**
     * Path de Almacenamiento
     * @var string $Path Ruta de almacenamiento del fichero
     */
    public $Path;

    /**
     * Extensiones permitidas
     * @var array $AllowedExts Colección de extensiones permitidas
     */
    public $AllowedExts;

    /**
     * Nombre de valirable $_FILE
     * @var string $Name Nombre de valirable $_FILE
     */
    public $Name;

    /**
     * Constructor
     */
    public function __construct(){
        $this->Path = "";
        $this->AllowedExts = array("php", "PHP");
        $this->Name = "file";
    }

    /**
     * Método para subir un fichero con la configuración del objeto
     * @return void
     * @throws UploaderException
     */
    public function Upload(){
        // Obtener la variable File
        $file =  $_FILES[$this->Name];
        // Extensión del fichero a guardar
        $extension = end(explode(".", $file["name"]));
        // Comprobar la extensión
        if (in_array($extension, $this->AllowedExts)){
            if ($file["error"] > 0){
                throw new \UploaderException("Return Code: "
                        . $file["error"]);
            }

            if (file_exists($this->Path. $file["name"])){
                throw new \UploaderException($file["name"]
                        ." already exists.");
            }

            move_uploaded_file($file["tmp_name"],
                    $this->Path.$file["name"]);

            return;
        }

        throw new \UploaderException("Invalid file");
    }

    /**
     * Método estático para la subida de ficheros.
     * @param string $sFile Nombre de la variable a recoger en $_FILE
     * @param string $path Ruta Relativa donde almacenar el fichero
     * @param array $extension Conjunto de extensiones válidas
     * @param boolean $overRide Indica si se sobreescribe
     * @return void
     * @throws UploaderException
     */
    public static function UploadFile($sFile="",
            $path="", $extension="", $overRide = false){
        // Obtener la variable File
        $file =  $_FILES[$sFile];
        // extraer nombre
        $fileName = $file[ "name" ];
        // Obtener extensión
        $sExtension = ($extension=="")
                ? explode(".", $fileName)
                : $extension;
        // Extensión del fichero a guardar
        $ext = end($sExtension);
        // Comprobar la extensión
        if (in_array($ext, $extension)){
            if ($file["error"] > 0){
                throw new \UploaderException("Return Code: "
                        . $file["error"]);
            }

            $exist = file_exists($path. $file["name"]);

            if($exist && !$overRide){
                throw new \UploaderException($file["name"]." "
                        . "already exists.");
            }
            elseif($exist && $overRide){
                unlink($path. $file["name"]);
            }

            move_uploaded_file($file["tmp_name"], $path.$file["name"]);

            return;
        }

        throw new \UploaderException("Invalid file");
    }
}
