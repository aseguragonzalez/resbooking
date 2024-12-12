<?php

declare(strict_types=1);

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
