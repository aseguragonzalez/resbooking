<?php

	function filtrarNotificaciones($array = null){
		// filtrar parámetro
		if($array == null || !is_array($array)) return array();
		// Array de notificaciones a enviar
		$dtos = array();

		foreach($array as $dto){
			if((!isset($dto->_To) || $dto->_To == "" )
					&& (!isset($dto->confTo) || $dto->confTo == "" ))
				continue;

			$dtos[] = $dto;
		}

		// retornar dtos filtrados
		return $dtos;
	}

	function procesarContenido($html = "", $json = ""){
		if($json == "" ) return $html;
		$json = json_decode($json);
		settype( $json, "array");
		foreach($json as $key => $value)
			$html = str_replace( "{".$key."}", trim($value), $html);
		return $html;
	}

	function enviarMail($dto = null){
		if($dto == null) return;
		// cabecera
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$dto->_From."\r\n";
		// Obtener contenido del e-mail
		$html = procesarContenido(base64_decode($dto->confTemplate), $dto->Content);
		// Establecer destinatario
		if($dto->_To != ""){
			$to = $dto->_To;
		}
		else{
			$to = "";
			$headers .= "Bcc: $dto->confTo\r\n";
		}
//	$to = ($dto->_To != "") ? $dto->_To : $dto->confTo;
		// Establecer asunto (en texto plano)
 		$subject = $dto->confSubjectText;
		// Realizar envío
		mail($to, $subject, $html, $headers);
	}

	function actualizarEntidad($id = 0, $dao = null){
		if( $dao == null) return;
		// Actualizar el estado de envío
		$ent = $dao->Read( $id, "Notification" );
		$ent->Dispatched += 1;
		$ent->_To = $ent->To;
		$ent->_Subject = $ent->Subject;
                if( $ent->_Subject=="create-user"
                        || $ent->_Subject == "reset-password" ){
                    $ent->Content = "";
                }
		$dao->Update($ent);
	}

	try{
		// Obtener inyector de dependencias
		$injector = Injector::GetInstance();
		// Obtener refenrencia al gestor de trazas
		$log = $injector->Resolve( "ILogManager" );
		// Obtener referencia al dao
		$dao = $injector->Resolve( "IDataAccessObject" );
		// Obtener nombre de la cadena de conexión
		$connectionString = ConfigurationManager::GetKey( "connectionString" );
		// Obtener parámetros de conexión
		$oConnString = ConfigurationManager::GetConnectionStr($connectionString);
		// Configurar el objeto de conexión a datos
		$dao->Configure($oConnString);
		// Puesta a 0 del contador de envíos
		$contador = 0;
		// Log del inicio
		$date = new DateTime( "NOW" );
		$msg = "Se inicia el proceso de notificaciones. Hora:".$date->format('Y-m-d H:i:s');
		$log->LogInfo( $msg );
		// Obtener todas las notificaciones no enviadas
		$entities = $dao->GetByFilter( "NotificationDTO"	, array( "Dispatched" => 0 ));
		// filtrar notificaciones
		$entities = filtrarNotificaciones($entities);
		// Procesar los mensajes
		foreach($entities as $item){
			enviarMail($item);
			// Actualizar estado
			actualizarEntidad($item->Id, $dao);
			// Actualizar contador
			$contador++;
		}
		// log del final
		$date = new DateTime( "NOW" );
		$msg = "Se finaliza el proceso de notificaciones. Notificaciones enviadas: ".$contador.". Hora:".$date->format('Y-m-d H:i:s');
		$log->LogInfo( $msg );

	}
	catch(Exception $e){
		$log->LogError($e->getMessage());
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Sistema de notificaciones</title>
		<link rel="stylesheet" href="/notifications/css/bootstrap.min.css" media="screen"/>
		<link rel="stylesheet" href="/notifications/css/bootstrap-theme.css" media="screen"/>
		<script type="text/javascript" src="/notifications/js/bootstrap.min.js" ></script>
	</head>
	<body>
	<div class="container">
		<h1>Sistema de notificaciones</h1>
		<h3>Resumen de la ejecución</h3>
		<p>
			<?php echo $msg; ?>
		</p>
	</div>
	</body>
</html>
