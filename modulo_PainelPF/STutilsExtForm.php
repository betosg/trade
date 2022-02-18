<?php
	/* KERNELPS - Biblioteca de funções (utils.php) --------------------------------------------------------- */
	/* ------------------------------------------------------------------------------------------------------ */
	/* Algumas funções replicadas apenas para ofuncionamento adequado da LOGIN                                */
	/* ------------------------------------------------------------------------------------------------------ */

	session_start();               // Inicia o session
	session_cache_limiter("none"); // ATENÇÃO!!! Esta linha estipula o tipo de cache que as páginas terão. Está "none" por causa das exportações que estouram o cache.
	set_time_limit(600); 		   // Limite de tempo para execução do script em si (página php)
	
	include_once("../../_tradeunion/_scripts/scripts.js");
	include_once("../../_tradeunion/_scripts/STscripts.js");
	include_once("../../_tradeunion/_database/STconfiginc.php");
	include_once("../../_tradeunion/_class/multi-language/multilang.php");
	include_once("../../_tradeunion/_class/multi-language/functions.inc.php");
	include_once("../../_tradeunion/_database/athtranslate.php");
	//include_once("../../_tradeunion/_database/athsendmail.php");
	
	function abreDBConn($prDBName){
		if($prDBName != "") {
			try{
				$objConn = new PDO("pgsql:host=" . CFG_DB_HOST . ";port=" . CFG_DB_PORT . ";dbname=" . $prDBName . ";user=" . CFG_DB_USER . ";password=" . CFG_DB_PASS);
				$objConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Força para que sejam mostrado erros, se existirem.
				return($objConn);
			} catch(PDOException $e) {
				mensagem("Aviso: Não foi possível se conectar ao banco","O sistema encontra-se em manutenção.<br>
						 Aguarde alguns instantes e tente novamente, ou entre em contato com o administrador.<br><br>
						 ", $e->getMessage(),"","standarderro",1);
				die();
			}
		}
	}
	
	function replaceParametersSession($prString) {
		$retValue = $prString;
		$mixPos = strpos($retValue,"{");
		
		if($mixPos !== false){
			while($mixPos !== false){
				$strIndex  = substr($retValue, $mixPos+1 , strpos($retValue,"}")-($mixPos+1));
				$strAuxSQL = str_replace("{".$strIndex."}",getsession($strIndex),$retValue);
				$retValue  = $strAuxSQL;
				$mixPos = strpos($retValue,"{");
			}
		}
		return($retValue);
	}
	
	function getValue($prRS, $prFieldName, $prBoolQuote=true){ // serve para resultsets e arrays em geral
		$retValue = (isset($prRS[$prFieldName])) ?  html_entity_decode($prRS[$prFieldName]) : "";
		if($prBoolQuote){ $retValue = str_replace('"',"&quot;",$retValue); }
		return($retValue);
	}
	
	function montaCombo($prObjConn, $prSQL, $prValor, $prCampo, $prSearch, $prGroup=""){
		$objResult = $prObjConn->query(replaceParametersSession($prSQL));
		$retDBname = "";
		$strGroup = "";
	
		foreach($objResult as $objRS){
			if($prGroup != ""){
				if(getValue($objRS,$prGroup) != $strGroup){
					$strGroup = getValue($objRS,$prGroup);
					$retDBname .= "	<optgroup label=\"" . getValue($objRS,$prGroup) . "\">\n";
				}
			}
			$retDBname .= "	<option value='" . getValue($objRS,$prValor) . "'";
			
			if(trim(substr($prSearch,0,6)) == '[text]'){
				if(trim(strtoupper(getValue($objRS,$prCampo))) == trim(strtoupper(substr($prSearch,6,strlen($prSearch))))){ $retDBname .= " selected"; }
			}else{
				if(getValue($objRS,$prValor) == $prSearch){ $retDBname .= " selected"; }
			}
			$retDBname .= ">" . getValue($objRS,$prCampo) . "</option>\n";
		}
		$objResult->closeCursor();
		return($retDBname);
	}
	
	function request($prParam){
		(isset($_REQUEST[$prParam])) ? $retValue = $_REQUEST[$prParam] : $retValue = "";
		return($retValue);
	}
	
	function getsession($prParam){
		(isset($_SESSION[$prParam])) ? $retValue = $_SESSION[$prParam] : $retValue = "";
		return($retValue);
	}
	
	function requestForm($prParam){
		(isset($_POST[$prParam])) ? $retValue = $_POST[$prParam] : $retValue = "";
		return($retValue);
	}
	
	function requestQueryString($prParam){
		(isset($_GET[$prParam])) ? $retValue = $_GET[$prParam] : $retValue = "";
		return($retValue);
	}
	
	function getcookie($prParam){
		(isset($_COOKIE[$prParam])) ? $retValue = $_COOKIE[$prParam] : $retValue = "";
		return($retValue);
	}
	
	function setsession($prName, $prValue){
		if(is_null($prValue)){
		    session_unregister($_SESSION[$prName]);
			unset($_SESSION[$prName]);
		}
		else { $_SESSION[$prName] = $prValue; }
	}
	
	function now(){	return(date("Y-m-d H:i:s")); }
	
	function findLogicalPath($prPasta = ""){
		$retValue  = "http://" . $_SERVER["HTTP_HOST"] . "/";
		$retValue .= ($_SERVER["HTTP_HOST"] == "www." . CFG_SYSTEM_NAME . ".com.br") ? $prPasta : CFG_SYSTEM_NAME . "/" . $prPasta;	
		return($retValue);
	}

	function findPhysicalPath($prPasta = "") {
		$retValue  = strtolower(realpath("../"));
		$retValue .= (DIRECTORY_SEPARATOR == "/") ? "/" . $prPasta : "\\" . $prPasta;	
		return($retValue);
	}
	
	function redirect($prURL){ header("Location:" . $prURL); }
	
	function mensagem($prTitulo, $prAviso, $prAdText="", $prHyperlink="", $prAcao="standardinfo", $prFlagHTML=0, $prBackground="default"){
 		$strAcao   = str_replace("standard","",strtolower($prAcao));
		($strAcao == "") ? $strAcao = "aviso" : NULL;
		$strTitulo = $prTitulo;
		$strAviso  = $prAviso;
	  
	    if($prFlagHTML != 0){ 
			echo("<html>
				  	<head>
				  		<title></title>
						<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
						<link href=\"../_tradeunion/_css/".CFG_SYSTEM_NAME.".css\" rel=\"stylesheet\" type=\"text/css\">
					</head>");
			echo("<body style=\"margin:8px;\" text=\"#000000\" bgcolor=\"#FFFFFF\" ");		
			if($prBackground == "default") {
				echo("background=\"../_tradeunion/img/bgFrame_".CFG_SYSTEM_THEME."_main.jpg\"");
			}
			else {
				echo("background=\"" . $prBackground . "\"");
			}
			echo(" >");
		}
		echo("<center>");
		athBeginWhiteBox("100%"); //450
		echo("
			<table width=\"100%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
			<tr>
			<td valign=\"top\" width=\"1%\"><img src=\"../_tradeunion/img/mensagem_" .  $strAcao . ".gif\" hspace=\"5\"></td>
			<td width=\"99%\">
				<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr><td style=\"font-size:14px;padding-left:5px;padding-bottom:5px;\"><b>" . $strTitulo . "</b></td></tr>
					<tr><td class=\"padrao_gde\" style=\"padding:10px 0px 10px 5px;\"><b>" . $strAviso . "</b></td></tr>
					<tr><td height=\"1\" bgcolor=\"#BDBDBF\"></td></tr>
					<tr><td style=\"padding:10px 0px 10px 5px;\">" . $prAdText . "</td></tr>
					<tr><td align=\"right\" class=\"comment_peq\">" . basename($_SERVER["PHP_SELF"]) . "</td></tr>
				</table>
			</td>
			</tr>");
  		
  		if($prHyperlink != "") { echo("<tr><td align=\"right\" colspan=\"2\"><button onClick=\"location.href='" . $prHyperlink . "'\">Ok</button></td></tr>"); }  
  		echo("</table>");
  		athEndWhiteBox();
  		echo("</center><br>");
  		if($prFlagHTML != 0) { echo("</body></html>"); }
	}
	
	/* -------------------------------------------------------------------------------------------------------------- */
	/* INI - Funções de LAYOUT -------------------------------------------------------------------------------------- */
	/* -------------------------------------------------------------------------------------------------------------- */
	function athBeginFloatingBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
		$strWidthTotal = (strpos($prWidth,"px") !== false) ? $prWidth : $prWidth . "px";
		$prHeight = ($prHeight != "" && strpos($prHeight,"px") !== false) ? $prHeight : $prHeight . "px";
		$strWidthConteudo = (strpos($prWidth,"%") !== false) ? $prWidth : intval($prWidth - 18) . "px";
		$auxStr =	" 
		<div class=\"dialog_glass\" style=\"width:" . $strWidthTotal . "; height:" . $prHeight . ";\">
			<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			<div class=\"center\">
				<div class=\"conteudo\" style=\"width:" . $strWidthConteudo . ";  height:" . $prHeight . ";\">";
		if($prTitulo != "" && $prHeadBGColor != ""){
			$auxStr .= "
					<div class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidthConteudo . "px;\">
						<span style='margin-left:4px;'>" . $prTitulo . "</span>
					</div>";
		}
		if ($prEcho) { echo($auxStr); } else { return($auxStr); }
	}
	
	function athEndFloatingBox($prEcho=true){
	   $auxStr = "	 
				</div>
			</div>
			<div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		</div>	";
	  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
	}
	 
	function athBeginShapeBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
		$strWidthTotal = (strpos($prWidth,"px") !== false) ? $prWidth : $prWidth . "px";
		$prHeight = ($prHeight != "" && strpos($prHeight,"px") !== false) ? $prHeight : $prHeight . "px";
	
		$strWidthConteudo = (strpos($prWidth,"%") !== false) ? $prWidth : intval($prWidth - 18) . "px";
		$auxStr =	" 
		<div class=\"dialog_shape\" style=\"width:" . $strWidthTotal . "; height:" . $prHeight . ";\">
			<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			<div class=\"center\">
				<div class=\"conteudo\" style=\"width:" . $strWidthConteudo . ";  height:" . $prHeight . ";\">";
		if($prTitulo != "" && $prHeadBGColor != ""){
			$auxStr .= "
					<div class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidthConteudo . "px;\">
						<span style='margin-left:4px;'>" . $prTitulo . "</span>
					</div>";
		}
		if ($prEcho) { echo($auxStr); } else { return($auxStr); }
	}
	
	function athEndShapeBox($prEcho=true){
	   $auxStr = "	 
				</div>
			</div>
			<div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		</div>	";
	  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
	}
	 
	function athBeginWhiteBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
		$strWidthTotal = (strpos($prWidth,"px") !== false) ? $prWidth : $prWidth . "px";
		$prHeight = ($prHeight != "" && strpos($prHeight,"px") !== false) ? $prHeight : $prHeight . "px";
		$strWidthConteudo = (strpos($prWidth,"%") !== false) ? $prWidth : intval($prWidth - 18) . "px";
		$auxStr =	" 
		<div class=\"dialog_white\" style=\"width:" . $strWidthTotal . "; height:" . $prHeight . ";\">
			<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			<div class=\"center\">
				<div class=\"conteudo\" style=\"width:" . $strWidthConteudo . ";  height:" . $prHeight . ";\">";
		if($prTitulo != "" && $prHeadBGColor != ""){
			$auxStr .= "
					<div class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidthConteudo . "px;\">
						<span style='margin-left:4px;'>" . $prTitulo . "</span>
					</div>";
		}
		if ($prEcho) { echo($auxStr); } else { return($auxStr); }
	}
	
	function athEndWhiteBox($prEcho=true){
	   $auxStr = "	 
				</div>
			</div>
			<div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		</div>	";
	  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
	}
	
	function athBeginClassBox($prClass, $prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
		$strWidthTotal = (strpos($prWidth,"px") !== false) ? $prWidth : $prWidth . "px";
		$prHeight = ($prHeight != "" && strpos($prHeight,"px") !== false) ? $prHeight : $prHeight . "px";
		$strWidthConteudo = (strpos($prWidth,"%") !== false) ? $prWidth : intval($prWidth - 18) . "px";
		$auxStr =	" 
		<div class=\"" . $prClass . "\" style=\"width:" . $strWidthTotal . "; height:" . $prHeight . ";\">
			<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			<div class=\"center\">
				<div class=\"conteudo\" style=\"width:" . $strWidthConteudo . ";  height:" . $prHeight . ";\">";
		if($prTitulo != "" && $prHeadBGColor != ""){
			$auxStr .= "
					<div class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidthConteudo . "px;\">
						<span style='margin-left:4px;'>" . $prTitulo . "</span>
					</div>";
		}
		if ($prEcho) { echo($auxStr); } else { return($auxStr); }
	}
	
	function athEndClassBox($prEcho=true){
	   $auxStr = "	 
				</div>
			</div>
			<div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		</div>	";
	  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
	}
	/* -------------------------------------------------------------------------------------------------------------- */
	/* FIM - Funções de LAYOUT -------------------------------------------------------------------------------------- */
	/* -------------------------------------------------------------------------------------------------------------- */
	

