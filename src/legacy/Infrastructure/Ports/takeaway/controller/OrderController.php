<?php



require_once "model/OrderModel.php";

/**
 * Controlador para el formulario de compras
 *
 * @author manager
 */
class OrderController extends \TakeawayController{

    /**
     * @ignore
     * Constructor.
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Carga el formulario de pedido
     * @param int $project Identidad del proyecto al que pertenece
     */
    public function Index($project = 0){
        try{
            // Instanciar modelo
            $model = new \OrderModel($project);
            // Configurar el modelo con los datos de proyecto
            $model->GetOrderForm();
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Realiza el registro del pedido
     */
    public function Send($project = 0){
        try{
            // Instanciar modelo
            $model = new \OrderModel($project);
            // Configurar el modelo con los datos de proyecto
            $model->GetOrderForm();
            // Obtener los datos del pedido
            $dto = $this->GetEntity("OrderDTO");

            if($model->Save($dto)== TRUE){
                return $this->Partial("Saved", $model);
            }
            else{
                return $this->Partial("Index", $model);
            }
        }
        catch (Exception $e) {

            $model = new \SaasModel();

            return $this->Partial("error", $model);
        }
    }

    /**
     * Caracterización de la entidad enviada por el formulario
     * @param string $entityName Nombre de la entidad a recuperar
     * @return Object
     */
    public function GetEntity($entityName = ""){
        $entity = parent::GetEntity($entityName);
        if($entityName == "OrderDTO"){
            $entity instanceof \OrderDTO;
            $date = new DateTime("NOW");
            $entity->IP =  $_SERVER['REMOTE_ADDR'];
            $entity->Advertising = isset($_REQUEST["Advertising"]) ? TRUE : NULL;
            $entity->Date = $date->format("Y-m-d h:i:s");
            $entity->DeliveryDate = $date->format("Y-m-d");
            $entity->Amount = number_format($entity->Amount, 2);
            $entity->Total = number_format($entity->Total, 2);
        }
        return $entity;
    }
}
