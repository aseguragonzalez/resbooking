<?php

require_once "model/DiscountsModel.php";
require_once "model/dtos/WeekNavDTO.php";

/**
 * Controlador para la gestión de descuentos y ofertas
 *
 * @author manager
 */
class DiscountsController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Carga la lista de descuentos registrados
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar el modelo
            $model = new \DiscountsModel();
            // Cargar la colección de descuentos
            $model->GetDiscounts();
            // Renderizar la vista
            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso de almacenamiento del descuento
     * @return String Vista renderizada
     */
    public function Save(){
        try{
            // Obtener la referencia con la información del descuento
            $entity = $this->GetEntity("DiscountDTO");
            // Instanciar el modelo
            $model = new \DiscountsModel();
            // Guardar información del descuento
            $model->Save($entity);
            // Cargar la lista de descuentos actualizada
            $model->GetDiscounts();
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Proceso de eliminación del descuento
     * @param int $id Identidad del descuento
     * @return String Vista renderizada
     */
    public function Remove($id = 0){
        try{
            // Instanciar el modelo
            $model = new \DiscountsModel();
            // Eliminar el registro del descuento
            $model->Delete($id);
            // Cargar la lista actualizada de descuentos
            $model->GetDiscounts();
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Remove", $e);
        }
    }

    /**
     * Carga el calendario de eventos/excepciones de un descuento
     * @return string Vista renderizada
     */
    public function Exceptions($id = 0){
        try{
            // Obtener la información de la solicitud
            $year = filter_input(INPUT_GET, "year");
            $week = filter_input(INPUT_GET, "week");
            // Instanciar el modelo
            $model = new \DiscountsModel();
            // Cargar la colección de eventos
            $model->GetEvents($id, $year, $week);
            // Retornar la vista
            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Exceptions", $e);
        }
    }

    /**
     * Acción para el registro del evento
     * @return String Json
     */
    public function SetEvent(){
        try{
            // Obtener la información del evento
            $entity = $this->GetEntity("DiscountOnEvent");
            // Instanciar el modelo
            $model = new \DiscountsModel();
            // Registrar el evento
            $json = $model->SetEvent($entity);
            // Retornar la serialización del resultado
            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetEvent", $e);

            return $this->ReturnJSON($json);
        }
    }
}
