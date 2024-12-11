<?php

declare(strict_types=1);

/**
 * Motor de recordatorios. Genera el registro de notificaciones de tipo
 * recordatorio para todos los proyectos configurados en base a los parámetros
 * de la configuración de cada proyecto: TimeSpan y comensales
 *
 * @author alfonso
 */
class RemindersEngine {

    /**
     * Referencia a la instancia actual del motor
     * @var \RemindersEngine
     */
    private static $_reference = NULL;

    /**
     * Referencia al Objeto de acceso a datos
     * @var \IDataAccessObject
     */
    private $_dao = NULL;

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager
     */
    private $_log = NULL;

    /**
     * Asunto para el registro de notificaciones
     * @var string
     */
    private $_subject = "";

    /**
     * Referencia a la instancia DateTime actual
     * @var \DateTime
     */
    private $_date = NULL;

    /**
     * Número de registros realizados en la última ejecución
     * @var int
     */
    private $_send = 0;

    /**
     * Colección de configuraciones de recordatorio
     * @var array
     */
    private $_configurations = [];

    /**
     * Constructor de la clase
     * @param \IDataAccessObject $dao Referencia al objeto de acceso datos
     * @param \ILogManager $log Referencia al gestor de trazas
     * @param string $subject Asunto de las notificaciones
     */
    private function __construct($dao = NULL, $log = NULL, $subject = "") {
        if($dao instanceof \IDataAccessObject){
            $this->_dao = $dao;
        }
        if($log instanceof \ILogManager){
            $this->_log = $log;
        }
        if(!empty($subject)){
            $this->_subject = $subject;
        }
        $this->_date = new \DateTime("NOW");

        // Filtro para buscar las configuraciones
        $filter = ["Reminders" => 1];
        // Cargar configuraciones de proyectos
        $this->_configurations =
                $this->_dao->GetByFilter("ConfigurationService", $filter);
        // Cantidad de proyectos configurados
        $count = count($this->_configurations);
        // Generar traza de seguimiento
        $this->_log->LogInfo("Se han encontrado $count proyectos configurados");
    }

    /**
     * Genera el registro de la notificación
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @param \BookingNotificationEngineDTO $dto Referencia al dto de la reserva
     * @param string $sDto Serialización del dto
     */
    private function setNotification($project = 0, $service = 0, $dto = NULL){
        if($dto != NULL){
            $date = new \DateTime($dto->Date);
            $dto->Date = strftime("%A %d de %B del %Y",$date->getTimestamp());
            $dto->Ticket = $this->getTicket($dto->Email, $dto);
            $not = new \Notification();
            $not->Project = $project;
            $not->Service = $service;
            $not->To = $dto->Email;
            $not->Subject = $this->_subject;
            $not->Date = $this->_date->format( "y-m-d h:i:s" );
            $not->Content = $this->JsonEncodeObject($dto);
            if(!empty($not->Content)){
                $this->_dao->Create($not);
                $this->_send++;
            }
            else{
                $this->_log->LogError("Notificación no generada "
                        . "para la reserva $dto->Id");
            }
        }
    }

    /**
     * Obtiene un ticket de validación para las notificaciones
     * @param string $user Destinatario de Ticket
     * @param \BookingNotificationDTO $dto Referencia al DTO de la reserva
     * @return string Ticket generado
     */
    private function getTicket($user = "", $dto = NULL){
        // Establecer el destinatario de la notificación
        if($user == ""){ $user = "admin"; }
        // Array de parámetros del ticket
        $arr = ["User" => $user, "Project" => $dto->Project, "Id" => $dto->Id ];
        // Serialización de la información del ticket
        $text = $this->JsonEncodeObject($arr);
        // Codificación del ticket
        return base64_encode($text);
    }

    /**
     * Proceso para el registro de recordatorios
     * @return int Número de notificaciones registradas
     */
    public function SendReminders(){
        // Iniciar el número de notificaciones registradas
        $this->_send = 0;
        // Recorrer todos los proyectos configurados para generar los recordatorios
        foreach($this->_configurations as $config){
            // Búsqueda de las reservas para susceptibles de generar un recordatorio
            $dtos = $this->findBookings($config->Project, $config->TimeSpan,
                    $config->TimeFilter , $config->Diners);
            // Recorrer todas las reservas filtradas para generar los recordatorios
            foreach($dtos as $dto){
                // Comprobación de posiles recordatorios repetidos
                if(!$this->existsReminders($config->Project, $config->Service, $dto->Id)){
                    // Generar el registro del recordatorio
                    $this->setNotification($config->Project, $config->Service, $dto);
                }
            }
        }
        return $this->_send;
    }

