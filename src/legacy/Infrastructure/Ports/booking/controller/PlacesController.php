<?php



// Cargar la referencia al modelo
require_once "model/PlacesModel.php";

/**
 * Controlador para la gestión de espacios
 *
 * @author alfonso
 */
class PlacesController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        // Indicamos al constructor que para todas las acciones
        // es necesario que esté contextualizado un proyecto
        parent::__construct(TRUE);
    }

    /**
     * Acción para cargar la lista de espacios disponibles
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo de datos
            $model = new \PlacesModel();
            // Cargar la lista de espacios registrados
            $model->GetPlaces();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para almacenar un nuevo espacio
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Cargar la info del "espacio"
            $entity = $this->GetEntity( "Place" );
            // Instanciar model
            $model = new \PlacesModel();
            // Guardar la entidad
            $model->Save($entity);
            // Cargar la lista de salas
            $model->GetPlaces();
            // retornar vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Acción para eliminar un espacio existente
     * @param int $id Identidad del espacio a eliminar
     * @return string Vista renderizada
     */
    public function Delete($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \PlacesModel();
            // Eliminación del registro
            $model->Delete($id);
            // Cargar la lista de salas
            $model->GetPlaces();
            // retornar vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Delete", $e);
        }
    }

}