/* Retorna se um valor (string) represetna uma data */
function is_date($prDate){
	if(isset($prDate) && !is_null($prDate) && $prDate != "") {
		$arrDate = explode("-",$prDate);
		if(isset($arrDate[0]) && isset($arrDate[1]) && isset($arrDate[2])) {
			$retValue = checkdate((int) $arrDate[1], (int) $arrDate[2], (int) substr($arrDate[0],0,2));
		}
		else { $retValue = false; }
	}	
	else { $retValue = false; }
	return($retValue);
}
	
/* Retorna a data (string) no formato do idioma especificado */
function cDate($prIdioma, $prDate, $prDataHora){
	$retValue = NULL;
	if($prDate != ""){
		(strpos($prDate,"-")) ? $arrDate = explode("-",$prDate) : $arrDate = explode("/",$prDate);
		
		$strHMS		= substr($arrDate[2],5);
		$arrDate[2] = substr($arrDate[2],0,4);
		
		if(strtoupper($prIdioma) == "EN") { $retValue =  $arrDate[2] . "-" . $arrDate[0] . "-" . $arrDate[1]; }
		elseif(strtoupper($prIdioma) == "ES" || strtoupper($prIdioma) == "PTB") { $retValue =  $arrDate[2] . "-" . $arrDate[1] . "-" . $arrDate[0]; }
		
		if($prDataHora){
			($strHMS == "") ? $strHMS = "00:00:00" : NULL ;
			if(strpos(strtoupper($strHMS),"PM")){
				$arrHora = explode(":",$strHMS);
				$strHMS  = $arrHora[0] + 12 . ":" . $arrHora[1] . ":" . substr($arrHora[2],0,2); 
			}elseif(strpos(strtoupper($strHMS),"AM")){
				$strHMS  = substr($strHMS,0,8);
			}
			$retValue .= " " . $strHMS;
		}
	}
	return($retValue);
}

