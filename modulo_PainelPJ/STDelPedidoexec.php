<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

/*** RECEBE PARAMETROS ***/
$intCodPedido = request("var_chavereg");
$strObs = request("var_obs_delecao");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = "";
if($intCodPedido == "") $strMsg .= "Selecionar pedido<br>";
if($strObs == "") $strMsg .= "Informar observação/motivo<br>";

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

/*** ABRE TRANSAÇÃO PARA DELETAR PEDIDO, GERAR LOG E DEPOIS COMPLEMENTAR COM OBS DO USUÁRIO ***/
$objConn->beginTransaction();
try{
	$strSQL = " DELETE FROM prd_pedido WHERE cod_pedido = ".$intCodPedido;
	$objConn->query($strSQL);
	
	$strSQL = " SELECT cod_pedido_deletado FROM prd_pedido_deletado WHERE cod_pedido = ".$intCodPedido;
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
	$intCodPedidoDeletado = getValue($objRS,"cod_pedido_deletado");
	$objResult->closeCursor();
	
	if ($intCodPedidoDeletado != "") {
		$strSQL = " UPDATE prd_pedido_deletado 
					SET obs_delecao = '".$strObs."' 
					  , sys_usr_ins = '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."'
					WHERE cod_pedido_deletado = ".$intCodPedidoDeletado;
		$objConn->query($strSQL);
	}
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}
$objConn = NULL;
redirect("STindex.php");
?>