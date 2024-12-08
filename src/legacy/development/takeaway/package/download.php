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

// Establecer el nivel de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Definir constantes para la ejecución
define("_PATH_", "output/");
define("_HASH_", "md5");
define("_CONTENTTYPE_", "application/data");
// Cargar las dependencias
require_once "download_functions.php";
// Obtener el nombre del paquete
$package_name = filter_input(INPUT_POST, "name");
// Obtener la configuración
$isDebug = filter_input(INPUT_POST, "debug") != FALSE;
// Establece si es modo min
$isMin = filter_input(INPUT_POST, "min") != FALSE;
// Procesar el nombre del paquete
$paquete = processPackageName($package_name, $isDebug, $isMin);
// Obtener el contenido
$content = readFiles("config.xml");
// Generar el paquete
if(!$isDebug){
    $newContent = "<?php ".clearContents($content, $isMin)." ?>";
}
else{
    $newContent = "<?php $content ?>";
}
// Guardar fichero
saveFile($paquete, $newContent);
// Retornar fichero
putFile($paquete, _CONTENTTYPE_, $newContent);
