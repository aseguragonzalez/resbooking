<?php

	include_once("model/SectionModel.php");

	///<summary>
	/// Controlador para la gestión de secciones
	///</summary>
	class SectionController extends LcmsController{

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Cargar constructor padre
			parent::__construct();
		}

		///<summary>
		/// Carga la lista de secciones asociadas al proyecto
		///</summary>
		public function Index(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instancia del modelo
				$model = new SectionModel();
				// Obtener la colección de secciones
				$model->Get();
				// Renderizar vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Index", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Acción para la creación de la sección
		///</summary>
		public function Create(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instancia del modelo
				$model = new SectionModel();
				// Cargar los datos del formulario
				$model->LoadFormData();
				// Renderizar vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Create", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Acción para la edición de la sección
		///</summary>
		public function Edit($id = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instancia del modelo
				$model = new SectionModel();
				// Obtener la entidad buscada
				$model->Read($id);
				// Cargar datos de la entidad en el formulario
				$model->LoadFormData();
				// Renderizar vista
				return $this->PartialView($model);
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
				// Obtener los datos de la entidad desde la petición
				$entity = $this->GetEntity( "Section" );
				// Instancia del modelo
				$model = new SectionModel();
				// Guardar los datos de la sección y redirigir
				if($model->Save($entity))
					// Redirigir el flujo
					return $this->RedirectTo("Edit", "Section", array( "id" => $model->Entity->Id ));

				// Cargar formulario
				$model->LoadFormData();
				// Renderizar vista
				return $this->Partial("Edit", $model);
			}
			catch(Exception $e){
				// Registrar el error
				$this->Log->LogErrorTrace("Save", $e);
				// Relanzar el error
				throw $e;
			}
		}

		///<summary>
		/// Acción para eliminar una sección
		///</summary>
		public function Delete($id = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0) return $this->RedirectTo( "Index", "Home" );
				// Instancia del modelo
				$model = new SectionModel();
				// Eliminar la entidad especificada
				$model->Delete($id);
				// Redirigir el flujo
				return $this->RedirectTo("Index","Section");
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
