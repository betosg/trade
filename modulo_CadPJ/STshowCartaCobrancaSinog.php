<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
include_once("../_database/athsendmail.php");
//erroReport();
// INI: INCLUDE requests ORDI�RIOS -------------------------------------------------------------------------------------
/*
 Por defini��o esses s�o os par�metros que a p�gina anterior de prepara��o (execaslw.php) manda para os executores.
 Cada executor pode utilizar os par�metros que achar necess�rio, mas por defini��o queremos que todos fa�am os
 requests de todos os par�metros enviados, como no caso abaixo:
 Vari�veis e Carga:
	 -----------------------------------------------------------------------------
	 vari�vel          | "alimenta��o"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora in�cio do relat�rio
	 $intRelCod		   | C�digo do relat�rioRodap� do relat�rio
	 $strRelASL		   | ASL - Conulta com par�metros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com par�metros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/T�tulo do relat�rio
	 $strRelDesc	   | Descri��o do relat�rio	
	 $strRelHead	   | Cabe�alho do relat�rio
	 $strRelFoot	   | Rodap� do relat�rio		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralat�rios - sem o nome da tabela do campo que ser� retornado
	 -----------------------------------------------------------------------------  */
//include_once("../modulo_ASLWRelatorio/_include_aslRunRequest.php");
// FIM: INCLUDE requests ORDI�RIOS -------------------------------------------------------------------------------------


// INI: INCLUDE funcionalideds B�SICAS ---------------------------------------------------------------------------------
/* Fun��es
	 filtraAlias($prValue)
	 ShowDebugConsuta($prA,$prB)
	 ShowCR("CABECALHO/RODAPE",str)
  A��es:
  	 SEGURAN�A: Faz verifica��o se existe usu�rio logado no sistema
  Vari�veis e Carga:
	 -----------------------------------------------------------------------------
	 vari�vel          | "alimenta��o"
	 -----------------------------------------------------------------------------
	 $strDIR           | Pega o diretporio corrente (usado na exporta��o) 
	 $arrModificadores | Array contendo os modificadores ([! ], [$ ], ...) do ASL
	 $strSQL           | SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
	 -----------------------------------------------------------------------------  */
//include_once("../modulo_ASLWRelatorio/_include_aslRunBase.php");
// FIM: INCLUDE funcionalideds B�SICAS ---------------------------------------------------------------------------------

$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");

$intCodPJ = request("var_cod_pj");
$intPagina = request("var_pagina");

$intTamanho = 50;




//Se n�o veio nada no c�digo de PJ ent�o � para gerar para v�rios porque 
//vai estar sendo executado via relat�rios ASL. Logo, deve paginar.


$objConn = abreDBConn(CFG_DB);
$strCartaCobranca             = getVarEntidade($objConn,"msg_cobranca_atraso_parte1");
$strCartaCobrancaRemetente    = getVarEntidade($objConn,"msg_cobranca_remetente");
$strCartaCobrancaTelefone     = getVarEntidade($objConn,"msg_cobranca_telefone");
$strCartaCobrancaLogotipo     = str_ireplace("..","",getVarEntidade($objConn,"logotipo_empresa"));
$strCartaCobrancaEmailAssunto = getVarEntidade($objConn,"msg_cobranca_email_assunto");

//die();
try{
	
		$strSQL = "	SELECT out_cod_pj, out_razao_social, out_matricula, out_endprin_cep, out_endprin_logradouro
		, out_endprin_numero, out_endprin_complemento, out_endprin_bairro, out_endprin_cidade, out_endprin_estado
		, out_vlr_conta, out_dt_vcto, out_historico, out_email , out_titulo
					    FROM sp_busca_assoc_carta_cobranca (".$intCodPJ.",'','',1,".$intTamanho.",'".$strDirCliente."','')  ";

	
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}


if  ($objResult->rowCount() < 1){
	mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("sem_titulos_carta_cobranca_atraso",C_NONE),"","info",1);
	die();
}

$strMailCorpo = "<html> 
		<head>
		<title>".CFG_SYSTEM_NAME."</title>
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
		<link href='https://tradeunion.proevento.com.br/_tradeunion/_css/".CFG_SYSTEM_NAME.".css' rel='stylesheet' type='text/css'>
		<script>
		</script>
		<style>
		.folha { page-break-after: always; }
		.texto { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; }
		</style>
		</head>
		<body bgcolor='#ffffff'  leftmargin='20'>
		<table style='padding-top:20px'>
		<tr>
			<td class='texto'><img src='https://tradeunion.proevento.com.br/_tradeunion/".$strCartaCobrancaLogotipo."'  border='0'></td>
		</tr>
		<tr>
			<td class='texto'>";