/* Retorna a data  (date) no formato do idioma especificado */
function dDate($prIdioma, $prDate, $prDataHora){
	$retValue = "";
	$strDataHora = "";
	
	if(is_date($prDate)){
		switch(strtoupper($prIdioma)){
			case "EN":
				if($prDataHora){ 
					$strDataHora = " h:i:s A";
					if(!strpos($prDate,":")){ $prDate .= "00:00:00"; }
				 } 
				$retValue = date("m/d/Y".$strDataHora,strtotime($prDate));
			break;
			case "PTB":
			 	if($prDataHora){ 
					$strDataHora = " H:i:s";
					if(!strpos($prDate,":")){ $prDate .= "00:00:00"; }
				 } 
				$retValue = date("d/m/Y".$strDataHora,strtotime($prDate));
			break;
			case "ES":
				if($prDataHora){ 
					$strDataHora = " h:i:s A";
					if(!strpos($prDate,":")){ $prDate .= "00:00:00"; }
				 } 
				$retValue = date("d/m/Y".$strDataHora,strtotime($prDate));
			break;
			case "DEFAULT":
				if($prDataHora){ 
					$strDataHora = " H:i:s";
					if(!strpos($prDate,":")){ $prDate .= "00:00:00"; }
				 } 
				$retValue = date("Y-m-d".$strDataHora,strtotime($prDate));
			default:
				$retValue = "";
			break;
		}
	}
	return($retValue);
}

