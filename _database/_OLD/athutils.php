<?php
/***************************************************************************\
\			 fpsproject v 0.2.6 - (athUtils.php)							/
/			         Biblioteca de funções									\	
\			 																/
/			  Todas as funções de script criadas ou sobrescritas			\
\			  pela equipe de desenvolvimento estão nesse documento.			/
/			  Quando for inserir uma nova função aqui, observe as			\
\			  categorias em que elas estão conforme os comentários 			/
/			  abaixo.														\
\***************************************************************************/


/* Funções de Data e Hora - Início */

function now(){
	return(date("Y-m-d H:i:s"));
}

function dateAdd($interval, $number, $date, $datetime=false) {

    $datearray = getdate(strtotime($date));
    $hours = $datearray['hours'];
    $minutes = $datearray['minutes'];
    $seconds = $datearray['seconds'];
    $month = $datearray['mon'];
    $day = $datearray['mday'];
    $year = $datearray['year'];

    switch(strtolower($interval)) {
        case 'yyyy':
            $year+=$number;
            break;
        case 'q':
            $year+=($number*3);
            break;
        case 'm':
            $month+=$number;
            break;
        case 'y':
        case 'd':
        case 'w':
            $day+=$number;
            break;
        case 'ww':
            $day+=($number*7);
            break;
        case 'h':
            $hours+=$number;
            break;
        case 'n':
            $minutes+=$number;
            break;
        case 's':
            $seconds+=$number;
            break;
    }
    $strFormatDate = ($datetime) ? "Y-m-d H:i:s" : "Y-m-d";
	$retValue= date($strFormatDate,mktime(0,0,0,$month,$day,$year));
    
	return $retValue;
}

function dayDiff($prDate1,$prDate2){
	$retValue = abs(strtotime($prDate1) - strtotime($prDate2));
	$retValue = $retValue/(3600*24);
	return($retValue);
}

function dateNow(){
	return(date("Y-m-d"));
}

function timeNow(){
	return(date("H:i:s"));
}

function is_date($prDate){
	
	if(isset($prDate) && !is_null($prDate) && $prDate != ""){
		$arrDate = explode("-",$prDate);
		if(isset($arrDate[0]) && isset($arrDate[1]) && isset($arrDate[2])){
			$retValue = checkdate((int) $arrDate[1], (int) $arrDate[2], (int) substr($arrDate[0],0,2));
		}
		else{
			$retValue = false;
		}
		
	}	
	else{
		$retValue = false;
	}
	
	return($retValue);
}

