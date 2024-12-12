<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para solicitudes / pedidos
 *
 * @author manager
 */
class RequestsManagement extends \BaseManagement
    implements \IRequestsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IRequestsServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \IRequestsRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \IRequestsManagement
     */
    private static $_reference = null;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->repository = RequestsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = $this->repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = RequestsServices::GetInstance($this->aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto para el contexto
     * @param int $service Identidad del servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(RequestsManagement::$_reference == null){
            RequestsManagement::$_reference =
                   new \RequestsManagement($project, $service);
        }
        return RequestsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \RequestsAggregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual la solicitud
     * indicada mediante su identidad
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    public function GetRequest($id = 0) {
        // Carga las dependencias: Categorías, productos..
        $this->GetRequestDependences();
        // Cargar el registro de la solicitud
        $result = $this->GetRequestById($id);
        // Si la carga ha sido un éxito
        if($result == 0){
            $this->GetItemsByRequest($id);
        }
        return $result;
    }

    /**
     * Proceso para cargar en el agregado los solicitudes registradas
     * @param string $sDate Filtro opcional por fecha
     */
    public function GetRequests($sDate = ""){
        $date = null;
        if($sDate != ""){
            try{
                $date = new \DateTime($sDate);
            } catch (Exception $ex) {
                $date = new \DateTime("NOW");
            }
        }
        $this->aggregate->Requests =
                $this->repository->GetRequestsByDate($date);
    }

    /**
     * Proceso para cargar en el agregado las solicitudes pendientes
     */
    public function GetRequestsPending(){
        $filter = ["Project" => $this->IdProject, "WorkFlow" => null];
        $this->aggregate->Requests =
                $this->repository->GetByFilter("Request", $filter);
    }

    /**
     * Proceso de registro o actualización de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetRequest($request = null) {

        $result = $this->Services->Validate($request);

        if(!is_array($result) && $result == true ){
            $result = [];
            if($request->Id == 0){
                $res = $this->repository->Create($request);
                $result[] = ($res != false) ? 0 : -1;
                $request->Id = ($res) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($request);
                $result[] = ($res != false) ? 0 : -2;
            }

            if($res != false){
                $this->aggregate->Requests[$request->Id] = $request;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveRequest($id = 0) {
        // Cargamos la información de la solicitud y eliminamos los items
        // asociados a la misma
        if($this->GetRequestById($id) == 0
                && $this->RemoveItemsByRequest($id) == 0){
            // Actualizar el estado
            $this->aggregate->Request->State = 0;
            // Guardar cambios
            $res = ($this->repository->Update($this->aggregate->Request) != false);
            // Modificar la información en el agregado
            if($res && isset($this->aggregate->Requests[$id])){
                unset($this->aggregate->Requests[$id]);
            }
            $this->aggregate->Request = null;
            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para actualizar el estado de la solicitud indicada
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del estado de workflow
     * @return int Código de operación
     */
    public function SetState($id = 0, $state = 0) {

        if($this->GetRequestById($id) != 0){
            return -1;
        }

        if(!$this->Services->ValidateChangeState(
                $this->aggregate->Request->WorkFlow, $state)){
            return -2;
        }

        $this->aggregate->Request->WorkFlow = $state;

        if($this->repository->Update($this->aggregate->Request) == false){
            return -3;
        }

        $this->aggregate->Requests[$id] = $this->aggregate->Request;

        return 0;
    }

    /**
     * Carga la información de categorías y productos del proyecto
     */
    private function GetRequestDependences(){
        // Cargamos todos los productos y categorías
        $filter = ["Project" => $this->IdProject ];
        $this->aggregate->Products =
                $this->repository->GetByFilter( "Product", $filter );
        $this->aggregate->Categories =
                $this->repository->GetByFilter( "Category", $filter );
    }

    /**
     * Carga el registro de la solicitud filtrada por su identidad
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function GetRequestById($id = 0){
        // Buscamos la información en la lista de solicitudes del agregado
        $this->aggregate->Request =
                $this->Services->GetById($this->aggregate->Requests, $id);
        // Si no se ha encontrado, buscamos en base de datos
        if($this->aggregate->Request instanceof \Request == false){
            $this->aggregate->Request = $this->repository->Read("Request", $id);
        }
        // Retornamos el código de operación
        return ($this->aggregate->Request instanceof \Request) ? 0 : -1;
    }

    /**
     * Carga en el agregado la lista de detalles de la
     * solicitud especificada
     * @param int $id Identidad de la solicitud
     */
    private function GetItemsByRequest($id = 0){
        $filter = ["Request" => $id ];

        $this->aggregate->Items =
                $this->repository->GetByFilter( "RequestItem", $filter );
    }

    /**
     * Proceso de baja de los registros de detalle del solicitud
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    private function RemoveItemsByRequest($id = 0){
        $results = [];
        $this->GetItemsByRequest($id);
        foreach($this->aggregate->Items as $item){
            $results[] = $this->RemoveItemById($item->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }

    /**
     * Proceso de baja de un registro de detalle
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function RemoveItemById($id = 0){
        $item = null;
        if(count($this->aggregate->Items) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $item = $this->repository->GetByFilter( "RequestItem", $filter );
        }
        else{
            $item = $this->Services->GetById($this->aggregate->Items, $id);
        }
        if($item != null){
            return ($this->repository->Update($item) != false)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para establecer el estado de una solicitud
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del nuevo estado
     * @return \Request Referencia a la solicitud
     */
    public function SetRequestState($id = 0, $state = 0){
        $request = $this->GetById($this->aggregate->Requests, $id);
        if($request != null){
            $request->WorkFlow = $state;
            return $request;
        }
        return null;
    }
}
