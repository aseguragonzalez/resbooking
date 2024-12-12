<?php



// Cargar dependencias
require_once( "model/OrderModel.php" );

/**
 * Controlador para el acceso al catálogo de productos
 *
 * @author alfonso
 */
class OrderController extends \SaasController{

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    /**
     * Acción para obtener el formulario de prepedidos
     * @param int $id Identidad del proyecto
     */
    public function Index($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \OrderModel($id);
            // Objeto a retornar
            $resultDTO = [
                "Error" => false,
                "Result" => true,
                "Content" => $this->PartialView($model),
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetPreOrder" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

}
