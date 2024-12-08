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

/*
Dependencias :
- Clase Injector para la inyección de componentes
- Componentes definidos : [ IDataAccessObject ]
- Clase ConfigurationManager para el acceso al config.xml
- Claves de config.xml : [ path, resources, connectionString ]
*/

/**
 * Clase base para los model
 */
class Model{

    /**
     * Referencia al inyector de dependencias
     * @var \Injector $Injector Gestor de inyección de dependencias
     */
    protected $Injector = null;

    /**
     * Referencia al objeto de acceso a datos
     * @var \IDataAccessObject Referencia al objeto de acceso a datos
     */
    protected $Dao = null;

    /**
     * Ruta base para enlaces
     * @var string $Path Ruta base para la navegación
     */
    public $Path = "";

    /**
     * Ruta base para recursos locales
     * @var string $Resources Ruta base par alos recursos
     */
    public $Resources = "";

    /**
     * Título de la página a renderizar
     * @var string $Title Cabecera del formulario
     */
    public $Title = "";

    /**
     * Array de opciones menú
     * @var array $Menu Colección de items para el menú de navegación
     */
    public $Menu = array();

    /**
     * Array de errores
     * @var array $ErrorList Colección de errores detectados
     */
    public $ErrorList = array();

    /**
     * Constructor
     */
    public function __construct(){
        // Obtener path
        $this->Path = ConfigurationManager
                ::GetKey( "path" );
        // Obtener ruta de recursos
        $this->Resources = ConfigurationManager
                ::GetKey( "resources" );
        // Obtener nombre de la cadena de conexión
        $connectionString = ConfigurationManager
                ::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString = ConfigurationManager
                ::GetConnectionStr($connectionString);
        // Cargar las referencias
        $this->Injector = Injector::GetInstance();
        // Cargar el objeto de acceso a datos
        $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
        // Configurar el objeto de conexión a datos
        $this->Dao->Configure($oConnString);
    }
}
