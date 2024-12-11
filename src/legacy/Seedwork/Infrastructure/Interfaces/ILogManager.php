<?php

declare(strict_types=1);

/**
 * Interfaz para el gestor de trazas
 *
 * @author alfonso
 */
interface ILogManager{

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de información a guardar
     */
    public function LogInfo($message);

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de información a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogInfoTrace($message, $e);

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de depuración a guardar
     */
    public function LogDebug($message);

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de depuración a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogDebugTrace($message, $e);

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro.
     * @param string $message Mensaje de advertencia a guardar
     */
    public function LogWarn($message);

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de advertencia a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogWarnTrace($message, $e);

    /**
     * Genera una traza tipificada como error con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error a guardar
     */
    public function LogError($message);

    /**
     * Genera una traza tipificada como error con el mensaje pasado como
     * parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogErrorTrace($message, $e);

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error FATAL a guardar
     */
    public function LogFatal($message);

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error FATAL a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogFatalTrace($message, $e);

    /**
     * Obtiene una referencia a la instancia actual del gestor de trazas.
     */
    public static function GetInstance();
}
