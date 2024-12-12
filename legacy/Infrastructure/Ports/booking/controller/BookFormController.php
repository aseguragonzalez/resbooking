<?php



// Cargar las dependencias del modelo
require_once 'model/BookFormModel.php';

/**
 * Controlador para el registro de reservas manuales
 *
 * @author alfonso
 */
class BookFormController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(true);
    }

    /**
     * Acción para cargar el formulario de registro
     * @param int $json Flag para indicar si es formulario json o vista completa
     * @return string Vista renderizada
     */
    public function Index($json = 0){
        try{
            // Instanciar modelo de datos
            $model = new \BookFormModel();
            // Cargar la información del formulario
            $model->SetForm();
            // Comprueba si se retorna json o vista
            if($json != 0){
                // Objeto a retornar
                $resultDTO = [
                    "Error" => false,
                    "Result" => true,
                    "Content" => $this->Partial("IndexJSON", $model),
                    "Message" => ""
                ];
                // Serializar el resultado
                return $this->ReturnJSON($resultDTO);
            }
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            if($json != 0){
                // Procesado del error
                $obj = $this->ProcessJSONError("Index" , $e);
                // Retornar serialización
                return $this->ReturnJSON($obj);
            }
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso para guardar los datos de la reserva
     * @param int $json Flag para indicar si es formulario json o vista completa
     * @return string vista renderizada
     */
    public function Save($json = 0){
        try{
            // Obtene los datos de la reserva
            $entity = $this->GetEntity("Booking");
            // Instanciar el modelo de datos
            $model = new \BookFormModel();
            // Guardar
            $result = $model->Save($entity);
            // Cargar la información del formulario
            $model->SetForm();
            // Comprueba si se retorna json o vista
            if($json != 0){
                // Objeto a retornar
                $resultDTO = [
                    "Error" => !$result,
                    "Result" => $result,
                    "Content" => $this->Partial("IndexJSON", $model),
                    "Message" => ""
                ];
                // Serializar el resultado
                return $this->ReturnJSON($resultDTO);
            }
            // Renderizar resultado
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Save", $e);
        }
    }
}
