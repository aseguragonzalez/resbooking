<?php



// Cargar la referencia al modelo para el report
require_once "model/ReportModel.php";
// Cargar la referencia al model de pendientes
require_once "model/dto/ReportDTO.php";

/**
 * Controlador para la gestión del report de un proyecto
 *
 * @author alfonso
 */
class ReportController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(TRUE);
    }

    /**
     * Acción para cargar el report de las reservas de un proyecto
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo de datos
            $model = new \ReportModel();
            // Cargar datos del report
            $model->GetReport();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

}
