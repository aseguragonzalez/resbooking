<?php

declare(strict_types=1);

/**
 * Interfáz de acceso al sistema de notificaciones
 *
 * @author alfonso
 */
interface INotificator{

    /**
     * Genera la notificación con los datos proporcionados
     * @param array $data Colección de parámetros para la notificación
     * @param string $content Contenido de la notificación
     */
    public function Send($data, $content);

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string $templateName Identidad de la plantilla
     */
    public function GetTemplate($templateName);

}
