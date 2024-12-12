<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para categorías
 */
class CategoriesManagement extends \BaseManagement
    implements \ICategoriesManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ICategoriesServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \ICategoriesRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \ICategoriesManagement
     */
    private static $_reference = null;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->repository = CategoriesRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = $this->repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = CategoriesServices::GetInstance($this->aggregate);
    }

    /**
     * Proceso para cargar en el agregado actual la categoría
     * indicada mediante su identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function GetCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->aggregate->Categories, $id);
        if($category != null){

            $this->aggregate->Category = $category;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de la categoría
     * @param \Category $category Referencia a la categoría
     * @return array Códigos de operación
     */
    public function SetCategory($category = null) {
        $category->Project = $this->IdProject;
        $result = $this->Services->Validate($category);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($category->Id == 0){
                $res = $this->repository->Create($category);
                $result[] = ($res != false) ? 0 : -1;
                $category->Id = ($res != false) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($category);
                $result[] = ($res != false) ? 0 : -2;
            }

            if($res != false){
                $this->aggregate->Categories[$category->Id] = $category;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->aggregate->Categories, $id);
        if($category != null){
            // Eliminar todas las referencias asociadas a la categoría
            $this->RemoveReferences($id);
            // Establecer el estado
            $category->State = 0;
            // Actualizar
            $res = ($this->repository->Update($category) != false);

            if($res == true){
                unset($this->aggregate->Categories[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ICategoriesManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(CategoriesManagement::$_reference == null){
            CategoriesManagement::$_reference =
                   new \CategoriesManagement($project, $service);
        }
        return CategoriesManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \CategoriesAggregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Proceso de baja de todas las referencias asociadas a una categoría
     * @param int $id Identidad de la categoría
     */
    private function RemoveReferences($id = 0){
        // Buscar las subcategorías
        $filter = ["Parent" => $id, "State" => 1];
        // Obtener todas las subcategorias
        $categories = $this->Services->GetListByFilter(
                $this->aggregate->Categories, $filter);
        // Proces de eliminación de subcategorías
        foreach($categories as $item){
            // Actualizar la categoría actual
            $item->State = 0;
            // Actualizar el estado en bbdd
            $this->repository->Update($item);
            // Actualizar los productos relacionados
            $products = $this->repository->GetByFilter("Product",
                ["Category" => $item->Id, "State" => 1]);
            // Actualizar el estado en bbdd
            foreach($products as $prod){
                $prod->State = 0;
                $this->repository->Update($prod);
            }
        }
    }
}
