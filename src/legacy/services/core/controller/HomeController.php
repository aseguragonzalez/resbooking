<?php

    // Cargar Model
    include_once("model/HomeModel.php");

    ///<summary>
    /// Controlador para los formularios públicos
    ///</summary>
    class HomeController extends \SaasController{

            ///<summary>
            /// Constructor por defecto
            ///</summary>
            public function __construct(){
                    parent::__construct();
            }

            ///<summary>
            /// Carga la página inicial
            ///</summary>
            public function Index(){
                    try{
                            // Instanciar modelo
                            $model = new HomeModel();
                            // Determinar la vista a cargar
                            $view = ($model->Username != "") ? "AuthIndex" : "Index" ;
                            // Procesado de la vista
                            return $this->Partial($view, $model);
                    }
                    catch(Exception $e){
                            // Procesar la información de la excepción
                            $this->Log->LogErrorTrace( "Index", $e );
                            // Relanzar excepción
                            throw $e;
                    }
            }

            ///<summary>
            /// Obtiene el formulario con la información del servicio
            ///</summary>
            public function About(){
                    try{
                            // Instanciar modelo
                            $model = new HomeModel();
                            // Determinar la vista a cargar
                            $view = ($model->Username != "") ? "AuthAbout" : "About" ;
                            // Procesado de la vista
                            return $this->Partial($view, $model);
                    }
                    catch(Exception $e){
                            // Procesar la información de la excepción
                            $this->Log->LogErrorTrace( "About", $e );
                            // Relanzar excepción
                            throw $e;
                    }
            }

            ///<summary>
            /// Obtiene el formulario con la política de privacidad
            ///</summary>
            public function Privacity(){
                    try{
                            // Instanciar modelo
                            $model = new HomeModel();
                            // Determinar la vista a cargar
                            $view = ($model->Username != "") ? "AuthPrivacity" : "Privacity" ;
                            // Procesado de la vista
                            return $this->Partial($view, $model);
                    }
                    catch(Exception $e){
                            // Procesar la información de la excepción
                            $this->Log->LogErrorTrace( "Privacity", $e );
                            // Relanzar excepción
                            throw $e;
                    }
            }

            ///<summary>
            /// Obtiene el formulario sobre la advertencia legal
            ///</summary>
            public function Legal(){
                    try{
                            // Instanciar modelo
                            $model = new HomeModel();
                            // Determinar la vista a cargar
                            $view = ($model->Username != "") ? "AuthLegal" : "Legal" ;
                            // Procesado de la vista
                            return $this->Partial($view, $model);
                    }
                    catch(Exception $e){
                            // Procesar la información de la excepción
                            $this->Log->LogErrorTrace( "Legal", $e);
                            // Relanzar excepción
                            throw $e;
                    }
            }

    }
