<?php

	///<summary>
	/// Model para el login de aplicación
	///</summary>
	class ResourcesModel extends SaasModel{

		///<summary>
		///
		///</summary>
		public $Entity = "";

		///<summary>
		///
		///</summary>
		public $BasePath = "";

		///<summary>
		/// Colección de los nombres de las imágenes del proyecto
		///</summary>
		public $Images = array();

		///<summary>
		/// Colección de los nombres de las hojas de estilo del proyecto (CSS)
		///</summary>
		public $StyleSheets = array();

		///<summary>
		/// Colección de los scripts disponibles en el proyecto
		///</summary>
		public $Scripts = array();

		///<summary>
		/// Colección de los layout disponibles en el proyecto
		///</summary>
		public $Layouts = array();

		///<summary>
		/// Colección de plantillas para secciones
		///</summary>
		public $SectionTemplates = array();

		///<summary>
		/// Colección de plantillas para contenidos del proyecto
		///</summary>
		public $ContentTemplates = array();

		///<summary>
		/// Colección de plantillas para noticias del proyecto
		///</summary>
		public $NewsTemplates = array();

		///<summary>
		/// Colección de plantillas para galerías del proyecto
		///</summary>
		public $GalleryTemplates = array();

		///<summary>
		/// Resuelve el path para la entidad
		///</summary>
		private function GetPath($entity = ""){

			if($entity == "") {	return; }

			if($entity == "Image"){
				// Obtener path del config.xml
				$path = ConfigurationManager::GetKey( "imagesPath" );
				// Guardar BasePath
				$this->BasePath = str_replace("{Project}",$this->ProjectPath ,$path."/");
			}
			else if($entity == "StyleSheet"){
				// Obtener path del config.xml
				$path = ConfigurationManager::GetKey( "cssPath" );
				// Guardar BasePath
				$this->BasePath = str_replace("{Project}",$this->ProjectPath ,$path."/");
			}
			else if($entity == "JavaScript"){
				// Obtener path del config.xml
				$path = ConfigurationManager::GetKey( "scriptPath" );
				// Guardar BasePath
				$this->BasePath = str_replace("{Project}",$this->ProjectPath ,$path."/");
			}
			else if($entity == "Layout"){
				// Obtener path del config.xml
				$path = ConfigurationManager::GetKey( "layoutPath" );
				// Guardar BasePath
				$this->BasePath = str_replace("{Project}",$this->ProjectPath ,$path."/");
			}
			else{
				// Obtener path del config.xml
				$path = ConfigurationManager::GetKey( "templatePath" );
				// Reemplazar el nombre de la entidad
				$path = str_replace( "{Entity}", $entity, $path);
				// Guardar BasePath
				$this->BasePath = str_replace( "{Project}", $this->ProjectPath , $path."/");
			}

			$this->BasePath = str_replace( "//", "/", $this->BasePath);

			return $this->BasePath;
		}

		///<summary>
		/// Constructor por defecto
		///</summary>
		public function __construct($entity = ""){
			// Constructor padre
			parent::__construct();

			$this->Entity = $entity;

			if($this->Entity == ""){ return; }

			$this->GetPath($entity);
		}

		///<summary>
		/// Guarda el fichero enviado
		///</summary>
		public function SaveFile(){
			// Validar el nombre de la entidad
			if($this->Entity == ""){ return;}
			// Crear filtro de extensiones
			if($this->Entity == "Image"){
				$filter = array( "jpg" , "JPG" , "png", "PNG" , "GIF" , "gif" );
			}
			else if($this->Entity == "StyleSheet"){
				$filter = array( "css" );
			}
			else if($this->Entity == "JavaScript"){
				$filter = array( "js" );
			}
			else{
				$filter = array( "html", "htm" );
			}

			// Operaciones para adaptar el nombre del fichero
      $file =  $_FILES[ "file" ];
			//  Eliminar espacios en blanco
			$file["name"] = str_replace(" ", "-", $file["name"]);
			// Pasar a minúsculas
			$file["name"] = strtolower($file["name"]);
			// Almacenar fichero
			Uploader::UploadFile("file", $this->BasePath , $filter, true);
		}

		///<summary>
		/// Renombra el fichero especificado
		///</summary>
		public function RenameFile($fileName = "", $newFileName = ""){
			if($fileName != "" && $newFileName != ""){
				return rename ( $this->BasePath.$fileName , $this->BasePath.$newFileName );
			}
			return false;
		}

		///<summary>
		/// Obtiene el contenido del fichero especificado
		///</summary>
		public function DownloadFile($fileName = ""){
			if($fileName != ""){
				return file_get_contents ($this->BasePath.$fileName);
			}
			return "";
		}

		///<summary>
		/// Elimina el fichero especificado
		///</summary>
		public function DeleteFile($fileName = ""){
			if($fileName != ""){
				return unlink ($this->BasePath.$fileName);
			}
			return false;
		}

		///<summary>
		/// Carga todos los ficheros indicados
		///</summary>
		public function LoadFiles(){
			// Cargar listas de archivos según su entidad
			$path = $this->GetPath( "Image" );
			$this->Images = FileManager::GetFiles($path);

			foreach($this->Images as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->Images[$index]);
				}
			}

			$path = $this->GetPath( "StyleSheet" );
			$this->StyleSheets = FileManager::GetFiles($path);
			foreach($this->StyleSheets as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->StyleSheets[$index]);
				}
			}

			$path = $this->GetPath( "JavaScript" );
			$this->Scripts = FileManager::GetFiles($path);
			foreach($this->Scripts as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->Scripts[$index]);
				}
			}

			$path = $this->GetPath( "Layout" );
			$this->Layouts = FileManager::GetFiles($path);
			foreach($this->Layouts as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->Layouts[$index]);
				}
			}

			$path = $this->GetPath( "Content" );
			$this->ContentTemplates = FileManager::GetFiles($path);
			foreach($this->ContentTemplates as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->ContentTemplates[$index]);
				}
			}

			$path = $this->GetPath( "News" );
			$this->NewsTemplates = FileManager::GetFiles($path);
			foreach($this->NewsTemplates as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->NewsTemplates[$index]);
				}
			}

			$path = $this->GetPath( "Section" );
			$this->SectionTemplates = FileManager::GetFiles($path);
			foreach($this->SectionTemplates as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->SectionTemplates[$index]);
				}
			}

			$path = $this->GetPath( "Gallery" );
			$this->GalleryTemplates = FileManager::GetFiles($path);
			foreach($this->GalleryTemplates as $index => $item){
				if($item["Name"] == "." || $item["Name"] == ".."){
					unset($this->GalleryTemplates[$index]);
				}
			}
		}
	}

?>
