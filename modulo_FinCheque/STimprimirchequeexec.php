<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// REQUESTS
	$intCodDado   = request("var_chavereg");
	$strModelo    = request("var_modelo");
	$boolCruzado  = request("var_cruzado");
	$boolNominal  = request("var_nominal");
	$dblVlrCheque = request("var_valorcheque");
	$strLINK = "";
	
	// TRATAMENTO CONTRA CAMPOS VAZIOS
	$boolCruzado = ($boolCruzado == "") ? "0" : $boolCruzado;
	$boolNominal = ($boolNominal == "") ? "0" : $boolNominal;
	$strErrMsg   = "";
	$strErrMsg  .= ( $intCodDado == "") ? getTText("err_cod_cheque",  C_NONE) : "";
	//$strErrMsg  .= (($dblVlrCheque < 100) && ($boolNominal != "0")) ? getTText("err_cheque_nominal_vlr_minimo",C_NONE) : "";
	if($strErrMsg != ""){ mensagem("err_sql_titulo","err_impr_cheques",$strErrMsg,"STimprimircheque.php?var_chavereg=".$intCodDado,"erro",1); die(); }
	
	// SWITCH DO TIPO DE CHEQUE
	switch($strModelo){
		case "BRADESCO":
			$strLINK = "../modulo_FinCheque/STchequebradesco.php?var_chavereg=".$intCodDado."&var_cruzado=".$boolCruzado."&var_nominal=".$boolNominal;
		break;
		
		case "BANCO_DO_BRASIL":
			$strLINK = "../modulo_FinCheque/STchequebancodobrasil.php?var_chavereg=".$intCodDado."&var_cruzado=".$boolCruzado."&var_nominal=".$boolNominal;
		break;
		
		case "BANRISUL":
			$strLINK = "../modulo_FinCheque/STchequebanrisul.php?var_chavereg=".$intCodDado."&var_cruzado=".$boolCruzado."&var_nominal=".$boolNominal;
		break;
		
		case "HSBC":
			$strLINK = "../modulo_FinCheque/STchequehsbc.php?var_chavereg=".$intCodDado."&var_cruzado=".$boolCruzado."&var_nominal=".$boolNominal;
		break;
		
		default:
			$strLINK = "../modulo_FinCheque/STchequebradesco.php?var_chavereg=".$intCodDado."&var_cruzado=".$boolCruzado."&var_nominal=".$boolNominal;
		break;
	}
	
	// REDIRECT PARA LINK DE MODELO ENCONTRADO
	redirect($strLINK);
?>