<?php

	/*
		script de arranque ejecuciones con cron
	*/

	// Cargar scripts básicos
	require_once("../../references/scripts/trycatch.common.php");
	require_once("../../references/scripts/trycatch.io.functions.php");
	// Cargar el gestor de configuraciones
	require_once("../../references/componentes/General/ConfigurationManager.php");
	// Establecer el formato de fecha-hora
	set_time();
	// Establecer los manejadores de errores
	set_handlers();
	// Establecer el modo depuración
	set_debug(false);
	// Obtener las dependencias a cargar
	$references = ConfigurationManager::GetReferences();
	// Cargar las dependencias y scripts
	load_references($references);
	// cargar sistema de notificaciones
	require_once( "notification-engine.php" );
?>
