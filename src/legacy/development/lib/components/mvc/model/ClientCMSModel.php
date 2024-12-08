<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Model para la gestión de secciones
 *
 * @author alfonso
 */
class ClientCMSModel extends \Model{

    /**
     * Resultado de la operación previa
     * @var string Mensaje de resultado
     */
    public $eResult = "";

    /**
     * Resultado de la operación previa
     * @var string Clase CSS a utilizar en el mensaje
     */
    public $eResultclass = "has-success";

    /**
     * Ruta base para recursos externos
     * @var string Url externa del recurso
     */
    public $ExtResources = "";

    /**
     * Identidad del proyecto
     * @var int Identidad del proyecto
     */
    public $Project = 0;

    /**
     * Nombre del proyecto
     * @var string Nombre del proyecto
     */
    public $ProjectName = "";

    /**
     * Path del proyecto
     * @var string Ruta del proyecto
     */
    public $ProjectPath = "";

    /**
     * Descripción de la sección o contenido
     * @var string Descripción del proyecto
     */
    public $Description = "";

    /**
     * Keywords de la sección o contenido
     * @var string Colección de términos clave
     */
    public $Keywords = "";

    /**
     * Autor
     * @var string Autor
     */
    public $Author = "";

    /**
     * Link
     * @var string Link de acceso
     */
    public $Link = "";

    /**
     * Contenido buscado
     * @var string Contenido
     */
    public $Content = "";

    /**
     * Nombre del template a utilizar
     * @var string Plantilla a utilizar
     */
    public $Template = "";

    /**
     * Indica si vemos contenidos en borrador o no
     * @var boolean Estado de publicación
     */
    public $Draft = 0;

    /**
     * Nombre del tipo de entidad
     * @var string Tipología del contenido
     */
    public $TypeName = "";

    /**
     * Colección de secciones root
     * @var array Colección de secciones para el menú principal
     */
    public $Menu = array();

    /**
     * Colección de secciones hijas
     * @var array Colección de subsecciones
     */
    public $Sections = array();

    /**
     * Colección de contenidos de tipo noticias
     * @var array Colección de contenidos de tipo noticias
     */
    public $News = array();

    /**
     * Colección de contenidos de tipo content
     * @var array Colección de contenidos de tipo genérico
     */
    public $Contents = array();

