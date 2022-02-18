<?php
include_once("../_database/athdbconn.php");


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";

/*** RECEBE PARAMETROS ***/
$strOperacao = request("var_oper");
$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");;



if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}


try{
	
	$strSQL = "SELECT cod_pf, cod_pf_curriculo from cad_pf_curriculo where cod_pf = ".$intCodDado . " limit 1 ";
	$objResult = $objConn->query($strSQL);
	}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	
	die();
}
//Cria array associativo para o sql gerado acima
$objRS = $objResult->fetch();
setsession("cadpfcurriculo_chave_app","213");
//getsession($strSesPfx . "_dir_modulo")
setsession("ins_curriculo_cod_pf",$intCodDado);
setsession("var_return","../modulo_PainelPF/STindex.php");
if( getValue($objRS,"cod_pf_curriculo") == "" ){
	

	//setsession(CFG_SYSTEM_NAME."_login_away"     , $strLoginAway				     );
	$strRedirect = "../_fontes/insupddelmastereditor.php?var_oper=INS&var_basename=modulo_cadPfCurriculo&var_populate=yes&var_return=../modulo_PainelPF/STindex.php";
}else{
	$strRedirect = "../_fontes/insupddelmastereditor.php?var_populate=yes&var_oper=UPD&var_chavereg=".getValue($objRS,"cod_pf_curriculo")."&var_basename=modulo_cadPfCurriculo&var_populate=yes&var_return=../modulo_PainelPF/STindex.php";
}
$objConn = NULL;

redirect($strRedirect);
?>