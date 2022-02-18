<?php 
include_once("../_database/athdbconn.php");

$strCodigos = request("var_chavereg");
$strAcao    = request("var_acao");
$strPasta   = request("var_pasta");
$strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));

$objConn = abreDBConn(CFG_DB);

try{
	$boolAction = ($strAcao == "lido") ? "true" : "false";
	
	$strSQL = " UPDATE msg_mensagem SET
				 lido = " . $boolAction . "
				WHERE cod_mensagem IN (" . $strCodigos . ")";
	$objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

echo("
<script>
	parent." . CFG_SYSTEM_NAME . "_left.location.reload();
	location.href = \"" . getsession($strSesPfx . "_grid_default") . "?var_pasta=" . $strPasta . "\"
</script>
	");
?>