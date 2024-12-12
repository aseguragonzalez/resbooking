<?php

declare(strict_types=1);

/**
 * Controlador base para la aplicación
 *
 * @author manager
 */
class TakeawayController extends \SaasController{

    /**
     * Constructor.
     * @param boolean $projectRequired Indica si es necesario que exista un
     * proyecto activo.
     * @throws ProjectException
     */
    public function __construct($projectRequired = false) {
        parent::__construct();
        if($projectRequired == true && $this->Project <= 0){
            throw new ProjectException("Proyecto no seleccionado");
        }
    }

    /**
     * Procesado de las excepciones capturadas por el controlador
     * @param string $method Nombre del método que origina el error
     * @param \Exception $e Referencia a la excepción capturada
     * @return string Vista redenrizada
     */
    public function ProcessError($method = "", $e = null) {
        // Crear traza de error
        $this->Log->LogErrorTrace($method, $e);
        // Instanciar Modelo
        $model = new \SaasModel();
        // Renderizado de la vista de error
        return $this->Partial( "error", $model);
    }

    /**
     * Procesado de errores en operaciones asíncronas con JSON
     * @param string $method Método donde se produce la captura de error
     * @param \Exception $e Referencia a la excepción capturada
     * @return \JsonResultDTO
     */
    public function ProcessJSONError($method = "", $e = null){
        // Crear traza de error
        $this->Log->LogErrorTrace($method, $e);

        $dto = new \JsonResultDTO();
        $dto->Result = false;
        $dto->Error = $e->getMessage();
        $dto->Code = 200;
        $dto->Exception = $e;
        return $dto;
    }
}