function diffMes($prData1, $prData2){
//Calcula a diferença de meses entre duas datas
//o retorno da função é o numero de meses. 
//08/11/2011 By GS
	$data1 = $prData1; 
	$arr = explode('/',$data1); 
	
	$data2 = $prData2; 
	$arr2 = explode('/',$data2); 
	
	$dia1 = $arr[0]; 
	$mes1 = $arr[1]; 
	$ano1 = $arr[2]; 
	
	$dia2 = $arr2[0]; 
	$mes2 = $arr2[1]; 
	$ano2 = $arr2[2]; 
	
	$a1 = ($ano1 - $ano2)*12;
	$m1 = ($mes1 - $mes2);
	$m3 = ($m1 + $a1);
	return $m3;
}
/* Recebe um valor float/double no formato 1000000.00 e retorna  o valor float/double COMO STRING no formato 1.000.000,00 (by Aloisio) */
function FloatToMoeda($valor) {
	$result = number_format((double) $valor, 2, ',', '.');
	return $result;
}


/* Recebe um valor float/double COMO STRING no formato 1.000.000,00  e retorna o valor float/double correspondente no formato 1000000.00 ( by Aloisio) */
function MoedaToFloat($valor) {
	$cont = strlen($valor);	
	$result = $valor;
	for($i=0; $i< $cont; $i++) { $result = str_replace('.','',$result);	}
	$result = str_replace(',','.',$result);
	return $result;
}

