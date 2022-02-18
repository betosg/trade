<?php 
/* KERNELPS - Biblioteca de funções (athUtils.php) --------------------------------------------------------- */
/* --------------------------------------------------------------------------------------------------------- */
/* Todas as PHP criadas ou sobrescritas pela equipe para todos os sistemas do KERNELPS (funções gerais)      */ 
/* estão nesse documento.  Quando for inserir uma nova função aqui, observe as  categorias em que elas estão */
/* conforme os comentários abaixo.												                             */ 	
/* Funções específicas de cada projeto e usadas em seus respectivos módulos devem estar na STathutils.       */
/* ----------------------------------------------------------------------------- revised by Aless 17/05/11 - */
/*
 INI - ÍNDICE de funções -----------------------------------------------------------------------------------

 Data e Hora
	 now()		
	 dateNow()	
	 timeNow()	
	 dateAdd($interval, $number, $date, $datetime=false) 
	 dayDiff($prDate1,$prDate2)
	 is_date($prDate)
	 cDate($prIdioma, $prDate, $prDataHora)
	 dDate($prIdioma, $prDate, $prDataHora)
 LAYOUT
	 athBeginFloatingBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true)
	 athEndFloatingBox($prEcho=true)
	 athBeginShapeBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true)
	 athEndShapeBox($prEcho=true)
	 athBeginWhiteBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true)
	 athEndWhiteBox($prEcho=true)
	 athBeginClassBox($prClass, $prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="", $prEcho=true)
	 athEndClassBox($prEcho=true)
	 athBeginCssMenu()	
	 athBeginCssSubMenu()
	 athCssMenuAddItem($prLink,$prTarget,$prTitle,$prNextIsSub=0) 
	 athEndCssSubMenu()
	 athEndCssMenu()
	 MontaLinkGrade($pr_modulo, $pr_pagina, $pr_cod, $pr_img, $pr_title, $pr_extra) 
 SISTEMA 
	 psVersion($prTarget)
	 findLogicalPath($prPasta = "") 
	 findPhysicalPath($prPasta = "") 
	 verficarAcesso($intCodUsuario, $intCodApp, $strAppDir="", $strTpReturn="die")
	 montaCombo($prObjConn, $prSQL, $prValor, $prCampo, $prSearch, $prGroup="")
	 montaArraySiteInfo(&$objConn ,&$pr_arrScodi, &$pr_arrSdesc)
	 montaArraysContainer($prStrSQL, &$prArrScodi, &$prArrSdesc, &$objConnLocal)
	 arrayIndexOf($prArray, $prCampo)
	 insertTagSQL($prParam)
	 removeTagSQL($prParam)
	 insertTagParam($prParam)
	 removeTagParam($prParam)
	 replaceParametersSession($prString) 
	 barcode39($prBarCode, $boolExibirCodigo=true, $prAltura="", $prLargura="", $prCaminho="../img/")
	 barCode25($prValor)
	 mensagemStd($prTitulo, $prAviso, $prAdText="", $prFlagHTML=0, $prBackground="default", $prWidth) 
	 microtime_float()
 STRING
	 gerarSenha($prMaxNum, $prPar1)
	 left($prStr, $prLength) 
	 right($prStr, $prLength)
	 getNormalString($prString)   
	 removeAcento($prString)
	 removeEspChar($prString)
	 returnCodigo($prString) 
	 returnChar($prString)
	 getTextBetweenTagsDOM($pr_tag, $pr_html, $pr_strict=0)  
	 getTextBetweenTags($prValor, $prTagIni, $prTagFim, &$prPosIni, &$prPosFim) 
	 prepStr($prStr)  
 MATEMÁTICAS e FORMATAÇÂO NUMÉRICA
	 formatcurrency($prVlrCurrency, $prDec=2)
	 MoedaToFloat($valor)
	 FloatToMoeda($valor)
	 valorPorExtenso($prValor=0) 
 WRAPPERS 
	 redirect($prURL)
	 getValue($prRS, $prFieldName, $prBoolQuote=true) 
	 request($prParam)
	 requestForm($prParam)
	 requestQueryString($prParam)
	 getcookie($prParam)
	 getsession($prParam)
	 setsession($prName, $prValue)
 TRADUÇÃO
	 getTText($prKeyWord, $prWordMode)
	 langIndexComment($strIndex, $strFile)
 LÓGICA de REVISTA
	 arquivoRelacionado($prNivel, $prCodigo, &$prObjConn)
	 linkRelacionado($prNivel, $prCodigo, &$prObjConn)
	 montaSiteAreaSQL($prCodSiteArea, $prTipoCons, $prLocalID = "") 
	 montaLogicaRevistaSQL($prTipo, $prCodigo) 	
	 retTipoPai($prTipo) 
	 retTipoFilho($prTipoPai) 
	 montaChildsSQL($prTipo, $prCodigo, $prArea, $prOrdenacao1, $prOrdenacao2) 
	 printBanner($prCodBanner, $prArquivo, $prLargura, $prAltura, $prBorda, $prTipo, $objConn) 

 FIM - ÍNDICE de funções -----------------------------------------------------------------------------------
*/


/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções de Data e Hora --------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
/* Retorna data e hora atual */
function now()		{ return(date("Y-m-d H:i:s")); }

/* Retorna data  atual */
function dateNow()	{ return(date("Y-m-d")); }

/* Retorna hora atual */
function timeNow()	{ return(date("H:i:s")); }

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

/* Retorna a diferença entre duas datas (em DIAS) */
function dayDiff($prDate1,$prDate2){
	$retValue = abs(strtotime($prDate1) - strtotime($prDate2));
	$retValue = $retValue/(3600*24);
	return($retValue);
}

