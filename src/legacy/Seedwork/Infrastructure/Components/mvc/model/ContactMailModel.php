<?php

declare(strict_types=1);

/**
 * Model para la gestión email de contacto
 *
 * @author alfonso
 */
class ContactMailModel extends \SaasModel{

    /**
     * Constructor
     */
    public function __construct(){
        // Llamar al constructor padre
        parent::__construct();
    }

    /**
     * Método que guarda la información relativa a la entidad
     * Para el envío es necesario que exista la clave en webconfig
     * para obtener la plantilla a utilizar : <add key="mailContacto"
     * value="notifications/nombreplantilla.html" />
     * @param array $data Array de datos
     */
    public function Send($data = null){
        // Gestor de dependencias
        $injector = Injector::GetInstance();
        // Referencia al notificador
        $notificator = $injector->Resolve( "INotificator" );
        // Obtener template
        $template = $notificator->GetTemplate( "mailContacto" );
        // Procesado de la notificación
        foreach($data as $key => $value){
            $template = str_replace( "{".$key."}", $value, $template );
        }
        // Setear parámetros
        $toFrom = ConfigurationManager::GetKey( "mail-from" );
        // Establecer título
        $Subject = "Solicitud de Contacto";
        // Configuración de la notificación
        $config = array( "To" => $toFrom,
                "From" => $toFrom ,
                "Subject" => $Subject ,
                "IsHtml" => true
            );
        // Generar notificación
        $notificator->Send($config, $template);
    }

}
