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
 * Obtiene la colección de referencias del paquete
 * @param string $file path al xml de pruebas
 * @return array Colección de pruebas de reservas definidias
 */
function getReferences($file){
    $paths = array();
    $xml = simplexml_load_file($file);
    $references = $xml->references->children();
    foreach($references as $reference){
        $attrs = $reference->attributes();
        $paths[] = (string)$attrs->path;
    }
    return $paths;
}

/**
 * Obtiene los contenidos de los ficheros especificados en el config.xml
 * @return string Contenido del fichero conjunto
 */
function readFiles($file = "config.xml"){
    $references = getReferences($file);
    $fileContent = "";
    foreach($references as $ref){
        $fileContent .= getContent($ref);
    }
    return $fileContent;
}

/**
 * Descarga del fichero solicitado
 * @param string $filename Nombre del fichero
 * @param string $contentType Tipo MIME del contenido
 * @param string $content Contenido del fichero
 */
function putFile($filename, $contentType, $content){
    header( "Content-type: $contentType" );
    header( "Content-Disposition: attachment; filename='$filename'" );
    echo $content;
    exit();
}

/**
 * Genera una copia del archivo que se descarga
 * @param string $filename Nombre del fichero
 * @param string $content Contenido del fichero
 */
function saveFile($filename="", $content=""){
    $date = new DateTime("NOW");
    $hash = hash(_HASH_, $content);
    $path = _PATH_.$hash."-".$date->format("Ymdhis")."-".$filename;
    file_put_contents($path, $content);
}

/**
 * Elimina los comentarios de código de tipo /* * /
 * @param string $content Contenido del fichero fuente
 * @return string contenido del fichero de salida
 */
function deleteCommentsTypeI($content = ""){
    $next = false;
    do{
        $first = strpos( $content,  "/*" );
        $last = strpos( $content, "*/" );
        $next = !($first === false && $last === false);
        if($first < $last){
            $comment = substr($content, $first, ($last - $first + 2));
            $content = str_replace( $comment, "", $content);
        }
    }while($next == true);
    return $content;
}

/**
 * Elimina los comentarios de código de tipo //
 * @param string $content Contenido del fichero fuente
 * @return string contenido del fichero de salida
 */
function deleteCommentsTypeII($content = ""){
    $pattern = '/\/\/(.*)\n/i';
    $replaceValue = ' ';
    return preg_replace($pattern, $replaceValue , $content);
}

/**
 * Elimina los comentarios del contenido.
 * @param string $sContent Contenido del fichero de salida
 * @param boolean $isMin Indica si debe ser un fichero compacto
 * @return string
 */
function clearContents($sContent = "", $isMin = FALSE ){
    // Elimina un tipo de comentarios
    $iContent = deleteCommentsTypeI($sContent);
    // Elimina un tipo de comentarios
    $content = deleteCommentsTypeII($iContent);
    // Caracteres especiales a eliminar
    $caracteres = ($isMin == TRUE)
            ? array( "\n", "\t", "  " ) : array();
    // eliminar lista de caracteres
    return str_replace($caracteres, "", $content);
}

/**
 * Obtiene el contenido del fichero de texto eliminado los marcadores
 * de inicio y fin de script php
 * @param string $path
 * @return string Contenido del fichero
 */
function getContent($path = ""){
    $values = array( "<?php", "?>", "<?" );
    if($path != "" && file_exists($path)){
        return str_replace($values, "", file_get_contents($path));
    }
    return "";
}

/**
 * Procesado del nombre del paquete a descargar
 * @param string $packageName Nombre del paquete seleccionado
 * @param boolean $isDebug
 * @param boolean $isMin
 * @return string Nombre del paquete generado
 */
function processPackageName($packageName = "",
        $isDebug = FALSE, $isMin = TRUE){

    $name = ($packageName == "" )
            ? "package" : str_replace(".php", "", $packageName);
    if(strlen($name) > 30){
        $name = substr($name, 0, 30);
    }

    if($isDebug == TRUE){
        $name = "debug.$name";
    }

    if($isMin == TRUE){
        $name = "$name.min";
    }

    return strtolower("$name.php");
}

/**
 * Obtiene la colección de referencias establecidas
 * en el fichero de configuraciones
 * @param string $path Ruta al fichero de configuración
 * @return array
 */
function getReferencesFromFile($path = "config.xml"){
    $xml = simplexml_load_file($path);
    $references = $xml->references->children();
    $items = [];
    foreach($references as $reference){
        $attrs = $reference->attributes();
        $data = [
                "name" => (string)$attrs->name,
                "path" => (string)$attrs->path,
                "hash" => md5((string)$attrs->path)];
        settype($data, "object");
        $items[$data->hash] = $data;
    }
    return $items;
}

/**
 * Obtiene la colección con los hash de las referencias seleccionadas.
 * El hash se utiliza para identificar univocamente la referencia selecionada
 * @param array $ref Referencia al array de controles de formulario
 * @return array
 */
function getSelectedReferencesFromArray($ref = NULL){
    $items = [];
    if($ref != NULL && is_array($ref)){
        $keys = array_keys($ref);
        foreach($keys as $name){
            if(strpos($name, "chk_") !== FALSE){
                $hash = str_replace( "chk_", "", $name );
                $items[$hash] = $hash;
            }
        }
    }
    return $items;
}

/**
 * Filtra la colección de referencias utilizando un
 * array de hashes de referencias
 * @param array $hashes Colección de hashes
 * @param array $references Colección de referencias a filtrar
 * @return array Referencias filtradas
 */
function filterSelectedReferences($hashes  = NULL, $references = NULL){
    $items = [];
    if(is_array($hashes) && is_array($references)){
        foreach($hashes as $hash){
            if(isset($references[$hash])){
                $items[$hash] = $references[$hash];
            }
        }
    }
    return $items;
}

/**
 * Obtiene un único fichero con el contenido de todos
 * los ficheros seleccionados
 * @param array $files Referencia a la colección de ficheros seleccionados
 * @return string
 */
function readFilterFiles($files = NULL){
    $fileContent = "";
    if(is_array($files)){
        foreach($files as $ref){
            $fileContent .= getContent($ref->path);
        }
    }
    return $fileContent;
}
