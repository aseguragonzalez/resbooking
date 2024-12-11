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


error_reporting(E_ALL);

ini_set('display_errors', 1);

require_once "libs/debug.resbooking.lib.php";

// Establecer localización para las fechas
setlocale(LC_ALL,"es_ES.UTF-8");
// Establecer el formato de fecha-hora
set_time();
// Establecer el modo depuración
set_debug(false);

// array datos
$data = [];
// variable fecha
$d = new \DateTime( "NOW" );
// Obtener fecha de la solicitud actual
$sDate = filter_input(INPUT_GET, "date");
// Establecer fecha del log
$date = !empty($sDate) ? $sDate : $d->format( "Ynj" );

$path = "./logs/data-$date.log";
$contenido = FALSE;
if(file_exists($path) == TRUE){
    // Obtener contenido del log
    $contenido = file_get_contents($path);
}

// Comprobar que está vacío
if($contenido != FALSE){
    $contenido = substr ($contenido , 0, (strlen($contenido) - 2) );
    $contenido = "[".$contenido."]";
    $data = json_decode($contenido);
    $rData = array_reverse($data);
}
else{
    $rData = [];
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema de notificaciones</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
        <link rel="stylesheet" href="css/bootstrap-theme.css" media="screen"/>

    </head>
    <body>
        <div class="container">
            <div class="page-header">
                <h1>Sistema de notificaciones <small>Registro de trazas</small></h1>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nivel</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rData as $item): ?>
                        <tr>
                            <td><?php echo $item->date; ?></td>
                            <td><?php echo $item->level; ?></td>
                            <td><?php echo $item->details; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
