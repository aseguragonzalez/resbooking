<?php

declare(strict_types=1);

/**
 * Controlador para el envío de notificaciones
 *
 * @author alfonso
 */
class ContactMailController extends \Controller{

    /**
     * Constructor por defecto
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción por defecto : envio de notificación
     */
    public function Send(){
        try{
            // Instanciar modelo
            $model = new \ContactMailModel();
            // Obtener toda la información de la petición
            $data = $this->GetMailData();
            // Enviar mail
            $model->Send($data);
            // Establecer resultado
            $_SESSION["eResult"] =
                    "Su solicitud ha sido procesada correctamente.";

            $_SESSION["eResultClass"] = "has-success";
            // Redirigir la petición
            return $this->Redirect();
        }
        catch(Exception $e){
            // Generar traza de error
            $this->Log->LogErrorTrace( "Send" , $e);
            // Relanzar el error
            throw $e;
        }
    }

    /**
     * Método para obtener los parámetros de la llamada
     */
    private function GetMailData(){
        $array = array();
        if(isset($_POST)){
            foreach($_POST as $key => $value){
                // Eliminamos posibles tags html y php.
                $array[$key] = strip_tags($value);
            }
        }
        return $array;
    }

    /**
     * Método para redirigir el flujo de la petición
     */
    private function Redirect(){

        $url = ConfigurationManager::GetKey( "path" );

        if(isset($_SERVER["HTTP_REFERER"])
                && $_SERVER["HTTP_REFERER"] != ""){
            $url = $_SERVER["HTTP_REFERER"];
        }

        return "<script type='text/javascript'>"
            . "window.location='$url'</script>";
    }

}
