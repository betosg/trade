<?php
include_once("../_database/athdbconn.php");

error_reporting(E_ALL);

$intCodDado = request("var_chavereg");
$strAction  = request("var_action");
$strValor   = request("var_valor");

$arrArquivo = file("../_database/STconfiginc.php");
$resArquivo = fopen("../_database/STconfiginc.php","wb");

$intI = 1;
foreach($arrArquivo as $strLine) {
	if($intI != $intCodDado) {
		fwrite($resArquivo,$strLine);
	}
	$intI++;
}

fclose($resArquivo);

header("Location:" . $strAction);
?>