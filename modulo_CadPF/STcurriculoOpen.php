<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";

/*** RECEBE PARAMETROS ***/
$strOperacao = request("var_oper");
$intCodDado = request("var_chavereg");



if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}


try{
	
	 $strSQL = "SELECT cod_pf, cod_pf_curriculo from cad_pf_curriculo where cod_pf = ".$intCodDado . " limit 1 ";
	//die();
	$objResult = $objConn->query($strSQL);
	}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	
	die();
}
//Cria array associativo para o sql gerado acima
$objRS = $objResult->fetch();

setsession("cadpfcurriculointerna_chave_app","215");
if( getValue($objRS,"cod_pf_curriculo") == "" ){
	setsession("ins_curriculo_cod_pf",$intCodDado);

	//setsession(CFG_SYSTEM_NAME."_login_away"     , $strLoginAway				     );
	$strRedirect = "../_fontes/insupddelmastereditor.php?var_oper=INS&var_basename=modulo_cadPfCurriculoInterna";
}else{
	$strRedirect = "../_fontes/insupddelmastereditor.php?var_oper=UPD&var_chavereg=".getValue($objRS,"cod_pf_curriculo")."&var_basename=modulo_cadPfCurriculoInterna";
}
$objConn = NULL;

redirect($strRedirect);
?>