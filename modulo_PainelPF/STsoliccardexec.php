<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

/*** RECEBE PARAMETROS ***/
$strOpcao 			= request("var_opcao");
$strGerar 			= request("var_gerar");
$intCodPJ 			= request("var_cod_pj");
$intCodPF 			= request("var_cod_pf");
$intCodProduto  	= request("var_cod_produto");
$dblValor 			= request("var_valor");
$strObs  			= request("var_obs");
$dtVcto 			= request("var_data_vcto");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = '';

if($strOpcao == "") $strMsg .= "Selecionar para quem será gerado pedido<br>";
if(($intCodPJ == "") && ($strOpcao == "uma_empresa")) $strMsg .= "Informar empresa<br>";
if($intCodProduto == "") $strMsg .= "Informar produto<br>";
if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

$dtVcto = date("d/m/Y");

if ($dblValor == "") $dblValor = "0";
$dblValor = str_replace(".","",$dblValor);
$dblValor = str_replace(",",".",$dblValor);		

//-------------------------------------------------------------------------
//Chama a PROC que gera as contribuições (uma apenas ou em lote)
//-------------------------------------------------------------------------

$intCodConta       = "NULL"; //getVarEntidade($objConn, "pedido_cod_conta_banco_padrao");
$intCodPlanoConta  = "NULL"; //getVarEntidade($objConn, "pedido_cod_plano_conta_padrao");
$intCodCentroCusto = "NULL"; //getVarEntidade($objConn, "pedido_cod_centro_custo_padrao");
$intCodCFGBoleto   = "NULL"; //getVarEntidade($objConn, "cod_cfg_boleto_padrao");
$intCodJob         = "NULL"; //getVarEntidade($objConn, "fin_cod_job");

$strHistorico = "";
$intParcelas = "NULL";
$strFrequencia = "";

$objConn->beginTransaction();
try{
	$strSQL = " SELECT * FROM sp_gera_pedido('".$strGerar."', ".$intCodPJ.", ".$intCodPF.", ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", 'BOLETO', '".$strHistorico."', '".$strObs."', '".$strObs."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
	$objConn->query($strSQL);
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}

$objConn = NULL;
redirect("STColabAtivos.php");
?>