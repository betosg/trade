<?php
include_once("../_database/athdbconn.php");

$intCodDado				 = request("var_chavereg");
$strLocation			 = request("var_location");
$strTipoConta 			 = request("var_tipo_conta");
$intCodConta 			 = request("var_cod_conta");
$intCodigo 				 = request("var_codigo");
$strTipo 				 = request("var_tipo");
$intCodCentroCusto 		 = request("var_cod_centro_custo");
$intCodPlanoConta 		 = request("var_cod_plano_conta");
$dblVlrOrigA 			 = request("var_vlr_orig");
$dblVlrMulta 			 = request("var_vlr_multa");
$dblVlrJuros 			 = request("var_vlr_juros");
$dblVlrDesc 			 = request("var_vlr_desc");
$dblVlrLctoNorm 		 = request("var_vlr_lcto");
$strNumLcto 			 = request("var_num_lcto");
$dateNumLctoNorm 		 = cDate(CFG_LANG,request("var_dt_lcto"),false);
//$dateNumCred 		     = cDate(CFG_LANG,request("var_dt_cred"),false);
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

if($strNumLcto == "") 						{  $strMsg .= "Informar número do lançamento<br>"; }
if($intCodConta == "") 						{  $strMsg .= "Parâmetro inválido para conta<br>"; }
if($strHistorico == "") 					{  $strMsg .= "Informar histórico<br>"; }
if($intCodPlanoConta == "") 				{  $strMsg .= "Informar plano de conta<br>"; }
if($intCodCentroCusto == "") 				{  $strMsg .= "Informar centro de custo<br>"; }
if($intCodDado == "")					 	{  $strMsg .= "Parâmetro inválido para conta pagar e receber<br>"; }
if(($intCodigo == "") || ($strTipo == "")) 	{  $strMsg .= "Informar entidade<br>"; }

if(($strTipoConta != "pagar") && ($strTipoConta != "receber")) {
	$strMsg .= "Parâmetro inválido para tipo de conta<br>" . $strTipoConta; 
}

if($strDocumento == "") $strMsg .= "Informar o tipo de documento<br>";




if(!is_date($dateNumLctoNorm)) {  
	$dateNumLctoNorm = dateNow();
}
elseif($dateNumLctoNorm > dateNow()) {  
	$strMsg .= "Não é permitido lançamento com data futura (" . $dateNumLctoNorm . ")<br>";
}

if($dblVlrMulta    == "") { $dblVlrMulta    = 0; }
if($dblVlrJuros    == "") { $dblVlrJuros    = 0; }
if($dblVlrDesc     == "") { $dblVlrDesc     = 0; }
if($dblVlrLctoNorm == "") { $dblVlrLctoNorm = 0; }

if((!is_numeric(formatcurrency($dblVlrLctoNorm))) || (formatcurrency($dblVlrLctoNorm) < 0)) {  $strMsg .= "Informar valor do lançamento<br>"; }

if(strtoupper($strDocumento) == 'CHEQUE'){
	$numDocumento = $strChequeNumero;
}else if(strpos(strtoupper($strDocumento),'CARTAO_') !== false){
	$numDocumento = $strCartaoNumero;
	$strExtraDocumento = $strCartaoValidade . ";" . $strCartaoPortador . ";" . $strExtraDocumento;
}else{
	$numDocumento = '';
}

if($strTpLcto == 'rapido'){
	$strExtraDocumento = strtoupper($strTpLcto);
}

$objConn = abreDBConn(CFG_DB);

if($intCodConta == ""){ 
	try{
		$strSQL = "SELECT nome, sys_dtt_ins FROM fin_conta WHERE cod_conta = " . $intCodConta;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	foreach($objResult as $objRS){
		if($dateNumLctoNorm < getValue($objRS,"sys_dtt_ins")) { 
			$strMsg .= "A data do lançamento (" . dDate($CFG_LANG,$dateNumLctoNorm,false) . ") não corresponde com a data de criação da conta 
					   " . getValue($objRS,"nome") . " (" . dDate(getValue($objRS,"sys_dtt_ins"),false) . ").<br>";
		}
	}
	
	$objResult->closeCursor();
}

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

//-----------------------------
// Formatações
//-----------------------------
$dblVlrLctoNorm = formatcurrency($dblVlrLctoNorm,2);
$dblVlrMulta    = formatcurrency($dblVlrMulta,2);
$dblVlrJuros    = formatcurrency($dblVlrJuros,2);
$dblVlrDesc     = formatcurrency($dblVlrDesc,2);

//----------------------------
// Insere dados do lançamento 
//----------------------------
$objConn->beginTransaction();
try{
	$strSQL  = " INSERT INTO fin_lcto_ordinario (cod_conta_pagar_receber, tipo, codigo, cod_conta, cod_plano_conta, cod_centro_custo, historico, obs, num_lcto, dt_lcto, vlr_lcto, vlr_multa, vlr_juros, vlr_desc, sys_dtt_ins, sys_usr_ins, tipo_documento, num_documento, extra_documento) ";
	$strSQL .= " VALUES (" . $intCodDado . ", '" . $strTipo . "', " . $intCodigo . ", " . $intCodConta . ", " . $intCodPlanoConta . ", " . $intCodCentroCusto . ", '" . $strHistorico . "', '" . $strObs . "', '" . $strNumLcto . "', '" . $dateNumLctoNorm	. "', " . $dblVlrLctoNorm . ", " . $dblVlrMulta . ", " . $dblVlrJuros . ", " . $dblVlrDesc . ", CURRENT_TIMESTAMP, '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "', '" . $strDocumento . "', '" . $numDocumento . "', '" . $strExtraDocumento . "') ";
	
	$objConn->query($strSQL);
	
	
	$strSQL  = " UPDATE fin_conta_pagar_receber ";
	$strSQL .= " SET sys_dtt_ult_lcto = CURRENT_TIMESTAMP ";
	$strSQL .= "   , sys_usr_ult_lcto = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
	$strSQL .= " WHERE cod_conta_pagar_receber = " . $intCodDado;
	
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
	<!--
		<script>
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>')
			.src = 'STifrlancamento.php?var_chavereg=<?php echo($intCodDado)?>&var_field_detail=cod_conta_pagar_receber';
		</script>
	  -->
<?php
}else{
	//DEBUG die($strLocation." LCTO_NORMAL");
	redirect($strLocation);
}
?>