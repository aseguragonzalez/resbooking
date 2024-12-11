<?php



class ProductDTO{

    public $Id = "";

    public $Code = "";

    public $Name = "";

    public $Description = "";

    public $Price = "";

    public function __construct($name = ""){
        $this->Id = str_replace(" ", "_" ,trim(strtolower($name)));
        $this->Code = $this->Id."-UN-CODIGO";
        $this->Name = $name;
        $this->Description = "Una descripción para $name";
        $this->Price = rand(1,20)." €/ud";
    }
}

/**
 * Modelo para el catálogo de productoss
 *
 * @author alfonso
 */
class OrderModel extends \ResbookingModel{

    /**
     * Colección de productos
     * @var array
     */
    public $Entities = [];

    /**
     * Constructor
     * @param int $project ID del proyecto actual
     */
    public function __construct($project = 0){
        // Cargar constructor padre
        parent::__construct();
        // Asignar id de proyecto
        $this->Project = $project;

        for($i = 1; $i < 10; $i++){
            $this->Entities[] = new \ProductDTO("Un producto-".$i);
        }

    }
}
