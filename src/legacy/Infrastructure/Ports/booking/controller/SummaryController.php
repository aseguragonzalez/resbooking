<?php



// Cargar la referencia al model
require_once "model/SummaryModel.php";
// Cargar la referencia al dto
require_once "model/dto/SummaryDTO.php";

/**
 * Controlador para la gestión de informes
 *
 * @author alfonso
 */
class SummaryController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(true);
    }

    /**
     * Acción para generar el informe de reservas
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Obtener los datos de la petición
            $dto = $this->GetEntity( "SummaryDTO" );
            // Instanciar modelo de datos
            $model = new \SummaryModel();
            // Precesar el resumen de las reservas
            $model->GetSummary($dto);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para la generación del resumen
     * @return string Vista renderizada
     */
    public function Summary(){
        try{
            $dto = $this->GetEntity( "SummaryDTO" );
            // Instanciar modelo de datos
            $model = new \SummaryModel();
            // Procesar el resumen de reservas
            $model->GetSummary($dto);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Summary", $e);
        }
    }
}
