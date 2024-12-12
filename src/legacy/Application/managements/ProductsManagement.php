<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para productos
 */
class ProductsManagement extends \BaseManagement implements \IProductsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IProductsServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \IProductsRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \IBaseLineManagement
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
        $this->repository = ProductsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = $this->repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ProductsServices::GetInstance($this->aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ProductsManagement::$_reference == null){
            ProductsManagement::$_reference =
                   new \ProductsManagement($project, $service);
        }
        return ProductsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0) {
        $product = $this->Services->GetById(
                $this->aggregate->Products, $id);
        if($product != null){
            $this->aggregate->Product = $product;

            $this->GetImagesByProduct($id);

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = null) {

        $product->Project = $this->IdProject;

        $result = $this->Services->Validate($product);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($product->Id == 0){
                $res = $this->repository->Create($product);
                $result[] = ($res != false) ? 0 : -1;
                $product->Id = ($res != false) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($product);
                $result[] = ($res != false) ? 0 : -2;
            }

            if($res != false){
                $this->aggregate->Products[$product->Id] = $product;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0) {
        // Obtener referencia
        $product = $this->Services->GetById(
                $this->aggregate->Products, $id);
        if($product != null){
            if($this->RemoveImages($id) == 0){

                $product->State = 0;

                $res = ($this->repository->Update($product) != false);

                if($res){
                    unset($this->aggregate->Products[$id]);
                }

                return $res ? 0 : -1;
            }
        }
        return -2;
    }

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0) {
        $image = null;

        if(count($this->aggregate->Images) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $images = $this->repository->GetByFilter( "Image", $filter );
        }
        else{
            $images = $this->Services->GetById(
                    $this->aggregate->Images, $id);
        }

        $image = current($images);

        if($image != null){
            $image->State = 0;
            return ($this->repository->Update($image) != false)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = null) {
        $date = new \DateTime("NOW");
        $image->Date = $date->format("Y-m-d h:i:s");
        $result = $this->Services->ValidateImage($image);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($image->Id == 0){
                $res = $this->repository->Create($image);
                $result[] = ($res != false) ? 0 : -1;
            }
            else{
                $res = $this->repository->Update($image);
                $result[] = ($res != false) ? 0 : -2;
            }
        }
        return $result;
    }

    /**
     * Carga en el agregado la colección de imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function GetImagesByProduct($id = 0){
        $filter = ["Product" => $id, "State"  => 1];
        $this->aggregate->Images =
                $this->repository->GetByFilter( "Image", $filter );
    }

    /**
     * Eliminar todas las imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function RemoveImages($id = 0){
        $results = [];
        $this->GetImagesByProduct($id);
        foreach($this->aggregate->Images as $image){
            $results[] = $this->RemoveImage($image->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }
}