/* Adiciona 'valores' (dias, meses, anos, horas...) a uma data */
function dateAdd($interval, $number, $date, $datetime=false) {
    $datearray	= getdate(strtotime($date));
    $hours		= $datearray['hours'];
    $minutes	= $datearray['minutes'];
    $seconds	= $datearray['seconds'];
    $month		= $datearray['mon'];
    $day		= $datearray['mday'];
    $year		= $datearray['year'];

    switch(strtolower($interval)) {
        case 'yyyy'	: $year+=$number;		break;
        case 'q'	: $year+=($number*3);	break;
        case 'm'	: $month+=$number;      break;
        case 'y'	:
		case 'd'	:
        case 'w'	: $day+=$number;		break;
        case 'ww'	: $day+=($number*7);	break;
        case 'h'	: $hours+=$number;		break;
        case 'n'	: $minutes+=$number;	break;
        case 's'	: $seconds+=$number;	break;
    }
    $strFormatDate	= ($datetime) ? "Y-m-d H:i:s" : "Y-m-d";
	$retValue		= date($strFormatDate,mktime(0,0,0,$month,$day,$year));
	return $retValue;
}	


function getVarEntidade($probjConn, $prIDVar) {
	$strLocalSQL = " SELECT valor FROM sys_var_entidade WHERE id_var = '" . $prIDVar . "' ";
	
	$objLocalResult = $probjConn->query($strLocalSQL);
	$objLocalRS = $objLocalResult->fetch();
	
	if($objLocalRS !== array())
		$Valor = getValue($objLocalRS, "valor");
	else 
		$Valor = "";
	$objLocalResult->closeCursor();
	
	return($Valor);
}

