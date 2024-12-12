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
    private static $_reference = null;

    /**
     * Referencia al repositorio actual
     * @var \IRequestsRepository
     */
    protected $repository = null;

    /**
     * Referencia al agregado
     * @var \RequestsAggregate
     */
    protected $aggregate = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \RequestsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->repository = RequestsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null){
        if(RequestsServices::$_reference == null){
            RequestsServices::$_reference = new \RequestsServices($aggregate);
        }
        return RequestsServices::$_reference;
    }

    /**
     * Proceso de validación de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return boolean
     */
    public function Validate($request = null){
        return true;
    }

    /**
     * Proceso de validación en el cambio de estado de una solicitud
     * @param int $current Identidad del estado actual
     * @param int $next Identidad del estado próximo
     * @return boolean
     */
    public function ValidateChangeState($current = 0, $next = 0){
        return true;
    }
}
