<?php
	header("Content-Type:text/html; charset=iso-8859-1");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");

	include_once("../_database/athdbconn.php");

	$objConn = abreDBConn(CFG_DB);

	$intCodPF = request("var_cod_pf");

	$strSQL = " SELECT dt_emissao FROM prd_ped_credencial WHERE cod_pf = '" . $intCodPF . "'";
	$objResult = $objConn->query($strSQL);

	$objRS = $objResult->fetch();
	echo(getValue($objRS,"dt_emissao"));
?>