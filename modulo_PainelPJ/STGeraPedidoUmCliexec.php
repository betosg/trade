<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

/*** RECEBE PARAMETROS ***/

$intCodPJ = request("var_cod_pj");
$strTipo = request("var_tipo_produto");
$intCodProduto = request("var_cod_produto");
$strObs = request("var_obs");
$strArquivo = request("var_arquivo");
$strRetorno = request("var_retorno");
$intQtdeParc = request("var_qtde_parc");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = '';

if($intCodPJ == "") $strMsg .= "Informar empresa<br>";
if($intCodProduto == "") $strMsg .= "Informar produto<br>";

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

if (($intQtdeParc == "") || ($intQtdeParc == 0)) {
  $intQtdeParc = 1;
}

$objConn->beginTransaction();
try{
	$strSQL = " SELECT rotulo, msg_cobranca, tipo, dt_ini_val_produto, dt_fim_val_produto, valor
				FROM prd_produto
				WHERE cod_produto = ".$intCodProduto;
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
	
	$strProd_Rotulo = getValue($objRS,"rotulo");
	$strProd_MsgCobranca = getValue($objRS,"msg_cobranca");
	$strProd_Tipo = getValue($objRS,"tipo");
	$strProd_DtIni = getValue($objRS,"dt_ini_val_produto");
	$strProd_DtFim = getValue($objRS,"dt_fim_val_produto");
	$strProd_Valor = getValue($objRS,"valor");
	
	if ($strProd_Valor == "") $strProd_Valor = "0";
	if ($strProd_DtIni != "") 
		$strProd_DtIni = "'".$strProd_DtIni."'";
	else 
		$strProd_DtIni = "NULL";
	if ($strProd_DtFim != "") 
		$strProd_DtFim = "'".$strProd_DtFim."'";
	else 
		$strProd_DtFim = "NULL";
	
	$strSQL = " INSERT INTO prd_pedido(cod_pj, situacao, obs, valor, qtde_parc, it_cod_produto, it_descricao, it_valor, it_obs, it_tipo, it_dt_ini_val_produto, it_dt_fim_val_produto, it_arquivo, sys_dtt_ins, sys_usr_ins) 
				VALUES (".$intCodPJ.", 'aberto', '".$strObs."', ".$strProd_Valor.", ".$intQtdeParc.", ".$intCodProduto.", '".$strProd_Rotulo."', ".$strProd_Valor.", NULL, '".$strProd_Tipo."', ".$strProd_DtIni.", ".$strProd_DtFim.", '".$strArquivo."', CURRENT_TIMESTAMP, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
	$objConn->query($strSQL);
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"STGeraPedidoUmCli.php","erro",1);
	$objConn->rollBack();
	die();
}

$objConn = NULL;
redirect($strRetorno."?var_tipo=".$strTipo);
?>