    /**
     * Colección de contenidos de tipo galería
     * @var array Colección de galerías disponibles
     */
    public $Gallery = array();

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar el constructor padre
        parent::__construct();
        // Establecer los parámetros del contexto
        $this->SetContext();
        // Establecer el modo de acceso
        $this->SetDraft();
        // Establecer el menú principal
        $this->LoadMenu();
    }

    /**
     * Método que obtiene todas las secciones filtradas por proyecto
     */
    public function LoadSection($sectionN = ""){
        // Generar log
        $this->CreateSectionLog($sectionN);
        // Cargar toda la información de la sección
        $section = $this->LoadSectionData( $sectionN );
        // Cargar Noticias
        $this->LoadNews($section);
        // Cargar Contenidos de la sección
        $this->LoadContents($section);
        // Cargar las galerías de la sección
        $this->LoadGalleries($section);
    }

    /**
     * Método que obtiene la sección por su identidad
     */
    public function LoadContent($sectionN = "", $content = ""){
        // Establecer el nombre de la sección
        $sectionName = $sectionN."/".$content;
        // Generar traza
        $this->CreateSectionLog($sectionName);

        if(!$this->ValidateSection( $sectionName )){
            // Obtener la referencia al contenido
            $content = $this->GetContentByLink($sectionN, $content);
            // Cargar la info del contenido
            $this->SetContent($content);
        }
        else{
            $this->LoadSection( $sectionN );
        }
    }

    /**
     * Establecer el modo de visualización
     */
    private function SetDraft(){
        // Setear el estado de los contenidos
        $this->Draft = (defined("_DRAFT_"))? _DRAFT_ : 0;
    }

    /**
     * Configurar los parámetros del contexto
     */
    private function SetContext(){
        // Establecer la ruta a recursos externos
        $this->ExtResources = ConfigurationManager::GetKey("extresources");

        // Cargar la identidad del proyecto
        $this->Project = (isset($_SESSION["projectId"]))
                ? $_SESSION["projectId"] : 0;
        // Cargar el nombre de proyecto
        $this->ProjectName = (isset($_SESSION["projectName"]))
                ? $_SESSION["projectName"] : "";
        // Cargar la Ruta de proyecto
        $this->ProjectPath = (isset($_SESSION["projectPath"]))
                ? $_SESSION["projectPath"] : "";
        // Establecer el resultado de la última operación
        $this->eResult = (isset($_SESSION["eResult"])
                && $_SESSION["eResult"] != "")
                ? $_SESSION["eResult"] : "";
        // Establecer el estilo CSS del resultado de la última operación
        $this->eResultClass = (isset($_SESSION["eResultClass"])
                && $_SESSION["eResultClass"] != "")
                ? $_SESSION["eResultClass"] : "has-success";

        if(isset($_SESSION["eResult"])){
            unset($_SESSION["eResult"]);
        }

        if(isset($_SESSION["eResultClass"])){
            unset($_SESSION["eResultClass"]);
        }
    }

    /**
     * Cargar las opciones del menú principal
     */
    private function LoadMenu(){
        // Filtro de búsqueda
        $filter = ($this->Draft == 0)
            ? array( "Project" => $this->Project , "Root" => NULL,
                "Draft" => $this->Draft )
            : array( "Project" => $this->Project , "Root" => NULL );
        // Cargar todas las opciones del menú ( Secciones de tipo root )
        $this->Menu = $this->Dao->GetByFilter( "Section", $filter );
        // $this->Menu =
        // $this->Dao->GetByFilterAndOrder( "Section", $filter, $order);
    }

    /**
     * Obtener vector de imagenes de gallery
     * @param \Content Referencia a un objeto contenido
     * @return array
     */
    private function SetGalleryImages( $gallery = null ){
        // Comprobar que existe
        if(!isset($gallery) || $gallery == null){
            return;
        }
        // Obtener vector de paths
        $images = explode(";",$gallery->Content);
        // Array de imagenes
        $galImages = array();
        // Completar array de imágenes
        foreach($images as $image){
            if($image != ""){
                $galImages[] = array( "src" => $image );
            }
        }
        // retornar
        return $galImages;
    }

    /**
     * Obtener todas las urls de las imágenes del contenido
     * @param int Tipo de contenido
     * @return void
     */
    private function SetImages( $content = 0 ){
        // Adaptar contenido si es tipo galería
        if($content->Type != 3){
            return;
        }
        // Iniciar el array de imágenes
        $this->Images = array();
        // Obtener las urls de las imágenes
        $images = explode(";",$this->Content);
        // Agregar cada una de las imágenes
        foreach($images as $image){
            if($image != "") {
                $this->Images[] = array( "src" => $image );
            }
        }
    }

    /**
     * Carga la primera sección disponible
     * @return \Section
     * @throws UrlException
     */
    private function GetFirstSection(){
        // Cargar filtro de búsqueda
        $filter = ($this->Draft == 0)
            ? array( "Project" => $this->Project,
                "Draft" => $this->Draft, "State" => true )
            : array( "Project" => $this->Project, "State" => true );
        // Ejecutar la búsqueda
        $sections = $this->Dao->GetByFilter( "Section", $filter );
        // Comprobación del resultado de la búsqueda
        if(count($sections) == 0){
            throw new \UrlException( "LoadSection : empty" );
        }
        // Asignar primer resultado
        return $sections[0];
    }

    /**
     * Obtiene la sección filtrada por su propiedad Link
     * @param string cadena de búsqueda
     * @return \Section
     * @throws UrlException
     */
    private function GetSectionByLink($sectionN = ""){
        $filter = ($this->Draft == 0)
            ? array( "Project" => $this->Project,
                "Link" => $sectionN, "Draft" => $this->Draft,
                "State" => true )
            : array( "Project" => $this->Project,
                "Link" => $sectionN, "State" => true );
        // Ejecutar la búsqueda
        $sections = $this->Dao->GetByFilter( "Section", $filter );
        // Comprobación del resultado de la búsqueda
        if(count($sections) == 0) {
            throw new \UrlException( "LoadSection : ".$sectionN );
        }
        // Asignar primer resultado
        return $sections[0];
    }

    /**
     * Establece las propiedades del Model a partir de un objeto sección
     * @param \Section Referencia a la sección
     */
    private function SetSection($section = null){
        if($section != null){
            $this->Title = $section->Name;
            $this->Description = $section->Description;
            $this->Keywords = $section->Keywords;
            $this->Author = $section->Author;
            $this->Link = $section->Link;
            $this->Template = $section->Template;
            $this->TypeName = "Section";
        }
    }

    /**
     * Cargar el modelo con la información de la sección
     * @param string Propiedad Link de la sección
     * @return \Section
     */
    private function LoadSectionData($sectionN = ""){
        $section = null;
        // Si no se ha seleccionado una sección, buscamos la primera
        if($sectionN == ""){
            // Cargar la primera sección disponible
            $section = $this->GetFirstSection();
        }
        else{
            // Asignar primer resultado
            $section = $this->GetSectionByLink($sectionN);
        }

        if(isset($section)){
            $this->SetSection($section);

            $filter = ($this->Draft == 0)
                ? array( "Root" => $section->Id,
                    "Draft" => $this->Draft, "State" => true )
                : array( "Root" => $section->Id, "State" => true );

            $this->Sections = $this->Dao->GetByFilter( "Section", $filter );
            //$this->Sections =
            //$this->Dao->GetByFilterAndOrder( "Section", $filter, $order);
        }

        return $section;
    }

    /**
     * Obtiene un objeto Content filtrado por la propiedad Link
     * @param string Nombre de la sección Padre
     * @param string Propiedad Link del objeto Content
     * @return \Content
     * @throws UrlException
     */
    private function GetContentByLink($sectionN = "", $content = ""){
        // Cargar el contenido buscado
        $filter = ($this->Draft == 0)
            ? array( "Link" => $content, "Draft" => $this->Draft,
                "State" => true )
            : array( "Link" => $content, "State" => true );
        // Búsqueda del contenido
        $contents = $this->Dao->GetByFilter( "Content", $filter);
        // Comprobación del resultado de la búsqueda
        if(count($contents) == 0){
            throw new \UrlException( "LoadContent : ".$sectionN );
        }

        return $contents[0];
    }

    /**
     * Configurar los datos del contenido
     * @param string Nombre de la sección Padre
     * @param \Content Referencia al objeto Content
     * @return void
     */
    private function SetContent($sectionN = "", $content = null){
        if($content == null){
            return;
        }

        $this->Title = $content->Title;
        $this->Description = $content->Description;
        $this->Keywords = $content->Keywords;
        $this->Author = $content->Author;
        $this->Template = $content->Template;
        $this->Link = $sectionN."/".$content->Link;
        $this->Content = base64_decode($content->Content);
        $this->TypeName = $this->GetTypeName($content->Type);
        $this->SetImages($content);
    }

    /**
     * Carga la colección de noticias asociadas a una sección
     * @param \Section Referencia a la sección padre
     * @return void
     */
    private function LoadNews($section = null){

        if($section == null || !isset($section)){
            return;
        }

        $filter = ($this->Draft == 0)
            ? array( "Section" => $section->Id, "Type" => 1,
                "Draft" => $this->Draft, "State" => true )
            : array( "Section" => $section->Id, "Type" => 1,
                "State" => true );

        $this->News = $this->Dao->GetByFilter( "Content", $filter );

        foreach($this->News as $new){

            $new->Content = base64_decode($new->Content);

            if($new->ExtLink != 1){
                $new->Link = $section->Link."/".$new->Link;
            }
        }
    }

    /**
     * Cargar la colección de contenidos genéricos asociados a una sección
     * @param \Section Referencia a la sección padre
     * @return void
     */
    private function LoadContents($section = null){
        // Validación del parámetro
        if(!isset($section) || $section == null){
            return;
        }

        // Cargar Contenidos de la sección
        $filter = ($this->Draft == 0)
                ? array( "Section" => $section->Id, "Type" => 2,
                    "Draft" => $this->Draft, "State" => true )
                : array( "Section" => $section->Id, "Type" => 2,
                    "State" => true );

        $this->Contents = $this->Dao->GetByFilter( "Content", $filter);

        foreach($this->Contents as $content){

            $content->Content = base64_decode($content->Content);

            if($content->ExtLink != 1){
                $content->Link = $section->Link."/".$content->Link;
            }
        }
    }

    /**
     * Cargar la colección de galerías asociadas a una sección
     * @param \Section Referencia a la sección padre
     * @return void
     */
    private function LoadGalleries($section = null){
        // Validación del parámetro
        if(!isset($section) || $section == null){
            return;
        }

        // Cargar galería de imagenes de la sección
        $filter = ($this->Draft == 0)
            ? array( "Section" => $section->Id, "Type" => 3,
                "Draft" => $this->Draft, "State" => true )
            : array( "Section" => $section->Id, "Type" => 3,
                "State" => true );

        $this->Gallery = $this->Dao->GetByFilter( "Content", $filter);

        foreach($this->Gallery as $gallery){

            $gallery->Content = base64_decode($gallery->Content);

            if($gallery->ExtLink != 1){
                $gallery->Link = $section->Link."/".$gallery->Link;
            }

            $this->{$gallery->Title} = $this->SetGalleryImages( $gallery );
        }

    }

    /**
     * Genera la traza sobre la sección solicitada
     * @param type $sectionN
     */
    private function CreateSectionLog($sectionN = ""){
        // Obtener implementación
        $log = $this->Injector->Resolve( "ILogManager" );

        $log->LogInfo("Url - LoadSection : ".$sectionN);
    }

    /**
     * Proceso de validación de la sección solicitada
     * @param string Nombre de la sección solicitada
     * @return boolean
     */
    private function ValidateSection($sectionName = ""){
        // Cargar filtro de búsqueda
        $filter = ($this->Draft == 0)
            ? array( "Project" => $this->Project, "Link" => $sectionName,
                "Draft" => $this->Draft, "State" => true )
            : array( "Project" => $this->Project, "Link" => $sectionName,
                "State" => true );
        // Ejecutar la búsqueda
        $sections = $this->Dao->GetByFilter( "Section", $filter );
        // Comprobación del resultado de la búsqueda
        return (count($sections) != 0);
    }

    /**
     * Obtiene el nombre de la tipología de contenido
     * @param int Identidad de la tipología
     * @return string
     */
    private function GetTypeName($type = 1){
        $typeName = "";
        switch( $type ){
            case 1:
                $typeName = "News";
                break;
            case 2:
                $typeName = "Content";
                break;
            case 3:
                $typeName = "Gallery";
                break;
        }
        return $typeName;
    }

}
