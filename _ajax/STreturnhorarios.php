<?php
	// HEADERS ANTI-CACHE
	header("Content-Type:text/html; charset=iso-8859-1");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");

	// INCLUDES
	include_once("../_database/athdbconn.php");

	// ABERTURA DE CONEXÃO COM DB
	$objConn  = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strDATE 	= request("var_dia");
	$intTEMPO	= getVarEntidade($objConn,"ag_intervalo_atendimento_homo");
	$strTMANHA 	= getVarEntidade($objConn,"ag_horario_atendimento_manha");
	$strTTARDE 	= getVarEntidade($objConn,"ag_horario_atendimento_tarde");
	$strTMANHA  = ($strTMANHA == "") ? "09:00|12:00" : $strTMANHA;
	$strTTARDE  = ($strTTARDE == "") ? "13:00|18:00" : $strTTARDE;
	$arrTMANHA  = explode("|",$strTMANHA);
	$arrTTARDE  = explode("|",$strTTARDE);
	$intTMANHAINI = mktime(substr($arrTMANHA[0],0,2),substr($arrTMANHA[0],3,2));
	$intTMANHAFIM = mktime(substr($arrTMANHA[1],0,2),substr($arrTMANHA[1],3,2));
	$intTTARDEINI = mktime(substr($arrTTARDE[0],0,2),substr($arrTTARDE[0],3,2));
	$intTTARDEFIM = mktime(substr($arrTTARDE[1],0,2),substr($arrTTARDE[1],3,2));
	$auxBOOLEAN = false;
	// $intTEMPO	  = mktime(0,$intTEMPO);
	
	//echo(substr($arrTMANHA[0],0,2)."<br/>");
	//echo(substr($arrTMANHA[1],0,2)."<br/>");
	//echo($intTMANHAINI."<br/>");
	//echo($intTMANHAFIM."<br/>");
	//echo($intTEMPO."<br/>");
	//echo(($intTEMPO + $intTMANHAINI)."<br/>");
	//echo(date("H:i",$intTMANHAINI));
		
	// SQL QUE RETORNA OS HORÁRIOS
	try {
		$strSQL = "SELECT EXTRACT(hour FROM ag_agenda.prev_dtt_ini) AS hora, EXTRACT(minute FROM ag_agenda.prev_dtt_ini) AS minuto FROM ag_agenda WHERE ag_agenda.prev_dtt_ini BETWEEN '".$strDATE." ".$arrTMANHA[0]."' AND '".$strDATE." ".$arrTMANHA[1]."' OR ag_agenda.prev_dtt_ini BETWEEN '".$strDATE." ".$arrTTARDE[0]."' AND '".$strDATE." ".$arrTTARDE[1]."' ORDER BY hora";
		// die($strSQL);
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	
	$strHORARIOSPR = "";
	foreach($objResult as $objRS){
		$strHORARIOSPR .= (strlen(getValue($objRS,"hora")) == 1) ? "|0".getValue($objRS,"hora") : "|".getValue($objRS,"hora");
		$strHORARIOSPR .= (getValue($objRS,"minuto") == "0") ? ":".getValue($objRS,"minuto")."0" : ":".getValue($objRS,"minuto");
	}
	
	$strALLHORARIOS = "";
	while(!$auxBOOLEAN){
		// echo(date("H:i",$intTMANHAINI)."<br />");
		if(!stristr($strHORARIOSPR,date("H:i",$intTMANHAINI))){
			$strALLHORARIOS .= "|".date("H:i",$intTMANHAINI);
		}
		$strDATE = date("H:i",$intTMANHAINI);
		$intTMANHAINI = mktime(substr($strDATE,0,2),substr($strDATE,3,2)+$intTEMPO);
		if($intTMANHAINI == $intTMANHAFIM){ $auxBOOLEAN = true; }
	}
	$auxBOOLEAN = false;
	
	while(!$auxBOOLEAN){
		// echo(date("H:i",$intTTARDEINI)."<br />");
		if(!stristr($strHORARIOSPR,date("H:i",$intTTARDEINI))){
			$strALLHORARIOS .= "|".date("H:i",$intTTARDEINI);
		}
		$strDATE = date("H:i",$intTTARDEINI);
		$intTTARDEINI = mktime(substr($strDATE,0,2),substr($strDATE,3,2)+$intTEMPO);
		if($intTTARDEINI == $intTTARDEFIM){ $auxBOOLEAN = true; }
	}
	
	// echo($strHORARIOSPR."<br />");
	
	// $strALLHORARIOS = trim($strALLHORARIOS);
	if($strALLHORARIOS == ""){ echo("@@"); }	
	else{ echo(substr($strALLHORARIOS,1,strlen($strALLHORARIOS))); }
	// TRATAMENTO DOS HORÁRIOS 
	/*for($auxCounter = $intTMANHAINI; $auxCounter <= $intTMANHAFIM; $auxCounter + $intTEMPO){
		echo(date("HH:ii",$auxCounter));
	}*/
	$objResult->closeCursor();
?>
