<?php



require_once("model/PendingModel.php");
require_once("model/dtos/StateDTO.php");

/**
 * Controlador para la gestión de solicitudes
 *
 * @author manager
 */
class PendingController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(true);
    }

    /**
     * Proceso para la listar las solicitudes pendientes
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar el modelo
            $model = new \PendingModel();
            // Cargar la lista de solicitudes
            $model->GetRequestsPending();
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para obtener el número de pedidos pendientes
     * @param int $id Identidad del proyecto
     * @return string Serialización JSON
     */
    public function GetRequestCount($id=0){
        try{
            // Instanciar el modelo
            $model = new \PendingModel();
            // Cargar la lista de solicitudes
            $json = $model->GetRequestCount($id);
            // Retornar contenido JSON
            return $this->ReturnJSON($json);
        }
        catch(Exception $e){
            // Procesado del error
            $json = $this->ProcessError("GetRequestCount", $e);

            return $this->ReturnJSON($json);
        }
    }
}
