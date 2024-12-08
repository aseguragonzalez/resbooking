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
 * Gestión de ficheros y directorios
 *
 * @author alfonso
 */
class FileManager{

    /**
     * Obtiene la colección de estructuras [ Path, Name ] que identifican
     * a cada uno de los ficheros del path pasado como parámetro
     * @param string $path Ruta a listar
     * @return array
     */
    public static function GetFiles($path = ""){
        // Iniciar array a devolver
        $array = array();
        // Validar path
        if($path == "") {
            return $array;
        }
        // Abrir descriptor del path
        if(($fileManager = opendir($path))== true) {
            while (false !== ($file = readdir($fileManager))){
                // Crear estructura de datos a guardar
                $item =
                    array( "Path" =>  $path."/".$file ,"Name" => $file );
                // Agregar estructura al array final
                array_push($array, $item);
            }
                    // Cerrar descriptor
            closedir($fileManager);
        }
        // retornar array de estructuras
        return $array;
    }

    /**
     * Obtiene la colección de estructuras [ Path, Name ] que identifican
     * a cada uno de los ficheros del path pasado como parámetro filtrados
     * por la extensión
     * @param string $path Ruta de acceso
     * @param string $extension Extensiones a filtrar
     * @return array
     */
    public static function GetFilterFiles($path = "", $extension = ""){
        // Iniciar array a retornar
        $array = array();
        // Validar el path a inspeccionar
        if($path == ""){
            return $array;
        }

        // Validar la extensión para filtrar
        if($extension == ""){
            return FileManager::GetFiles($path);
        }
            // Abrir el descriptor
        if(($fileManager = opendir($path))==true){
            while(false !== ($file = readdir($fileManager))) {
                // Determinar la posición de la extensión del fichero
                $posicion = strrpos($file, "." );
                // Validar la posición
                if(!is_numeric($posicion)){
                    continue;
                }
                // Extraer la extensión del fichero
                $ext = substr( $file, $posicion + 1);
                // Validar la extensión
                if($ext != "" && stristr($extension, $ext)){
                    // Iniciar estructura a agregar
                    $item = array( "Path" =>
                        $path."/".$file ,"Name" => $file );
                    // Agregar estructura al array a devolver
                    array_push($array, $item);
                }
            }
            // Cerrar el descriptor
            closedir($fileManager);
        }
        // Retornar la colección
        return $array;
    }

    /**
     * Realiza una copia recursiva de un path origen en otro destino
     * @param string $source Ruta origen
     * @param string $destination Ruta destino
     */
    public static function CopyDirectory( $source, $destination ) {
        if ( is_dir( $source ) ) {
            // Crear directorio destino
            @mkdir( $destination, 0777 );
            // Asignar permisos del directorio destino
            chmod($destination, 0777);
            // Establecer referencia al directorio origen
            $directory = dir( $source );
            // Recorrer el descriptor
            while ( FALSE !== ( $readdirectory = $directory->read())){
                // Comprobar que no se apunta al
                // directorio actual o al padre
                if ( $readdirectory == '.' || $readdirectory == '..' ){
                    continue;
                }
                // definir el path a copiar
                $PathDir = $source . '/' . $readdirectory;
                // validar si el path corresponde a un fichero
                if(is_dir( $PathDir )){
                    // Realizar copia recursiva
                    FileManager::CopyDirectory( $PathDir,
                            $destination . '/' . $readdirectory );
                    continue;
                }
                // Copiar el fichero si se trata de fichero
                copy( $PathDir, $destination . '/' . $readdirectory );
            }
            // Cerrar descriptor
            $directory->close();
        }else {
            // Copiar el fichero origen en el destino
            copy( $source, $destination );
        }
    }
}
