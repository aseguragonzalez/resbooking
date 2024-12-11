<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz ILogManager
 *
 * @author alfonso
 */
class LogManager implements \ILogManager{

    /**
     * Referencia al gestor de trazas actual
     * @var \ILogManager $_logmanager Referencia al gestor de trazas actual
     */
    private static $_logmanager = null;

    /**
     * Nombre del fichero destino
     * @var string $FileName Ruta de acceso al fichero de log
     */
    protected $FileName = null;

    /**
     * Constructor
     * @var string $fileName Ruta al fichero de log
     */
    public function __construct($fileName = ""){
        if(LogManager::$_logmanager == null){
            // Apuntar el nombre del fichero destino
            $this->FileName = $fileName;
            // Almacenar la referencia al gestor de trazas
            LogManager::$_logmanager = $this;
        }
    }

    /**
     * Se encarga de realizar la escritura de la traza en el fichero
     * @var object $type Tipología del mensaje a guardar
     * @var string $data Mensaje a guardar
     */
    private function WriteLog($type, $data){
        $date = new DateTime( "NOW" );
        // Preparar la traza a serializar
        $trace = array( "date" => $date->format("Ynj - h:i:s A"),
            "level" => $type, "details" => $data );
        // Serializar a json
        $trace = json_encode($trace).",\n";
        // Fijar el nombre del fichero utilizando la fecha (dia, año y mes)
        $fileName = ($this->FileName == "" || $this->FileName == null)
                ? "logs/data-".date("Ynj").".log"
                : $this->FileName.date("Ynj").".log";
        // Abrir el flujo al fichero en modo agregar
        $fp = fopen($fileName, 'a');
        // Escribir el mensaje
        fwrite($fp, $trace);
        // Cerrar el flujo de escritura
        fclose($fp);
    }

    /**
     * Se encarga de realizar la escritura de la traza de
     * error en el fichero
     * @var object $type Tipología del mensaje
     * @var string $data Mensaje que se desea guardar
     * @var Exception $e Referencia a la excepción que se va a trazar
     */
    private function WriteErrorLog($type, $data, $e){
        $date = new DateTime( "NOW" );
        // Preparar la traza a serializar
        $trace = array(
            "date" => $date->format("Ynj - h:i:s A"),
            "level" => $type, "details" => $data,
            "exception" => $e->getMessage());
        // Serializar a json
        $trace = json_encode($trace).",\n";
        // Fijar el nombre del fichero utilizando la fecha (dia, año y mes)
        $fileName = ($this->FileName == "" || $this->FileName == null)
                ? "logs/data-".date("Ynj").".log"
                : $this->FileName.date("Ynj").".log";
        // Abrir el flujo al fichero en modo agregar
        $fp = fopen($fileName, 'a');
        // Escribir el mensaje
        fwrite($fp, $trace);
        // Cerrar el flujo de escritura
        fclose($fp);
    }

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de información a guardar
     */
    public function LogInfo($message){
        $this->WriteLog( "Info" , $message);
    }

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de información a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogInfoTrace($message, $e){
        $this->WriteErrorLog( "Info" , $message, $e);
    }

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de depuración a guardar
     */
    public function LogDebug($message){
        $this->WriteLog( "Debug" , $message);
    }

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de depuración a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogDebugTrace($message, $e){
        $this->WriteErrorLog( "Debug" , $message, $e);
    }

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro.
     * @param string $message Mensaje de advertencia a guardar
     */
    public function LogWarn($message){
        $this->WriteLog( "Warn" , $message);
    }

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de advertencia a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogWarnTrace($message, $e){
        $this->WriteErrorLog( "Warn" , $message, $e);
    }

    /**
     * Genera una traza tipificada como error con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error a guardar
     */
    public function LogError($message){
        $this->WriteLog( "Error" , $message);
    }

    /**
     * Genera una traza tipificada como error con el mensaje pasado como
     * parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogErrorTrace($message, $e){
        $this->WriteErrorLog( "Error" , $message, $e);
    }

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error FATAL a guardar
     */
    public function LogFatal($message){
        $this->WriteLog( "Fatal" , $message);
    }

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error FATAL a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogFatalTrace($message, $e){
        $this->WriteErrorLog( "Fatal" , $message, $e);
    }

    /**
     * Obtiene una referencia a la instancia actual del gestor de trazas.
     * @var string $fileName Ruta para el fichero de log
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya existe una referencia
        if(LogManager::$_logmanager == null){
            LogManager::$_logmanager = new \LogManager( $fileName );
        }
        // Retornar la referencia al gestor actual
        return LogManager::$_logmanager;
    }

}
