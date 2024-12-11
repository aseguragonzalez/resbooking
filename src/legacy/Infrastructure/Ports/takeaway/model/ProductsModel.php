<?php

declare(strict_types=1);

/**
 * Modelo para la gestión de productos
 *
 * @author manager
 */
class ProductsModel extends \TakeawayModel{

    /**
     * Referencia al producto en edición
     * @var \Product
     */
    public $Entity = NULL;

    /**
     * Colección de productos
     * @var array
     */
    public $Entities = [];

    /**
     * Colección de categorías disponibles
     * @var array
     */
    public $Categories = [];

    /**
     * Mensaje de error en el nombre del producto
     * @var String
     */
    public $eName = "";

    /**
     * Clase CSS a aplicar en el mensaje del nombre de producto
     * @var String
     */
    public $eNameClass = "";

    /**
     * Mensaje de error en la descripción del producto
     * @var String
     */
    public $eDesc="";

    /**
     * Clase CSS a aplicar en el mensaje de la descripción
     * @var String
     */
    public $eDescClass="";

    /**
     * Mensaje de error en los keywords del producto
     * @var String
     */
    public $eKeywords="";

    /**
     * Clase CSS a aplicar en el mensaje de los keywords
     * @var String
     */
    public $eKeywordsClass="";

    /**
     * Mensaje de error en la categoría del producto
     * @var String
     */
    public $eCategory="";

    /**
     * Clase CSS a aplicar en el mensaje de la categoría asociada
     * @var String
     */
    public $eCategoryClass="";

    /**
     * Mensaje de error en la referencia del producto
     * @var String
     */
    public $eReference="";

    /**
     * Clase CSS a aplicar en el mensaje de la referencia establecida
     * @var String
     */
    public $eReferenceClass="";

    /**
     * Mensaje de error en el precio del producto
     * @var String
     */
    public $ePrice="";

    /**
     * Clase CSS a aplicar en el mensaje del precio
     * @var String
     */
    public $ePriceClass="";

    /**
     * Mensaje de error en el criterio de orden del producto
     * @var String
     */
    public $eOrd="";

    /**
     * Clase CSS a aplicar en el mensaje del criterio de orden
     * @var String
     */
    public $eOrdClass="";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Productos",
                "Productos",
                "ProductsManagement");
        // Configuración del modelo
        $this->SetModel();
    }

    /**
     * Carga la colección de productos activos y los formatea para la tabla
     */
    public function GetProducts(){
         // Asignar los productos del agregado
        $this->Entities = array_filter($this->Aggregate->Products,
                function($item){
                    return $item->State == 1;
                });

        foreach($this->Entities as $item){
            $item->ResName = $this->GetCutText($item->Name);
            $item->Desc = $this->GetCutText($item->Description);
            $item->Keys = $this->GetCutText($item->Keywords);
            $item->CategoryName = $this->GetCategoryName($item->Category);
        }
    }

    /**
     * Procedimiento para cargar la información de un producto
     * @param Int $id Identidad del producto
     */
    public function GetProduct($id = 0){
        if($id > 0){
            // Proceso para carga la información de un producto
            $result = $this->Management->GetProduct($id);

            if($result != 0){
                $this->TranslateResultCodes(_OP_READ_, [$result]);
            }
            else{
                $this->Entity = $this->Aggregate->Product;
            }
        }
    }

    /**
     * Procedimiento para guardar la información de un producto
     * @param \Product $entity
     */
    public function Save($entity = NULL){

        if(!empty($entity->Price)){
            $price = floatval(str_replace(",", ".", $entity->Price));
            $entity->Price = round($price, 2);
        }

        // Proceso de almacenamiento de un producto
        $result = $this->Management->SetProduct($entity);

        if(is_array($result) == FALSE){
            throw new Exception("Save: SetProduct: "
                    . "Códigos de operación inválidos");
        }

        $this->Entity = $entity;

        if(count($result) != 1 || $result[0] != 0){
            $this->TranslateResultCodes(_OP_CREATE_, $result);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * Procedimiento para la baja de un producto
     * @param int $id
     */
    public function Delete($id = 0){
        // Proceso de baja de producto
        $result = $this->Management->RemoveProduct($id);

        if($result != 0){
            $this->TranslateResultCodes(_OP_DELETE_, [$result]);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * @ignore
     * Establecimiento de los códigos de operación
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()
            ];
    }

    /**
     * @ignore
     * Configuración estándar del modelo
     */
    protected function SetModel(){
        // Iniciar entity model
        $this->Entity = new \Product();
        // Cargar las categorías
        $this->SetCategories();
    }

    /**
     * Establece la colección de categoría activas para el formulario de
     * edición de productos
     */
    private function SetCategories(){
        $this->Categories = array_filter($this->Aggregate->Categories,
                function($item){
                    return $item->State == 1;
                });
    }

    /**
     * Obtiene el nombre de la categoría indicada
     * @param int $id Identidad de la categoría
     * @return string
     */
    private function GetCategoryName($id = 0){
        $cats = array_filter($this->Aggregate->Categories,
                function($item) use($id){
                    return $item->Id == $id;
                });

        if(count($cats) == 1){
            return current($cats)->Name;
        }

        return "";
    }

    /**
     * Obtiene los mensajes de error al "leer" un producto desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado el producto" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" un producto
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "El producto no ha sido encontrada" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de una categoría en el repositorio principal
     * @return array
     */
    private function GetSaveMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido crear el producto." ],
                -2 => ["name" => "eResult",
                    "msg" => "No se ha podido actualizar el producto" ],
                -3 => ["name" => "eResult",
                    "msg" => "Entidad no válida." ],
                -4 => ["name" => "eName",
                    "msg" => "El nombre es un campo obligatorio" ],
                -5 => ["name" => "eName",
                    "msg" => "El nombre no puede tener más de 100 caracteres" ],
                -6 => ["name" => "eName",
                    "msg" => "Ya existe un producto con el mismo nombre" ],
                -7 => ["name" => "eDesc",
                    "msg" => "La descripción es un campo obligatorio" ],
                -8 => ["name" => "eDesc",
                    "msg" => "La descripción no puede tener más de 140 caracteres" ],
                -9 => ["name" => "eKeywords",
                    "msg" => "Debe introducir un conjunto de keywords" ],
                -10 => ["name" => "eKeywords",
                    "msg" => "Los keywords no pueden tener más de 140 caracteres" ],
                -11 => ["name" => "eCategory",
                    "msg" => "La categoría seleccionada no es válida" ],
                -12 => ["name" => "eReference",
                    "msg" => "La referencia es un campo obligatorio." ],
                -13 => ["name" => "eReference",
                    "msg" => "La longitud máxima es de 20 caracteres" ],
                -14 => ["name" => "eReference",
                        "msg" => "La referencia ya existe." ],
                -15 => ["name" => "ePrice",
                        "msg" => "El campo precio es obligatorio" ],
                -16 => ["name" => "ePrice",
                        "msg" => "El precio introducido no es válido" ],
                -17 => ["name" => "eOrd",
                        "msg" => "El campo orden es obligatorio." ],
                -18 => ["name" => "eOrd",
                        "msg" => "El orden introducido no es válido" ],
                -19 => ["name" => "eAttr",
                        "msg" => "No se ha especificado una definición" ],
                ];
    }
}
