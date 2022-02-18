<?php

	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	$strArquivo 	= request("arquivo");
	$strNomeArquivo = request("nome");
	
	header("Content-Type: application/save");
	header("Content-Length:".filesize($strArquivo));
	header('Content-Disposition: attachment; filename="' . $strNomeArquivo . '"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Pragma: no-cache'); 
	
	$fp = fopen("$strArquivo", "r");
	fpassthru($fp);
	fclose($fp);
?>