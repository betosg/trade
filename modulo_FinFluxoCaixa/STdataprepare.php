<?php
include_once("../_database/athdbconn.php");
 
$strAux = $_POST;
if($strAux == ""){
	$strAux = $_GET;
}
 
$strSesPfx        = strtolower(str_replace("modulo_","",basename(getcwd())));
$arrOrderByPadrao = explode(" ORDER BY ",str_replace("\r\n"," ",getsession($strSesPfx . "_select_orig")));
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
			case "num": 	 (($strAuxValue != "") && (is_numeric($strAuxValue))) ? $strAuxValue = (" = " . $strAuxValue . " ") : NULL;
							 break;
							 	
			case "str":		 ($strAuxValue != "") ? $strAuxValue = " LIKE '" . $strAuxValue . "%' " : NULL;
							 break;
							 
			case "streq":	 ($strAuxValue != "") ? $strAuxValue = " = '" . $strAuxValue . "' " : NULL;
							 break;
							 
			case "autodate": $strAuxValue = " = current_timestamp ";
							 break;
							 	
			case "bool":	 ($strAuxValue != "") ? $strAuxValue = (" = " . $strAuxValue) : NULL;
							 break;
								
			case "cripto": 	 ($strAuxValue != "") ? $strAuxValue = " = '" . md5($strAuxValue) . "'" : NULL;
							 break;
								
			case "date":	 $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 ($strAuxValue != "" && is_date($strAuxValue)) ? $strAuxValue = " <= '" . $strAuxValue . "'" : NULL;
							 break;
							 
			case "datetime": $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 ($strAuxValue != "" && is_date($strAuxValue)) ? $strAuxValue = " = '" . $strAuxValue . "'" : NULL ;
							 break;
							 
			case "moeda":    $strAuxValue = (($strAuxValue != "") && (is_numeric($strAuxValue))) ? formatcurrency($strAuxValue,2) : $strAuxValue = "";
							 break;
		}
		if($strAuxValue != ""){
			if(strpos($arrOrderByPadrao[0]," WHERE ") === false && $strWhereFiltro == "") {
				$strWhereFiltro .= " WHERE " . $strAuxField . $strAuxValue;
			}
			else{
				$strWhereFiltro .= " AND " . $strAuxField . $strAuxValue;
			}
		}
		
}
$strSQL = $arrOrderByPadrao[0] . $strWhereFiltro;

if(isset($arrOrderByPadrao[1])){
	$strSQL .= " ORDER BY " . $arrOrderByPadrao[1];
}

setsession($strSesPfx . "_select",$strSQL);



redirect("STdata.php"); 
?>