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
 * Interfaz para el modulo http que coordina la ejecución de la petición
 *
 * @author  alfonso
 */
interface IHttpModule{

    /**
     * Se encarga de realizar las tareas comunes a cualquier petición de
     * cliente como generar una traza, comprobar si existe sesión...
     */
    public function BeginRequest();

    /**
     * Se encarga de realizar el procesado de la petición. Para ello debe
     * hacer uso de las diferentes clases con las que se constituye el
     * proyecto como por ejemplo el manejador de peticiones IHttpHandler.
     */
    public function ProcessRequest();

    /**
     * Se encarga de realizar las tareas comunes previas a la finalización
     * del procesado de la petición como puede ser la generación de trazas.
     */
    public function EndRequest();

    /**
     * Es el punto de entrada de cualquier aplicación. Debe encargarse de
     * asegurar la carga de dependencias básicas y gestionar la ejecución
     * de los métodos de tratamiento de peticiones.
     */
    public static function Start();

    /**
     * Realiza el procesado de errores a nivel global de la aplicación.
     * @param int $errno Código de error
     * @param string $errstr Mensaje del error
     * @param string $errfile Fichero en el que se produce el error
     * @param string $errline Línea de ejecución que ha fallado
     * @param string $errcontext Contexto de ejecución
     */
    public static function ApplicationError($errno = 0, $errstr = null,
            $errfile = null, $errline = null, $errcontext = null);

    /**
     * Realiza el procesado de excepciones a nivel global de la aplicación.
     * @param int $errno Código de error
     * @param string $errstr Mensaje del error
     * @param string $errfile Fichero en el que se produce el error
     * @param string $errline Línea de ejecución que ha fallado
     * @param string $errcontext Contexto de ejecución
     */
    public static function ApplicationFatal($errno = 0, $errstr = null,
            $errfile = null, $errline = null, $errcontext = null);

}
