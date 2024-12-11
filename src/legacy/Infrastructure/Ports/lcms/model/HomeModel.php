<?php

	///<summary>
	/// Model para HomeController
	///</summary>
	class HomeModel extends SaasModel{

		///<summary>
		/// Colección de proyectos
		///</summary>
		public $Projects = array();

		///<summary>
		/// Constructor
		///</summary>
		public function __construct(){
			// Constructor padre
			parent::__construct();
		}

		///<summary>
		/// Carga todos los proyectos con permisos
		///</summary>
		public function LoadProjects(){
			// filtro búsqueda de proyectos
			$filter = array( "Username" => $this->Username, "IdService" => $this->Service );
			// Filtrar la lista de proyectos
			$this->Projects = $this->Dao->GetByFilter( "ProjectInfo", $filter );
		}

		///<summary>
		/// Establece el proyecto seleccionado
		///</summary>
		public function SetProject($id = 0){
			$filter = array( "Username" => $this->Username, "IdService" => $this->Service, "Id" => $id);
			// Filtrar el proyecto seleccionado
			$projects = $this->Dao->GetByFilter( "ProjectInfo", $filter);
			// Cargamos el primer resultado de los obtenidos
			if(count($projects) > 0){
				$project = $projects[0];
				$_SESSION["projectId"] = $project->Id;
				$_SESSION["projectName"] = $project->Name;
				$_SESSION["projectPath"] = $project->Path;
			}
		}
	}

?>
