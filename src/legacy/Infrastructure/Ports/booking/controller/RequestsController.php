<?php



// Cargar la referencia al model de pendientes
require_once "model/RequestsModel.php";

/**
 * Controlador para la gestión de solicitudes pendientes de procesar
 *
 * @author alfonso
 */
class RequestsController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(TRUE);
    }

    /**
     * Acción principal. Obtiene la colección de solicitudes pendientes
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo de datos
            $model = new \RequestsModel();
            // Cargar las reservas registradas sin estado
            $model->GetRequests();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para obtener el número de solicitudes pendientes
     * @return string JSON del resultado
     */
    public function GetCount(){
        try{
            // Instanciar modelo de datos
            $model = new \RequestsModel();
            // Obtener cuenta de pendientes
            $count = $model->GetRequestCount();
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => 0,
                "Error" => FALSE,
                "Exception" => NULL,
                "Count" => $count
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetCount" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para la confirmación de reservas
     * @param int $id Identidad de la reserva
     * @return string vista renderizada
     */
    public function Accept($id = 0){
        try{
            // Recuperar el estado
            $state = ConfigurationManager::GetKey( "reservado" );
            // Instanciar modelo de datos
            $model = new \RequestsModel();
            // Ejecutar operación
            $result = $model->ChangeState($id, $state);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $result,
                "Error" => ($result == -1),
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("Accept" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para la de anulación de reservas
     * @param int $id Identidad de la reserva
     * @return string Vista renderizada
     */
    public function Cancel($id = 0){
        try{
            // Recuperar el estado
            $state = ConfigurationManager::GetKey( "anulado" );
            // Instanciar modelo de datos
            $model = new \RequestsModel();
            // Ejecutar operación
            $result = $model->ChangeState($id, $state, TRUE);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $result,
                "Error" => ($result == -1),
                "Exception" => NULL
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("Cancel" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }
}
