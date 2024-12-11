<?php


require_once("model/ProductsModel.php");

/**
 * Controllador para la gestión de productos
 *
 * @author manager
 */
class ProductsController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Proceso para la lista de productos
     * @return string Vista renderizada
     */
    public function Index(){
        try{
           // Instanciar el modelo
           $model = new \ProductsModel();
           // Cargar la lista de productos
           $model->GetProducts();
           // Retornar la vista renderizada
           return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso para editar un producto
     * @param int? $id Identidad del producto
     * @return string Vista renderizada
     */
    public function Edit($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->GetProduct($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Edit", $e);
        }
    }

    /**
     * Proceso para almacenar un producto
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Obtener registro de reserva
            $entity = $this->GetEntity( "Product" );
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->Save($entity);
            // Retornar la vista renderizada
            return $this->Partial("Edit", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Procedimiento de eliminación de un producto
     * @param int $id Identidad del producto
     * @return string Vista renderizada
     */
    public function Delete($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->Delete($id);
            // Cargar la lista de productos
            $model->GetProducts();
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }
}
