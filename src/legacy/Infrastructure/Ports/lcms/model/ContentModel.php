<?php

	///<summary>
	/// Model para la gestión de contenidos
	///</summary>
	class ContentModel extends SaasModel{

		///<summary>
		/// Identidad de la sección padre
		///</summary>
		public $Section = 0;

		///<summary>
		/// Colección de secciones disponibles en el proyecto
		///</summary>
		public $Sections = array();

		///<summary>
		/// Colección de tipos de contenidos disponibles del proyecto
		///</summary>
		public $Types = array();

		///<summary>
		/// Colección de plantillas disponibles
		///</summary>
		public $Templates = array();

		///<summary>
		/// Colección de plantillas disponibles
		///</summary>
		public $Images = array();

		///<summary>
		/// Referencia a la entidad
		///</summary>
		public $Entity = null;

		///<summary>
		/// Colección de contenidos disponibles para una sección
		///</summary>
		public $Entities = array();

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Constructor padre
			parent::__construct();

			$this->Entity = new Content();
			// Establecer la sección
			if(isset($_SESSION["sectionId"]))
				$this->Section = $_SESSION["sectionId"];
		}

		///<summary>
		/// Carga la lista de posibles enlaces externos para el contenido
		///</summary>
		private function GetLinks(){
			// Configurar el filtro para la selección de atos
			$filter = array( "Project" => $this->Project );
			// Cargar secciones del proyecto
			$sections = $this->Dao->GetByFilter( "Section", $filter);

			foreach($sections as $section){
				// Agregar enlace de la sección
				$this->DataLinks[] = array("Link" => $section->Link);
				// Cargar contenidos hijos de la sección
				$contents = $this->Dao->GetByFilter( "Content" , array( "Section" , $section->Id ));
				// Construir enlaces hijos
				foreach($contents as $content){
					// Comprobamos que sea un contenido enlazable
					if($content->ExtLink != 0){ continue; }
					// Agregar enlace a la lista
					$this->DataLinks[] = array("Link" => $section->Link."/".$content->Link );
				}
			}
		}

		///<summary>
		/// Lista los ficheros de un directorio
		///</summary>
		private function GetFiles($entity = "", $projectName = "", $ext = "" ){

			$path = ConfigurationManager::GetKey( "templatePath" );

			$path = str_replace("{Project}", $projectName, $path );

			$path = str_replace("{Entity}", $entity, $path );

			return FileManager::GetFilterFiles( $path, $ext );
		}

		///<summary>
		/// Lista las imágenes
		///</summary>
		private function GetImages($projectName = ""){

			$path = ConfigurationManager::GetKey( "imagesPath" );

			$path = str_replace("{Project}", $projectName, $path );

			$path = str_replace("//", "/", $path );

			return FileManager::GetFilterFiles( $path , ".png, .jpg" );
		}

		public function SetSection($id = 0){
			// Recuperamos la última sección seleccionada
			if(is_numeric($id) && $id != 0)
				$_SESSION["sectionId"] = $id;

			// Establecer la sección
			if(isset($_SESSION["sectionId"]))
				$this->Section = $_SESSION["sectionId"];
		}

		///<summary>
		/// Obtiene la colección de contenidos filtrados por sección
		///</summary>
		public function Get(){
			// Filtro para la búsqueda
			$filter = array( "Section" => $this->Section );
			// Cargar la lista de contenidos
			$this->Entities = $this->Dao->GetByFilter( "Content" , $filter);
			// Decodificar los contenidos
			foreach($this->Entities as $entity)
				$entity->Content = base64_decode($entity->Content);
		}

		///<summary>
		/// Carga los datos para el index
		///</summary>
		public function LoadIndex(){
			// Cargar tipos de contenidos
			$this->Types = $this->Dao->Get ( "ContentType" );
			// Cargar secciones del proyecto
			$this->Sections = $this->Dao->GetByFilter( "Section", array( "Project" => $this->Project ));
			// Establecer el título
			$this->Title = "Contenidos";
		}

		///<summary>
		/// Obtiene la información de un contenido por su identidad
		///</summary>
		public function Read($id = 0){
			// Obtiene la entidad de bbdd
			$this->Entity = $this->Dao->Read($id, "Content" );
			// Decodifica el contenido
			$this->Entity->Content = base64_decode($this->Entity->Content);
		}

		///<summary>
		/// Carga las dependencias necesarias para el formulario de contenidos
		///</summary>
		public function LoadFormData($type = 0){
			// Filtro para la búsqueda
			$filter = array( "Project" => $this->Project);
			// Cargar la lista de secciones
			$this->Sections = $this->Dao->GetByFilter( "Section" , $filter);
			// Establecer el tipo
			$this->Entity->Type = $type;
			// Cargar ficheros de recursos
			if($this->ProjectPath != "") {
				if($type == 1){
					$this->Title = "Noticia";
					$this->Templates = $this->GetFiles( "News", $this->ProjectPath, ".html" );
				}
				elseif($type == 2){
					$this->Title = "Contenido";
					$this->Templates = $this->GetFiles( "Content", $this->ProjectPath, ".html" );
					$this->Images = $this->GetImages( $this->ProjectPath );
				}
				elseif($type == 3){
					$this->Title = "Galería";
					$this->Templates = $this->GetFiles( "Gallery", $this->ProjectPath, ".html" );
					$this->Images = $this->GetImages( $this->ProjectPath );
				}
			}
			// Cargar lista de links
			$this->GetLinks();
		}

		///<summary>
		/// Método que guarda la información relativa a la entidad
		///</summary>
		public function Save($entity = null){

			$this->Entity = $entity;

			$this->ErrorList = $this->Dao->IsValid($entity);

			if(count($this->ErrorList) != 0) return false;

			if($entity->Id == 0)
				$this->Entity->Id = $this->Dao->Create($entity);
			else
				$this->Dao->Update($entity);

			return true;
		}

		///<summary>
		/// Método que elimina la entidad identificada por su id
		///</summary>
		public function Delete($id = 0){
			$this->Dao->Delete($id, "Content");
		}

	}
?>
