<?php
include_once("../_database/athdbconn.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//???

$objConn = abreDBConn(CFG_DB);

$intCodNFEntrada   = request("var_cod_nf_entrada");
$intCodPJFornec    = request("var_cod_pj_fornec");
$dblVlrConta       = request("var_vlr_conta");
$intCodConta 	   = request("var_cod_conta");
$intCodPlanoConta  = request("var_cod_plano_conta");
$intCodCentroCusto = request("var_cod_centro_custo");
$intCodJob         = request("var_cod_job");
$intCodCFGBoleto   = request("var_cod_cfg_boleto");
$dateDtEmissao     = request("var_dt_emissao");
$strHistorico      = request("var_historico");
$strObs            = request("var_obs");

$strOpcaoVariosTitulos = request("var_varios_titulos");
$dateDtVcto            = request("var_dt_vcto");
$dateDtPrimVcto        = request("var_dt_prim_vcto");
$intParcelas           = request("var_parcelas");
$strFrequencia         = request("var_frequencia");
$dateDtBaseVcto        = request("var_dt_base_vcto");
$strPrzVctos           = request("var_prz_vctos");
$strVlrVctos           = request("var_vlr_vctos");

$strOpcaoQuitacao          = request("var_opcoes_quitacao");
$dateDtLcto                = request("var_dt_lcto");
$intCodContaQuitacao       = request("var_cod_conta_quitacao");
$intCodPlanoContaQuitacao  = request("var_cod_plano_conta_quitacao");
$intCodCentroCustoQuitacao = request("var_cod_centro_custo_quitacao");
$intCodJobQuitacao         = request("var_cod_job_quitacao");

$dblVlrConta = str_replace(",",".",str_replace(".","",$dblVlrConta));
$dblTotal = 0;

$strMSG = "";
if ($intCodNFEntrada == "") $strMSG .= "Informe Código da NF de Entrada<br>";
if ($intCodPJFornec == "") $strMSG .= "Informe Fornecedor<br>";
if ($intCodConta == "") $strMSG .= "Selecione Conta<br>";
if ($intCodPlanoConta == "") $strMSG .= "Selecione Plano de Conta<br>";
if ($intCodCentroCusto == "") $strMSG .= "Selecione Centro de Custo<br>";
//if ($intCodJob == "") $strMSG .= "Selecione Job<br>";
if ($intCodCFGBoleto == "") $strMSG .= "Selecione Boleto<br>";
if ($dateDtEmissao == "") $strMSG .= "Informe Data de Emissão<br>";
if ($strHistorico == "") $strMSG .= "Informe Histórico<br>";
if ($strOpcaoVariosTitulos == "A") {
	if ($dateDtVcto == "") $strMSG .= "Informe Data de Vcto<br>";
}
if ($strOpcaoVariosTitulos == "B") {
	if ($dateDtPrimVcto == "") $strMSG .= "Informe Primeira Data de Vcto<br>";
	if ($intParcelas == "") $strMSG .= "Informe Parcelas<br>";
	if ($strFrequencia == "") $strMSG .= "Selecione Frequência<br>";
}
if ($strOpcaoVariosTitulos == "C") {
	if ($dateDtBaseVcto == "") $strMSG .= "Informe Data Base de Vcto<br>";
	if ($strPrzVctos == "") $strMSG .= "Informe Prazos dos Vencimentos<br>";
	if ($strVlrVctos == "") $strMSG .= "Informe Valores dos Vencimentos<br>";
	
	$arrPrzVctos = explode(",", $strPrzVctos);
	$arrVlrVctos = explode(";", $strVlrVctos);
	if (sizeof($arrPrzVctos) != sizeof($arrVlrVctos)) $strMSG .= "Quantidade de prazos e de valores não são os mesmos<br>";
	
	$intParcelas = sizeof($arrVlrVctos);
	
	$dblTotal = 0;
	foreach($arrVlrVctos as $Valor){ 
		$Valor = str_replace(",",".",str_replace(".","",$Valor));
		$dblTotal += $Valor;
	} 
	if (round(abs($dblTotal - $dblVlrConta), 2) >= 0.01) $strMSG .= "Valor informado não bate com valor total dos vencimentos<br>";
	
	$intPrazo = 0;
	foreach($arrPrzVctos as $Valor){ 
		if ($Valor <= $intPrazo) $strMSG .= "Um prazo A informado tem que ser menor que o prazo B e assim por diante<br>";
		$intPrazo = $Valor;
	} 
}
if ($strOpcaoQuitacao == "B") {
	if ($dateDtLcto == "") $strMSG .= "Informe Data de Lcto<br>";
	if ($intCodContaQuitacao == "") $strMSG .= "Selecione Conta para Quitação do Título<br>";
	if ($intCodPlanoContaQuitacao == "") $strMSG .= "Selecione Plano de Conta para Quitação do Título<br>";
	if ($intCodCentroCustoQuitacao == "") $strMSG .= "Selecione Centro de Custo para Quitação do Título<br>";
	//if ($intCodJobQuitacao == "") $strMSG .= "Selecione Job para Quitação do Título<br>";
}

if($strMSG == ""){
	//if ($strObs == "") $strObs = "NULL";
	if ($intCodCFGBoleto == "") $intCodCFGBoleto = "NULL";
	if ($intCodJob == "") $intCodJob = "NULL";
	if ($intCodContaQuitacao == "") $intCodContaQuitacao = "NULL";
	if ($intCodPlanoContaQuitacao == "") $intCodPlanoContaQuitacao = "NULL";
	if ($intCodCentroCustoQuitacao == "") $intCodCentroCustoQuitacao = "NULL";
	if ($intCodJobQuitacao == "") $intCodJobQuitacao = "NULL";
	if ($dateDtLcto == "") $dateDtLcto = "NULL"; else $dateDtLcto = "'".$dateDtLcto."'";
	
	$strCodigoGrupo = ($strOpcaoVariosTitulos != "A") ? "'".gerarSenha(5, 7)."'" : "NULL";
	
	$objConn->beginTransaction();
	try{
		$strSQL = "";
		if ($strOpcaoVariosTitulos == "A") $strSQL = " SELECT out_codigo, out_tipo, out_cod_titulo FROM sp_gera_varios_titulos (TRUE,".$intCodPJFornec.",'cad_pj_fornec',".$intCodConta.",".$intCodPlanoConta.",".$intCodCentroCusto.",".$intCodJob.",".$intCodCFGBoleto.",".$dblVlrConta.",'BOLETO','".$dateDtEmissao."','".$strHistorico."','".$strObs."',".$strCodigoGrupo.",'".$strOpcaoVariosTitulos."','".$dateDtVcto."',NULL,NULL,NULL,NULL,NULL,NULL,'".$strOpcaoQuitacao."',".$dateDtLcto.",".$intCodContaQuitacao.",".$intCodPlanoContaQuitacao.",".$intCodCentroCustoQuitacao.",".$intCodJobQuitacao.",'".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		if ($strOpcaoVariosTitulos == "B") $strSQL = " SELECT out_codigo, out_tipo, out_cod_titulo FROM sp_gera_varios_titulos (TRUE,".$intCodPJFornec.",'cad_pj_fornec',".$intCodConta.",".$intCodPlanoConta.",".$intCodCentroCusto.",".$intCodJob.",".$intCodCFGBoleto.",".$dblVlrConta.",'BOLETO','".$dateDtEmissao."','".$strHistorico."','".$strObs."',".$strCodigoGrupo.",'".$strOpcaoVariosTitulos."',NULL,'".$dateDtPrimVcto."',".$intParcelas.",'".$strFrequencia."',NULL,NULL,NULL,'".$strOpcaoQuitacao."',".$dateDtLcto.",".$intCodContaQuitacao.",".$intCodPlanoContaQuitacao.",".$intCodCentroCustoQuitacao.",".$intCodJobQuitacao.",'".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		if ($strOpcaoVariosTitulos == "C") $strSQL = " SELECT out_codigo, out_tipo, out_cod_titulo FROM sp_gera_varios_titulos (TRUE,".$intCodPJFornec.",'cad_pj_fornec',".$intCodConta.",".$intCodPlanoConta.",".$intCodCentroCusto.",".$intCodJob.",".$intCodCFGBoleto.",".$dblVlrConta.",'BOLETO','".$dateDtEmissao."','".$strHistorico."','".$strObs."',".$strCodigoGrupo.",'".$strOpcaoVariosTitulos."',NULL,NULL,".$intParcelas.",NULL,'".$dateDtBaseVcto."','".$strPrzVctos."','".$strVlrVctos."','".$strOpcaoQuitacao."',".$dateDtLcto.",".$intCodContaQuitacao.",".$intCodPlanoContaQuitacao.",".$intCodCentroCustoQuitacao.",".$intCodJobQuitacao.",'".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		
		if ($strSQL != "") {
			$objResult = $objConn->query($strSQL);
			
			$strCodigos = "";
			foreach($objResult as $objRS) $strCodigos .= getValue($objRS,"out_cod_titulo").",";
			if ($strCodigos != "") {
				$strCodigos = substr($strCodigos, 0, -1);
				
				$strSQL = " UPDATE fin_conta_pagar_receber 
							SET cod_nf_entrada = ".$intCodNFEntrada."
							WHERE cod_conta_pagar_receber IN (".$strCodigos.") ";
				$objConn->query($strSQL);
				
				$strSQL = " UPDATE fin_nf_entrada 
							SET titulo_gerado = TRUE
							WHERE cod_nf_entrada = ".$intCodNFEntrada;
				$objConn->query($strSQL);
			}
		}
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objResult->closeCursor();
	
	if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) 
      $strLocation = "../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo"); 
    else 
      $strLocation = "../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo"); 
       
	redirect($strLocation);
}
else{
	mensagem("err_dados_titulo","err_dados_submit_desc",$strMSG,"","erro",1);
	die();
}
?>