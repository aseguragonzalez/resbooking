<?php

declare(strict_types=1);

/**
 * Implementación para el motor de notificaciones
 *
 * @author alfonso
 */
class NotificationsEngine {

    /**
     * Referencia a la instancia actual del motor de notificaciones
     * @var \NotificationsEngine
     */
    private static $_reference = NULL;

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager
     */
    private $_log = NULL;

    /**
     * Referencia al objeto de acceso a datos
     * @var \IDataAccessObject
     */
    private $_dao = NULL;

    /**
     * Cabecera SMTP estándar para los envíos de notificaciones
     * @var string
     */
    private $_headers = "";

    /**
     * Colección de tags de reemplazo en las notificaciones
     * @var array
     */
    private $_tags = [ "{From}", "{Bcc}" ];

    /**
     * Constructor
     * @param \IDataAccessObject $dao Referencia al objecto de acceso a datos
     * @param \ILogManager $log Referencia al gestor de trazas
     * @throws Exception Excepción generada por error en los parámetros del constructor
     */
    private function __construct($dao = NULL, $log = NULL) {

        if($dao != NULL && $dao instanceof \IDataAccessObject){
            $this->_dao = $dao;
        }
        else{
            throw new Exception("IDataAccessObject reference fail");
        }

        if($log != NULL && $log instanceof \ILogManager){
            $this->_log = $log;
        }
        else{
            throw new Exception("ILogManager reference fail ");
        }
        $this->_headers = 'MIME-Version: 1.0' . "\r\n"
                . 'Content-type: text/html; charset=utf-8' . "\r\n"
                . 'From: {From}' . "\r\n". 'Bcc: {Bcc}' . "\r\n";
    }

    /**
     * Proceso para el envío de notificaciones
     */
    public function SendNotifications(){
        // Se registra el inicio
        $this->_log->LogInfo( "Se inicia el proceso de notificaciones." );
        // Establecer el filtro de búsqueda de notificaciones pendientes
        $filter = ["Dispatched" => 0 ];
        // Obtener las notificaciones aplicando el filtro
        $entities = $this->_dao->GetByFilter("NotificationDTO", $filter);

        $count = count($entities);

        $this->_log->LogInfo( "Se han encontrado $count notificaciones sin enviar" );

        // Filtrar las notificaciones correctamente configuradas
        $dtos = array_filter($entities, function($item){
            return !empty($item->_To) || !empty($item->confTo);
        });

        $count = count($dtos);

        $this->_log->LogInfo( "Se han filtrado $count notificaciones bien configuradas" );

        $enviados = 0;

        foreach($dtos as $dto){
            // Valores de reemplazo
            $values = [$dto->_From, $dto->confTo];
            // Reemplazar campos de la cabecera
            $headers = str_replace($this->_tags, $values, $this->_headers);
            // Establecer destinatario principal
            $to = (!empty($dto->_To)) ? $dto->_To : "";
            // Establecer asunto de la notificación
            $subject = $dto->confSubjectText;
            // Decodificar la plantilla
            $template = $this->decodeTemplate($dto->Id, $dto->confTemplate);
            // Decodificar dto de información para la notificación
            $object = $this->decodeObject($dto->Id, $dto->Content);
            // Validaro los parámetros
            if($template === FALSE || $object === NULL){
                $this->_log->LogError("Error en la plantilla o el objeto. Id: $dto->Id");
                continue;
            }
            // Envío del mensaje
            $enviado = $this->sendNotification($dto->Id, $to, $subject,
                    $template, $object, $headers);

            if($enviado == TRUE){
                $enviados++;
            }
        }

        $this->_log->LogInfo("Se han enviado $enviados Notificaciones. Fin proceso");
    }

    /**
     * Proceso para el envío del email y actualización del estado de registro
     * @param int $id Identidad del registro
     * @param string $to Destinatario principal
     * @param string $subject Asunto del mensaje
     * @param string $template Plantilla a utilizar
     * @param object $object Referencia al objeto contenedor
     * @param string $headers Cabeceras SMTP
     */
    private function sendNotification($id = 0, $to = "", $subject = "",
            $template = "", $object = NULL, $headers = ""){
        $result = FALSE;
        // Generar contenido del mensaje
        $content = $this->replaceObject($template, $object);
        // Obtener el registro del envío para actualizar
        $dto = $this->_dao->Read($id, "Notification");
        $dto->_To = $dto->To;
        $dto->_Subject = $dto->Subject;
        // Realizar envío del mensaje
        if(mail($to, $subject, $content, $headers) != FALSE){
            $dto->Dispatched += 1;
            $subjects = ["create-user", "reset-password"];
            if(in_array($dto->_Subject, $subjects)){
                $dto->Content = "";
            }
            $result = TRUE;
        }
        else{
            $msg = "-";
            $err = error_get_last();
            if(is_array($err)){
                $msg = $err["message"];
            }
            $dto->Dispatched = -1;
            $mensaje = "Error al enviar el mensaje. Parámetros: To:$to, Subject: $subject, "
                    . "Headers: $headers, Detalles: $msg,Content: $content";
            $this->_log->LogError($mensaje);
        }
        $this->_dao->Update($dto);

        return $result;
    }