/* Retorna data  atual */
function dateNow()	{ return(date("Y-m-d")); }

/* Retorna hora atual */
function timeNow()	{ return(date("H:i:s")); }

/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções auxiliares de TRADUÇÃO ------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
function getTText($prKeyWord, $prWordMode){
	global $objLang;
	
	$retValue = $objLang->GetString($prKeyWord);
	if(is_null($retValue) || $retValue == ""){
		$retValue   = "<i>" . $prKeyWord . "</i>"; /* para identificar que este índice não foi encontrado! */
		$prWordMode = 0;
	}
	switch($prWordMode){
		case 1: $retValue = ucwords($retValue);		break;
		case 2: $retValue = strtoupper($retValue);	break;
		case 3: $retValue = strtolower($retValue);	break;
	}
	return($retValue);
}

function langIndexComment($strIndex, $strFile){
	$retValue = "";
	if(preg_match("/>(" . $strIndex . ")[ ]+'/",$strFile)){ /* Verifica se a linha no arquivo modelo existe */
		$retValue = preg_replace("/(.*)>". $strIndex . "[ ]*('*)|('*\\r*)|(\\n.*)/","",substr($strFile,strpos($strFile,">" . $strIndex)));
	}
	return($retValue);
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções auxziliares de TRADUÇÃO ------------------------------------------------------------------------ */
/* -------------------------------------------------------------------------------------------------------------- */


function mensagem_local($prTitulo, $prAviso, $prAdText="", $prHyperlink="", $prAcao="standardinfo", $prFlagHTML=0, $prBackground="default"){
  global $objConn;
  if(strpos(strtolower($prAcao),"standard") === false) {
	$objLangLocal = new phpMultiLang("../../_tradeunion/_database/errlang/","../../_tradeunion/_database/errlang/");
	
	if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { $locale = CFG_LANG; }
	else{
		switch(CFG_LANG){
	        case "ptb": $locale = "pt_BR"; break;
	        case "en":  $locale = "en_US"; break;
	        case "es":  $locale = "es_ES"; break;
	    }
	}
	$objLangLocal->AssignLanguage(CFG_LANG,NULL,array("LC_ALL",$locale));
	$objLangLocal->AssignLanguageSource(CFG_LANG,CFG_LANG . ".lang",3600);
		
	$objLangLocal->SetLanguage(CFG_LANG,false);
	
	$strAcao   = $prAcao;
	$strTitulo = $objLangLocal->GetString($prTitulo);
	$strAviso  = $objLangLocal->GetString($prAviso);
	$objLang = NULL;
  }
  else {
	$strAcao   = str_replace("standard","",strtolower($prAcao));
	($strAcao == "") ? $strAcao = "aviso" : NULL;
    $strTitulo = $prTitulo;
	$strAviso  = $prAviso;
  }
  
  // Tratamento para verificação se há mensagem de aviso em EXCEPTION DE BANCO
  // Quando uma Exceptiion de DB tiver a indicação na string do erro, esta indicação
  // será tratada para exibição da imagem correspondente na mensagem
  // Exemplo: ... 
  // IF(var_cod_produto IS NULL)THEN
  //   	RAISE EXCEPTION '[KPS_AVISO]Não foi encontrado um produto vigente do tipo Credencial.';
  // ...
  $strFlagMSG = "";
  $strFlagMSG = (stristr($prAdText,"[KPS_AVISO]")) ? "aviso" : $strFlagMSG;
  $strFlagMSG = (stristr($prAdText,"[KPS_ERRO]") ) ? "erro"  : $strFlagMSG;
  $strFlagMSG = (stristr($prAdText,"[KPS_INFO]") ) ? "info"  : $strFlagMSG;
  $strAcao    = ($strFlagMSG != "") ? $strFlagMSG : $strAcao;
  // Verifica se o FLAG está NULO
  if($strFlagMSG != ""){
    $prAdText   = getError($objConn);
	$strREPLACE = ("[KPS_".strtoupper($strFlagMSG)."]");
	$prAdText   = str_replace($strREPLACE,"",$prAdText);
	$prAdText   = str_replace("ERRO:","",$prAdText);
  }
  
  if($prFlagHTML != 0){ 
    echo("<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>");
    echo("<link href='../../_tradeunion/_css/tradeunion.css' rel='stylesheet' type='text/css'></head>");
	echo("<body style='margin:8px;' text='#000000' bgcolor=\"#FFFFFF\" ");		
	if ($prBackground == "default") { echo("background=\"../../_tradeunion/img/bgFrame_imgVGREY_main.jpg\""); }
	else { echo("background='" . $prBackground . "'"); }
	echo(" >");
  }
  echo("<center>");
  athBeginWhiteBox("100%"); //450
  echo("
		<table width='100%' align='center' border='0' cellpadding='5' cellspacing='0'>
		  <tr>
			<td valign='top' width='1%'><img src='../../_tradeunion/img/mensagem_" .  $strAcao . ".gif' hspace='5'></td>
			<td width='99%'>
  				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr><td style='font-size:14px;padding-left:5px;padding-bottom:5px;  text-align:left;'><b>" . $strTitulo . "</b></td></tr>
					<tr><td class='padrao_gde' style='padding:10px 0px 10px 5px; text-align:left;'><b>");
					
					if($strFlagMSG != ""){
						// $prAdText = str_replace("ERRO:","",$prAdText);
						// $prAdText = str_replace("[KPS_AVISO]","",$prAdText);
						echo($prAdText."</b></td></tr><tr><td height='1' bgcolor='#BDBDBF'></td></tr>");
					}else{
						echo($strAviso."</b></td></tr><tr><td height='1' bgcolor='#BDBDBF'></td></tr>");
						echo("<tr><td style='padding:10px 0px 10px 5px; text-align:left;'>" . $prAdText . "</td></tr>");
					}
					
					echo("<tr><td align='right' class='comment_peq'>" . basename($_SERVER["PHP_SELF"]) . "</td></tr>
				</table>
			</td>
		  </tr>
	  ");
  
  if($prHyperlink != "") { echo("<tr><td align='right' colspan='2'><button onClick=\"location.href='" . $prHyperlink . "'\">Ok</button></td></tr>"); }  
  echo("</table>");
  athEndWhiteBox();
  echo("</center><br>");

  if($prFlagHTML != 0) { echo("</body></html>"); }
}

function getCliente(){
	$strDir = getcwd();
	$arrDir = explode('/', $strDir);
	$strCliente = $arrDir[sizeof($arrDir)-2]; 
	
	return($strCliente);
}

function sendEmail($prFrom, $prTo, $prCc, $prBcc, $prSubject, $prMsg, $prHtmlFlag){
	include_once("../../_tradeunion/_class/mail/phpmailer.php");
	
	$mail = new PHPMailer();
	
	//ENVIAR VIA PHP MAIL
	//Se utilizar serviço SMTP (IsSMTP) teremos problemas de:
	//1) envio sem autenticação 
	//2) destinatários de mais um email (separados por , ou ;)
	$mail->IsMail();						
	
	$mail->Host 	 = CFG_SMTP_SERVER; //SERVIDOR DE SMTP, USE smtp.SeuDominio.com
	
	//Estamos sem autenticar porque o envio é mais rápido
	// by Clv/Aless/Luciano 14/09/2009
	$mail->SMTPAuth  = false; //ATIVA O /SMTP AUTENTICADO
	
	$mail->Username  = CFG_EMAIL_SENDER;		  //EMAIL PARA SMTP AUTENTICADO (pode ser qualquer conta de email do seu domínio)
	$mail->Password  = CFG_EMAIL_PASS;			  //SENHA DO EMAIL PARA SMTP AUTENTICADO
	$mail->SMTP_PORT = CFG_SMTP_PORT; 			  //PORTA do serviço
	
	$mail->From 	= $prFrom;					  //E-MAIL DO REMETENTE 
	$mail->FromName = CFG_SYSTEM_NAME; 			  //NOME DO REMETENTE
	$mail->AddAddress($prTo,"");				  //E-MAIL DO DESINATÁRIO, NOME DO DESINATÁRIO 
	$mail->AddBcc(CFG_EMAIL_AUDITORIA);			  //E-MAIL DO COPIA OCULTA
	$mail->WordWrap = 50;                         //ATIVAR QUEBRA DE LINHA
	$mail->IsHTML($prHtmlFlag);                   //ATIVA MENSAGEM NO FORMATO HTML
	$mail->Subject = $prSubject;                  //ASSUNTO DA MENSAGEM
	$mail->Body    = $prMsg;                      //MENSAGEM NO FORMATO HTML
	//$mail->AltBody = "Teste de envio via PHP";  //MENSAGEM NO FORMATO TXT

	//$mail->AddReplyTo("suporte@proevento.com.br"," Suporte Proevento "); //UTILIZE PARA DEFINIR OUTRO EMAIL DE RESPOSTA (opcional)
	
	if(!$mail->Send()){
		mensagem_local("err_mail_titulo","err_mail_desc1",$mail->ErrorInfo,"","erro",1);
		if($_SERVER["SERVER_NAME"] != "the_atena") { die(); }
	}
}

function emailNotify($prBody, $prSubject, $prEmails, $prFrom, $prDebug=""){
	$strCLIENTE = getCliente(); //Nome do cliente usando o path da pasta
	
	if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br"))
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/_" . CFG_SYSTEM_NAME;
	else
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/" . CFG_SYSTEM_NAME . "/_" . CFG_SYSTEM_NAME;
	
	$strBody =	"
	<html>
	<body>
		<table border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\" width=\"100%\" style=\"font:11px Tahoma;\">
			<tr><td colspan=\"2\"><img src=\"" . $strPATH . "/img/logomarca_mail.gif\" border=\"0\"></td></tr>
			<tr><td colspan=\"2\" height=\"10px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr><td colspan=\"2\" align=\"right\"><small>
			<b>Data:&nbsp;" . dDate(CFG_LANG,now(),true) . "</b></small></td></tr>
			<tr><td height=\"25px\" colspan=\"2\" style='font-weight:bold; padding-left:10px;' align='left'>".$strCLIENTE."</td></tr>
			<tr><td height=\"15px\" colspan=\"2\"></td></tr>
			<tr><td colspan=\"2\" style=\"padding-left:10px;\">" . $prBody . "</td></tr>
			<tr><td height=\"3px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr>
				<td align=\"right\" colspan=\"2\" style=\"font-family:Tahoma, Verdana; font-size:9;\">
				<div style=\"padding-right:5px; color:#999999\"><a href='http://www.athenas.com.br' target='_blank' style='color:#006699; font:none 11px; text-decoration:none;'>Copyright GRUPO PROEVENTO - Athenas Software & Systems</a></div>
				</td>
			</tr>
		</table>
	</body>
	</html>";
	
	if($prDebug != true) {  
		sendEmail($prFrom, $prEmails, "", "", $strCLIENTE.": ".$prSubject, $strBody, true);
	} else {
		echo("<script type=\"text/javascript\" language=\"javascript\">
				var objWin = window.open('','" . CFG_SYSTEM_NAME . "_EMAIL_POPUP','width=800,height=600,scrollbars=yes');
				objWin.document.write('" . str_replace("'","\'",str_replace("\r\n","",$strBody)) . "');
				objWin.document.close();
			  </script>");
	}
}

?>