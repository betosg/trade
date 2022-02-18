<?php
include_once("../_database/athdbconn.php");
 
$strAux = $_POST;
if($strAux == ""){
	$strAux = $_GET;
}
 
$strSesPfx        = strtolower(str_replace("modulo_","",basename(getcwd())));
$arrOrderByPadrao = explode(" ORDER BY ",str_replace("\r\n"," ",getsession($strSesPfx . "_select_orig")));
$arrSQLPadrao     = explode("    WHERE ",getsession($strSesPfx . "_select_orig"));
$strWhereFiltro   = "";
	
foreach($strAux as $strCampo => $strValor){ 
	
	$strAuxValue = str_replace("'","''",$strValor);
	
	if(strpos($strCampo,"dbvar_") === 0){
		$strCampo = str_replace("dbvar_","",$strCampo);
		$strAuxType  = substr($strCampo,0,strpos($strCampo,"_"));
		$strAuxField = substr($strCampo,strpos($strAuxType,"_") + strlen($strAuxType) + 1);	
		$strAuxField = str_replace("<PONTO>",".",$strAuxField);
	}
	
	switch(strtolower($strAuxType)){
			case "num": 	 (($strAuxValue == "") || (!is_numeric($strAuxValue))) ? NULL : $strAuxValue = (" = '" . $strAuxValue . "' ");
							 break;
								
			case "str":		 ($strAuxValue != "") ? $strAuxValue = (" LIKE '" . $strAuxValue . "%' ") : NULL;
							 break;
							 
			case "streq":	 ($strAuxValue != "") ? $strAuxValue = (" = '" . $strAuxValue . "' ") : NULL;
							 break;
							 
			case "autodate": ($strAuxValue == "") ? $strAuxValue = (" = now() ") : NULL;
							 break;
								
			case "bool":	 ($strAuxValue == "") ? $strAuxValue = (" = " . $strAuxValue) : NULL;
							 break;
								
			case "cripto": 	 ($strAuxValue != "") ? $strAuxValue = " = '" . md5($strAuxValue) . "'" : NULL;
							 break;
								
			case "date":	 (($strAuxValue == "") || (!is_date($strAuxValue))) ? NULL : $strAuxValue = " = '" . cDate(CFG_LANG, $strAuxValue, false) . "'";
							 break;
							 
			case "datetime": (($strAuxValue == "") || (!is_date($strAuxValue))) ? NULL : $strAuxValue = " = '" . cDate(CFG_LANG, $strAuxValue, true) . "'";
							 break;
							 
			case "moeda":    if(($strAuxValue == "") || (!is_numeric($strAuxValue))){
							   $strAuxValue = "";
							 }
							 else{
							   $strAuxValue = number_format((double) $strAuxValue,2);
							   $strAuxValue = str_replace(".","",$strAuxValue);
							   $strAuxValue = " = " . str_replace(",",".",$strAuxValue);
							 }
							 break;
		}
		if($strAuxValue != ""){
			if(strpos($arrSQLPadrao[0],"  WHERE ") === false && $strWhereFiltro == "") {
				$strWhereFiltro .= "  WHERE " . $strAuxField . $strAuxValue;
			}
			else{
				$strWhereFiltro .= " AND " . $strAuxField . $strAuxValue;
			}
		}
		
}
$strWhereFiltro .= ($strWhereFiltro == "") ? " WHERE " : " AND ";
$strSQL = $arrSQLPadrao[0] . $strWhereFiltro . " 
  msg_mensagem.cod_mensagem = msg_destinatario.cod_mensagem
 AND msg_mensagem.cod_mensagem = msg_remetente.cod_mensagem
 AND msg_mensagem.cod_msg_pasta = msg_pasta.cod_msg_pasta
 AND msg_remetente.cod_user_remetente = sys_usuario.id_usuario
 AND msg_destinatario.cod_user_destinatario = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'
 AND msg_pasta.cod_user = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'";

if(isset($arrOrderByPadrao[1])){
	$strSQL .= " ORDER BY " . $arrOrderByPadrao[1];
}

setsession($strSesPfx . "_select",$strSQL);

redirect(getsession($strSesPfx . "_grid_default") . "?var_pasta=pesquisa"); 
?>