/* Retorna se um valor (string) representa uma data */
function is_date($prDate){
	if(isset($prDate) && !is_null($prDate) && $prDate != "") {
		$arrDate = explode("-",$prDate);
		if(isset($arrDate[0]) && isset($arrDate[1]) && isset($arrDate[2])) {
			$retValue = checkdate((int) $arrDate[1], (int) $arrDate[2], (int) $arrDate[0]);
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
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de Data e Hora --------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */



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

/* INI - Menu pureCSS over TABLESORT (Leandro)) */
function athBeginCssMenu()		{ echo("\n<div class=\"cssMenuDiv\"><ul class=\"cssMenu cssMenum\">"); }
function athBeginCssSubMenu()	{ echo("\n<ul class=\"cssMenum\">"); }
function athCssMenuAddItem($prLink,$prTarget,$prTitle,$prNextIsSub=0) {
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
function athEndCssSubMenu()	{ echo("\n</ul></li>"); }
function athEndCssMenu()	{ echo("\n</ul></div>"); }
/* FIM - Menu pureCSS over TABLESORT */

/* Facilita a montagem de um link/href para um campo/valor dentro de uma table/grade (by Aless) */
function MontaLinkGrade($pr_modulo, $pr_pagina, $pr_cod, $pr_img, $pr_title, $pr_extra) {
	$strIMG = "<img src='../img/" . $pr_img . "' border='0' title='" . $pr_title . "'>";
	$strA   = "<a href='../" . $pr_modulo . "/" . $pr_pagina . "?var_chavereg=" . $pr_cod . $pr_extra . "' style='cursor:pointer;'>" . $strIMG . "</a>";
	return($strA);
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de LAYOUT -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */


/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções de SISTEMA ------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
/* Exibe a versão do sistema  (lendo o arquivo .txt que fica no diretório raiz. Ex: v4.01.1.32.txt )   (by Alan) */
function psVersion($prTarget){
	$resDir = opendir("../../");
	chdir("../../");
	$intDataArquivoMaior = 0;
	$strNomeArquivo = "";
	while(false !== ($strFile = readdir($resDir))) {
	// Essa função: file_exists - não pode ser chamada no PS3, por questões de liberaçãod e acesso/segurança. 
	// Desta forma esta comentada para que não dê erro
	// if(file_exists($strFile)) {  
			if(ereg("(.*)\.html$",$strFile)){ 
				$intDataArquivo = date ("YmdHis", filemtime($strFile));
				if($intDataArquivo > $intDataArquivoMaior ){
					$intDataArquivoMaior = $intDataArquivo;
					$strNomeArquivo = $strFile;
				}
			}
		}
	//}
	echo("<a href='../../".$strNomeArquivo."' class='comment_med' target='" . $prTarget . "'>KERNEL ".str_replace(".html","",$strNomeArquivo)."</a>");
	//echo(str_replace(".txt","",$strNomeArquivo));
	closedir($resDir);
}

/* Retorna o caminho lógico (by Alan) */
function findLogicalPath($prPasta = "") {
	$retValue  = "http://" . $_SERVER["HTTP_HOST"] . "/";
	$retValue .= (($_SERVER["HTTP_HOST"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["HTTP_HOST"] == CFG_SYSTEM_NAME . ".proevento.com.br")) ? $prPasta : CFG_SYSTEM_NAME . "/" . $prPasta;
	return($retValue);
}

/* Retorna o caminho físico (by Aless /Alan) */
function findPhysicalPath($prPasta = "") {
	$retValue  = strtolower(realpath("../../"));
	$retValue .= (DIRECTORY_SEPARATOR == "/") ? "/" . $prPasta : "\\" . $prPasta;
	return($retValue);
}

/* Efetua a verificação de acesso relacionando usuário x ação em questão e seus respectivos direitos (by Aless/Alan) */
function verficarAcesso($intCodUsuario, $intCodApp, $strAppDir="", $strTpReturn="die"){
    $flagOk = true;
	/* Se por algum motivo não consegue buscar o nome do sistema na SESSION então esta deve ter expirado */
    if (getsession(CFG_SYSTEM_NAME . "_db_name")=="") { 
		mensagem("err_session_expired_titulo", "err_session_expired_desc","","","erro","1"); 
		die(); 
	}
	//Verifica se o existe codigo de usuario modelo, caso exista busca os direitos do usuario modelo
	if (getsession(CFG_SYSTEM_NAME . "_cod_user_refdir")!="")
		{
		 	$intCodUsuario = getsession(CFG_SYSTEM_NAME . "_cod_user_refdir");			
		
		}
		
	$objConnLocal = abreDBConn(getsession(CFG_SYSTEM_NAME . "_db_name"));

	/* Busca se o user tem o direito apra ação solicitada no modulo/app especificado */
	if(!is_null($intCodUsuario) && is_numeric($intCodUsuario) && !is_null($intCodApp) && is_numeric($intCodApp)){	
		if($strAppDir != ""){
			$strSQL = " SELECT cod_app FROM sys_app_direito , sys_app_direito_usuario 
						 WHERE cod_app = " . $intCodApp . " 
						   AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito
						   AND sys_app_direito.id_direito = '" . $strAppDir . "'
						   AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
		} else {
			$strSQL = " SELECT cod_app_direito_usuario FROM sys_app_direito , sys_app_direito_usuario 
						 WHERE cod_app = " . $intCodApp . " 
						   AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito
						   AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
		}
	
		try {
			$objResult = $objConnLocal->query($strSQL);
			$intNumRows = $objResult->rowCount();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
//			mensagem("err_sql_titulo","err_sql_desc",$strSQL,"","erro",1);
			die();
		}
	}
	else { $intNumRows = 0;	}
	
	if($intNumRows == 0){
		try{
			$strSQL = " SELECT id_direito FROM sys_app_direito , sys_app_direito_usuario 
						 WHERE cod_app = " . $intCodApp . " 
						   AND sys_app_direito.cod_app_direito = sys_app_direito_usuario.cod_app_direito 
						   AND sys_app_direito_usuario.cod_usuario = " . $intCodUsuario;
			$objResult2 = $objConnLocal->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
//			mensagem("err_sql_titulo","err_sql_desc",$strSQL,"","erro",1);
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
		
		if($strTpReturn == "die") {
			mensagem("err_acesso_titulo","err_acesso_desc",$strErrDesc,"","erro",1);
			die();
		} else { $flagOk = false; }
		$objResult2->closeCursor();
	}
	$objResult->closeCursor();
	$objConnLocal = NULL;
	
	return($flagOk);
}

/* Montagem de um Select/Option (combo). Obs.: Para melhor uso em páginas externas como login, etc... */
/* diferentemente de  outros projetos onde funções como essa que tem acesso a banco ficariam na athDBConn, */
/* optamos por colocá-la aqui na athutils, mesmo porque a athDBConn deste framework/KernelPS inclui  */
/* a athutils por default (by Aless/Clv) */
function montaCombo($prObjConn, $prSQL, $prValor, $prCampo, $prSearch, $prGroup=""){
	$objResult	= $prObjConn->query(replaceParametersSession($prSQL));
	$retDBname	= "";
	$strGroup	= "";

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
		} else {
			if(getValue($objRS,$prValor) == $prSearch){ $retDBname .= " selected"; }
		}
		
		$retDBname .= ">" . getValue($objRS,$prCampo) . "</option>\n";
	}
	$objResult->closeCursor();
	return($retDBname);
}

/* Montagem de um array ocm as informações básicas do sistema na tabela CONTAINER sys_info (by Aless) */
function montaArraySiteInfo(&$objConn ,&$pr_arrScodi, &$pr_arrSdesc){
  $strSQL		= "SELECT titulo, descricao FROM sys_info ";
  $objResult	= $objConn->query($strSQL);
  $strAuxScodi	= "";
  $strAuxSdesc	= "";
  
  foreach($objResult as $objRS){
    $strAuxScodi .= "|" . $objRS["titulo"];
    $strAuxSdesc .= "|" . $objRS["descricao"];
  }
  $pr_arrScodi = explode("|",$strAuxScodi);
  $pr_arrSdesc = explode("|",$strAuxSdesc);
  $objResult = NULL;
}

/* Genérica para montagem de um ARRAYs a partir de tabelas do tipo CONTAINER (by Aless) */
function montaArraysContainer($prStrSQL, &$prArrScodi, &$prArrSdesc, &$objConnLocal){
  $strSQLLocal		= $prStrSQL;
  $objResultLocal	= $objConnLocal->query($strSQLLocal);
  $auxStrScodi		= "";
  $auxStrSdesc		= "";
  foreach($objResultLocal as $objRSLocal){
    $auxStrScodi .= "|" . $objRSLocal[0];
    $auxStrSdesc .= "|" . $objRSLocal[1];
  }
  $prArrScodi = explode("|",$auxStrScodi);
  $prArrSdesc = explode("|",$auxStrSdesc);
  $objResultLocal = NULL;
}

/* Retorna a posição de um elemento do array (by Aless) */
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

/* Troca SÍMBOLOS gráficos específicos em comandos SQL por TAGS proprietários para facilitar a manipulação  */
/* ou mesmo o envio de SQl via parãmetro (get), session, etc... (by Aless /Clv) */
function insertTagSQL($prParam){
	$retValue = "";
	$retValue = str_replace("!","<ASLW_EXCLAMACAO>"		,$prParam);
	$retValue = str_replace("%","<ASLW_PERCENT>"		,$retValue);
	$retValue = str_replace("#","<ASLW_SHARP>"			,$retValue);
	$retValue = str_replace("'","<ASLW_APOSTROFE>"		,$retValue);
	$retValue = str_replace("\"","<ASLW_ASPAS>"			,$retValue);
	$retValue = str_replace("@","<ASLW_ARROBA>"			,$retValue);
	$retValue = str_replace("?","<ASLW_INTERROGACAO>"	,$retValue);
	$retValue = str_replace("&","<ASLW_ECOMERCIAL>"		,$retValue);
	$retValue = str_replace(":","<ASLW_DOISPONTOS>"		,$retValue);
	$retValue = str_replace("+","<ASLW_PLUS>"			,$retValue);
	$retValue = str_replace("-","<ASLW_MINUS>"			,$retValue);
	return($retValue);
}

/* Troca TAGS proprietários em comandos SQL por seus respectivos SÍMBOLOS gráficos facilitando a troca de */
/* SLQ via parametros (get), session, etc... (by Aless /Clv) */
function removeTagSQL($prParam){
	$retValue = "";
	$retValue = str_replace("<ASLW_EXCLAMACAO>"	,"!"	,$prParam);
	$retValue = str_replace("<ASLW_PERCENT>"	,"%"	,$retValue);
	$retValue = str_replace("<ASLW_SHARP>"		,"#"	,$retValue);
	$retValue = str_replace("<ASLW_APOSTROFE>"	,"'"	,$retValue);
	$retValue = str_replace("<ASLW_ASPAS>"		,"\""	,$retValue);
	$retValue = str_replace("<ASLW_ARROBA>"		,"@"	,$retValue);
	$retValue = str_replace("<ASLW_INTERROGACAO>","?"	,$retValue);
	$retValue = str_replace("<ASLW_ECOMERCIAL>"	,"&"	,$retValue);
	$retValue = str_replace("<ASLW_DOISPONTOS>"	,":"	,$retValue);
	$retValue = str_replace("<ASLW_PLUS>"		,"+"	,$retValue);
	$retValue = str_replace("<ASLW_MINUS>"		,"-"	,$retValue);
	return($retValue);
}

/* Troca SÍMBOLOS gráficos específicos usados em parâmetros html por TAGS proprietários (by Aless /Clv) */
function insertTagParam($prParam){
	$retValue = "";
	$retValue = str_replace("&","<PARAM_EC>",$prParam);
	$retValue = str_replace("%","<PARAM_PC>",$retValue);
	$retValue = str_replace("?","<PARAM_QM>",$retValue);
	return($retValue);
}

/* Troca TAGS proprietários em parâmetros html por seus respectivos SÍMBOLOS gráficos (by Aless /Clv) */
function removeTagParam($prParam){
	$retValue = "";
	$retValue = str_replace("<PARAM_EC>","&",$prParam);
	$retValue = str_replace("<PARAM_PC>","%",$retValue);
	$retValue = str_replace("<PARAM_QM>","?",$retValue);
	return($retValue);
}

/* Retorna o valor correspondente a(s) varíavel(eis) ambiente "{var}"  especificada na string recebida. */
/* Usada no tratamento de variáveis ambientes, permitindo que além delas sejam executadas algumas  */
/* funções específicas* (by Aless) */
function replaceParametersSession($prString) {
	$retValue = $prString;
    // Funções específicas* ----------------------------------------------------------------------------
	$retValue = str_replace("{now()}"		,now()										, $retValue );
	$retValue = str_replace("{dateNow()}"	,dateNow()									, $retValue );
	$retValue = str_replace("{timeNow()}"	,timeNow()									, $retValue );
	$retValue = str_replace("{cDate()}"		,dDate(CFG_LANG,date("Y-m-d"),false)		, $retValue );
	$retValue = str_replace("{dDate()}"		,dDate(CFG_LANG,date("Y-m-d H:i:s"),true)	, $retValue );
	// -----------------------------------------------------------------------------------------------
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

/* Retorna o Desenho (imagens) do código de barras, padrão 39.   */
/* Necessida das imasgens: barcode39_shim_black.gif  e barcode39_shim.gif (by Alan/Aless - ref. Mauro) */
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

	if(!is_numeric($intNarrow)) { $intNarrow=1.5; }
	$intHeight = $prAltura;
	if(!is_numeric($intHeight)) { $intHeight = 15; }

	$strBarCode = $prBarCode;
	$strBarCode = "*" . $strBarCode . "*";
	$strConv = "";

	for($t=0;$t<strlen($strBarCode);$t++){
		for($s=1;$s<=44;$s++){
			if(substr($strBarCode,$t,1) == substr($a[$s],0,1)) {
				$strConv = $strConv . substr($a[$s],1) . "s";
			}
		}
	}
	$b=1;
	for($t=0;$t<strlen($strConv);$t++) {
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


/* Retorna o Desenho (imagens) do código de barras, padrão 25.   */
/* Necessida das imasgens: boleto_p[...].gif  e boleto_b[...].gif (by Alan/Aless - ref. Mauro) */
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
				for($intI=0;$intI<=5;$intI++) {
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
	if(strlen($strTexto) % 2 != 0) { $strTexto .= "0"; }
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

/* Função destinada a montar a mensagens infomativas em geral (sem a opção de dialog message) */
/* Mais usada para colocação dos textos explicativos de cada módulo/aplicação do sistenma ( by GS 16/05/11) */
function mensagemStd($prTitulo, $prAviso, $prAdText="", $prFlagHTML=0, $prBackground="default", $prWidth) { 
  if($prFlagHTML != 0) { 
	echo("<html>
			<head>
				<title></title>
				<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				<link href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" rel=\"stylesheet\" type=\"text/css\">
			</head>");
	echo("<body style=\"margin:8px;\" text=\"#000000\" bgcolor=\"#FFFFFF\" ");		
	if ($prBackground == "default") { echo("background=\"../img/bgFrame_" . CFG_SYSTEM_THEME . "_main.jpg\""); } else { echo("background=\"" . $prBackground . "\""); }
	echo(" >");
  }
  echo("<center>");
  athBeginWhiteBox($prWidth);
  echo("
		<table id=\"headerMensagenStd\" name=\"headerMensagenStd\" width=\"100%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
		  <tr>
			<td valign=\"top\" width=\"1%\"><img src=\"../img/mensagem_infoapp.gif\" hspace=\"5\"></td>
			<td width=\"99%\">
				<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr><td style=\"font-size:14px;padding-left:5px;padding-bottom:5px;\" align=\"left\"><b>" . $prTitulo . "</b></td></tr>
					<tr><td class=\"padrao_gde\" style=\"padding:10px 0px 10px 5px;\" align=\"left\"><b>" . $prAviso . "</b></td></tr>
					<tr><td height=\"1\" bgcolor=\"#BDBDBF\"></td></tr>
					<tr><td style=\"padding:10px 0px 10px 5px;\" align=\"left\">" . $prAdText . "</td></tr>
					<tr><td align=\"right\" class=\"comment_peq\">" . basename($_SERVER["PHP_SELF"]) . "</td></tr>
				</table>
			</td>
		  </tr>
		 </table>
	  "); 
  athEndWhiteBox();
  echo("</center><br>");

  if($prFlagHTML != 0) { echo("</body></html>"); }
}

/* Retorna os milesegundos atuais, podendo ser usado para verificação de performance, conexão, etc... (by Akless) */
function microtime_float(){
	/* exemplo de uso:
	$start = microtime_float();
	echo("blablalblababa");
	$end = microtime_float();
	echo 'Script Execution Time: ' . round($end - $start, 3) . ' seconds';   */
  list ($msec, $sec) = explode(' ', microtime());
  $microtime = (float)$msec + (float)$sec;
  return $microtime;
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de SISTEMA ------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções de STRING -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
/* Retorna uma sequência de caracteres aleatórios do tamanho especificado (by Aless) */
function gerarSenha($prMaxNum, $prPar1){
	$retValue = "";
	if($prPar1 == 1) { $strValores = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"; }
	if($prPar1 == 2) { $strValores = "0,1,2,3,4,5,6,7,8,9"; }
	if($prPar1 == 3) { $strValores = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"; }
    if($prPar1 == 4) { $strValores = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z"; }
    if($prPar1 == 5) { $strValores = "0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z"; }
    if($prPar1 == 6) { $strValores = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z"; }
    if($prPar1 == 7) { $strValores = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z"; }
	$arrXArray = explode(",",$strValores);
	
	while(strlen($retValue) < $prMaxNum){
		$strAux		= $arrXArray[rand(0,count($arrXArray)-1)];
		$retValue	= $retValue . $strAux;
	}
	return(trim($retValue));
}

/* Retorna a  string completando com caracter "0" a esquerda aé o tamanho solicitado */
function left($prStr, $prLength) { return(substr($prStr, 0, $prLength)); }

/* Retorna a tantos caracteres da string da diretia pra esquerda */
function right($prStr, $prLength) {	return(substr($prStr, -$prLength)); }

/* Normaliza uma string - Elimina caracteres especiais e acentuação (by Aless) */
function getNormalString($prString) {  
	$a = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýþÿRr ~^!@#$%¨&*()=+/:;?<>,|¹²³£¢¬§ªº°´`";  
	$b = "AAAAAAACEEEEIIIIDNOOOOO0UUUUYbsaaaaaaaceeeeiiiidnoooooouuuybyRr___________________________________";  
	$prString = strtr($prString, $a, $b);	 	
	return ($prString);
}  	

/* Remove apenas acentos e "ç" string  (by Aless) */
function removeAcento($prString){
	$a = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðñòóôõöùúûýþÿ";  
	$b = "AAAAAACEEEEIIIINOOOOOUUUUYaaaaaaceeeeiiiidnooooouuuyby";  
	$prString = strtr($prString, $a, $b);	 	
	return ($prString);
}

/* ->Mantida por compatibilidade (verificar se os sistemas a utilizam), pois a "getNormalString" funciona de forma similar */
function removeEspChar($prString) {
	$a = "&ÀàÁáÂâÃãÄäÇçÈèÉéÊêËëÌìÍíÎîÏïÑñòòÓóÔôÕõÖöÙùÚúÛûÜüß÷ÿ<>\"\'° ¹²³";
	$b = "_____________________________________________________________";  
	$prString = strtr($prString, $a, $b);	 	
	return($prString);
}

/* Função que transforma os caracteres especiais no seu respectivo código (by Aless) */
function returnCodigo($prString) {
    //$trans_tbl = get_html_translation_table (HTML_ENTITIES);
    //$trans_tbl = array_flip ($trans_tbl);
    //return strtr ($prString, $trans_tbl);
	
	$prString = str_replace("&", "&amp;",$prString);
	$prString = str_replace("À", "&Agrave;",$prString);
	$prString = str_replace("à", "&agrave;",$prString);
	$prString = str_replace("Á", "&Aacute;",$prString);
	$prString = str_replace("á", "&aacute;",$prString);
	$prString = str_replace("Â", "&Acirc;",$prString);
	$prString = str_replace("â", "&acirc;",$prString);
	$prString = str_replace("Ã", "&Atilde;",$prString);
	$prString = str_replace("ã", "&atilde;",$prString);
	$prString = str_replace("Ä", "&Auml;",$prString);
	$prString = str_replace("ä", "&auml;",$prString);
	$prString = str_replace("Ç", "&Ccedil;",$prString);
	$prString = str_replace("ç", "&ccedil;",$prString);
	$prString = str_replace("È", "&Egrave;",$prString);
	$prString = str_replace("è", "&egrave;",$prString);
	$prString = str_replace("É", "&Eacute;",$prString);
	$prString = str_replace("é", "&eacute;",$prString);
	$prString = str_replace("Ê", "&Ecirc;",$prString);
	$prString = str_replace("ê", "&ecirc;",$prString);
	$prString = str_replace("Ë", "&Euml;",$prString);
	$prString = str_replace("ë", "&euml;",$prString);
	$prString = str_replace("Ì", "&Igrave;",$prString);
	$prString = str_replace("ì", "&igrave;",$prString);
	$prString = str_replace("Í", "&Iacute;",$prString);
	$prString = str_replace("í", "&iacute;",$prString);
	$prString = str_replace("Î", "&Icirc;",$prString);
	$prString = str_replace("î", "&icirc;",$prString);
	$prString = str_replace("Ï", "&Iuml;",$prString);
	$prString = str_replace("ï", "&iuml;",$prString);
	$prString = str_replace("Ñ", "&Ntilde;",$prString);
	$prString = str_replace("ñ", "&ntilde;",$prString);
	$prString = str_replace("ò", "&Ograve;",$prString);
	$prString = str_replace("ò", "&ograve;",$prString);
	$prString = str_replace("Ó", "&Oacute;",$prString);
	$prString = str_replace("ó", "&oacute;",$prString);
	$prString = str_replace("Ô", "&Ocirc;",$prString);
	$prString = str_replace("ô", "&ocirc;",$prString);
	$prString = str_replace("Õ", "&Otilde;",$prString);
	$prString = str_replace("õ", "&otilde;",$prString);
	$prString = str_replace("Ö", "&Ouml;",$prString);
	$prString = str_replace("ö", "&Ouml;",$prString);
	$prString = str_replace("Ù", "&Ugrave;",$prString);
	$prString = str_replace("ù", "&ugrave;",$prString);
	$prString = str_replace("Ú", "&Uacute;",$prString);
	$prString = str_replace("ú", "&uacute;",$prString);
	$prString = str_replace("Û", "&Ucirc;",$prString);
	$prString = str_replace("û", "&ucirc;",$prString);
	$prString = str_replace("Ü", "&Uuml;",$prString);
	$prString = str_replace("ü", "&uuml;",$prString);
	$prString = str_replace("ß", "&szlig;",$prString);
	$prString = str_replace("÷", "&divide;",$prString);
	$prString = str_replace("ÿ", "&yuml;",$prString);
	$prString = str_replace("<", "&lt;",$prString);
	$prString = str_replace(">", "&gt;",$prString);
	$prString = str_replace("\"", "&quot;",$prString);
	$prString = str_replace("'", "''",$prString);
	$prString = str_replace("°", "&deg;",$prString);
	$prString = str_replace("¹", "&sup1;",$prString);
	$prString = str_replace("²", "&sup2;",$prString);
	$prString = str_replace("³", "&sup3;",$prString);
	$prString = str_replace("´","&acute;",$prString);
	return($prString);
}

/* Função que transforma códigos especiais em seus respectivos caracteres (by Aless) */
function returnChar($prString){
	//return (htmlspecialchars_decode($prString, ENT_QUOTES));
	$prString = str_replace("&amp;","&",$prString);
	$prString = str_replace("&Agrave;","À",$prString);
	$prString = str_replace("&agrave;","à",$prString);
	$prString = str_replace("&Aacute;","Á",$prString);
	$prString = str_replace("&aacute;","á",$prString);
	$prString = str_replace("&Acirc;","Â", $prString);
	$prString = str_replace("&acirc;","â", $prString);
	$prString = str_replace("&Atilde;","Ã", $prString);
	$prString = str_replace("&atilde;","ã", $prString);
	$prString = str_replace("&Auml;","Ä", $prString);
	$prString = str_replace("&auml;","ä", $prString);
	$prString = str_replace("&Ccedil;","Ç", $prString);
	$prString = str_replace("&ccedil;","ç", $prString);
	$prString = str_replace("&Egrave;","È", $prString);
	$prString = str_replace("&egrave;","è", $prString);
	$prString = str_replace("&Eacute;","É", $prString);
	$prString = str_replace("&eacute;","é", $prString);
	$prString = str_replace("&Ecirc;","Ê", $prString);
	$prString = str_replace("&ecirc;","ê", $prString);
	$prString = str_replace("&Euml;","Ë", $prString);
	$prString = str_replace("&euml;","ë", $prString);
	$prString = str_replace("&Igrave;","Ì", $prString);
	$prString = str_replace("&igrave;","ì", $prString);
	$prString = str_replace("&Iacute;","Í", $prString);
	$prString = str_replace("&iacute;","í", $prString);
	$prString = str_replace("&Icirc;","Î", $prString);
	$prString = str_replace("&icirc;","î", $prString);
	$prString = str_replace("&Iuml;","Ï", $prString);
	$prString = str_replace("&iuml;","ï", $prString);
	$prString = str_replace("&Ntilde;","Ñ", $prString);
	$prString = str_replace("&ntilde;","ñ", $prString);
	$prString = str_replace("&Ograve;","ò", $prString);
	$prString = str_replace("&ograve;","ò", $prString);
	$prString = str_replace("&Oacute;","Ó", $prString);
	$prString = str_replace("&oacute;","ó", $prString);
	$prString = str_replace("&Ocirc;","Ô",$prString);
	$prString = str_replace("&ocirc;","ô", $prString);
	$prString = str_replace("&Otilde;","Õ", $prString);
	$prString = str_replace("&otilde;","õ", $prString);
	$prString = str_replace("&Ouml;","Ö", $prString);
	$prString = str_replace("&Ouml;","ö", $prString);
	$prString = str_replace("&Ugrave;","Ù", $prString);
	$prString = str_replace("&ugrave;","ù",$prString);
	$prString = str_replace("&Uacute;","Ú", $prString);
	$prString = str_replace("&uacute;","ú", $prString);
	$prString = str_replace("&Ucirc;","Û", $prString);
	$prString = str_replace("&ucirc;","û",$prString);
	$prString = str_replace("&Uuml;","Ü",$prString);
	$prString = str_replace("&uuml;","ü",$prString);
	$prString = str_replace("&szlig;","ß",$prString);
	$prString = str_replace("&divide;","÷",$prString);
	$prString = str_replace("&yuml;","ÿ", $prString);
	$prString = str_replace("&lt;","<", $prString);
	$prString = str_replace("&gt;",">", $prString);
	$prString = str_replace("&quot;","\"", $prString);
	$prString = str_replace("''","'", $prString);
	$prString = str_replace("&deg;","°",$prString);
	$prString = str_replace("&sup1;","¹",$prString);
	$prString = str_replace("&sup2;","²",$prString);
	$prString = str_replace("&sup3;","³",$prString);
	$prString = str_replace("&acute;","´",$prString);
	return($prString);
}

/* Retorna a string que estiver entre a TAG especificada (by Aess) */
function getTextBetweenTagsDOM($pr_tag, $pr_html, $pr_strict=0) { 
	/* 
	$pr_tag: O nome da tag
	$pr_html: XML ou XHTML
	$pr_tp:   (1-LoadXML *-LoadHTML)
	@return array

	Exemplo de uso: 

	html = "<body><h1>Heading</h1><a  href='php.org'>PHPORG</a><p>teste</p><p>teste com a <a href='php2.org2'>LINK PHP2.ORG2 </a></p><p>Broken paragraph</body>";
	$content = getTextBetweenTags('a', $html);
	foreach( $content as $item ) { echo $item.'<br />'; }
	*/

	$dom = new DOMDocument();
	if($pr_strict==1) { $dom->loadXML($pr_html); }
	else { $dom->loadHTML($pr_html); }

	/* Descarta espaços em branco */
	$dom->preserveWhiteSpace = false;
	$content = $dom->getElementsByTagname($pr_tag);
	$out = array();
	foreach ($content as $item) { $out[] = $item->nodeValue; }
	return $out;
}

/* Retorna a string que estiver entre a TAG (ou substrings) especificadas (by Aless) */
/* Correção do somatorio do $prPosFim. caso ache a tag o calculo é feito onde a posFim retorna true ou false BY GS/CLV 27/03/2012*/
function getTextBetweenTags($prValor, $prTagIni, $prTagFim, &$prPosIni, &$prPosFim) {
	$prPosIni = strpos($prValor, $prTagIni);
	$prPosFim = strpos($prValor, $prTagFim);
	
	if ($prPosIni === false) $prPosIni = -1;
	
	if ($prPosFim === false) $prPosFim = -1;
	else 	$prPosFim =  $prPosFim + strlen($prTagFim);
	
	$Texto = "";
	if (($prPosIni != -1) && ($prPosFim != -1)) {
	$Texto = trim(substr($prValor, $prPosIni + strlen($prTagIni), $prPosFim - strlen($prTagFim) - ($prPosIni + strlen($prTagIni))));
	
	}
	return $Texto;
}
/* Prepara string para gravação no banco (by Alan) */
function prepStr($prStr) { 
 $retValue = str_replace("'","''",$prStr); return($retValue); 
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de STRING -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções de MATEMÁTICAS e FORMATAÇÂO NUMÉRICA ----------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
function formatcurrency($prVlrCurrency, $prDec=2){
	$retValue = $prVlrCurrency;

	if(!empty($retValue) && $retValue != "") { 
		$retValue = (strpos(",",$retValue) !== false) ? number_format($retValue, $prDec) : $retValue;
		$retValue = str_replace(".","",$retValue);
		$retValue = str_replace(",",".",$retValue);
	}
	return($retValue);
}

/* Recebe um valor float/double COMO STRING no formato 1.000.000,00  e retorna o valor float/double correspondente no formato 1000000.00 ( by Aloisio) */
function MoedaToFloat($valor) {
	$cont = strlen($valor);	
	$result = $valor;
	for($i=0; $i< $cont; $i++) { $result = str_replace('.','',$result);	}
	$result = str_replace(',','.',$result);
	return $result;
}

/* Recebe um valor float/double no formato 1000000.00 e retorna  o valor float/double COMO STRING no formato 1.000.000,00 (by Aloisio) */
function FloatToMoeda($valor) {
	$result = number_format($valor, 2, ',', '.');
	return $result;
}

/* Retoran string com o valor escrito por extenso (by Leandro) */
function valorPorExtenso($prValor=0) {
	// Desenvolvido por..: André Camargo
	// Versão............: 1.2 09:00 28/10/2009
	// Descricao.........: Esta função recebe um valor numérico e retorna uma 
	//                     string contendo o valor de entrada por extenso
	// Parametros Entrada: $prValor (formato que a função number_format entenda :)
	$dblValor = $prValor;
	
	$strSingular 	= array("centavo","real","mil","milhão","bilhão","trilhão","quatrilhão");
	$strPlural 	 	= array("centavos","reais","mil","milhões","bilhões","trilhões","quatrilhões");
	$strCentenas 	= array("","cem","duzentos","trezentos","quatrocentos","quinhentos","seiscentos","setecentos","oitocentos","novecentos");
	$strDezenas 	= array("","dez","vinte","trinta","quarenta","cinquenta","sessenta","setenta","oitenta","noventa");
	$strDezenasMais = array("dez","onze","doze","treze","quatorze","quinze","dezesseis","dezesete","dezoito","dezenove");
	$strUnidades 	= array("","um","dois","três","quatro","cinco","seis","sete","oito","nove");
	$z=0;

	$dblValor = number_format($dblValor, 2, ".", ".");
	$intInteiro = explode(".",$dblValor);
	for($auxContador=0;$auxContador<count($intInteiro);$auxContador++)
		for($ii=strlen($intInteiro[$auxContador]);$ii<3;$ii++)
			$intInteiro[$auxContador] = "0".$intInteiro[$auxContador];

	// $strFim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$strFim = count($intInteiro) - ($intInteiro[count($intInteiro)-1] > 0 ? 1 : 2);
	for ($auxContador=0;$auxContador<count($intInteiro);$auxContador++) {
		$dblValor = $intInteiro[$auxContador];
		$rc = (($dblValor > 100) && ($dblValor < 200)) ? "cento" : $strCentenas[$dblValor[0]];
		$rd = ($dblValor[1] < 2) ? "" : $strDezenas[$dblValor[1]];
		$ru = ($dblValor > 0) ? (($dblValor[1] == 1) ? $strDezenasMais[$dblValor[2]] : $strUnidades[$dblValor[2]]) : "";
	
		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($intInteiro)-1-$auxContador;
		$r .= $r ? " ".($dblValor > 1 ? $strPlural[$t] : $strSingular[$t]) : "";
		if ($dblValor == "000")$z++; elseif ($z > 0) $z--;
		if (($t==1) && ($z>0) && ($intInteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$strPlural[$t]; 
		if ($r) @$rt = @$rt . ((($auxContador > 0) && ($auxContador <= $strFim) && ($intInteiro[0] > 0) && ($z < 1)) ? ( ($auxContador < $strFim) ? ", " : " e ") : " ") . $r;
	}
	return(@$rt ? @$rt : "zero");
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de MATEMÁTICAS e FORMATAÇÂO NUMÉRICA ----------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */




/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções WRAPPERS --------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
function redirect($prURL) { header("Location:" . $prURL); }

/* Equivalente a getValue ASP, aqui no PHP serve para resultsets e arrays em geral (by Aless) */
function getValue($prRS, $prFieldName, $prBoolQuote=true) {
	$retValue = (isset($prRS[$prFieldName])) ?  html_entity_decode($prRS[$prFieldName]) : "";
	if($prBoolQuote){ $retValue = str_replace('"',"&quot;",$retValue); }
	return($retValue);
}

/* Equivalente a getParam ASP (by Aless) */
function request($prParam){
	(isset($_REQUEST[$prParam])) ? $retValue = $_REQUEST[$prParam] : $retValue = "";
	return($retValue);
}

/* POST (by Alan) */
function requestForm($prParam){
	(isset($_POST[$prParam])) ? $retValue = $_POST[$prParam] : $retValue = "";
	return($retValue);
}

/* GET (by Alan) */
function requestQueryString($prParam){
	(isset($_GET[$prParam])) ? $retValue = $_GET[$prParam] : $retValue = "";
	return($retValue);
}

/* getcookie (by Alan) */
function getcookie($prParam){
	(isset($_COOKIE[$prParam])) ? $retValue = $_COOKIE[$prParam] : $retValue = "";
	return($retValue);
}

/* getsession(by Alan) */
function getsession($prParam){
	(isset($_SESSION[$prParam])) ? $retValue = $_SESSION[$prParam] : $retValue = "";
	return($retValue);
}

/* setsession(by Alan) */
function setsession($prName, $prValue){
	if(is_null($prValue)){
	    session_unregister($_SESSION[$prName]);
		unset($_SESSION[$prName]);
	}
	else{
		$_SESSION[$prName] = $prValue;
	}
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções WRAPPERS --------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */



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
	if(ereg(">(" . $strIndex . ")[ ]+'",$strFile)){ /* Verifica se a linha no arquivo modelo existe */
		$retValue = preg_replace("/(.*)>". $strIndex . "[ ]*('*)|('*\\r*)|(\\n.*)/","",substr($strFile,strpos($strFile,">" . $strIndex)));
	}
	return($retValue);
}
/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções auxziliares de TRADUÇÃO ------------------------------------------------------------------------ */
/* -------------------------------------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------------------------------------- */
/* INI - Funções de LÓGICA de REVISTA --------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
function arquivoRelacionado($prNivel, $prCodigo, &$prObjConn){
	try {
		$strSQL  = "SELECT cod_arquivo_relacionado, codigo, tipo, arquivo, descricao, ordem, titulo, tamanho, dtt_criacao ";
		$strSQL .= "  FROM lj_arquivo_relacionado WHERE tipo <=> '". $prNivel ."' AND codigo = ".$prCodigo." ORDER BY ORDEM ";
		$objResult = $prObjConn->query( $strSQL );
			
		if($objResult->rowCount() > 0 ) {
			echo ("<table width='360' border='0' align='left' cellpadding='0' cellspacing='0'>");
			echo ("<tr><td height='5' class='titulo_promocao_mdo' colspan='4'><b>Arquivos Relacionados:</b></td></tr>");
			$intI = 0;
			foreach($objResult as $objRS) {
				(($intI%2) == 0)? $strColor = "#F3F3F3" : $strColor = "#FFFFFF";
				$strAux = strtoupper(getValue($objRS,"arquivo"));
				if(strpos($strAux,"HTTP:") !== false) {	$strAux = getValue($objRS,"arquivo"); } else { $strAux = "../../apas/upload/".getValue($objRS,"arquivo");	}
				echo("<tr style='cursor:pointer'>");
				echo("  <td width='2' height='17'></td>");
				echo("  <td align='left' valign='middle' bgcolor='".$strColor."'>");
				echo("   <a href='".$strAux."' target='_blank' class='texto_corpo_mdo'>");
				echo("	 <img src='../img/BulletArqRel2.gif' border='0' alt=''>&nbsp;".getValue($objRS,"titulo")."");
				echo("   </a>");
				echo("  </td>");
				echo("  <td align='left' valign='middle' class='texto_corpo_mdo' bgcolor='".$strColor."'>".getValue($objRS,"tamanho")."</td>");
				echo("</tr>");
				$intI++;
			}
			echo("</table>");
		}
	}
	catch(PDOException $e) {
		mensagem("err_sql_title","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}

function linkRelacionado($prNivel, $prCodigo, &$prObjConn){
	try{
		$strSQL  = " SELECT codigo_relacionado, tipo_relacionado, link, rotulo ";
		$strSQL .= " FROM lj_relacoes WHERE codigo_relacionar = ".$prCodigo." AND tipo_relacionar <=> '".$prNivel."' ORDER BY ORDEM ";
		$objResult = $prObjConn->query($strSQL);
		
		if($objResult->rowCount() > 0){
			$strCOR_PRI = "#FFFFFF";
			echo("<table width='360' border='0' align='left' cellpadding='0' cellspacing='0' background='../img/bgArqRel.gif\'>
					 <tr><td class='titulo_destaque_mdo' colspan='3' style='".$strCOR_PRI."'>Links relacionados:</td></tr>
					 <tr><td height='5' colspan='3'></td></tr><tr><td width='10'></td>
					  <td align='center'><table width='100%' border='0' cellspacing='0' cellpadding='0'>"	);
			
			foreach($objResult as $objRS) {
				if(getValue($objRS,'codigo_relacionado') != ""){
					if(getValue($objRS,'tipo_relacionado') != "MATERIA"){
						$strSQL1 = "SELECT titulo FROM lj_".getValue($objRS,'tipo_relacionado')." WHERE cod_".getValue($objRS,'tipo_relacionado')." = ".getValue($objRS,'codigo_relacionado');
					} else { $strSQL1 = "SELECT titulo FROM lj_".getValue($objRS,'tipo_relacionado');	}
					$objResult1 = $prObjConn->query($strSQL1);
					if($objResult1->rowCount() > 0){
						$objRS1 = $objResult1->fetch();
						$strLink = "<a href='show".getValue($objRS,'tipo_relacionado').".php?var_chavereg=".getValue($objRS,'codigo_relacionado')."' class='texto_corpo_mdo'>".getValue($objRS1,"titulo")."</a>";
						echo $strLink;
						die;
					}
					$objResult1->closeCursor();
				} else {
					if(getValue($objRS,'rotulo') == "" || is_null(getValue($objRS,'rotulo')) ){
						$strRotulo = getValue($objRS,'link');
					} else { $strRotulo = getValue($objRS,'rotulo'); }
					if(getValue($objRS,'link') != ""){
						if(strpos(strtolower(getValue($objRS,'link')),"javascript:") !== false){
							$strLink = "<a href=".getValue($objRS,"link")." class='texto_corpo_mdo'>".$strRotulo."</a>";
						} else { $strLink = "<a href=".getValue($objRS,"link")." class='texto_corpo_mdo' target='_blank'>".$strRotulo."</a>"; }
					}
				}
				if($strLink != '') {
					echo("<tr><td width='1%' bgcolor=".$strCOR_PRI."><img src='../img/BulletLinksRel.gif' hspace='3' alt=\"\"></td>");
					echo("<td nowrap>".$strLink."</td></tr><tr><td colspan='2' height='5'></td></tr>");
				}
			}
			echo("</table></td><td width='10'></td></tr></table>");
		}
	}
	catch(PDOException $e) {
		mensagem("err_sql_title","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}

function montaSiteAreaSQL($prCodSiteArea, $prTipoCons, $prLocalID = "") {
    $strSQL = " SELECT
				  lj_revista.titulo  AS revista_titulo, 
				  lj_exemplar.titulo AS exemplar_titulo , 
				  lj_secao.titulo    AS secao_titulo , 
				  lj_materia.titulo  AS materia_titulo, 
				  
				  lj_revista.rotulo_menu  AS revista_rotulo, 
				  lj_exemplar.rotulo_menu AS exemplar_rotulo , 
				  lj_secao.rotulo_menu    AS secao_rotulo , 
				  lj_materia.rotulo_menu  AS materia_rotulo, 
				  
 		          lj_revista.cod_revista   AS cod_revista, 
    		      lj_exemplar.cod_exemplar AS cod_exemplar, 
    		      lj_secao.cod_secao       AS cod_secao, 
    		      lj_materia.cod_materia   AS cod_materia, 

                  lj_revista.texto  AS revista_texto, 
                  lj_exemplar.texto AS exemplar_texto , 
                  lj_secao.texto    AS secao_texto , 
                  lj_materia.texto  AS materia_texto, 

                  lj_revista.descricao  AS revista_descricao, 
                  lj_exemplar.descricao AS exemplar_descricao, 
                  lj_secao.descricao    AS secao_descricao, 
                  lj_materia.descricao  AS materia_descricao, 

                  lj_revista.cod_revista  AS revista_cod_pai, 
                  lj_exemplar.cod_revista AS exemplar_cod_pai,  
                  lj_secao.cod_exemplar   AS secao_cod_pai, 
                  lj_materia.cod_secao    AS materia_cod_pai, 

                  lj_revista.img          AS revista_img, 
                  lj_exemplar.img         AS exemplar_img, 
                  lj_secao.img            AS secao_img, 
                  lj_materia.img          AS materia_img, 

                  lj_revista.img_thumb    AS revista_img_thumb, 
                  lj_exemplar.img_thumb   AS exemplar_img_thumb, 
                  lj_secao.img_thumb      AS secao_img_thumb, 
                  lj_materia.img_thumb    AS materia_img_thumb, 

                  lj_revista.img_thumb_over    AS revista_img_thumb_over, 
                  lj_exemplar.img_thumb_over   AS exemplar_img_thumb_over, 
                  lj_secao.img_thumb_over      AS secao_img_thumb_over, 
                  lj_materia.img_thumb_over    AS materia_img_thumb_over, 

                  lj_revista.img_descricao    AS revista_img_descricao, 
                  lj_exemplar.img_descricao   AS exemplar_img_descricao, 
                  lj_secao.img_descricao      AS secao_img_descricao, 
                  lj_materia.img_descricao    AS materia_img_descricao, 

                  lj_site_area.tipo as tipo, lj_site_area.cod_site_area, lj_site_area.cod, 
                  lj_site_area.bloqueado, lj_site_area.ordem, lj_site_area.cod_revista ";

	$strSOLFrom = " FROM ((( (lj_site_area  LEFT  JOIN lj_revista ON lj_site_area.cod_revista=lj_revista.cod_revista) 
                      LEFT JOIN lj_exemplar ON lj_site_area.cod_revista = lj_exemplar.cod_exemplar)
                      LEFT JOIN lj_secao ON lj_site_area.cod_revista = lj_secao.cod_secao)
                      LEFT JOIN lj_materia ON lj_site_area.cod_revista = lj_materia.cod_materia) ";

    switch($prTipoCons) {
		case "JOIN-ALL":
			if(!empty($prLocalID)){
				$strSQL .= $strSOLFrom . " WHERE lj_revista.local_id = '" . $prLocalID . "' AND lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false ORDER BY lj_site_area.ordem ";
			} else {
				$strSQL .= $strSOLFrom . " WHERE lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false ORDER BY lj_site_area.ordem ";
			}
			break;
		case "JOIN-ALLIMAGES": 
			$strSQL .= " ,rv_images.img, rv_images.img_thumb " . $strSOLFrom . "
	                       LEFT JOIN rv_images ON (rv_site_area.tipo = rv_images.tipo 
						   AND lj_site_area.cod_revista = rv_images.codigo)
                           WHERE lj_site_area.cod_site_area = '" . $prCodSiteArea . "' AND lj_site_area.bloqueado = false
                           ORDER BY lj_site_area.ordem, RV_IMAGES.ordem ";
			break;
    }

    return($strSQL);
}

//-------------------------------------------------------------------------------------
// Facilita a montagem do SQL de cada Show: RV, EX, SE e MA
//------------------------------------------------------------------------- by Aless --
function montaLogicaRevistaSQL($prTipo, $prCodigo) {	
	$strSQL = " SELECT lj_" . $prtipo . ".cod_" . $prtipo .
			  "       ,lj_" . $prtipo . ".texto 
					  ,lj_" . $prtipo . ".img 
					  ,lj_" . $prtipo . ".img_thumb 
					  ,lj_" . $prtipo . ".img_thumb_over 
			      FROM lj_" . $prTipo . " WHERE cod_" . $prTipo . " = " . $prCodigo;
	return($strSQL);
}

//------------------------------------------------------------------------
// Retorna o tipo do pai de EX, SE, MA
//----------------------------------------------------- by Aless & Davi --
function retTipoPai($prTipo) {
	switch(strtolower($prTipo)) {
		Case "revista"	: $strRetTipoPai = "revista"; break;
		Case "exemplar"	: $strRetTipoPai = "revista"; break;
		Case "secao"	: $strRetTipoPai = "exemplar"; break;
		Case "materia"	: $strRetTipoPai = "secao";	break;
	}
	return($strRetTipoPai);
}

//------------------------------------------------------------------------
// Retorna o tipo do filho de RV, EX, SE
//----------------------------------------------------- by Aless & Davi --
function retTipoFilho($prTipoPai) {
	switch(strtolower($prTipoPai)) {
		Case "revista"	: $strRetTipoFilho = "exemplar"; break;
		Case "exemplar"	: $strRetTipoFilho = "secao"; break;
		Case "secao"	: $strRetTipoFilho = "materia"; break;
		Case "materia"	: $strRetTipoFilho = "materia"; break;
	}
	return($strRetTipoFilho);
}

//-- NOVA ----------------------------------------------------------------------
// Facilita a montagem dos filhos de RV, EX, SE e MA (com LEFT OUTER JOIN)
//----------------------------------------------------------- by Aless & Davi --
function montaChildsSQL($prTipo, $prCodigo, $prArea, $prOrdenacao1, $prOrdenacao2) {
	$strTipoFilho = retTipoFilho($prTipo);
	//if($strTipoFilho == $prTipo){ $strTipoFilho = $strTipoFilho . " AS t1"; }

	$strSQL = " SELECT lj_" . $strTipoFilho . ".cod_" . $strTipoFilho . "
				      ,lj_" . $strTipoFilho . ".titulo
				      ,lj_" . $strTipoFilho . ".texto
				      ,lj_" . $strTipoFilho . ".descricao
				      ,lj_" . $strTipoFilho . ".rotulo_menu AS rotulo
				      ,lj_" . $strTipoFilho . ".img
				      ,lj_" . $strTipoFilho . ".img_thumb
				      ,lj_" . $strTipoFilho . ".img_thumb_over
				      ,lj_" . $strTipoFilho . ".img_descricao
				      ,lj_" . $strTipoFilho . ".dtt_publicacao AS dtt_pub
				      ,lj_" . $prTipo . ".cod_"                 . $prTipo . "
				      ,lj_" . $prTipo . ".titulo AS "           . $prTipo . "_titulo 
				      ,lj_" . $prTipo . ".texto AS "            . $prTipo . "_texto
				      ,lj_" . $prTipo . ".descricao AS "        . $prTipo . "_descricao
				      ,lj_" . $prTipo . ".rotulo_menu AS "      . $prTipo . "_rotulo
				      ,lj_" . $prTipo . ".img AS "              . $prTipo . "_img
				      ,lj_" . $prTipo . ".img_thumb AS "        . $prTipo . "_img_thumb
				      ,lj_" . $prTipo . ".img_thumb_over AS "   . $prTipo . "_img_thumb_over
				      ,lj_" . $prTipo . ".img_descricao AS "    . $prTipo . "_img_descricao
				      ,lj_" . $prTipo . ".dtt_publicacao AS "   . $prTipo . "_dtt_pub ";

	$strSQL .= "   FROM lj_" . $prTipo;
	if($prTipo != $strTipoFilho) {
		$strSQL .=  " LEFT OUTER JOIN 
					    lj_" . $strTipoFilho . " ON lj_" . $prTipo . ".cod_" . $prTipo . " = lj_" . $strTipoFilho . ".cod_" . $prTipo;
	}
	//Caso deseje pesquisar incluíndo o parâmetro área
	If(trim($prArea) != "") { $strSQL .= " , lj_site_area "; }

	$strSQL .= "  WHERE lj_" . $prTipo . ".cod_" . $prTipo . " = " . $prCodigo . " AND lj_" . $strTipoFilho . ".dtt_inativo IS NULL ";

	//Caso deseje pesquisar incluíndo o parâmetro área
	if(trim($prArea) != "") {
		$strSQL .= "   AND lj_site_area.tipo = '" . $prTipo . "' 
					   AND lj_site_area.cod_site_area = '" . $prArea . "' 
					   AND lj_" . $strTipoFilho . ".cod_revista = lj_site_area.cod_revista ";
	}
	
	$strSQL .= " ORDER BY lj_" . $strTipoFilho . ".dtt_publicacao" . " " . $prOrdenacao1 . ", lj_" . $strTipoFilho . ".ordem  " . $prOrdenacao2;
	return($strSQL);
}

function printBanner($prCodBanner, $prArquivo, $prLargura, $prAltura, $prBorda, $prTipo, $objConn){ 
	$strAux = "";
	if($prLargura != "") { $strAux .= " width =\"" . $prLargura . "\""; }
	if($prAltura  != "") { $strAux .= " height =\"" . $prAltura . "\""; }
	if($prBorda   != "") { $strAux .= " border=\"" . $prBorda . "\""; } else { $strAux .= " border=\"0\""; }
	$prArquivo = strtolower(trim($prArquivo));
	
	switch($prTipo){
		case "IMG"		: echo("<img src='" . $prArquivo . "'" . $strAux . " alt=\"\">"); break;
		case "FLASH"	: echo("<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' " . $strAux. ">
								<param name='movie' value='" . $prArquivo . "'>
								<param name='quality' value='high'>
								<embed src='" . $prArquivo . "' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' " . $strAux . "></embed>
							  </object>");	break;
		case "IFRAME"	: echo("<iframe src=\"" . arquivo . "\" width=\"" . largura . "\" height=\"" . altura . "\" frameborder=\"0\" scrolling=\"no\"></iframe>"); break;
	}
	
   if ($prCodBanner>0) {
 	   //$objConn = abreDBConn("prostudio_apas"); // ** PROVISÓRIO **
	   //$objConn = abreDBConn(CFG_DB);
	   $objConn->beginTransaction();
	   try{
			$strSQL  = "INSERT INTO stats_bannerlog (cod_banner, bl_tipo, bl_sessionid, bl_ipaddress, sys_usr_ins, sys_dtt_ins) ";
			$strSQL .= " VALUES ('".$prCodBanner."','visit','".session_id()."','".$_SERVER["REMOTE_ADDR"]."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP)";
			$objConn->query($strSQL);
			$objConn->commit();
	   }
		catch(PDOException $e){
			$objConn->rollBack();
			//ATENÇÃO
			// Colocamos o campo bl_unique_computed como UNIQUE, então gravamos as visitas por sessão, e no atualizar (F5) simplemente 
			//dentro de uma mesma sessão o sistema não contará, gerando esta exception e abortando a inserção.
			// ------------------------------------------------------------------------------------------------------------------
			//mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			//die();
	   }
	  //$objConn = NULL;
  }
}

/* -------------------------------------------------------------------------------------------------------------- */
/* FIM - Funções de LÓGICA de REVISTA --------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------------------------------- */
?>