$strEmail = "";
$strTabelaTitulos = "<table width='60%'>
							<tr>
								<td align='left' class='texto'>N� T�tulo</td>
								<td align='left' class='texto'>Vencimento</td>
								<td align='right' class='texto'>Valor</td>
								<td class='texto'></td>
							</tr>
							<tr>
								<td colspan='4'><hr></td>
							</tr>";
foreach($objResult as $objRS) {
		if ($strEmail == ""){
			$strEmail = getValue($objRS,"out_email");
			$strRazaoSocial = getValue($objRS,"out_razao_social");
		}
	$strCriptBoleto = base64_encode(getValue($objRS,"out_titulo")."|".CFG_DB);
$strTabelaTitulos .= "
	<tr>
		<td align='left'   class='texto'><strong>".getValue($objRS,"out_titulo")."</strong></td>
		<td align='left'   class='texto'><strong>".getValue($objRS,"out_dt_vcto")."</strong></td>
		<td align='right'  class='texto'><strong>".FloatToMoeda(getValue($objRS,"out_vlr_conta"))."</strong></td>
		<td align='right'  class='texto'><strong><a href='https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_boleto=".$strCriptBoleto."' target='_blank'>Boleto</a></strong></td>
	</tr>";
$strTituloCobrado = "[t�tulo:".getValue($objRS,"out_titulo")."|vcto:".getValue($objRS,"out_dt_vcto")."|valor:".FloatToMoeda(getValue($objRS,"out_vlr_conta"))."|boleto:https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_boleto=".$strCriptBoleto."]\r\n";
}
$strTabelaTitulos .= "</table>";


$objResult->closeCursor();



$strMailCorpoFim = "</td></tr>";
$strMailCorpoFim .= "</table>";
$strMailCorpoFim .= "</body>";
$strMailCorpoFim .= "</html>";

$strMsgLiberacao = $strCartaCobranca;

if ($strMsgLiberacao != "") {

	//$strMsgLiberacao = str_ireplace("[tag_logotipo]"                  , $strCartaCobrancaLogotipo  , $strMsgLiberacao);
	$strMsgLiberacao = str_ireplace("[tag_razao_social]"              , $strRazaoSocial            , $strMsgLiberacao);    
	$strMsgLiberacao = str_ireplace("[tag_email_ambiente_cobranca]"   , $strCartaCobrancaRemetente , $strMsgLiberacao);               
	$strMsgLiberacao = str_ireplace("[tag_tabela_titulo]"             , $strTabelaTitulos          , $strMsgLiberacao);     
	$strMsgLiberacao = str_ireplace("[tag_telefone_cobranca]"         , $strCartaCobrancaTelefone  , $strMsgLiberacao);         

	 
	
	echo $strMailCorpo = $strMailCorpo .$strMsgLiberacao . $strMailCorpoFim;
	//echo $strEmail ;

try{
	$strSQL = "INSERT INTO cad_pj_historico (   cod_pj    ,   historico            ,   tipo          ,    email        ,	sys_usr_ins                                 , sys_dtt_ins )";
    $strSQL .= "                     VALUES (".$intCodPJ.",	'".$strTituloCobrado."', 'CARTA_COBRAN�A','". $strEmail ."','".getsession(CFG_SYSTEM_NAME . "_id_usuario")."', CURRENT_TIMESTAMP)";	

$objConn->query($strSQL);
}
catch(PDOException $e){
mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
die();	
}

	$strEmail = explode(";", $strEmail);
	//echo count($strEmail);

   for ($x = 0; $x <= count($strEmail)-1; $x++) {
	//echo $x.": ".$strEmail[$x]." <br>";
		if ($strEmail[$x] !=""){
				emailNotify($strMailCorpo, $strCartaCobrancaEmailAssunto, $strEmail[$x], CFG_EMAIL_SENDER,"", $strCartaCobrancaRemetente, $strCartaCobrancaRemetente);
				//emailNotify($prBody      , $prSubject                   , $prEmails    , $prFrom         , $prDebug="", $prReply="", $prReplyName="")
		}
	}
	
}



$objConn = NULL;
?>
