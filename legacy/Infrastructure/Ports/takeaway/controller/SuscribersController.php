<?php



require_once ("model/SuscribersModel.php");

/**
 * Controlador para la gestión de suscriptores
 *
 * @author manager
 */
class SuscribersController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(true);
    }

    public function Index(){
        try{
            // Instanciar el modelo
            $model = new \SuscribersModel();
            // Cargar la lista de solicitudes
            $model->GetSuscribers();
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    public function Delete($id = 0){
        try{
            // Instanciar el modelo
            $model = new \SuscribersModel();
            // Cargar la lista de solicitudes
            $model->GetSuscribers();

            $model->Delete($id);
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }

}
