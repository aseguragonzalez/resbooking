<?php



error_reporting(E_ALL);

ini_set('display_errors', 1);

require_once "libs/debug.resbooking.lib.php";
require_once "libs/ConfigurationService.php";
require_once "libs/BookingNotificationEngineDTO.php";
require_once "libs/Notification.php";
require_once "libs/NotificationAlias.php";
require_once "libs/RemindersEngine.php";

// Modo de ejecución
$debug = !TRUE;
// Establecer el modo depuración
set_debug($debug);
// Establecer localización para las fechas
setlocale(LC_ALL,"es_ES.UTF-8");

// Establecer el formato de fecha-hora
set_time();

// Obtener inyector de dependencias
$injector = Injector::GetInstance();
// Obtener instancia para el log
$log = $injector->Resolve("ILogManager");
// Obtener instancia del DAO
$dao = $injector->Resolve("IDataAccessObject");
// Obtener nombre de la cadena de conexión
$connectionString = ConfigurationManager::GetKey( "connectionString" );
// Obtener parámetros de conexión
$oConnString = ConfigurationManager::GetConnectionStr($connectionString);
// Configurar el objeto de conexión a datos
$dao->Configure($oConnString);
// Obtener el Id del tipo recordatorio
$recordatorio = ConfigurationManager::GetKey( "tipo-recordatorio" );
// Obtener instancia del motor
$engine = \RemindersEngine::GetInstance($dao, $log, $recordatorio);
// Registrar notificaciones
$count = $engine->SendReminders();
// Log de registro
$log->LogInfo("Se han registrado $count recordatorios");

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Resbooking</title>
    </head>
    <body>
    </body>
</html>
