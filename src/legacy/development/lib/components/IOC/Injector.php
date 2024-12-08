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
 * Clase para la inyección de dependencias. Requiere que exista
 * una configuración previa de las dependencias en un fichero
 * de configuraciones (xml)
 *
 * @author alfonso
 */
class Injector{

    /**
     * Referencia al inyector de dependencias cargado
     */
    private static $_injector = null;

    /**
     * Referencia al repositorio de interfaces
     */
    private $_repository = null;

    /**
     * Constructor privado
     */
    private function __construct($fileName = ""){
        // Validación del nombre de fichero de configuración
        // pasado por parámetro
        $sFileName = ($fileName == "" || $fileName == null)
                ? "config.xml" : $fileName;
        // Cargamos el contenido de la configuración desde el xml
        $configurator = simplexml_load_file($sFileName);
        // Iniciar la referencia al repositorio de traducciones
        $this->_repository = array();
        // Leer XML
        $this->Load($configurator);
    }

    /**
     * Carga la información del fichero de configuración
     * @var object $xml Nodo xml a cargar
     */
    private function Load($xml = null){
        if(isset($xml) && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->interfaces->children();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $this->_repository[(string)$attributes->name] =
                        array(
                            "mapTo" => (string)$attributes->mapTo,
                            "src" => (string)$attributes->src
                        );
            }
        }
    }

    /**
     * Se encarga de obtener una nueva instancia que implemente la
     * interfaz identificada con el nombre interfaceName.
     * @param string $interfaceName Nombre de la interfaz a resolver
     * @param boolean $include Indica si hay que cargar la referencia
     * @return object
     * @throws InjectorException
     */
    public function Resolve($interfaceName = "", $include = FALSE){
        // Buscar la referencia en el repositorio
        if(!array_key_exists($interfaceName, $this->_repository)){
            return null;
        }
        // Recuperar los datos de la interfaz a resolver
        $data = $this->_repository[$interfaceName];
        // Comprobar los datos
        if($data["mapTo"] == "" || $data["src"] == ""){
            return NULL;
        }
        // Cargar dependencias
        if($include == TRUE){
            require_once($data["src"]);
        }
        // Crear instancia del objeto
        $object = new $data["mapTo"]();
        // Comprobar que la instancia cumple con la interfaz definida
        if (!$object instanceof $interfaceName){
            throw new \InjectorException($interfaceName);
        }
        // Devolver la instancia
        return $object;
    }

    /**
     * Obtiene una referencia al gestor de dependencias.
     * @param string $fileName Ruta del fichero de configuración
     * @return \Injector Referencia al injector actual
     */
    public static function GetInstance($fileName = ""){
        // Comprobación si ya existe una referencia
        // al gestor de dependencias
        if(Injector::$_injector == null){
            Injector::$_injector = new \Injector( $fileName );
        }
        // Retornar referencia al gestor actual
        return Injector::$_injector;
    }
}