    /**
     * Proceso de búsqueda de reservas con los criterios de recordatorio dados
     * @param int $project Identidad del proyecto padre
     * @param int $timespan Horas hasta la reserva
     * @param int $timefilter Filtro de horas para la generación de recordatorios
     * @param int $diners Mínimo número de comensales
     * @return array Colección de reservas filtradas
     */
    private function findBookings($project = 0, $timespan = 0,
        $timefilter = 0, $diners = 0){
        $date = $this->_date->format("Y-m-d");
        // Establecer la hora de reserva para la búsqueda
        $timeSpan = $timespan +  $this->_date->format("H");

        if($timeSpan > 24){
            $date = new DateTime($date);
            $date->add(new DateInterval('P1D'));
            $date = $date->format("Y-m-d");
            $timeSpan = $timeSpan - 24;
        }
        // filtrar reservas por el filtro de horas
        $bookings = $this->getFilterBookings($project, $date, $timefilter);
        // El estado anulado es el 6
        // Filtrado de reservas con el criterio de comensales
        $dtos = array_filter($bookings, function($item) use ($diners, $timeSpan){
            return $item->Diners >= $diners && $item->State != 6
                    && ($item->Start == $timeSpan + ":00" ||$item->Start == $timeSpan + ":30" );
        });
        // Cantidad de registros encontrados
        $count = count($dtos);
        // Mensaje de la traza de búsqueda
        $trazaDeBusqueda = "Se han encontrado $count reservas del "
                . "proyecto $project para el $date a las $timeSpan hrs. "
                . "con un mínimo de $diners comensales";
        // Generar traza de criterios de búsqueda y resultados
        $this->_log->LogInfo($trazaDeBusqueda);
        // Retornar reservas encontradas
        return $dtos;
    }

    private function getFilterBookings($project = 0, $date = "", $timefilter = 0){
        // colección de reservas válidas
        $bookings = [];
        // Filtros de búsqueda en base de datos
        $filter = ["Project" => $project, "Date" => $date];
        // Búsqueda de reservas
        $registers = $this->_dao->GetByFilter("BookingNotificationEngineDTO", $filter);
        // Intervalo del filtro
        $int = new DateInterval("PT".$timefilter."H");
        // filtro de registros válidos
        foreach($registers as $register){
            $sdate = "$register->Date $register->Start:00";
            $date = new \DateTime($sdate);
            $create = new \DateTime($register->CreateDate);
            $date->sub($int);
            if($date > $create){
                $bookings[] = $register;
            }
        }
        return $bookings;
    }

    /**
     * Proceso de comprobación sobre la existencia de recordatorios
     * de la reserva especificada
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @param int $id Identidad de la reserva
     * @return boolean Resultado de la operación
     */
    private function existsReminders($project = 0, $service = 0, $id = 0){
        // filtro para la búsqueda notificaciones existentes
        $filter = [ "Project" => $project, "Service" => $service,
            "_Subject" => $this->_subject,"Content" => '%{"Id":'.$id.'%'];
        // búsqueda de notificaciones
        $notifications = $this->_dao->GetByFilter("NotificationAlias", $filter);
        // Notificaciones encontradas
        $count = count($notifications);
        // Generar log de la búsqueda
        $this->_log->LogInfo("Se han encontrado $count "
                . "recordatorios para la reserva $id" );
        // resultado de la operación
        return ($count > 0);
    }

    /**
     * Método para proporcionar una instancia del motor de recordatorios.
     * @param \IDataAccessObject $dao Referencia al DAO a utilizar
     * @param \ILogManager $log Referencia al gestor de trazas
     * @param string $subject Asunto del tipo recordatorio
     * @return \RemindersEngine
     */
    public static function GetInstance($dao = NULL, $log= NULL, $subject = ""){
        if(\RemindersEngine::$_reference == NULL){
            \RemindersEngine::$_reference =
                    new \RemindersEngine($dao, $log, $subject);
        }
        return \RemindersEngine::$_reference;
    }

    /**
     * Serialización de un objeto en formato JSON
     * @param \Object $obj Referencia al objeto a serializar
     * @return string Objeto serializado
     */
    private function JsonEncodeObject($obj = NULL){
        $json = json_encode($obj);
        $this->LogJsonError(TRUE);
        return $json;
    }

    /**
     * Generar traza de error al manejar json
     * @param boolean $encode Flag para indicar si se trata de codificación o decodificación
     */
    private function LogJsonError($encode =  TRUE){
        $error = json_last_error();
        $message = ($encode) ? "Se ha producido un error al codificar: "
                : "Se ha producido un error al decodificar: ";
        switch($error) {
            case JSON_ERROR_NONE:
                $message = "";
                break;
            case JSON_ERROR_DEPTH:
                $message.= ' - Excedido tamaño máximo de la pila';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message.= ' - Desbordamiento de buffer o los modos no coinciden';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message.= ' - Encontrado carácter de control no esperado';
                break;
            case JSON_ERROR_SYNTAX:
                $message.= ' - Error de sintaxis, JSON mal formado';
                break;
            case JSON_ERROR_UTF8:
                $message.= ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
                break;
            default:
                $message.= ' - Error desconocido';
                break;
        }
        if(!empty($message)){
            $this->_log->LogError($message);
        }
    }
}
