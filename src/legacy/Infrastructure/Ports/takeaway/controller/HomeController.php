<?php



// Cargar dependencias
require_once( "model/HomeModel.php" );

/**
 * Controlador la pantalla de inicio y la información pública
 */
class HomeController extends \TakeawayController{

    /**
     * Constructor
     * @ignore
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
    }

    /**
     * Acción por defecto : Cargar la vista de inicio
     */
    public function Index(){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Configurar lista de proyectos
            return ($model->LoadProjects() == true)
                ? $this->RedirectTo( "Index" , "Requests" )
                    : $this->PartialView($model);

        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para seleccionar el proyecto
     */
    public function SetProject($id = 0){
        try{
            // Instanciar modelo
            $model = new \HomeModel();
            // Comprobamos si se ha seleccionado un proyecto del desplegable
            $projectInfo = $this->GetEntity( "ProjectInfo" );

            $projectId = (isset($id) && $id > 0)
                    ? $id : $projectInfo->Id;

            // Configurar el proyecto seleccionado
            return ($model->SetCurrent($projectId) == true)
                ? $this->RedirectTo( "Index" , "Requests" )
                    :$this->RedirectTo( "Index" , "Home" );

        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("SetProject", $e);
        }
    }

    /**
     * Obtiene el formulario con la información del servicio
     */
    public function About(){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Determinar el nombre de la vista
            $view = $this->GetViewNameByModel("About", $model);
            // Procesar la vista
            return $this->Partial($view, $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("About", $e);
        }
    }

    /**
     * Obtiene el formulario con la política de privacidad
     */
    public function Privacity(){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Determinar el nombre de la vista
            $view = $this->GetViewNameByModel("Privacity", $model);
            // Procesar la vista
            return $this->Partial($view, $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Privacity", $e);
        }
    }

    /**
     * Obtiene el formulario sobre la advertencia legal
     */
    public function Legal(){
        try{
            // Instanciar el modelo
            $model = new \HomeModel();
            // Determinar el nombre de la vista
            $view = $this->GetViewNameByModel("Legal", $model);
            // Procesar la vista
            return $this->Partial($view, $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Legal", $e);
        }
    }

    /**
     * Obtiene el nombre de la vista si el usuario está autenticado
     * @param string $name Nombre de la vista pública solicitada
     * @return string
     */
    public function GetViewNameByModel($name = "Index", $model = null){
        return ($model != null && $model->Username != "")
            ? "Auth$name" : $name;
    }
}
