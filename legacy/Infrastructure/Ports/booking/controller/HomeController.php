<?php



// Cargar la referencia al modelo para el inicio y selección de proyecto
require_once "model/HomeModel.php" ;

/**
 * Controlador para la sección pública y selección de proyecto
 *
 * @author alfonso
 */
class HomeController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción para cargar el formulario de inicio.
     * @return string vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Configurar lista de proyectos
            return ($model->LoadProjects() == true)
                ? $this->RedirectTo( "Index" , "Booking" )
                    : $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para la selección de proyecto
     * @param int $id Identidad del proyecto
     * @return string Vista renderizada
     */
    public function SetProject($id = 0){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Configurar el proyecto seleccionado
            return ($model->SetCurrent($id) == true)
                ? $this->RedirectTo( "Index" , "Booking" )
                    :$this->RedirectTo( "Index" , "Home" );
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("SetProject", $e);
        }
    }

    /**
     * Acción para cargar la política de privacidad
     * @return string Vista renderizada
     */
    public function Privacity(){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Determinar el nombre de la vista
            $view = ($model->Username != "")
                    ? "AuthPrivacity" : "Privacity";
            // Procesar la vista
            return $this->Partial($view, $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Privacity", $e);
        }
    }
}
