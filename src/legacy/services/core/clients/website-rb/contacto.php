<?php

	try{

		$data = array();
		foreach($_POST as $key => $value)
			$data[$key] = strip_tags($value);

		if( isset($data[ "from" ]) && $data[ "from" ] != ""
			&& isset($data[ "subject" ]) && $data[ "subject" ] != ""
				&& isset($data[ "text" ]) && $data[ "text" ] != "" ){
			$content = "";
			foreach($data as $key => $value)
				$content .= "<p><strong>$key</strong>: $value</p>";
			$content = "<div>$content</div>";
			// Asignar e-mail de contacto
			$email = "info@".$_SERVER['SERVER_NAME'];
			// Cabecera de tipo de contenidos
			$contentType = "Content-type: text/html; charset=iso-8859-1\r\n";
			// Construir la cabecera del mensaje
			$headers = str_replace( "{FROM}", $email, "From: {FROM}\r\n " );
			// Realizar envío
			mail($email, $email, $content, $contentType.$headers);
		}
	}
	catch(Exception $e){
		$url = "#error";
	}

	echo "<script type='text/javascript'>window.location='./'</script>";

?>
