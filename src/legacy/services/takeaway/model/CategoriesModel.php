<?php

/*
 * Copyright (C) 2015 manager
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
 * Modelo para la gestión de categorías
 *
 * @author manager
 */
class CategoriesModel extends \TakeawayModel{

    /**
     * Referencia a la categoría en edición
     * @var \Category
     */
    public $Entity = NULL;

    /**
     * Colección de categorías a listar
     * @var array
     */
    public $Entities = [];

    /**
     * Mensaje de error al seleccionar categoría padre
     * @var String
     */
    public $eParent = "";

    /**
     * Estilo CSS a utilizar en el mensaje de error de la categoría padre
     * @var String
     */
    public $eParentClass = "";

    /**
     * Mensaje de error sobre el código de categoría
     * @var String
     */
    public $eCode = "";

    /**
     * Estilo CSS a utilizar en el mensaje de error del código de categoría
     * @var String
     */
    public $eCodeClass = "";

    /**
     * Mensaje de error sobre el nombre de la categoría
     * @var String
     */
    public $eName = "";

    /**
     * Estilo CSS a utilizar en el mensaje de error del nombre de categoría
     * @var String
     */
    public $eNameClass = "";

    /**
     * Mensaje de error sobre la descripción
     * @var String
     */
    public $eDesc = "";

    /**
     * Estilo CSS a utilizar en el mensaje de error de la descripción
     * @var String
     */
    public $eDescClass = "";

    /**
     * Mensaje de error sobre la descripción XML
     * @var String
     */
    public $eXml = "";

    /**
     * Estilo CSS a utilizar en el mensaje de error del xml
     * @var String
     */
    public $eXmlClass = "";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Categorías",
                "Categorías",
                "CategoriesManagement");
        $this->SetModel();
    }

    /**
     * Carga la información de todas las categorías registradas y activas
     */
    public function GetCategories(){
        // Asignar las categorías del agregado
        $this->Entities = array_filter($this->Aggregate->Categories,
                function($item){
            return $item->State == 1;
        });

        foreach($this->Entities as $item){
            $item->Desc = $this->GetCutText($item->Description, 15);
            $item->ParentName = $this->GetParentName($item->Parent);
        }
    }

    /**
     * Procedimiento para cargar la información de una categoría
     * @param int $id Identidad de la categoría a cargar
     */
    public function GetCategory($id = 0){
        if($id > 0){
            // Procedimiento para cargar la información de una categoría
            $result = $this->Management->GetCategory($id);

            if($result != 0){
                $this->TranslateResultCodes(_OP_READ_, [$result]);
            }
            else{
                $this->Entity = $this->Aggregate->Category;
            }
        }
        $this->FilterParentCategories($id);
    }

    /**
     * Procedimiento para guardar los datos de una categoría
     * @param \Category $entity Referencia a la categoría
     * @throws Exception Excepción generada cuando el resultado
     * de la capa de aplicación no es el esperado
     */
    public function Save($entity = NULL){
        // Procedimiento para almacenar la categoría
        $result = $this->Management->SetCategory($entity);

        if(is_array($result) == FALSE){
            throw new Exception("Save: SetCategory: "
                    . "Códigos de operación inválidos");
        }

        if(count($result) != 1 || $result[0] != 0){
            $this->TranslateResultCodes(_OP_CREATE_, $result);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
        $this->Entity = $entity;
        $this->FilterParentCategories();
    }

    /**
     * Procedimiento para dar de baja una categoría
     * @param int $id Identidad de la categoría
     */
    public function Delete($id = 0){
        // Procedimiento de eliminación
        $result = $this->Management->RemoveCategory($id);

        if($result != 0){
            $this->TranslateResultCodes(_OP_DELETE_, [$result]);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
        }
    }

    /**
     * @ignore
     * Establecimiento de los códigos de operación
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()
            ];
    }

    /**
     * @ignore
     * Configura los valores de las propiedades del modelo
     */
    protected function SetModel() {
        $this->Entity = new \Category();
    }

    /**
     * Carga la lista de Categorías padre
     * @param int $id Identidad de la categoria que se va a editar
     */
    private function FilterParentCategories($id = 0){
        $this->Entities = array_filter(
                $this->Aggregate->Categories, function($item) use($id){
            return $item->Parent == NULL && $item->Id != $id ;
        });
    }

    /**
     * Obtener el nombre de la categoría padre
     * @param int $id Identidad de la categoría padre
     * @return string
     */
    private function GetParentName($id = 0){
        $parent = array_filter($this->Entities,
                function($item) use ($id) {
            return $item->Id == $id;
        });

        return (is_array($parent) && count($parent) > 0)
            ? current($parent)->Name : "Raíz";
    }

    /**
     * Obtiene los mensajes de error al "leer" una categoría desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado la categoría" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" una categoría
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "La categoría no ha sido encontrada" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de una categoría en el repositorio principal
     * @return array
     */
    private function GetSaveMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido crear la categoría" ],
                -2 => ["name" => "eResult",
                    "msg" => "No se ha podido actualizar la categoría" ],
                -3 => ["name" => "eResult",
                    "msg" => "Entidad no válida" ],
                -4 => ["name" => "eCode",
                    "msg" => "El código es un campo obligatorio" ],
                -5 => ["name" => "eCode",
                    "msg" => "El código no puede tener más de 10 caracteres" ],
                -6 => ["name" => "eCode",
                    "msg" => "El código ya está registrado" ],
                -7 => ["name" => "eName",
                    "msg" => "El nombre es un campo obligatorio" ],
                -8 => ["name" => "eName",
                    "msg" => "El nombre no puede tener más de 100 caracteres" ],
                -9 => ["name" => "eName",
                    "msg" => "El nombre ya existe. Introduzca otro distinto" ],
                -10 => ["name" => "eDesc",
                    "msg" => "La descripción es un campo obligatorio" ],
                -11 => ["name" => "eDesc",
                    "msg" => "La descripción no puede tener más de 500 caracteres" ],
                -12 => ["name" => "eXml",
                    "msg" => "Debe caracterizar la categoría" ],
                -13 => ["name" => "eParent",
                    "msg" => "La categoría raíz seleccionada no es válida" ]
                ];
    }
}
