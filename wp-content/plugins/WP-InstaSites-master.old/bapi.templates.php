
<?php
	header('Content-Type: application/javascript');	
	header('Cache-Control: public');
	//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		
	$path = realpath('bapi/bapi.ui.mustache.tmpl');
	$c = file_get_contents($path);
	$j2 = rawurlencode($c); //addslashes($c);	
	echo "	var t = '" . $j2 . "'\r\n";	
	echo "	t = decodeURIComponent(t);\r\n";
	echo "BAPI.templates.set(t);\r\n";	
?>