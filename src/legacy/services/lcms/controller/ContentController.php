<?php

	include_once("model/ContentModel.php");

	///<summary>
	/// Controlador para la gestión de contenidos
	///</summary>
	class ContentController extends LcmsController{

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Constructor padre
			parent::__construct();
		}

		///<summary>
		/// Acción por defecto : carga la lista de entidades
		///</summary>
		public function Index($section = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instanciar modelo
				$model = new ContentModel();
				// Establecer la sección
				$model->SetSection($section);
				// Obtener la colección de contenidos
				$model->Get();
				// Cargar los datos de proyecto
				$model->LoadIndex();
				// Renderizar la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace( "Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Acción para obtener el formulario de creación de contenidos
		///</summary>
		public function Create($type = 1){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instanciar modelo
				$model = new ContentModel();
				// Cargar datos de formulario
				$model->LoadFormData($type);
				// Seleccionar la vista a renderizar
				switch($type){
					case 1:
						$viewName = "News";
						break;
					case 2:
						$viewName = "Content";
						break;
					case 3:
						$viewName = "Gallery";
						break;
				}
				// Renderizar la vista
				return $this->Partial($viewName, $model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Edit", $e);
				// Relanzar el error
				throw $e;
			}
		}


		///<summary>
		/// Acción para la edición del contenido especificado
		///</summary>
		public function Edit($id = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instanciar modelo
				$model = new ContentModel();
				// Obtener datos de la entidad
				$model->Read($id);
				// Cargar formulario con los datos
				$model->LoadFormData($model->Entity->Type);
				// Establecer la vista a renderizar
				switch($model->Entity->Type){
					case 1:
						$viewName = "News";
						break;
					case 2:
						$viewName = "Content";
						break;
					case 3:
						$viewName = "Gallery";
						break;
				}
				// Renderizar la vista
				return $this->Partial($viewName, $model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Edit", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Acción para guardar la información de la entidad
		///</summary>
		public function Save(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Obtener datos de la entidad
				$entity = $this->GetEntity( "Content" );
				// Instanciar modelo
				$model = new ContentModel();
				// Guardar los datos
				if($model->Save($entity))
					// Redirigir el flujo
					return $this->RedirectTo("Edit", "Content", array( "id" => $model->Entity->Id));

				// Obtener el tipo de entidad
				$type = intval($entity->Type);
				// Cargar los datos del formulario
				$model->LoadFormData($type);
				// Definir la vista a renderizar
				switch($type){
					case 1:
						$viewName = "News";
						break;
					case 2:
						$viewName = "Content";
						break;
					case 3:
						$viewName = "Gallery";
						break;
					// Renderizar la vista
					return $this->Partial($viewName, $model);
				}
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Save", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Controlador para la gestión de secciones
		///</summary>
		public function Delete($id = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instanciar modelo
				$model = new ContentModel();
				// Eliminar la entidad
				$model->Delete($id);
				// Redirigir el flujo
				return $this->RedirectTo( "Index", "Content" );
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Delete", $e);
				// Relanzar el error
				throw $e;
			}
		}
	}

?>
