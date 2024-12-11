<?php

	$filename = "sitemap.xml";

	$url = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$pos = strpos($url, $filename);

	if($pos === FALSE || file_exists($filename) === FALSE ){
            header( "HTTP/1.0 404 Not Found" );
            print file_get_contents( "view/shared/_notfound.html" );
            exit();
	}
	else{
		header( "Content-disposition: attachment; filename=".$filename );
		header( "Content-type: text/xml" );
		readfile($filename);
	}

?>
