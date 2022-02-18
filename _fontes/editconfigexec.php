<?php
include_once("../_database/athdbconn.php");

$strBaseName	  = request("var_basename");	// basename indica a pasta da aplicaчуo - nome completo: Ex. "modulo_cadPj"
$strCampos		  = request("var_campos");
$intNumItensGrade = request("var_itens_grade");

$strSesPfx = strtolower(str_replace("modulo_","",$strBaseName));

if($strCampos != "" && $intNumItensGrade != "") { 

	if(getsession($strSesPfx . "_field_detail") != '') { 
		$strSQl = getsession($strSesPfx . "_select"); 
	}
	else { 
		$strSQl = getsession($strSesPfx . "_select_orig"); 
	}
	 
	$strQuery = " SELECT " . $strCampos . substr($strSQl,strpos($strSQl," FROM"));
	
	setsession($strSesPfx . "_select",$strQuery);
	setsession($strSesPfx . "_select_orig",$strQuery);
	setsession($strSesPfx . "_num_per_page",$intNumItensGrade);

	$strAuxGradeDefault = getsession($strSesPfx . "_grid_default");
	if (strpos($strAuxGradeDefault,"?") === false) { 
		$strAuxGradeDefault .= "?var_basename=" . $strBaseName; 
	}  
	else { 
		$strAuxGradeDefault .= "&var_basename=" . $strBaseName; 
	}

	redirect($strAuxGradeDefault);
}
else {
	mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
	die();
}
?>