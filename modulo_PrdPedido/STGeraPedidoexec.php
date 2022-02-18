<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

/*** RECEBE PARAMETROS ***/
$strOpcao = request("var_opcao");
$intCodPJ = request("var_cod_pj");

$strSocioOpcaoC = request("var_socio_opcao_c");
$strCategoriaOpcaoC = request("var_categoria_opcao_c");
$strPorteOpcaoC = request("var_porte_opcao_c");
$strSegmentoOpcaoC = request("var_segmento_opcao_c");
$strCategoriaExtraOpcaoC = request("var_categoria_extra_opcao_c");
$strDtFundacaoOpcaoC = request("var_dt_fundacao_opcao_c");

$strSocioOpcaoD = request("var_socio_opcao_d");
$strCategoriaOpcaoD = request("var_categoria_opcao_d");
$strPorteOpcaoD = request("var_porte_opcao_d");
$strSegmentoOpcaoD = request("var_segmento_opcao_d");
$strCategoriaExtraOpcaoD = request("var_categoria_extra_opcao_d");
$strDtFundacaoOpcaoD = request("var_dt_fundacao_opcao_d");

$strTipoIndice = request("var_tipo_indice");

$intCodProduto = request("var_cod_produto");
$dblValor = request("var_valor");
$dtVcto = request("var_dt_vcto");
$intCodConta = request("var_cod_conta");
$intCodPlanoConta = request("var_cod_plano_conta");
$intCodCentroCusto = request("var_cod_centro_custo");
$intCodJob = request("var_cod_job");
$intCodCFGBoleto = request("var_cod_cfg_boleto");
$strTipoDocumento = request("var_tipo_documento");
$strHistorico = request("var_historico");
$strObsPedido = request("var_obs_pedido");
$strObsTitulo = request("var_obs_titulo");
$intParcelas = request("var_parcelas");
$strFrequencia = request("var_frequencia");
$strGerar = request("var_gerar");

$usarIndice						 = getVarEntidade($objConn,"guia_usar_indice");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/

$strMsg = "";

if ($strOpcao == "varias_empresas") {
	$strSocio = $strSocioOpcaoC;
	$strCategoria = $strCategoriaOpcaoC;
	$strPorte = $strPorteOpcaoC;
	$strSegmento = $strSegmentoOpcaoC;
	$strCategoriaExtra = $strCategoriaExtraOpcaoC;
	$strDtFundacao = $strDtFundacaoOpcaoC;
} 
if ($strOpcao == "nao_possuem") {
	$strSocio = $strSocioOpcaoD;
	$strCategoria = $strCategoriaOpcaoD;
	$strPorte = $strPorteOpcaoD;
	$strSegmento = $strSegmentoOpcaoD;
	$strCategoriaExtra = $strCategoriaExtraOpcaoD;
	$strDtFundacao = $strDtFundacaoOpcaoD;
} 

if($strOpcao == "") $strMsg .= "Selecionar para quem será gerado pedido<br>";
if(($strOpcao == "uma_empresa") && ($intCodPJ == "")) $strMsg .= "Informar empresa<br>";
if(($strOpcao == "varias_empresas") || ($strOpcao == "nao_possuem")) {
	if (($strSocio == "") && ($strCategoria == "") && ($strPorte == "") && ($strSegmento == "") && ($strCategoriaExtra == "")) $strMsg .= "Selecione um dos combos<br>";
}
if($intCodProduto == "") $strMsg .= "Informar produto<br>";
if ($strGerar == "pedido_e_titulo") {
	if($dtVcto == "") $strMsg .= "Informar data de vencimento<br>";
	if($intCodConta == "") $strMsg .= "Informar conta banco<br>";
	if($intCodPlanoConta == "") $strMsg .= "Informar plano de conta<br>";
	if($intCodCentroCusto == "") $strMsg .= "Informar centro de custo<br>";
	if($strTipoDocumento == "") $strMsg .= "Informar Tipo de Documento<br>";
	if($strHistorico == "") $strMsg .= "Informar histórico<br>";
}
if ((($intParcelas != "") && ($strFrequencia == "")) || (($intParcelas == "") && ($strFrequencia != ""))) {
	$strMsg .= "Informar parcelas e frequência<br>";
}

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

$dtVcto = cDate(CFG_LANG, $dtVcto, false);

if ($dblValor == "") $dblValor = "0";
$dblValor = str_replace(".","",$dblValor);
$dblValor = str_replace(",",".",$dblValor);		

//----------------------------------------------------------------------------------------------
//Chama a PROC que gera os pedidos (e mais os títulos). Gera um pedido ou um lote de pedidos.
//----------------------------------------------------------------------------------------------
if ($intCodPJ == "") $intCodPJ = "NULL";
if ($intCodConta == "") $intCodConta = "NULL";
if ($intCodPlanoConta == "") $intCodPlanoConta = "NULL";
if ($intCodCentroCusto == "") $intCodCentroCusto = "NULL";
if ($intCodJob == "") $intCodJob = "NULL";
if ($intCodCFGBoleto == "") $intCodCFGBoleto = "NULL";
if ($intParcelas == "") $intParcelas = "NULL";

$objConn->beginTransaction();
try{
	if($usarIndice != "usar_indice"){
		if ($strOpcao == "uma_empresa") 
		$strSQL = " SELECT * FROM sp_gera_pedido('".$strGerar."', ".$intCodPJ.", NULL, ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", '".$strTipoDocumento."', '".$strHistorico."', '".$strObsPedido."', '".$strObsTitulo."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		else 
		$strSQL = " SELECT * FROM sp_gera_pedido_em_lote('".$strOpcao."', '".$strGerar."', '".$strSocio."', '".$strCategoria."', '".$strCategoriaExtra."', '".$strPorte."', '".$strSegmento."', '".$strDtFundacao."', ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", '".$strTipoDocumento."', '".$strHistorico."', '".$strObsPedido."', '".$strObsTitulo."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		//$objConn->query($strSQL);
		//$objConn->commit();
	}else{
echo $strOpcao . "<br>";
		if ($strOpcao == "uma_empresa") 
		$strSQL = " SELECT * FROM sp_gera_pedido_indice('".$strGerar."', ".$intCodPJ.", NULL, ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", '".$strTipoDocumento."', '".$strHistorico."', '".$strObsPedido."', '".$strObsTitulo."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."','".$strTipoIndice."') ";
		else 
		$strSQL = " SELECT * FROM sp_gera_pedido_em_lote_indice('".$strOpcao."', '".$strGerar."', '".$strSocio."', '".$strCategoria."', '".$strCategoriaExtra."', '".$strPorte."', '".$strSegmento."', '".$strDtFundacao."', ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", '".$strTipoDocumento."', '".$strHistorico."', '".$strObsPedido."', '".$strObsTitulo."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."','".$strTipoIndice."') ";		
	}
	//die($strSQL);
	$objConn->query($strSQL);
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"STGeraPedido.php","erro",1);
	$objConn->rollBack();
	die();
}

$objConn = NULL;
redirect("index.php");
?>