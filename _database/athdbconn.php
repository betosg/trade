<?php
/* KERNELPS - Biblioteca de fun��es (athdbconn.php) -------------------------------------------------------- */
/* --------------------------------------------------------------------------------------------------------- */
/* Todas as PHP criadas ou sobrescritas pela equipe para todos os sistemas do KERNELPS (fun��es gerais)      */ 
/* est�o nesse documento.  Quando for inserir uma nova fun��o aqui, observe as categorias em que elas est�o  */
/* conforme os coment�rios abaixo.												                             */ 	
/* ----------------------------------------------------------------------------- revised by Aless 17/05/11 - */
/*
 INI - �NDICE de fun��es -----------------------------------------------------------------------------------

 DBCONECTION/SISTEMA
	 abreDBConn($prDBName)
	 getError(&$prDBConn)
	 messageDlg($prTipoIcone, $prTitulo, $prAviso, $prTextoAdic="", $prHyperlink="", $prFlagHTML=0)

 *** ESTA FUNCAO TORNOU-SE OBSOLETA, DEVE SER SUBSTITUIDA POR "messageDlg" ***
	 mensagem($prTitulo, $prAviso, $prAdText="", $prHyperlink="", $prAcao="standardinfo", $prFlagHTML=0, $prBackground="default")
 FIM - �NDICE de fun��es -----------------------------------------------------------------------------------
*/

session_start();               // Inicia o session
session_cache_limiter("none"); // ATEN��O!!! Esta linha estipula o tipo de cache que as p�ginas ter�o. 
set_time_limit(600); 		   // Limite de tempo para execu��o do script em si (p�gina php)

include_once("athutils.php");
include_once("STathutils.php");
include_once("../_class/multi-language/multilang.php");
include_once("../_class/multi-language/functions.inc.php");

include_once("STconfiginc.php"); // S�o as constantes de configura��o b�sica, tais como cores, banco e etc...

function abreDBConn($prDBName){
	if($prDBName != "") {
		try{
			$strStrCon = "pgsql:host=" . CFG_DB_HOST . ";port=" . CFG_DB_PORT . ";dbname=" . $prDBName . ";user=" . CFG_DB_USER . ";password=";
			$objConn   = new PDO($strStrCon . CFG_DB_PASS);
			$objConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //For�a para que sejam mostrado erros, se existirem.
			return($objConn);
		} catch(PDOException $e) {
			messageDlg(C_MSG_ERRO
					   ,"BD indispon�vel / DB unavaliable."
					   ,"O sistema encontra-se em manuten��o. Aguarde alguns instantes e tente novamente, ou entre em contato com o administrador.<br>The system is down for maintenance. Wait a few seconds and try again or contact your administrator."
					   ,$e->getMessage() . "<br>" . $strStrCon . "******" ,"",1);
			die();
	    }
	}
	/*else {
		echo("<script>top.location.href = 'http://" . (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") ? $_SERVER["SERVER_NAME"] : $_SERVER["SERVER_NAME"] . "/" . CFG_SYSTEM_NAME) . "'</script>");
	}*/
}

function getError(&$prDBConn){
	if(!is_null($prDBConn)){
		$arrERROR = $prDBConn->errorInfo();
		return($arrERROR[2]);
	}else{
		return(null);
	}
}


