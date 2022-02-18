<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$intCodPF    = request("var_pf");
$strRelacao  = request("var_relacao");

$objConn = abreDBConn(CFG_DB);

$strSQL = " SELECT
				  pj.cod_pj,
				  pj.razao_social
			 FROM 
				  cad_pj AS pj
		LEFT JOIN cad_pf_pj AS pfpj ON pfpj.cod_pj = pj.cod_pj
			WHERE pfpj.cod_pf = " . $intCodPF . "
			  AND pfpj.relacao = '" . $strRelacao . "'
			  AND pfpj.categoria IS NULL ";
$objResult = $objConn->query($strSQL);

foreach($objResult as $objRS){
	echo(getValue($objRS,"cod_pj") . ";" . getValue($objRS,"razao_social") );
}

$objResult->closeCursor();
?>