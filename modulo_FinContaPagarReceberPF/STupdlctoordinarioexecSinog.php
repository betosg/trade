<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athutils.php");
//erroReport();
$intCodDado				 = request("var_chavereg");
$intCodLctoOrdinario     = request("var_cod_lcto_ordinario");
$strLocation			 = request("var_location");
$strTipoConta 			 = request("var_tipo_conta");
$intCodConta 			 = request("var_cod_conta");
$intCodigo 				 = request("var_codigo");
$strTipo 				 = request("var_tipo");
$intCodCentroCusto 		 = request("var_cod_centro_custo");
$intCodPlanoConta 		 = request("var_cod_plano_conta");
$intCodJob				 = request("var_cod_job");
$dblVlrOrigA 			 = request("var_vlr_orig");
$dblVlrMulta 			 = request("var_vlr_multa");
$dblVlrJuros 			 = request("var_vlr_juros");
$dblVlrDesc 			 = request("var_vlr_desc");
$dblVlrLctoNorm 		 = request("var_vlr_lcto");
$strNumLcto 			 = request("var_num_lcto");
$dateNumLctoNorm 		 = cDate(CFG_LANG,request("var_dt_lcto"),false);
$strHistorico 			 = request("var_historico");
$strObs 				 = request("var_obs");
$strDocumento 			 = request("var_documento");
$strChequeNumero 		 = request("var_cheque_numero");
$strCartaoNumero 		 = request("var_cartao_numero");
$strCartaoValidade 		 = request("var_cartao_validade");
$strCartaoPortador 		 = request("var_cartao_portador");
$strCartaoBandeira		 = request("var_cartao_bandeira");
$strExtraDocumento 		 = request("var_extra_documento");
$strTpLcto				 = request("var_tp_lcto");

$strMsg = "";


if($strHistorico == "") 					{  $strMsg .= "Informar histórico<br>"; }
if($intCodPlanoConta == "") 				{  $strMsg .= "Informar plano de conta<br>"; }
if($intCodCentroCusto == "") 				{  $strMsg .= "Informar centro de custo<br>"; }
//if($intCodJob == "") 						{  $strMsg .= "Informar job<br>"; }




if ($intCodJob == "") $intCodJob = "NULL";

$objConn = abreDBConn(CFG_DB);

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}



//----------------------------
// Insere dados do lançamento 
//----------------------------
$objConn->beginTransaction();
try{
	$strSQL = " UPDATE fin_lcto_ordinario set ";
	$strSQL .= "  cod_centro_custo = " . $intCodCentroCusto;
	$strSQL .= " ,cod_plano_conta = " . $intCodPlanoConta;
	$strSQL .= " ,historico = '" . $strHistorico. "'";
	$strSQL .= " ,obs = '" . $strObs . "'";
	$strSQL .= " ,cod_job = ". $intCodJob ;
	$strSQL .= " ,sys_dtt_upd = CURRENT_TIMESTAMP ";
	$strSQL .= " ,sys_usr_upd ='" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'";
	 $strSQL .= " WHERE cod_lcto_ordinario = " . $intCodLctoOrdinario;
	//die();
	$objConn->query($strSQL);

	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}

if($strTpLcto != 'rapido'){
	redirect($strLocation);
	//DEBUG die("LCTO_RAPIDO");
?>
	
		<script>
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>')
			.src = 'STifrlancamentoSinog.php?var_chavereg=<?php echo($intCodDado)?>&var_field_detail=cod_conta_pagar_receber';
		</script>
	  
<?php
}else{
	//DEBUG die($strLocation." LCTO_NORMAL");
	redirect($strLocation);
}
?>