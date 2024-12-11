<?php

	///<summary>
	/// Model para la gestión de secciones
	///</summary>
	class SectionModel extends SaasModel{

		///<summary>
		/// Colección de sectiones root
		///</summary>
		public $Sections = array();

		///<summary>
		/// Colección de plantillas
		///</summary>
		public $Templates = array();

		///<summary>
		/// Referencia una entidad de tipo Section
		///</summary>
		public $Entity = null;

		///<summary>
		/// Colección de secciones del proyecto
		///</summary>
		public $Entities = array();

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Constructor padre
			parent::__construct();
		}

		///<summary>
		/// Método que obtiene todas las secciones filtradas por proyecto
		///</summary>
		public function Get(){
			// Filtro a utilizar
			$filter = array( "Project" => $this->Project );
			// Obtener la colección de secciones
			$this->Entities = $this->Dao->GetByFilter( "Section", $filter);
			// Establecer el título
			$this->Title = "Secciones";
		}

		///<summary>
		/// Método que obtiene la sección por su identidad
		///</summary>
		public function Read($id = 0){
			// Obtener la entidad buscada por su ID
			$this->Entity = $this->Dao->Read($id, "Section");
		}

		///<summary>
		/// Método que "prepara" la información necesaria para el formulario.
		/// Debe cargar la lista de posibles secciones padre(root : null)
		///</summary>
		public function LoadFormData(){
			// Establecer el título
			$this->Title = "Sección";
			// Filtro para buscar las secciones
			$filter = array( "Project" => $this->Project );
			// Cargar la lista de secciones
			$this->Sections = $this->Dao->GetByFilter( "Section" , $filter);

			if($this->ProjectPath != "") {
				$path = ConfigurationManager::GetKey( "templatePath" );
				$path = str_replace("{Project}", $this->ProjectPath, $path );
				$path = str_replace("{Entity}", "Section", $path );
				$this->Templates = FileManager::GetFilterFiles( $path, ".html" );
			}

			// Quitar de la lista de secciones la actual en edición
			if(isset($this->Entity) && $this->Entity->Id > 0){
				$idSec = $this->Entity->Id;
				foreach($this->Sections as $index => $section){
					if($section->Id == $idSec){
						unset($this->Sections[$index]);
						continue;
					}
					if($section->Root != NULL)
						unset($this->Sections[$index]);
				}
			}

		}

		///<summary>
		/// Método que guarda la información relativa a la entidad
		///</summary>
		public function Save($entity = null){
			// Establecer root
			if($entity->Root == "null")	$entity->Root = NULL;
			// Establecer proyecto
			$entity->Project = $this->Project;
			// Validar entidad
			$this->ErrorList = $this->Dao->IsValid($entity);
			// Establecer la entidad
			$this->Entity = $entity;
			// Varlidar la lista de errores
			if(count($this->ErrorList) != 0) return false;
			// Comprobar si es creación o actualización
			if($entity->Id == 0)
				$this->Entity->Id = $this->Dao->Create($entity);
			else
				$this->Dao->Update($entity);
			// Salir
			return true;
		}

		///<summary>
		/// Método que elimina la entidad identificada por su id
		///</summary>
		public function Delete($id = 0){
			// Eliminar entidad
			$this->Dao->Delete($id, "Section");
		}
	}
?>