/* INI: messageDlg ------------------------------------------------------------------------- */
/* Fun��o nova para as mensagens de sistema para substituir a "mensagem"
Par�metros:
  prTipoIcone: tipo do �cone da mensagem, utilizar constantes C_MSG_INFO, C_MSG_AVISO e C_MSG_ERRO
  prTitulo   : t�tulo da mensagem (dever� chegar J� TRADUZIDO)
  prAviso    : aviso da mensagem (dever� chegar J� TRADUZIDO)
  prTextoAdic: texto adicional, cont�m a mensagem de erro pura do prestador de servi�o (SysOp, DB, WebService, etc), n�o tem tradu��o
  prHyperlink: determina a a��o do bot�o 'ok' da mensagem, se estiver vazio nem aparece
  prFlagHTML : determina se mensagem ser� colocada dentro de um HTML ou n�o
  
Observa��o:
  O campo prTextoAdic pode conter informa��es extras que ser�o tratadas por essa fun��o, vai vir dentro da string da seguinte forma
  [KPS_INFO]/[KPS_AVISO]/[KPS_ERRO]
  Essa informa��o permite modificar o tipo da mensagem, o �cone al�m de fazer com que o texto que venha ap�s essa TAG seja concatenado na parte de aviso da mensagem e neste caso
  
Exemplos de uso:
  messageDlg(C_MSG_AVISO,getTText("titulo1",C_NONE),getTText("aviso1",C_NONE));
  messageDlg(C_MSG_ERRO ,getTText("titulo2",C_NONE),getTText("aviso2",C_NONE));
  messageDlg(C_MSG_INFO ,getTText("titulo" ,C_NONE),getTText("aviso3",C_NONE)." ","ASDFSAD ASD FUI SADOIFUSADIOFU");
  messageDlg(C_MSG_INFO ,getTText("titulo" ,C_NONE),getTText("aviso4",C_NONE)." ","SQLSTATE[P0001]: Raise exception: 7 ERRO: [KPS_INFO]aviso_dados_catalogo","http://www.terra.com.br");
  messageDlg(C_MSG_ERRO ,getTText("titulo" ,C_NONE),getTText("aviso5",C_NONE)." ","SQLSTATE[P0001]: Raise exception: 8 ERRO: [KPS_INFO]aviso_dados_catalogo","javascript:history.back();");
*/
function messageDlg($prTipoIcone, $prTitulo, $prAviso, $prTextoAdic="", $prHyperlink="", $prFlagHTML=0){
	if (stristr($prTextoAdic,"[KPS_")) {
		//O campo prTextoAdic pode conter informa��es extras que ser�o tratadas por essa fun��o, vai vir dentro da string da seguinte forma
 		//[KPS_INFO]/[KPS_AVISO]/[KPS_ERRO], essa informa��o permite modificar o tipo da mensagem, o �cone al�m de fazer com que o texto 
		//que venha ap�s essa TAG seja concatenado na parte de aviso da mensagem e neste caso
		$arrA = explode("[KPS_",$prTextoAdic);
		$arrB = explode("]",$arrA[1]);
		$prTipoIcone = strtolower($arrB[0]);
		if ($prTipoIcone == "info")  $prTipoIcone = C_MSG_INFO;
		if ($prTipoIcone == "aviso") $prTipoIcone = C_MSG_AVISO;
		if ($prTipoIcone == "erro")  $prTipoIcone = C_MSG_ERRO;
		$strAvisoExtra = $arrB[1];
		
		$objLangLocal = new phpMultiLang("../_database/errlang","../_database/errlang");
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
		$strAvisoExtra = $objLangLocal->GetString($strAvisoExtra);
		$objLang = NULL;
		
		$prAviso .= $strAvisoExtra;
	}
	
	//Indica se � para exibir a mensagem dentro um HTML ou s� dentro de uma tabela
	if($prFlagHTML != 0){ 
		echo("<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>");
		echo("<link href='../_css/" . CFG_SYSTEM_NAME . ".css' rel='stylesheet' type='text/css'></head>");
		echo("<body style='margin:8px;' text='#000000' bgcolor='#FFFFFF' >");		
	}
	
	echo("<center>");
	athBeginWhiteBox("100%"); //450 (j� foi 450 por padr�o, mas mudamso para ocupar a �rea toda de onde for aberta)
	echo("
		<table width='100%' align='center' border='0' cellpadding='5' cellspacing='0'>
		  <tr>
			<td valign='top' width='1%'><img src='../img/".$prTipoIcone."' hspace='5'></td>
			<td width='99%'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr><td style='font-size:14px;padding-left:5px;padding-bottom:5px;  text-align:left;'><b>".$prTitulo."</b></td></tr>
					<tr><td class='padrao_gde' style='padding:10px 0px 10px 5px; text-align:left;'><b>".$prAviso."</b></td></tr><tr><td height='1' bgcolor='#BDBDBF'></td></tr>
					<tr><td style='padding:10px 0px 10px 5px; text-align:left;'>".$prTextoAdic."</td></tr>
					<tr><td align='right' class='comment_peq'>" . basename($_SERVER["PHP_SELF"]) . "</td></tr>
				</table>
			</td>
		  </tr>
	  ");
	
	//se tiver prHyperlink, desenha um bot�o para retornar/ir a algum lugar
	if($prHyperlink != "") { 
		if (!(stristr(strtolower($prHyperlink),"javascript:"))) { $prHyperlink = "location.href='".$prHyperlink."'"; }
		echo("<tr><td align='right' colspan='2'><button onClick=\"" . $prHyperlink . "\">Ok</button></td></tr>"); 
	}  
	
	echo("</table>");
	athEndWhiteBox();
	echo("</center><br>");
	//aqui faz o fechamento do HTML se foi aberto mais acima
	if($prFlagHTML != 0) { echo("</body></html>"); }
}
/* FIM: messageDlg ------------------------------------------------------------------------- */

function mensagem($prTitulo, $prAviso, $prAdText="", $prHyperlink="", $prAcao="standardinfo", $prFlagHTML=0, $prBackground="default"){
  global $objConn;
  if(strpos(strtolower($prAcao),"standard") === false) {
	$objLangLocal = new phpMultiLang("../_database/errlang","../_database/errlang");
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
  
  // Tratamento para verifica��o se h� mensagem de aviso em EXCEPTION DE BANCO
  // Quando uma Exceptiion de DB tiver a indica��o na string do erro, esta indica��o
  // ser� tratada para exibi��o da imagem correspondente na mensagem
  // Exemplo: ... 
  // IF(var_cod_produto IS NULL)THEN
  //   	RAISE EXCEPTION '[KPS_AVISO]N�o foi encontrado um produto vigente do tipo Credencial.';
  // ...
  $strFlagMSG = "";
  $strFlagMSG = (stristr($prAdText,"[KPS_AVISO]")) ? "aviso" : $strFlagMSG;
  $strFlagMSG = (stristr($prAdText,"[KPS_ERRO]") ) ? "erro"  : $strFlagMSG;
  $strFlagMSG = (stristr($prAdText,"[KPS_INFO]") ) ? "info"  : $strFlagMSG;
  $strAcao    = ($strFlagMSG != "") ? $strFlagMSG : $strAcao;
  // Verifica se o FLAG est� NULO
  if($strFlagMSG != ""){
    $prAdText   = getError($objConn);
	$strREPLACE = ("[KPS_".strtoupper($strFlagMSG)."]");
	$prAdText   = str_replace($strREPLACE,"",$prAdText);
	$prAdText   = str_replace("ERRO:","",$prAdText);
  }
  
  if($prFlagHTML != 0){ 
    echo("<html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>");
    echo("<link href='../_css/" . CFG_SYSTEM_NAME . ".css' rel='stylesheet' type='text/css'></head>");
	echo("<body style='margin:8px;' text='#000000' bgcolor=\"#FFFFFF\" ");		
	if ($prBackground == "default") { echo("background=\"../img/bgFrame_" . CFG_SYSTEM_THEME . "_main.jpg\""); }
	else { echo("background='" . $prBackground . "'"); }
	echo(" >");
  }
  echo("<center>");
  athBeginWhiteBox("100%"); //450
  echo("
		<table width='100%' align='center' border='0' cellpadding='5' cellspacing='0'>
		  <tr>
			<td valign='top' width='1%'><img src='../img/mensagem_" .  $strAcao . ".gif' hspace='5'></td>
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
  
  if($prHyperlink != "") { echo("<tr><td align='right' colspan='2'><button class='inputcleanActionOk' onClick=\"location.href='" . $prHyperlink . "'\">Ok</button></td></tr>"); }  
  echo("</table>");
  athEndWhiteBox();
  echo("</center><br>");

  if($prFlagHTML != 0) { echo("</body></html>"); }
}
?>