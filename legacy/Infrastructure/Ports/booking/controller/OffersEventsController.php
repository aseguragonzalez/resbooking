<?php



// Cargar referencia al model para eventos de ofertas
require_once "model/OffersEventsModel.php";
// Cargar referencia al DTO para navegación de calendario
require_once "model/dto/WeekNavDTO.php";

/**
 * Controlador para la gestión de eventos asociados a una oferta
 *
 * @author alfonso
 */
class OffersEventsController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(true);
    }

    /**
     * Acción principal. Carga el calendario de eventos de la oferta indicada
     * para la semana y anyo seleccionado.
     * @param int $id Identidad de la oferta
     * @param string $week Semana del anyo
     * @param string $year Anyo de consulta
     * @return string Vista renderizada
     */
    public function Index($id = 0){
        try{
            $week = filter_input(INPUT_GET, "week");
            $year = filter_input(INPUT_GET, "year");
            // Instanciar modelo de datos
            $model = new \OffersEventsModel();
            // Cargar la información del modelo
            $model->GetEvents($id, $year, $week);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso para crear o eliminar un registro de evento
     * @return string Serializacion JSON
     */
    public function SetEvent(){
        try{
            // Obtener dto
            $OfferEvent = $this->GetEntity("OfferEvent");
            // Instanciar modelo
            $model = new \OffersEventsModel();
            // Obtener la colección de configuraciones
            $result = $model->SetEvent($OfferEvent);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $result,
                "Error" => ($result == -1),
                "Exception" => null
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetOffers" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }
}
