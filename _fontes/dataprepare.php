<?php
include_once("../_database/athdbconn.php");

$strAux = $_POST;
if($strAux == "") { $strAux = $_GET; }
//print_r($strAux);
 
$strBaseName	= request("var_basename"); //basename indica a pasta da aplicação - nome completo: Ex. "modulo_cadPj"
$strSesPfx		= strtolower(str_replace("modulo_","",$strBaseName));

$arrOrderByPadrao = explode(" ORDER BY ",str_replace("\r\n"," ",getsession($strSesPfx . "_select_orig")));
$strWhereFiltro   = "";
	
foreach($strAux as $strCampo => $strValor) { 
	$strAuxValue = str_replace("'","''",$strValor);
	
	//Tudo que vem de parâmetro com prefixo "var_" é considerado como parâmetro de fioltragem, exceto o "var_basename"
	if ( (strpos($strCampo,"var_") === 0) && (strpos($strCampo,"var_basename") === false) ){
		$strCampo = preg_replace("/^var_/","",$strCampo);
		$strAuxType  = substr($strCampo,0,strpos($strCampo,"_"));
		$strAuxField = substr($strCampo,strpos($strAuxType,"_") + strlen($strAuxType) + 1);	
		$strAuxField = str_replace("__",".",$strAuxField);
	
	switch(strtolower($strAuxType)){
			case "num": 	 (($strAuxValue != "") && (is_numeric($strAuxValue))) ? $strAuxValue = (" = " . $strAuxValue . " ") : NULL;
							 break;
							 	
			case "str":		 ($strAuxValue != "") ? $strAuxValue = (" <=> '" . $strAuxValue . "%' ") : NULL;
							 break;
							 
			case "streq":	 ($strAuxValue != "") ? $strAuxValue = (" = '" . $strAuxValue . "' ") : NULL;
							 break;
							 
			case "autodate": $strAuxValue = (" = current_timestamp ");
							 break;
							 	
			case "bool":	 ($strAuxValue != "") ? $strAuxValue = (" = " . $strAuxValue) : NULL;
							 break;
								
			case "cripto": 	 ($strAuxValue != "") ? $strAuxValue = " = '" . md5($strAuxValue) . "'" : NULL;
							 break;
								
			case "date":	 $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 ($strAuxValue != "" && is_date($strAuxValue)) ? $strAuxValue = " = '" . $strAuxValue . "'" : NULL;
							 break;
							 
			case "datetime": $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 ($strAuxValue != "" && is_date($strAuxValue)) ? $strAuxValue = " = '" . $strAuxValue . "'" : NULL;
							 break;
							 
			case "moeda":    $strAuxValue = str_replace(',','.',str_replace('.','',$strAuxValue)); /*se estiver no formato 9.999,99 trocamos para 9999.99 - by Vini 14.12.2012*/
			                 $strAuxValue = (($strAuxValue != "") && (is_numeric($strAuxValue))) ? $strAuxValue = " = round(cast(" . $strAuxValue . " as numeric),2) " : "";
							 break;

			case "moeda4cd": $strAuxValue = str_replace(',','.',str_replace('.','',$strAuxValue)); /*se estiver no formato 9.999,9999 trocamos para 9999.9999 - by Vini 14.12.2012*/
			                 $strAuxValue = (($strAuxValue != "") && (is_numeric($strAuxValue))) ? $strAuxValue = " = round(cast(" . $strAuxValue . " as numeric),4) " : "";
							 break;							

			case "dateinterval": 
							 $strREDt1 = "(0?[0-9]|1?[012])\/([012]?[0-9]|3[01])\/(\d{4})";
							 $strREDt2 = "([012]?[0-9]|3[01])\/(0?[0-9]|1?[012])\/(\d{4})";
							 $strREDt3 = "((current_date|current_timestamp)( (\-|\+)+ interval '[0-9]+ (day(s)?|month(s)?|year(s)?|hour(s)?|minute(s)?|second(s)?)')*)";
							 
							 $strRegExData = "(" . $strREDt1 . "|" . $strREDt2 . "|" . $strREDt3 . ")";
								
							 if(preg_match("/" . $strRegExData . "( *)( E | AND | Y | ATÉ | UNTIL | HASTA |-)+( *)" . $strRegExData . "/i",$strAuxValue) != 0){
								$arrTmpDates  = preg_split("/( E | AND | Y | ATÉ | UNTIL | HASTA |-)+/i",$strAuxValue);
								
								$dateTmpDate1 = (is_date(cDate(CFG_LANG,trim($arrTmpDates[0]),false))) ? "'" . cDate(CFG_LANG,trim($arrTmpDates[0]),false) . "'" : trim($arrTmpDates[0]);
								$dateTmpDate2 = (is_date(cDate(CFG_LANG,trim($arrTmpDates[1]),false))) ? "'" . cDate(CFG_LANG,trim($arrTmpDates[1]),false) . "'" : trim($arrTmpDates[1]);								
								
								$strAuxValue  = str_replace("''","'"," BETWEEN " . $dateTmpDate1 . " AND " . $dateTmpDate2);
							 } elseif(preg_match("/" . $strRegExData . "/i",$strAuxValue)) {
								$strAuxValue = " LIKE '" . cDate(CFG_LANG,$strAuxValue,false) . "%'";
							 } else {
								$strAuxValue = "";
							 }
							break;
			case "status":    
							if($strAuxValue == "A") {
								$strAuxValue = " IS NULL ";
							}
							elseif($strAuxValue == "I") {
								$strAuxValue = " IS NOT NULL ";
							}
							else $strAuxValue= "";
							break;
		}
		if($strAuxValue != ""){
            switch(strtolower($strAuxType)){
				case "moeda": $strAuxField = "round(cast(" .$strAuxField . " as numeric),2) ";
				     break;
					 
   			    case "moeda4cd": $strAuxField = "round(cast(" .$strAuxField . " as numeric),4) ";
				     break;			
			}		
		
			if(strpos($arrOrderByPadrao[0]," WHERE ") === false && $strWhereFiltro == "") {
				$strWhereFiltro .= " WHERE " . $strAuxField . $strAuxValue;
			}
			else{
				$strWhereFiltro .= " AND " . $strAuxField . $strAuxValue;
			}
		}
	}
		
}

$strSQL = $arrOrderByPadrao[0] . $strWhereFiltro;
if(isset($arrOrderByPadrao[1])){ $strSQL .= " ORDER BY " . $arrOrderByPadrao[1]; }

setsession($strSesPfx . "_select",$strSQL);

//DEBUG: echo($strSQL. "<br><br>");

$strAuxGradeDefault = getsession($strSesPfx . "_grid_default");
if (strpos($strAuxGradeDefault,"?") === false) 
  { $strAuxGradeDefault .= "?var_basename=" . $strBaseName; }  
else 
  { $strAuxGradeDefault .= "&var_basename=" . $strBaseName; }

//DEBUG: echo($strAuxGradeDefault. "<br><br>"); //die();

redirect($strAuxGradeDefault); 
?>