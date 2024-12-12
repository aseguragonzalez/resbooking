<?php

declare(strict_types=1);

/**
 * Modelo para la gestión de imágenes
 *
 * @author manager
 */
class ImagesModel extends \TakeawayModel{

    /**
     * Referencia al producto padre
     * @var \Product
     */
    public $Product = null;

    /**
     * Referencia a la imagen en edición
     * @var \Image
     */
    public $Entity = null;

    /**
     * Colección de imágenes asociadas al producto
     * @var array
     */
    public $Entities = [];

    /**
     * Flag para los errores de validación
     * @var string
     */
    public $Error = false;

    /**
     * Mensaje de error en el nombre de imagen
     * @var string
     */
    public $eName = "";

    /**
     * Clase CSS a aplicar en el mensaje de error del nombre de imagen
     * @var string
     */
    public $eNameClass = "";

    /**
     * Mensaje de error en la descripción
     * @var string
     */
    public $eDescription = "";

    /**
     * Clase CSS a aplicar en el mensaje de error de la descripción
     * @var string
     */
    public $eDescriptionClass = "";

    /**
     * Mensaje de error en la ruta de imagen
     * @var string
     */
    public $ePath = "";

    /**
     * Clase CSS a aplicar en el mensaje de error de la ruta de imagen
     * @var string
     */
    public $ePathClass = "";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Imágenes",
                "Imagenes",
                "ProductsManagement");
        $this->SetModel();
    }

    /**
     * Carga la galería de imágenes asociadas a un producto y
     * la información del producto identificado por su Id
     * @param int $id Identidad del producto
     */
    public function GetImages($id = 0){
        if($id > 0){
            // Proceso para carga la información de un producto
            $result = $this->Management->GetProduct($id);

            if($result != 0){
                $this->TranslateResultCodes(_OP_READ_, [$result]);
            }
            else{
                $this->Product = $this->aggregate->Product;
                $this->Entities = $this->aggregate->Images;
            }
        }
    }

    public function SaveImages($id=0, $paths=null){
        $date = new \DateTime("NOW");
        $sdate = $date->format("Y-m-d H:i:s");
        foreach($paths as $key => $value){
            $img = new \Image();
            $img->Product = $id;
            $img->Name = $key;
            $img->Description = $key;
            $img->Path = $value;
            $img->State = true;
            $img->Date = $sdate;
            $this->Dao->Create($img);
        }
    }

    /**
     * Almacenamiento de la información de la imagen de producto
     * @param \Image $entity Referencia a la información de imagen
     */
    public function Save($entity = null){
        // Proceso de almacenamiento de una imagen
        $result = $this->Management->SetImage($entity);

        if(is_array($result) == false){
            throw new Exception("Save: SetImage: "
                    . "Códigos de operación inválidos");
        }

        $this->Entity = $entity;

        if(count($result) != 1 || $result[0] != 0){
            $this->TranslateResultCodes(_OP_CREATE_, $result);
            $this->Error = true;
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * Eliminación de una imagen mediante su identidad
     * @param int $id Identidad de la imagen
     */
    public function Delete($id = 0){
        // Proceso de eliminación de una imágen
        $result = $this->Management->RemoveImage($id);

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
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->Product = new \Product();
        $this->Entity = new \Image();
    }

    /**
     * Obtiene los mensajes de error al "leer" un producto desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado la información del producto" ]
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
                    "msg" => "La imágen no ha sido encontrada" ]
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
                    "msg" => "No se ha podido crear el registro correctamente." ],
                -2 => ["name" => "eResult",
                    "msg" => "No se ha podido actualizar el registro correctamente" ],
                -3 => ["name" => "eResult",
                        "msg" => "La referencia a la imagen no es válida" ],
                -4 => ["name" => "eName",
                    "msg" => "Debe establecer un nombre para la imagen." ],
                -5 => ["name" => "eName",
                    "msg" => "El nombre de la imagen no puede tener más de 45 caractéres." ],
                -6 => ["name" => "eName",
                    "msg" => "Ya existe una imagen con el mismo nombre" ],
                -7 => ["name" => "eDescription",
                    "msg" => "Debe incluir una descripción para la imagen" ],
                -8 => ["name" => "eDescription",
                    "msg" => "La descripción no puede tener más de 200 catactéres" ],
                -9 => ["name" => "ePath",
                    "msg" => "No se ha definido una imagen." ],
                -10 => ["name" => "eResult",
                    "msg" => "No se ha asociado un producto válido" ]
            ];
    }

}
