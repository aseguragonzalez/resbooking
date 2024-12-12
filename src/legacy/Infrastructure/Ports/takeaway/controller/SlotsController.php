<?php



require_once "model/SlotModel.php";

/**
 * Description of SlotsController
 *
 * @author alfonso
 */
class SlotsController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(true);
    }

    /**
     * Obtiene la línea base de turnos configurados
     * @return String Vista renderizada
     */
    public function Index(){
        try{
            $model = new \SlotModel();

            $model->GetSlots();

            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Configuración o eliminación de la Franja de reparto
     * @return String Json
     */
    public function Set(){
        try{
            $dto = $this->GetEntity("SlotConfigured");

            $model = new SlotModel();

            $result = $model->SetSlot($dto);

            return $this->ReturnJSON($result);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Set", $e);
        }
    }

}
