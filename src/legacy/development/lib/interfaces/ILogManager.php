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
