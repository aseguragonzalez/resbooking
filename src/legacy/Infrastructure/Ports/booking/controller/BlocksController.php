<?php



// Cargar la referencia al modelo
require_once "model/BlocksModel.php";
// Cargar referencia al DTO para navegación de calendario
require_once "model/dto/WeekNavDTO.php";

/**
 * Controlador para la gestión de bloqueo de turnos
 *
 * @author alfonso
 */
class BlocksController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        // Indicamos al constructor que para todas las acciones
        // es necesario que esté contextualizado un proyecto
        parent::__construct(true);
    }

    /**
     * Acción para carga de los bloqueos y aperturas existentes
     * @return string Vista renderizada
     */
    public function Index($week = 0){
        try{
            $year = filter_input(INPUT_GET, "year");
            // Instanciar el modelo de datos
            $model = new \BlocksModel();
            // Cargar la lista de bloqueos existentes
            $model->GetBlocks($year, $week);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Blocks", $e);
        }
    }

    /**
     * Configuración de un bloqueo
     * @return string resultado de la operación serializado en JSON
     */
    public function SetBlock(){
        try{
            // Instanciar modelo
            $model = new \BlocksModel();
            // Obtener referencia a los datos del bloqueo
            $block = $this->GetEntity("Block");

            $id = $model->SetBlock($block);
            // Establecer el objeto para el response
            $resultDTO = [
                "Result" => $id,
                "Error" => false,
                "Exception" => null
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch (Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetBlock" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

}
