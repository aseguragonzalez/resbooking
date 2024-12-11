<?php



// Cargar el modelo de datos
require_once "model/ConfigurationModel.php";

/**
 * Controlador para la gestión de configuraciones
 *
 * @author alfonso
 */
class ConfigurationController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        // Indicamos al constructor que para todas las acciones
        // es necesario que esté contextualizado un proyecto
        parent::__construct(TRUE);
    }

    /**
     * Acción por defecto
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo
            $model = new \ConfigurationModel();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso de registro/actualización de los parámetros de configuración
     * del servicio
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Obtener la entidad de base de datos
            $entity = $this->GetEntity("ConfigurationService");
            // Instanciar modelo
            $model = new \ConfigurationModel();
            // Proceso de guardado
            $model->Save($entity);
            // Procesar la vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Override del método de la clase base para obtener una instancia
     * de la clase solicitada a partir de los datos de la solicitud
     * @param string $entityName Nombre de la entidad que se recuperará
     * @return Object Referencia a la entidad
     */
    public function GetEntity($entityName = "") {
        if($entityName != "ConfigurationService"){
            return parent::GetEntity($entityName);
        }
        $obj = new \ConfigurationService();
        $obj->Id = filter_input(INPUT_POST, "Id");
        $obj->Project = filter_input(INPUT_POST, "Project");
        $obj->Service = filter_input(INPUT_POST, "Service");
        $obj->Diners = filter_input(INPUT_POST, "Diners");
        $obj->MinDiners = filter_input(INPUT_POST, "MinDiners");
        $obj->MaxDiners = filter_input(INPUT_POST, "MaxDiners");
        $obj->TimeSpan = filter_input(INPUT_POST, "TimeSpan");
        $obj->TimeFilter = filter_input(INPUT_POST, "TimeFilter");
        $obj->Advertising = filter_input(INPUT_POST,
                "Advertising", FILTER_VALIDATE_BOOLEAN) == TRUE ? 1:0;
        $obj->PreOrder = filter_input(INPUT_POST,
                "PreOrder", FILTER_VALIDATE_BOOLEAN)== TRUE ? 1:0;
        $obj->Reminders = filter_input(INPUT_POST,
                "Reminders", FILTER_VALIDATE_BOOLEAN)== TRUE ? 1:0;
        return $obj;
    }
}
