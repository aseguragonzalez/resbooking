<?php

declare(strict_types=1);

/**
 * Capa de servicio para la gestión de solicitudes/pedidos
 *
 * @author manager
 */
class RequestsServices extends \BaseServices implements \IRequestsServices{

    /**
     * Referencia
     * @var \IRequestsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IRequestsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \RequestsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \RequestsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = RequestsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(RequestsServices::$_reference == NULL){
            RequestsServices::$_reference = new \RequestsServices($aggregate);
        }
        return RequestsServices::$_reference;
    }

    /**
     * Proceso de validación de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return boolean
     */
    public function Validate($request = NULL){
        return TRUE;
    }

    /**
     * Proceso de validación en el cambio de estado de una solicitud
     * @param int $current Identidad del estado actual
     * @param int $next Identidad del estado próximo
     * @return boolean
     */
    public function ValidateChangeState($current = 0, $next = 0){
        return TRUE;
    }
}
