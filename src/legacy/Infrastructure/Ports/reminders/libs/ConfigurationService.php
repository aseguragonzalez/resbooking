<?php

declare(strict_types=1);

/**
 * Entidad con los parámetros de configuración del proyecto
 *
 * @author alfonso
 */
class ConfigurationService {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Mínimo número de comensales
     * @var int
     */
    public $MinDiners = 1;

    /**
     * Máximo número de comensales
     * @var int
     */
    public $MaxDiners = 25;

    /**
     * Flag para indicar si están activados los recordatorios
     * @var boolean
     */
    public $Reminders = false;

    /**
     * Ventana de tiempo previa para el envío de recordatorio [en horas]
     * @var int
     */
    public $TimeSpan = 1;

    /**
     * Filtro de tiempo para generar recordatorios [en horas]
     * @var int
     */
    public $TimeFilter = 24;

    /**
     * Mínimo número de comensales para enviar un recordatorio
     * @var int
     */
    public $Diners = 1;

    /**
     * Flag para indicar la suscripción al servicio de publicidad
     * en el formulario de reservas
     * @var boolean
     */
    public $Advertising = false;

    /**
     * Flag para indicar la suscripción al servicio de pre-pedidos
     * @var boolean
     */
    public $PreOrder = false;
}