function cDate($prIdioma, $prDate, $prDataHora){
	$retValue = NULL;
	if($prDate != ""){
		(strpos($prDate,"-")) ? $arrDate = explode("-",$prDate) : $arrDate = explode("/",$prDate);
		
		$strHMS		= substr($arrDate[2],5);
		$arrDate[2] = substr($arrDate[2],0,4);
		
		if(strtoupper($prIdioma) == "EN"){  	 
			$retValue =  $arrDate[2] . "-" . $arrDate[0] . "-" . $arrDate[1];
		}
		elseif(strtoupper($prIdioma) == "ES" || strtoupper($prIdioma) == "PTB"){
			$retValue =  $arrDate[2] . "-" . $arrDate[1] . "-" . $arrDate[0];
		}
		
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

/* Funções de Data e Hora - Fim	   */



/* Funções de layout - Início */

 
function athBeginFloatingBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
  $strWidth = (strpos($prWidth,"%") !== false) ?  $prWidth : intval($prWidth - 18);
  $auxStr =	" <div id=\"DialogGlass\" class=\"bordaBox\" style=\"width:" . $prWidth . "; height:" . $prHeight . ";\">
				<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
				<div class=\"center\">
					<div id=\"Conteudo\" class=\"conteudo\" style=\"width:" . $strWidth . ";  height:" . $prHeight . ";\">";
  if($prTitulo != "" && $prHeadBGColor != ""){
	$auxStr .= "<div id=\"GlassHeader\" class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidth . "px;\"><span style='margin-left:4px;'>" . $prTitulo . "</span></div>";
  }

  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

function athEndFloatingBox($prEcho=true){
   $auxStr = "	 </div>
			    </div>
			   <div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		     </div>	";
  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

function athBeginShapeBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true){
	$strWidth = (strpos($prWidth,"%") !== false) ?  $prWidth : intval($prWidth - 18);
	$auxStr = "
		 <div id=\"DialogShape\" class=\"bordaBox\" style=\"width:" . $prWidth . "; height:" . $prHeight . ";\">
			<div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			<div class=\"center\">
				<div id=\"Conteudo\" class=\"conteudo\" style=\"width:" . $strWidth . ";  height:" . $prHeight . ";\">";
	if($prTitulo != "" && $prHeadBGColor != ""){
	  $auxStr .= "<div id=\"ShapeHeader\" class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidth . "px;\"><span style='margin-left:4px;'>" . $prTitulo . "<span></div>";
	}
  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

function athEndShapeBox($prEcho=true){
  $auxStr = "  </div>
			  </div>
			 <div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		    </div> ";
  if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

function athBeginWhiteBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="",$prEcho=true){
  $strWidth = (strpos($prWidth,"%") !== false) ?  $prWidth : intval($prWidth - 18);
  $auxStr = " <div id=\"DialogWhite\" class=\"bordaBox\" style=\"width:" . $prWidth . "; height:" . $prHeight . ";\">
			   <div class=\"b1\"></div><div class=\"b2\"></div><div class=\"b3\"></div><div class=\"b4\"></div>
			    <div class=\"center\">
			  	 <div id=\"Conteudo\" class=\"conteudo\" style=\"width:" . $strWidth . ";  height:" . $prHeight . ";\">";
  if($prTitulo != "" && $prHeadBGColor != ""){
	  $auxStr .= "<div id=\"WhiteHeader\" class=\"header\" style=\"background-color:" . $prHeadBGColor . ";width:" . $strWidth . "px;\"><span style='margin-left:4px;'>" . $prTitulo . "<span></div>";
  }  
 if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

function athEndWhiteBox($prEcho=true){
  $auxStr = "	</div>
			  </div>
			  <div class=\"b4\"></div><div class=\"b3\"></div><div class=\"b2\"></div><div class=\"b1\"></div>
		    </div>";
 if ($prEcho) {echo($auxStr);} else {return($auxStr);}
}

// Menu pureCSS over TABLESORT
function athBeginCssMenu(){
	echo("\n<div class=\"cssMenuDiv\"><ul class=\"cssMenu cssMenum\">");
}

function athBeginCssSubMenu(){
	echo("\n<ul class=\"cssMenum\">");
}

function athCssMenuAddItem($prLink,$prTarget,$prTitle,$prNextIsSub=0){
	$strLink   = ($prLink == "") ? "#" : $prLink;
	$strTarget = $prTarget;
	$strTitle  = $prTitle;
	$intNextIsSub = $prNextIsSub;
	echo("\n<li class=\"cssMenui\"><a class=\"cssMenui\" href=\"".$strLink."\" target=\"".$strTarget."\">");
	echo($intNextIsSub == 1) ? "<span>" : "" ;
	echo("&nbsp;" . $strTitle . "&nbsp;");
	echo($intNextIsSub == 1) ? "</span>" : "" ;
	echo($intNextIsSub == 1) ? "<![if gt IE 6]></a><![endif]>" : "</a>";
	echo($intNextIsSub != 1) ? "</li>" : "";	
}

function athEndCssSubMenu(){
	echo("\n</ul></li>");
}

function athEndCssMenu(){
	echo("\n</ul></div>");
}
/* Funções de layout - Fim    */



/* Funções de Sistema - Início */

function psVersion(){
	$resDir = opendir("../../");
	chdir("../../");
	$intDataArquivoMaior = 0;
	while(false !== ($strFile = readdir($resDir))) {
	//	if(file_exists($strFile)){
			if(ereg("(.*)\.txt$",$strFile)){ 
				$intDataArquivo = date ("YmdHis", filemtime($strFile));
				if($intDataArquivo > $intDataArquivoMaior ){
					$intDataArquivoMaior = $intDataArquivo;
					$strNomeArquivo = $strFile;
				}
			}
		}
	//}
	echo(str_replace(".txt","",$strNomeArquivo));
	closedir($resDir);
}

function findLogicalPath($prPasta = ""){
	$retValue  = "http://" . $_SERVER["HTTP_HOST"] . "/";
	$retValue .= ($_SERVER["HTTP_HOST"] == "www." . CFG_SYSTEM_NAME . ".com.br") ? $prPasta : CFG_SYSTEM_NAME . "/" . $prPasta;
	return($retValue);
}

function findPhysicalPath($prPasta = "") {
	$retValue  = strtolower(realpath("../../"));
	$retValue .= (DIRECTORY_SEPARATOR == "/") ? "/" . $prPasta : "\\" . $prPasta;
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

function montaArraySiteInfo(&$objConn ,&$pr_arrScodi, &$pr_arrSdesc){

  $strSQL    = "SELECT titulo, descricao FROM sys_info ";
  $objResult = $objConn->query($strSQL);
  
  $strAuxScodi = "";
  $strAuxSdesc = "";
  
  foreach($objResult as $objRS){
    $strAuxScodi .= "|" . $objRS["titulo"];
    $strAuxSdesc .= "|" . $objRS["descricao"];
  }
  
  $pr_arrScodi = explode("|",$strAuxScodi);
  $pr_arrSdesc = explode("|",$strAuxSdesc);

  $objResult = NULL;
}

function montaArraysContainer($prStrSQL, &$prArrScodi, &$prArrSdesc, &$objConnLocal){
  $strSQLLocal     = $prStrSQL;
  $objResultLocal  = $objConnLocal->query($strSQLLocal);
  
  $auxStrScodi = "";
  $auxStrSdesc = "";
  
  foreach($objResultLocal as $objRSLocal){
    $auxStrScodi .= "|" . $objRSLocal[0];
    $auxStrSdesc .= "|" . $objRSLocal[1];
  }

  $prArrScodi = explode("|",$auxStrScodi);
  $prArrSdesc = explode("|",$auxStrSdesc);

  $objResultLocal = NULL;
}

function arrayIndexOf($prArray, $prCampo){
	$intIndex = 0;
	
	for($i=0;$i < count($prArray);$i++){
	   	if($prArray[$i] == $prCampo){
	   		$intIndex = $i;
			break;
	  	}
	}
	
	return($intIndex);
}

function insertTagSQL($prParam){
	$retValue = "";
	
	$retValue = str_replace("%","<ASLW_PERCENT>",$prParam);
	$retValue = str_replace("#","<ASLW_SHARP>",$retValue);
	$retValue = str_replace("'","<ASLW_APOSTROFE>",$retValue);
	$retValue = str_replace("\"","<ASLW_ASPAS>",$retValue);
	$retValue = str_replace("@","<ASLW_ARROBA>",$retValue);
	$retValue = str_replace("?","<ASLW_INTERROGACAO>",$retValue);
	$retValue = str_replace("&","<ASLW_ECOMERCIAL>",$retValue);
	$retValue = str_replace(":","<ASLW_DOISPONTOS>",$retValue);
	$retValue = str_replace("+","<ASLW_PLUS>",$retValue);
	$retValue = str_replace("-","<ASLW_MINUS>",$retValue);
	
	return($retValue);
}

function removeTagSQL($prParam){
	$retValue = "";

	$retValue = str_replace("<ASLW_PERCENT>","%",$prParam);
	$retValue = str_replace("<ASLW_SHARP>","#",$retValue);
	$retValue = str_replace("<ASLW_APOSTROFE>","'",$retValue);
	//$retValue = str_replace("<ASLW_ASPAS>","\"",$retValue);
	$retValue = str_replace("<ASLW_ASPAS>","&quot;",$retValue);
	$retValue = str_replace("<ASLW_ARROBA>","@",$retValue);
	$retValue = str_replace("<ASLW_INTERROGACAO>","?",$retValue);
	$retValue = str_replace("<ASLW_ECOMERCIAL>","&",$retValue);
	$retValue = str_replace("<ASLW_DOISPONTOS>",":",$retValue);
	$retValue = str_replace("<ASLW_PLUS>","+",$retValue);
	$retValue = str_replace("<ASLW_MINUS>","-",$retValue);

	return($retValue);
}

function insertTagParam($prParam){
	$retValue = "";
	
	$retValue = str_replace("&","<PARAM_EC>",$prParam);
	$retValue = str_replace("%","<PARAM_PC>",$retValue);
	$retValue = str_replace("?","<PARAM_QM>",$retValue);
	
	return($retValue);
}

function removeTagParam($prParam){
	$retValue = "";
	
	$retValue = str_replace("<PARAM_EC>","&",$prParam);
	$retValue = str_replace("<PARAM_PC>","%",$retValue);
	$retValue = str_replace("<PARAM_QM>","?",$retValue);
	
	return($retValue);
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

function barcode39($prBarCode, $boolExibirCodigo=true, $prAltura="", $prLargura="", $prCaminho="../img/"){

$retValue = "";

$a = array();

$a[1]="1wnnwnnnnw";
$a[2]="2nnwwnnnnw";
$a[3]="3wnwwnnnnn";
$a[4]="4nnnwwnnnw";
$a[5]="5wnnwwnnnn";
$a[6]="6nnwwwnnnn";
$a[7]="7nnnwnnwnw";
$a[8]="8wnnwnnwnn";
$a[9]="9nnwwnnwnn";
$a[10]="0nnnwwnwnn";
$a[11]="Awnnnnwnnw";
$a[12]="Bnnwnnwnnw";
$a[13]="Cwnwnnwnnn";
$a[14]="Dnnnnwwnnw";
$a[15]="Ewnnnwwnnn";
$a[16]="Fnnwnwwnnn";
$a[17]="Gnnnnnwwnw";
$a[18]="Hwnnnnwwnn";
$a[19]="Innwnnwwnn";
$a[20]="Jnnnnwwwnn";
$a[21]="Kwnnnnnnww";
$a[22]="Lnnwnnnnww";
$a[23]="Mwnwnnnnwn";
$a[24]="Nnnnnwnnww";
$a[25]="Ownnnwnnwn";
$a[26]="Pnnwnwnnwn";
$a[27]="Qnnnnnnwww";
$a[28]="Rwnnnnnwwn";
$a[29]="Snnwnnnwwn";
$a[30]="Tnnnnwnwwn";
$a[31]="Uwwnnnnnnw";
$a[32]="Vnwwnnnnnw";
$a[33]="Wwwwnnnnnn";
$a[34]="Xnwnnwnnnw";
$a[35]="Ywwnnwnnnn";
$a[36]="Znwwnwnnnn";
$a[37]="-nwnnnnwnw";
$a[38]=".wwnnnnwnn";
$a[39]=" nwwnnnwnn";
$a[40]="*nwnnwnwnn";
$a[41]="\$nwnwnwnnn";
$a[42]="/nwnwnnnwn";
$a[43]="+nwnnnwnwn";
$a[44]="%nnnwnwnwn";

$intNarrow = $prLargura;

if(!is_numeric($intNarrow)){ 
  $intNarrow=1.5;
}

$intHeight = $prAltura;
if(!is_numeric($intHeight)){
	$intHeight = 15;
}

$strBarCode = $prBarCode;
$strBarCode = "*" . $strBarCode . "*";
$strConv = "";

for($t=0;$t<strlen($strBarCode);$t++){
	for($s=1;$s<=44;$s++){
		if(substr($strBarCode,$t,1) == substr($a[$s],0,1)){
			$strConv = $strConv . substr($a[$s],1) . "s";
		}
	}
}

$b=1;


for($t=0;$t<strlen($strConv);$t++){
	if(substr($strConv,$t,1) == "n"){
		if($b == 1){ $retValue .= "<img src=" . $prCaminho . "barcode39_shim_black.gif width=" . $intNarrow . " height=" . $intHeight . ">"; }
		if($b == 0){ $retValue .= "<img src=" . $prCaminho . "barcode39_shim.gif width=" . $intNarrow . " height=" . $intHeight . ">"; }
		$b++;
		if($b == 2){ $b=0; }
	}

	if(substr($strConv,$t,1) == "w"){
		if($b == 1){ $retValue .= "<img src=" . $prCaminho . "barcode39_shim_black.gif width=" . $intNarrow*2 . " height=" . $intHeight . ">"; }
		if($b == 0){ $retValue .= "<img src=" . $prCaminho . "barcode39_shim.gif width=" . $intNarrow*2 . " height=" . $intHeight . ">"; }
		$b++;
		if($b == 2){ $b=0; }
	}

	if(substr($strConv,$t,1)=="s"){
		$retValue .= "<img src=" . $prCaminho . "barcode39_shim.gif width=" . $intNarrow . " height=" . $intHeight . ">";
		$b = 1;
	}
}

if($boolExibirCodigo){ $retValue .= "<br>*" . $prBarCode . "*<br>"; }

return($retValue);

}

function barCode25($prValor){

define("FINO"  ,1);
define("LARGO" ,3);
define("ALTURA",50);

if(empty($arrBarCodes[0])) {
  $arrBarCodes[0] = "00110";
  $arrBarCodes[1] = "10001";
  $arrBarCodes[2] = "01001";
  $arrBarCodes[3] = "11000";
  $arrBarCodes[4] = "00101";
  $arrBarCodes[5] = "10100";
  $arrBarCodes[6] = "01100";
  $arrBarCodes[7] = "00011";
  $arrBarCodes[8] = "10010";
  $arrBarCodes[9] = "01010";
  //for f1 = 9 to 0 step -1
  for($intF1=9;$intF1>=0;$intF1--){
    //for f2 = 9 to 0 Step -1
	for($intF2=9;$intF2>=0;$intF2--){
      $intF = $intF1 * 10 + $intF2;
      $strTexto = "";
      //for i = 1 To 5
	  for($intI=0;$intI<=5;$intI++){
		$strTexto += substr($arrBarCodes[$intF1], $intI, 1) + substr($arrBarCodes[$intF2], $intI, 1);
      }
      $arrBarCodes[$intF] = $strTexto;
    }
  }
}

//Desenho da barra


//Guarda inicial
$strCodigoBarra = "<img src=\"../img/boleto_p" . FINO . ".gif\" border=\"0\">
				   <img src=\"../img/boleto_b" . FINO . ".gif\" border=\"0\">
				   <img src=\"../img/boleto_p" . FINO . ".gif\" border=\"0\">
				   <img src=\"../img/boleto_b" . FINO . ".gif\" border=\"0\">
				   <img ";


$strTexto = $prValor;
if(strlen($strTexto) % 2 != 0) {
  $strTexto .= "0";
}

// Draw dos dados
while(intval(strlen($strTexto)) > 0) {
	$intI = intval(left($strTexto, 2)); 
	$strTexto = right($strTexto, strlen($strTexto) - 2);
	$intF = $arrBarCodes[$intI];
	for($intI=1;$intI<=10;$intI+=2) {
		$intF1 = (substr($intF, $intI, 1) == "0") ? FINO : LARGO;
		$strCodigoBarra .= "src='../img/boleto_p" . $intF1 . ".gif' border=\"0\"><img ";
	    
	    $intF2 = (substr($intF, $intI + 1, 1) == "0") ? FINO : LARGO;
		$strCodigoBarra .= "src='../img/boleto_b" . $intF2 . ".gif' border=\"0\"><img ";
	}
	if(strlen($strTexto) == 2) $strTexto = "";
}


// Draw guarda final
$strCodigoBarra .= "src=\"../img/boleto_p" . LARGO . ".gif\" border=\"0\">
			   <img src=\"../img/boleto_b" . FINO  . ".gif\" border=\"0\">
			   <img src=\"../img/boleto_p" . FINO  . ".gif\" border=\"0\">";

return($strCodigoBarra);

}
/* Funções de Sistema - Fim    */


/* Funções de String - Início */
function gerarSenha($prMaxNum, $prPar1){
	$retValue = "";
	
	if($prPar1 == 1) { $strValores = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"; }
	if($prPar1 == 2) { $strValores = "0,1,2,3,4,5,6,7,8,9"; }
	if($prPar1 == 3) { $strValores = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"; }
	
	$arrXArray = explode(",",$strValores);
	
	while(strlen($retValue) < $prMaxNum){
		$strAux = $arrXArray[rand(0,count($arrXArray)-1)];
		$retValue = $retValue . $strAux;
	}
	return(trim($retValue));
}

function left($prStr, $prLength) {
	return(substr($prStr, 0, $prLength));
}
 
function right($prStr, $prLength) {
	return(substr($prStr, -$prLength));
}
/* Funções de String - Fim    */


/* Funções matemáticas e de formatação de números - Início */
function formatcurrency($prVlrCurrency, $prDec=2){
	$retValue = $prVlrCurrency;
	
	if(!empty($retValue) && $retValue != "") { 
		$retValue = (strpos(",",$retValue) !== false) ? number_format($retValue, $prDec) : $retValue;
		$retValue = str_replace(".","",$retValue);
		$retValue = str_replace(",",".",$retValue);
	}
	
	return($retValue);
}
/* Funções matemáticas e de formatação de números - Fim     */


/* Funções de segurança - Início */
function verficarAcesso($intCodUsuario, $intCodApp, $strAppDir="", $strTpReturn="die"){
    $flagOk = true;
	
	//Se por algum motivo não consegue buscar o nome do sistema na SESSION então esta deve ter expirado
    if (getsession(CFG_SYSTEM_NAME . "_db_name")=="") { mensagem("err_session_expired_titulo", "err_session_expired_desc","","","erro","1"); die(); }

	$objConnLocal = abreDBConn(getsession(CFG_SYSTEM_NAME . "_db_name"));
	
	if(!is_null($intCodUsuario) && is_numeric($intCodUsuario) && !is_null($intCodApp) && is_numeric($intCodApp)){	
		
		if($strAppDir != ""){
			$strSQL = " SELECT cod_app 
						 FROM sys_app_direito , sys_app_direito_usuario 
						WHERE cod_app = " . $intCodApp . " 
						 AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito
						 AND sys_app_direito.id_direito = '" . $strAppDir . "'
						 AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
		}
		else{
			$strSQL = " SELECT cod_app_direito_usuario
						 FROM sys_app_direito , sys_app_direito_usuario 
						WHERE cod_app = " . $intCodApp . " 
						 AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito
						 AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
		}
		
		try{
			$objResult = $objConnLocal->query($strSQL);
			$intNumRows = $objResult->rowCount();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	else{
		$intNumRows = 0;
	}
	
	if($intNumRows == 0){
		try{
			$strSQL = " SELECT id_direito 
						 FROM sys_app_direito , sys_app_direito_usuario 
						WHERE cod_app = " . $intCodApp . " 
						 AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito 
						 AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
			$objResult2 = $objConnLocal->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
		
		$strErrDesc = "Ação a ser realizada:&nbsp;" . $strAppDir . "<br><br>
					   <div style=\"padding-left:10px;\">
					     &bull; Permissões desse usuário para esse módulo:<br>
						 <div style=\"padding-left:10px;\">";
		foreach($objResult2 as $objRS){
			$strErrDesc .= "- " . $objRS["id_direito"] . "<br>";
		}
		$strErrDesc .= "</div></div>";
		
		if($strTpReturn == "die"){
			mensagem("err_acesso_titulo","err_acesso_desc",$strErrDesc,"","erro",1);
			die();
		}else{
			$flagOk = false;
		}
				
		$objResult2->closeCursor();
	}
	
	$objResult->closeCursor();
	$objConnLocal = NULL;
	return($flagOk);
}

/* Funções de segurança - Fim    */

/* Funções auxliares de tradução - Início */

function getTText($prKeyWord, $prWordMode){
	global $objLang;
	
	$retValue = $objLang->GetString($prKeyWord);
	
	if(is_null($retValue) || $retValue == ""){
		$retValue   = "<i>" . $prKeyWord . "</i>"; // para identificar que este índice não foi encontrado!
		$prWordMode = 0;
	}
	
	switch($prWordMode){
		case 1: $retValue = ucwords($retValue);
				break;
		case 2: $retValue = strtoupper($retValue);
				break;
		case 3: $retValue = strtolower($retValue);
				break;
	}
	
	return($retValue);
}

function langIndexComment($strIndex, $strFile){
	$retValue = "";
	
	if(ereg(">(" . $strIndex . ")[ ]+'",$strFile)){ // Verifica se a linha no arquivo modelo existe
		$retValue = preg_replace("/(.*)>". $strIndex . "[ ]*('*)|('*\\r*)|(\\n.*)/","",substr($strFile,strpos($strFile,">" . $strIndex)));
	}
	
	return($retValue);
}

/* Funções auxliares de tradução - Fim */

/* Funçoes de logica de revista - Inicio*/
	function arquivoRelacionado($prNivel, $prCodigo, &$prObjConn){
	
		try{
		
			$strSQL = "	SELECT 
							cod_arquivo_relacionado, 
							codigo, 
							tipo, 
							arquivo, 
							descricao, 
							ordem, 
							titulo, 
							tamanho, 
							dtt_criacao
						FROM
							lj_arquivo_relacionado
						WHERE
							tipo <=> '". $prNivel ."'
						AND
							codigo = ".$prCodigo." 
						ORDER BY ORDEM";
							
			$objResult = $prObjConn->query( $strSQL );
			
			if($objResult->rowCount() > 0 ){
				echo ("<table width=\"360\" border=\"0\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">
<tr><td height=\"5\" class=\"titulo_promocao_mdo\" colspan=\"4\"><b>Arquivos Relacionados:</b></td></tr>");
				
				$intI = 0;
				foreach($objResult as $objRS){
					(($intI%2) == 0)? $strColor = "#F3F3F3" : $strColor = "#FFFFFF";
					
					$strAux = strtoupper(getValue($objRS,"arquivo"));
					if(strpos($strAux,"HTTP:") !== false){
						$strAux = getValue($objRS,"arquivo");
					}
					else{
						 $strAux = "../../apas/upload/".getValue($objRS,"arquivo");
					}
					echo("<tr style=\"cursor:pointer\">
							  <td width=\"2\" height=\"17\"></td>
							  <td align=\"left\" valign=\"middle\" bgcolor=".$strColor.">
								  <a href=".$strAux." target=\"_blank\" class=\'texto_corpo_mdo\'>
									<img src='../img/BulletArqRel2.gif' border='0' alt=''>&nbsp;".getValue($objRS,"titulo")."
								  </a>
							  </td>
							  <td align=\"left\" valign=\"middle\" class='texto_corpo_mdo' bgcolor=".$strColor.">".getValue($objRS,"tamanho")."</td>
						 </tr>");
					$intI++;
				}
				
				echo("</table>");

			}
			
			
		}
		catch(PDOException $e){
			mensagem("err_sql_title","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	
	}
	
	function linkRelacionado($prNivel, $prCodigo, &$prObjConn){
		
		try{
			$strSQL ="	SELECT 
							codigo_relacionado, 
							tipo_relacionado, 
							link, 
							rotulo
						FROM
							lj_relacoes
						WHERE
							codigo_relacionar = ".$prCodigo."
						AND
							tipo_relacionar <=> '".$prNivel."'
						ORDER BY ORDEM";
			$objResult = $prObjConn->query($strSQL);
			
			if($objResult->rowCount() > 0){
				$strCOR_PRI = "#FFFFFF";
				echo("<table width=\"360\" border=\"0\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\" background=\"../img/bgArqRel.gif\">
						 <tr>
							<td class=\"titulo_destaque_mdo\" colspan=\"3\" style='".$strCOR_PRI."'>Links relacionados:</td></tr>
						 <tr>
							<td height=\"5\" colspan=\"3\"></td></tr>
						 <tr>
						  <td width=\"10\"></td>
						  <td align=\"center\">
						    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">"
					);
				
				foreach($objResult as $objRS){
					if(getValue($objRS,'codigo_relacionado') != ""){
						if(getValue($objRS,'tipo_relacionado') != "MATERIA"){
							$strSQL1 = "SELECT titulo FROM lj_".getValue($objRS,'tipo_relacionado')." WHERE cod_".getValue($objRS,'tipo_relacionado')." = ".getValue($objRS,'codigo_relacionado');
						}
						else{
							$strSQL1 = "SELECT titulo FROM lj_".getValue($objRS,'tipo_relacionado');
						}
						$objResult1 = $prObjConn->query($strSQL1);
						if($objResult1->rowCount() > 0){
							$objRS1 = $objResult1->fetch();
							$strLink = "<a href='show".getValue($objRS,'tipo_relacionado').".php?var_chavereg=".getValue($objRS,'codigo_relacionado')."' class='texto_corpo_mdo'>".getValue($objRS1,"titulo")."</a>";
							echo $strLink;
							die;
						}
						$objResult1->closeCursor();
					}
					else{
						if(getValue($objRS,'rotulo') == "" || is_null(getValue($objRS,'rotulo')) ){
							$strRotulo = getValue($objRS,'link');
						}
						else{
							$strRotulo = getValue($objRS,'rotulo');
						}
						if(getValue($objRS,'link') != ""){
							if(strpos(strtolower(getValue($objRS,'link')),"javascript:") !== false){
								$strLink = "<a href=".getValue($objRS,"link")." class='texto_corpo_mdo'>".$strRotulo."</a>";
							}
							else{
								$strLink = "<a href=".getValue($objRS,"link")." class='texto_corpo_mdo' target='_blank'>".$strRotulo."</a>";
							}
						}
					}
					if($strLink != ''){
						echo("<tr>
								<td width=\"1%\" bgcolor=".$strCOR_PRI."><img src=\"../img/BulletLinksRel.gif\" hspace=\"3\" alt=\"\"></td>
								<td nowrap>".$strLink."</td>
							</tr>
							<tr>
								<td colspan=\"2\" height=\"5\"></td>
							</tr>");
					}
				}
				echo("</table>
				  </td>
				  <td width=\"10\"></td>
				 </tr>
				</table>");
			}

			
		}
		catch(PDOException $e){
			mensagem("err_sql_title","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
/* Funçoes de logica de revista - Fim*/

/* Funções wrappers - Início */

function redirect($prURL){
	header("Location:" . $prURL);
}

function request($prParam){
	(isset($_REQUEST[$prParam])) ? $retValue = $_REQUEST[$prParam] : $retValue = "";
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

function getsession($prParam){
	(isset($_SESSION[$prParam])) ? $retValue = $_SESSION[$prParam] : $retValue = "";
	return($retValue);
}

function setsession($prName, $prValue){
	if(is_null($prValue)){
	    session_unregister($_SESSION[$prName]);
		unset($_SESSION[$prName]);
	}
	else{
		$_SESSION[$prName] = $prValue;
	}
}

function getValue($prRS, $prFieldName, $prBoolQuote=true){ // serve para resultsets e arrays em geral
	$retValue = (isset($prRS[$prFieldName])) ?  html_entity_decode($prRS[$prFieldName]) : "";
	if($prBoolQuote){ $retValue = str_replace('"',"&quot;",$retValue); }
	return($retValue);
}

// prepara string para gravação no banco
function prepStr($prStr){
	$retValue = str_replace("'","''",$prStr);
	return($retValue);
}

/* REFERÊNCIAS EM ASP

'----------------------------
' Obtain database field value
'---------------- by Aless --
function GetValue(rs, strFieldName)
CONST bDebug = True
dim res
  on error resume next
  if rs is nothing then
  	GetValue = ""
  elseif (not rs.EOF) and (strFieldName <> "") then
    res = rs(strFieldName)
    if isnull(res) then 
      res = ""
    end if
    if VarType(res) = vbBoolean then
      if res then res = "1" else res = "0"
    end if
    GetValue = res
  else
    GetValue = ""
  end if
  if bDebug then response.write err.Description
  on error goto 0
end function

'----------------------------------------------
' Obtain specific URL Parameter from URL string
'---------------------------------- by Aless --
function GetParam(ParamName)
Dim auxStr
  if ParamName = "" then 
    auxStr = Request.QueryString
	if auxStr = Empty or Cstr(auxStr) = "" or isNull(auxStr) then auxStr = Request.Form
  else
   if Request.QueryString(ParamName).Count > 0 then 
     auxStr = Request.QueryString(ParamName)
   elseif Request.Form(ParamName).Count > 0 then
     auxStr = Request.Form(ParamName)
   else 
     auxStr = ""
   end if
  end if
  
  if auxStr = "" then
    GetParam = Empty
  else
    auxStr = Trim(Replace(auxStr,"'","''"))
    GetParam = auxStr
  end if
end function

*/


function MontaLinkGrade($pr_modulo, $pr_pagina, $pr_cod, $pr_img, $pr_title, $pr_extra) {
	$strIMG = "<img src='../img/" . $pr_img . "' border='0' title='" . $pr_title . "'>";
	$strA   = "<a href='../" . $pr_modulo . "/" . $pr_pagina . "?var_chavereg=" . $pr_cod . $pr_extra . "' style='cursor:pointer;'>" . $strIMG . "</a>";
	return($strA);
}
/* Funções wrappers - Fim    */

/* 
 ----------------------------------------------------- 
 getTextBetweenTags - Início
 ----------------------------------------------------- 
  @param string $pr_tag   O nome da tag
  @param string $pr_html  XML ou XHTML
  @param int    $pr_tp    (1-LoadXML *-LoadHTML)
  @return array

 Exemplo de uso: 

  $html = "<body><h1>Heading</h1><a 
           href='http://phpro.org'>PHPRO.ORG</a><p>
           paragraph here</p><p>Paragraph with a <a 
           href='http://phpro.org'>LINK TO PHPRO.ORG
           </a></p><p>This is a broken paragraph</body>";

  $content = getTextBetweenTags('a', $html);
  foreach( $content as $item ) { echo $item.'<br />'; }

 -------------------------------------------- by Aless - 
*/


function getTextBetweenTagsDOM($pr_tag, $pr_html, $pr_strict=0)
{
   $dom = new DOMDocument();

   if($pr_strict==1) {
     $dom->loadXML($pr_html);
   }
   else {
     $dom->loadHTML($pr_html);
   }

   // Descarta espaços em branco
   $dom->preserveWhiteSpace = false;

   $content = $dom->getElementsByTagname($pr_tag);

   $out = array();
   foreach ($content as $item) {
     $out[] = $item->nodeValue;
   }

  return $out;
}

function getTextBetweenTags($prValor, $prTagIni, $prTagFim, &$prPosIni, &$prPosFim) {
	$prPosIni = strpos($prValor, $prTagIni);
	$prPosFim = strpos($prValor, $prTagFim) + strlen($prTagFim);
	
	if ($prPosIni === false) $prPosIni = -1;
	if ($prPosFim === false) $prPosFim = -1;
	
	$Texto = "";
	if (($prPosIni != -1) && ($prPosFim != -1)) {
		$Texto = trim(substr($prValor, $prPosIni + strlen($prTagIni), $prPosFim - strlen($prTagFim) - ($prPosIni + strlen($prTagIni))));
	}
	return $Texto;
}

/*
 ----------------------------------------------------- 
 getTextBetweenTags - Fim
 ----------------------------------------------------- 
*/

// --------------------------------------------------------------------
// Recebe um valor float/double COMO STRING no formato 1.000.000,00 
// e retorna o valor float/double correspondente no formato 1000000.00
// ------------------------------------------------------ by Aloisio --
function MoedaToFloat($valor) {
	$cont = strlen($valor);	
	$result = $valor;
	for($i=0; $i< $cont; $i++){
		$result = str_replace('.','',$result);
	}
	$result = str_replace(',','.',$result);
	// return floatval($result);
	return $result;
}

// -------------------------------------------------------------
// Recebe um valor float/double no formato 1000000.00 e retorna 
// o valor float/double COMO STRING no formato 1.000.000,00
// ----------------------------------------------- by Aloisio --
function FloatToMoeda($valor) {
	$result = number_format($valor, 2, ',', '.');
	return $result;
}

?>