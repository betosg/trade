<?php
include_once("../_database/athdbconn.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

$intCodDado			= trim(request("var_chavereg"));
$intCodConta		= trim(request("var_cod_conta"));
$strTipo			= trim(request("var_tipo"));
$intCodigo			= trim(request("var_codigo"));
$intCodCentroCusto	= trim(request("var_cod_centro_custo"));
$intCodPlanoConta	= trim(request("var_cod_plano_conta"));
$dblVlrConta		= trim(request("var_vlr_conta"));
$strTipoDocumento	= trim(request("var_tipo_documento"));
$dateDtEmissao		= trim(cDate(CFG_LANG,request("var_dt_emissao"),false));
$dateDtVctoOrig		= trim(cDate(CFG_LANG,request("var_dt_vcto"),false));
$strHistorico		= trim(request("var_historico"));
$strObs				= trim(request("var_obs"));
$intCodCFGBoleto	= trim(request("var_cod_cfg_boleto"));

$strExibirAr		= request("var_exibir_ar");
if ($strExibirAr == ""){$strExibirAr = "false";}

$strButtonAction	= trim(request("var_button_action"));

/* -----------------------------
	ALTERA DADOS
----------------------------- */
if($intCodDado != "" && $intCodConta != "" && $strTipo != "" && $intCodigo != "" && $intCodCentroCusto != "" && $intCodPlanoConta != "" && $strTipoDocumento != "" && $dateDtEmissao != "" && $dateDtVctoOrig != "" && $strHistorico != ""){
	$objConn = abreDBConn(CFG_DB);
	
	if ($dblVlrConta != "") $dblVlrConta = str_replace(",",".",str_replace(".","",$dblVlrConta)); //formatcurrency($dblVlrConta,2);
	if ($intCodCFGBoleto == "") $intCodCFGBoleto = "NULL";
	
	try{
	$strSQL = " UPDATE fin_conta_pagar_receber
				SET  cod_conta = " . $intCodConta . "
				   , tipo = '" . $strTipo . "'
				   , codigo = " . $intCodigo . "
				   , cod_centro_custo = " . $intCodCentroCusto . "
				   , cod_plano_conta = " . $intCodPlanoConta . "
				   , exibir_ar = ".$strExibirAr;
	
	if ($dblVlrConta != "") $strSQL .= ", vlr_conta = " . $dblVlrConta;
	if ($dblVlrConta != "") $strSQL .= ", vlr_saldo = " . $dblVlrConta;
	
	$strSQL .= "   , tipo_documento = '" . $strTipoDocumento . "'
				   , dt_emissao = '" . $dateDtEmissao . "'
				   , dt_vcto = " . ((!$dateDtVctoOrig) ? 'NULL' : "'" . $dateDtVctoOrig . "'") . " 
				   , historico = '" . $strHistorico . "'
				   , obs = '" . $strObs . "'
				   , cod_cfg_boleto = " . $intCodCFGBoleto . "
				   , sys_dtt_upd = CURRENT_TIMESTAMP
				   , sys_usr_upd = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'
				WHERE cod_conta_pagar_receber = " . $intCodDado;
	$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if(strpos(getsession($strSesPfx . "_grid_default"),"?") === false) 
	$strAuxGridDefault = "../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo"); 
  else 
	$strAuxGridDefault = "../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo");	
  //$strAuxGridDefault = 
	  $strLocation = ($strButtonAction  == "aplicar") ? "STupdpagarreceber.php?var_chavereg=" . $intCodDado : $strAuxGridDefault;


	//$strLocation = ($strButtonAction  == "aplicar") ? "STupdpagarreceber.php?var_chavereg=" . $intCodDado : "data.php";
	
	redirect($strLocation);
}else{
	mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
	die();
}
?>