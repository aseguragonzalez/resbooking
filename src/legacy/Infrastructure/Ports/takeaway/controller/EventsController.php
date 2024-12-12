<?php



require_once "model/EventsModel.php";
require_once "model/dtos/WeekNavDTO.php";

/**
 * Description of EventsController
 *
 * @author alfonso
 */
class EventsController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(true);
    }

    /**
     * Obtiene el listado de eventos registrados
     * @return String Vista renderizada
     */
    public function Index($week = 0){
        try{
            $year = (isset($_GET["year"]) && is_numeric($_GET["year"]))
                    ? intval($_GET["year"]) : 0;

            $model = new \EventsModel();

            $model->GetEvents($week, $year);

            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Events", $e);
        }
    }

    /**
     * Alta o baja de un evento asociado al proyecto
     * @return String Json
     */
    public function SetEvent(){
        try{
            $entity = $this->GetEntity("SlotEvent");

            $model = new \EventsModel();

            $json = $model->SetEvent($entity);

            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetEvent", $e);

            return $this->ReturnJSON($json);
        }
    }
}
