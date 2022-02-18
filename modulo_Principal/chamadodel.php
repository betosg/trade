<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$intCodResposta = request("var_chavereg");
$intCodTodolist = request("var_cod_todolist");
$strOper 		= request("var_oper");

try{
	$objConn->query(" DELETE FROM tl_resposta WHERE cod_resposta = " . $intCodResposta);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

redirect("chamadoview.php?var_oper=" . $strOper . "&var_chavereg=" . $intCodTodolist);
?>