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
 * Compara que el contenido de 2 arrays de objetos es el mismo, utilizando
 * para la comparación la propiedad indicada o en su defecto la
 * propiedad "Id"
 * @param array $arrayA Referencia a la colección de objetos A
 * @param array $arrayB Referencia a la colección de objetos B
 * @param string $prop Nombre de la propiedad a utilizar en la comparación
 * @return int Código de operación:
 *  -  0: Las 2 colecciones son iguales
 *  - -1: Alguna de las 2 colecciones no es de tipo array
 *  - -2: Hay almenos un elemento en la colección A que no está en la B
 *  - -3: Hay almenos un elemento en la colección B que no está en la A
 */
function compare_objects_list($arrayA = NULL, $arrayB = NULL, $prop = "Id"){
    if(is_array($arrayA) && is_array($arrayB)){
        foreach($arrayA as $itemA){
            if(!is_object_in_array($itemA, $arrayB, $prop) == FALSE){
                return -2;
            }
        }
        foreach($arrayB as $itemB){
            if(!is_object_in_array($itemB, $arrayA, $prop) == FALSE){
                return -2;
            }
        }
        return 0;
    }
    return -1;
}

/**
 * Comprueba si existe una referencia en la colección indicada a almenos
 * un objeto con el mismo valor de la propiedad indicada
 * @param object $object Referencia al objeto a buscar
 * @param array $array Referencia a la colección
 * @param string $prop Nombre de la propiedad a comparar
 * @return boolean Resultado de la comparación
 */
function is_object_in_array($object, $array, $prop){
    $items = array_filter($array, function($item) use ($object, $prop){
        return $item->{$prop} == $object->{$prop};
    });
    return count($items) > 0;
}

/**
 * Obtiene el nodo principal del xml de definición de pruebas
 * establecida la ruta de acceso
 * @param string $path Ruta de acceso al xml de pruebas
 * @return object Nodo principal del xml de pruebas
 */
function read_test_file($path = ""){
    if(file_exists($path) == TRUE){
        $xml = simplexml_load_file($path);
        return $xml->root;
    }
    return NULL;
}

/**
 * Proceso de validación del resultado del test. Comprueba que todos
 * los códigos de error declarados han sido devueltos por el método que
 * se evalua.
 * @param array $rcodes colección de códigos devueltos por el método|función
 * @param array $codes colección de códigos esperados
 * @return boolean Resultado de la validación
 */
function validate_codes($rcodes = NULL, $codes = NULL){
    foreach($codes as $code){
        if(!in_array($code, $rcodes)){
            return FALSE;
        }
    }
    return TRUE;
}

/**
 * Obtiene el array de códigos configurados en el xml de pruebas
 * @param string $attr cadena de códigos
 * @return array
 */
function get_codes($attr = ""){
    $codes = array();
    $scodes = explode(",", $attr);
    foreach($scodes as $code){
        $a = intval($code);
        if(is_integer($a)){
            $codes[] = (int)$code;
        }
    }
    return $codes;
}

/**
 * Configuración del proyecto solicitado por url
 */
function set_project(){
    $project = 1;
    if(isset($_REQUEST["project"])){
        $p = intval($_REQUEST["project"]);
        $project = (is_numeric($p) && $p > 0) ? $p : 1;
    }
    define("_PROJECT_", $project);
}

/**
 * Validar el proyecto indicado
 */
function validate_project(){
    $br = BaseRepository::GetInstance();
    $pro = $br->Read("Project", _PROJECT_);
    return ($pro != NULL);
}

/**
 * Carga las clases de test seleccionadas en el formulario
 */
function get_selected_test(){

    require_once "test/test_baseclass.php";

    $checks = array_filter(array_keys($_REQUEST), function($item){
        strpos($item, "chk_") !== FALSE;
    });

    foreach($checks as $file){
        $path = "test/".str_replace("chk_", "", $file);
        if(file_exists($path) == TRUE){
            require_once $path;
        }
    }

    return $checks;
}

/**
 * Ejecuta los test selecionados
 * @param array $checks Array de checks de test seleccionados
 */
function run_test($config = "input/config_file.xml",
        $checks = NULL){
    if(is_array($checks) && !empty($checks)){
        foreach($checks as $item){

        }
    }
}

/**
 * Ejecución de cada test individual
 * @param int $prj Id proyecto
 * @param int $srv Id servicio
 * @param type $xml
 * @param string $test Nombre del test
 * @return array
 */
function run_selected_test($prj, $srv, $xml, $test = ""){

    $oTest = get_o_test($prj, $srv,$test);

    return ($oTest != NULL) ? $oTest->Test($xml) : [];
}

/**
 * Obtiene la clase de test
 * @param int $prj Id proyecto
 * @param int $srv Id servicio
 * @param string $test Nombre del test
 * @return \Test_Baseclass
 */
function get_o_test($prj, $srv,$test = ""){
    $oTest = NULL;
    switch ($test){
        case "chk_test_blocks_management":
            $oTest = new Test_Blocks_Management($prj, $srv);
            break;
        case "chk_test_booking_management":
            $oTest = new Test_Booking_Management($prj, $srv);
            break;
        case "chk_test_offers_management":
            $oTest = new Test_Offers_Management($prj, $srv);
            break;
        case "chk_test_places_management":
            $oTest = new Test_Places_Management($prj, $srv);
            break;
        case "chk_test_turns_management":
            $oTest = new Test_Turns_Management($prj, $srv);
            break;
    }
    return $oTest;
}