    /**
     * Proceso para el remplazo de arrays en una plantilla
     * @param string $template Contenido de la plantilla
     * @param array $arr Array de datos
     * @param string $tag Etiqueta utilizada
     * @return string Contenido actualizado
     */
    private function replaceArray($template = "", $arr = NULL, $tag = ""){
        $result = "";
        foreach($arr as $item){
            if(is_object($item)){
                $result .= $this->replaceObject($template, $item, $tag."item.");
            }
            elseif (is_array($item)) {
                continue;
            }
            else{
                $result .= str_replace("{".$tag."item}", $item, $template);
            }
        }
        return $result;
    }

    /**
     * Proceso par el remplazo de objetos en una plantilla
     * @param string $template Contenido de la plantilla
     * @param Object $object Referencia al objeto a reemplazar
     * @param string $tag Etiqueta utilizada
     * @return string Contenido actualizado
     */
    public function replaceObject($template = "", $object = NULL, $tag = ""){
        settype($object, "array");
        foreach($object as $name => $value){
            if(is_object($value)){
                $template = $this->replaceObject($template, $value, $tag."$name.");
            }
            elseif(is_array($value)){
                $tName = $tag."$name";
                $arrTemplate = $this->getArrayTemplate($tName, $template);
                $content = $this->replaceArray($arrTemplate, $value, "$tName.");

                $template = str_replace($arrTemplate, $content, $template);
            }
            else{
                $template = str_replace("{".$tag.$name."}", $value, $template);
            }
        }
        return $template;
    }

    /**
     * Procedimiento para obtener el patrón de repetición de una plantilla
     * @param string $tag Etiqueta de búsqueda
     * @param string $template Contenido de la plantilla
     * @return string Patrón encontrado
     */
    private function getArrayTemplate($tag = "", $template = ""){
        $result = "";
        // tag de búsqueda
        $name = "<!--$tag-->";
        // Longitud de la etiqueta
        $nameLength = strlen($name);
        // Buscamos la primera aparición de la subcadena $name en $content
        $start = strpos($template, $name);
        // Comprobar Si se ha encontrado la posición inicial
        if($start === FALSE){
            return $result;
        }
        // Buscamos si hay una segunda aparición
        $end = strpos($template , $name, ($start + 1));
        // Comprobar Si se ha encontrado la posición final
        if($end === FALSE){
            return $result;
        }
        // Extraer la subcadena del patrón
        return substr($template, $start, ($end - $start + $nameLength));
    }

    /**
     * Proceso para la decodificación de la plantilla
     * @param int $id Identidad del registro
     * @param string $base64Template Plantilla codificada en base64
     * @return string Plantilla decodificada
     */
    private function decodeTemplate($id = 0, $base64Template = ""){
        // Decodificar el contenido
        $template = base64_decode($base64Template);
        // Validar la operación
        if($template === FALSE){
            // Generar traza de error
            $this->_log->LogError("Plantilla no decodificable, id: $id");
        }
        return $template;
    }

    /**
     * Decodificación de un objeto serializado en JSON
     * @param int $id Identidad del registro
     * @param string $json Serialización del objeto
     * @return \Object
     */
    private function decodeObject($id = 0, $json = ""){
        // Decodificar objeto
        $obj = json_decode($json);
        // Logear error si procede
        $this->logJsonError($id, FALSE);
        // Retornar resultado
        return $obj;
    }

    /**
     * Generar traza de error al manejar json
     * @param int $id Identidad del registro
     * @param boolean $encode Flag para indicar si se trata de codificación o decodificación
     */
    private function logJsonError($id = 0, $encode =  TRUE){
        $error = json_last_error();
        $message = ($encode) ? "Se ha producido un error al codificar: "
                : "Se ha producido un error al decodificar (id: $id) : ";
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

    /**
     * Obtiene una referencia a la instancia actual del motor de notificaciones
     * @param \IDataAccessObject $dao Referencia al objecto de acceso a datos
     * @param \ILogManager $log Referencia al gestor de trazas
     * @return \NotificationsEngine Referencia a la instancia actual del motor
     */
    public static function GetInstance($dao = NULL, $log = NULL){
        if(NotificationsEngine::$_reference == NULL){
            NotificationsEngine::$_reference =
                    new \NotificationsEngine($dao, $log);
        }
        return NotificationsEngine::$_reference;
    }
}
