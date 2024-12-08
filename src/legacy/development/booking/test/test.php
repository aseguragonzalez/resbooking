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
// Definir constantes
define("DEBUG", FALSE);
define("_PATH_", "output/");
define("_HASH_", "md5");
define("_CONTENTTYPE_", "application/data");
// Cargar dependencias
require_once "lib/debug.resbooking.lib.php";
require_once "lib/debug.resbooking.common.php";
require_once "lib/upload_functions.php";
require_once "lib/ReaderXMLTest.php";
require_once "lib/IBookingManagementTest.php";

$project = 12; $service = 3;

// Subir paquete
upload_file("lib", "php");
// Subir fichero de configuración
upload_file("test", "xml");
// cargar el paquete
require_once "input/lib.php";
// Abrir fichero de pruebas
$xml = simplexml_load_file("input/test.xml");
$node = $xml->bookingmanagement;
$attr = $node->attributes();
// Obtener instancia de la clase de test
$test = IBookingManagementTest::
        GetInstance((string)$attr->name, $project, $service);
// Ejecutar test
$test->RunTest($node);

$error = $test->GetError();

$info = $test->Test;

?>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Test Unitarios</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"
              rel="stylesheet" type="text/css" />
        <script src="http://code.jquery.com/jquery-1.11.3.min.js"
                type="text/javascript" ></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"
                type="text/javascript" ></script>
    </head>
    <body>
        <div class="container">
             <div class="page-header">
                <h1>Test Unitarios</h1>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <strong>Resultados</strong>
                    <?php foreach($info as $i): ?>
                    <div class="<?php echo ($i["Resultado"]) ? "has-success" : "has-error"; ?>">
                        <p class="help-block">
                            <?php echo $i["Referencia"] . ": "?>
                            <?php if($i["Resultado"]) : ?>
                            <span class="glyphicon glyphicon-ok" ></span>
                            <?php else: ?>
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php endif; ?>
                            <?php echo $i["Mensaje"] ?>
                        </p>
                    </div>
                    <?php endforeach;?>
                </div>
                <div class="col-lg-6">
                    <strong>Detalles de la prueba</strong><br />
                    <?php foreach($error as $e): ?>
                    Referencia: <?php echo $e["Referencia"] ?> =>
                    <?php echo $e["Mensaje"] ?> <br/>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </body>
</html>
