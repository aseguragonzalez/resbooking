<?php

declare(strict_types=1);

/**
 * Capa de servicios para la gestión de categorías
 */
class CategoriesServices extends \BaseServices implements \ICategoriesServices{

    /**
     * Referencia
     * @var \ICategoriesServices
     */
    private static $_reference = null;

    /**
     * Referencia al repositorio actual
     * @var \ICategoriesRepository
     */
    protected $repository = null;

    /**
     * Referencia al agregado
     * @var \CategoriesAggregate
     */
    protected $aggregate = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \CategoriesAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->repository = CategoriesRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \ICategoriesServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null){
        if(CategoriesServices::$_reference == null){
            CategoriesServices::$_reference = new \CategoriesServices($aggregate);
        }
        return CategoriesServices::$_reference;
    }

    /**
     * Proceso de validación de categorías
     * @param \Category $entity Referencia a la categoría a validar
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null){
        if($entity != null){
            $this->ValidateCode($entity->Id, $entity->Code);
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateDescription($entity->Description);
            $this->ValidateXml($entity->Xml);
            $this->ValidateParent($entity->Parent);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? true : $this->Result;
    }

    /**
     * Proceso de validación del código de usuario
     * @param string $code Código de categoría
     */
    private function ValidateCode($id = 0, $code  = ""){
        // Validar código
        if(empty($code)){
            $this->Result[] = -4;
        }
        elseif(strlen($code) > 10){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateExistsCode($id, $code);
        }
    }

    /**
     * @ignore
     * Validación clave Unique del código de categoría
     * @param int $id Identidad de la entidad
     * @param string $code Código de categoría
     */
    private function ValidateExistsCode($id = 0, $code = ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Code" => $code, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->aggregate->Categories, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * @ignore
     * Proceso de validación del nombre de la categoría
     * @param string $name Nombre de la categoría
     */
    private function ValidateName($id = 0,$name = ""){
        if(empty($name)){
            $this->Result[] = -7;
        }
        elseif(strlen($name) > 100){
            $this->Result[] = -8;
        }
        else{
            $this->ValidateExistsName($id, $name);
        }
    }

    /**
     * Validación clave Unique del nombre de categoría
     * @param string $name Nombre de la categoría
     */
    private function ValidateExistsName($id = 0, $name= ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];
        // buscar algún item con el mismo nombre
        $items = $this->GetListByFilter(
                    $this->aggregate->Categories, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
            && count($items) > 0 && $items[0]->Id != $id){
            $this->Result[] = -9;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la descripción de categoría
     * @param string $desc Descripción de la categoría
     */
    private function ValidateDescription($desc = ""){
        if(empty($desc)){
            $this->Result[] = -10;
        }
        elseif(strlen($desc) > 500){
            $this->Result[] = -11;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la definición Xml de la categoría
     * @param string $xml Xml de descripción de la categoría
     * @return boolean
     */
    private function ValidateXml($xml = ""){
        if(empty($xml)){
            $this->Result[] = -12;
        }
    }

    /**
     * @ignore
     * Proceso de validación de la categoría padre
     * @param int $id Referencia a la categoría padre
     */
    private function ValidateParent($id = 0){
        if(is_numeric($id) && $id > 0){
            $filter = [ "Project" => $this->IdProject,
                "Id" => $id, "State" => 1 ];
            $items = $this->GetListByFilter(
                    $this->aggregate->Categories, $filter);
            if(empty($items) || count($items) == 0){
                $this->Result[] = -13;
            }
        }
    }
}
