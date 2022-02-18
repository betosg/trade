<?php
include_once("../_database/athdbconn.php");

$boolPagarReceber  = trim(request("var_pagar_receber"));
$intCodConta 	   = trim(request("var_cod_conta"));
$strTipo 		   = trim(request("var_tipo"));
$intCodigo 		   = trim(request("var_codigo"));
$intCodCentroCusto = trim(request("var_cod_centro_custo"));
$intCodPlanoConta  = trim(request("var_cod_plano_conta"));
$intCodJob         = trim(request("var_cod_job"));
$dblVlrConta       = trim(request("var_vlr_conta"));
$strTipoDocumento  = trim(request("var_tipo_documento"));
//$strNumDocumento   = trim(request("var_num_documento"));
$dateDtEmissao     = trim(request("var_dt_emissao"));
$dateDtVctoOrig    = trim(request("var_dt_vcto"));
$intAnoVcto        = trim(request("var_ano_vcto"));
$strHistorico      = trim(request("var_historico"));
$strObs            = trim(request("var_obs"));
$intParcelas       = trim(request("var_parcelas"));
$strFrequencia     = trim(request("var_frequencia"));
$intCodCFGBoleto   = trim(request("var_cod_cfg_boleto"));
$arquivo		   = trim(request("dbvar_str_arquivo_1"));

$strButtonAction   = trim(request("var_button_action"));

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

if($boolPagarReceber != "" && $intCodConta != "" && $strTipo != "" && $intCodigo != "" && $intCodCentroCusto != "" && $intCodPlanoConta != "" && $dblVlrConta != "" && $dateDtEmissao != "" && $dateDtVctoOrig != "" && $strHistorico != "" && (($intParcelas == "" && $strFrequencia == "") || ($intParcelas != "" && $strFrequencia != ""))){
	$objConn = abreDBConn(CFG_DB);
	
	$dblVlrConta = str_replace(",",".",str_replace(".","",$dblVlrConta));
	$strCodigoGrupo = ($intParcelas != "") ? "'".gerarSenha(5, 7)."'" : "NULL";
	if ($intCodCFGBoleto == "") $intCodCFGBoleto = "NULL";
	if ($intCodJob == "") $intCodJob = "NULL";
	if ($intParcelas == "") $intParcelas = "NULL";
	if ($intAnoVcto == "") $intAnoVcto = "NULL";
	
	try{
		$strSQL = " SELECT * FROM sp_gera_titulo (".$boolPagarReceber.",".$intCodigo.",'".$strTipo."',".$intCodConta.",".$intCodPlanoConta.",".$intCodCentroCusto.",".$intCodJob.",".$intCodCFGBoleto.",".$dblVlrConta.",'".$strTipoDocumento."','".$dateDtEmissao."','".$dateDtVctoOrig."',".$intAnoVcto.",'".$strHistorico."','".$strObs."',".$intParcelas.",'".$strFrequencia."',".$strCodigoGrupo.",'".getsession(CFG_SYSTEM_NAME."_id_usuario")."','".$arquivo."') ";
		$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

    //ajustado o redirect de acordo com a operacao... OK= "../fontes/data.php?...   - By Vini 16.10.2012
    if(strpos(getsession($strSesPfx . "_grid_default"),"?") === false) 
      $strAuxGridDefault = "../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo"); 
    else 
      $strAuxGridDefault = "../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo");	

	
	$strPagarReceber = ($boolPagarReceber == "true") ? "pagar_para" : "receber_de";
	$strLocation 	 = ($strButtonAction  == "ok") ? $strAuxGridDefault : "STinspagarreceber.php?var_oper=" . $strPagarReceber;
	
	redirect($strLocation);
}
else{
	mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
	die();
}
?>