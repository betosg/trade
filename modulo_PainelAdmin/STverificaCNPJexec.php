<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	error_reporting(E_ALL);
	
	$strMsg  = request("var_msg");
	$strDB   = request("var_db");
	
	if($strDB == ""){ 
		$strDB = (getcookie("db_name") != "") ? getcookie("db_name") : "tradeunion_sindieventos";
	}
	
	$objConn = abreDBConn($strDB);
	
	$strCNPJ = request('var_cnpj');
	
	try{
		$strSQL = "SELECT cod_empresa FROM cad_empresa WHERE cnpj='".$strCNPJ."'";
		$objResult = $objConn->query($strSQL);
	} 
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	$objRS = $objResult->fetch();
	
	if(getvalue($objRS,'cod_empresa') != ''){
		header("Location:STverificaCNPJ.php?var_msg=Já existe um cadastro com este CNPJ");
	}else{
		header("Location:STcadastroEmp.php?var_cnpj=".$strCNPJ);
	}
?>
