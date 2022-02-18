<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

try{
	$strSQL = " DELETE FROM fin_lcto_ordinario WHERE cod_lcto_ordinario = " . request("var_cod_lcto_ordinario") ;
	$objConn->query($strSQL);
}
catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
redirect("STifrlancamentoABFM.php?var_chavereg=" . request("var_chavereg"));
?>