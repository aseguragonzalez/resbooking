<?php

	include_once("model/ResourcesModel.php");

	///<summary>
	/// Controlador para la gestión de recursos de proyecto
	///</summary>
	class ResourcesController extends LcmsController{

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// constructor padre
			parent::__construct();
		}

		///<summary>
		/// Acción por defecto : carga la lista de entidades
		///</summary>
		public function Index($section = 0){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0){ return $this->RedirectTo( "Index", "Home" ); }
				// Instanciar model
				$model = new ResourcesModel("");
				// Listar todos los ficheros disponibles
				$model->LoadFiles();
				// Precesado de la vista
				return $this->PartialView($model);
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace( "Index", $e);

				throw $e;
			}
		}

		///<summary>
		/// Acción para guardar la información de la entidad
		///</summary>
		public function Save(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0){ return $this->RedirectTo( "Index", "Home" ); }
				// Instanciar modelo
				$model = new ResourcesModel($this->GetPostKey( "Entity" ));
				// Guardar fichero enviado
				$model->SaveFile();
				// Volver al index
				return $this->RedirectTo( "Index", "Resources");
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Save", $e);

				throw $e;
			}
		}

		///<summary>
		/// Controlador para la gestión de secciones
		///</summary>
		public function Rename(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0){ return $this->RedirectTo( "Index", "Home" ); }
				// Instanciar modelo
				$model = new ResourcesModel($this->GetPostKey( "Entity" ));
				// Ejecutar operación de eliminación
				$model->RenameFile($this->GetPostKey( "File" ), $this->GetPostKey( "NewName" ));
				// Volver al index
				return $this->RedirectTo( "Index", "Resources");
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Delete", $e);

				throw $e;
			}
		}

		///<summary>
		/// Controlador para la gestión de secciones
		///</summary>
		public function Download(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0){ return $this->RedirectTo( "Index", "Home" ); }
				// Instanciar modelo
				$model = new ResourcesModel($this->GetPostKey( "Entity" ));
				// Ejecutar operación de eliminación
				return $model->DownloadFile($this->GetPostKey( "File" ));
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Delete", $e);

				throw $e;
			}
		}

		///<summary>
		/// Controlador para la gestión de secciones
		///</summary>
		public function Delete(){
			try{
				// Comprobar que hay proyecto seleccionado
				if($this->Project == 0){ return $this->RedirectTo( "Index", "Home" ); }
				// Instanciar modelo
				$model = new ResourcesModel($this->GetPostKey( "Entity" ));
				// Ejecutar operación de eliminación
				$model->DeleteFile($this->GetPostKey( "File" ));
				// Volver al index
				return $this->RedirectTo( "Index", "Resources");
			}
			catch(Exception $e){
				$this->Log->LogErrorTrace("Delete", $e);

				throw $e;
			}
		}

		///<summary>
		/// Obtener el contenido de una clave post
		///</summary>
		private function GetPostKey( $key ){
			// Comprobar que está definida la clave
			if(!isset($_REQUEST[$key])){ return null;	}
			// Obtener el valor de la clave
			$value = $_REQUEST[$key];
			// Filtrar caracteres
			$value = strip_tags($value);
			// Devolver el contenido
			return $value;
		}
	}

?>
