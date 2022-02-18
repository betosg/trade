<?php
include_once("athdbconn.php");
include_once("athsendmail.php");
include_once("../_class/multi-language/multilang.php");
include_once("../_class/multi-language/functions.inc.php");

/*
************************	************************	************************	************************	************************
*    VERS�O 1.3.7PHP   *	*    VERS�O 1.3.6PHP   *	*    VERS�O 1.3.5PHP   *	*    VERS�O 1.3.4PHP   *	*    VERS�O 1.3.3PHP   *
*      13/02/2009      *	*      22/01/2009      *	*      29/04/2008      *	*      10/04/2008      *	*      29/02/2008      *
************************	************************	************************	************************	************************

************************	************************	************************	************************	************************	************************
*    VERS�O 1.3.2PHP   *	*    VERS�O 1.3.1PHP   *	*    VERS�O 1.2.0PHP   *	*    VERS�O 1.2.0bPHP  *	*    VERS�O 1.1.0PHP   *	*    VERS�O 1.0.3PHP   *
*      28/01/2008      *	*      20/12/2007      *	*      17/10/2007      *	*      12/09/2007      *	*      31/08/2007      *	*      29/08/2007      *
************************	************************	************************	************************	************************	************************

  
************************ Nome de Campos de Formul�rios ************************
*******************************************************************************
1� - Crie um prefixo - ex: DBVAR_
2� - Escolha o tipo de dados que a tabela recebe para este campo:
STR - Texto e Memo
NUM - N�mero inteiro
MOEDA - N�mero em ponto flutuante com 2 casas decimais
MOEDA4CD - N�mero em ponto flutuante com 4 casas decimais
AUTODATE - Data/Hora (obs: o valor para este campo deve ser vazio)
BOOL - Sim/N�o
DATE - Data
DATETIME - Data e hora
STATUS - Status de ativo/inativo
3� - Escreva o nome do campo na tabela
4� - Se o campo for requerido adicione "�" ao final de seu nome

Obs: Sempre adicione _ ap�s o Prefixo e o Tipo_campo_tabela

Ex:  Prefixo   Tipo_campo_Tabela    Nome_campo_Tabela   Nome_campo_formul�rio  � Requerido
	 DBVAR_          STR_               TEXTO             DBVAR_STR_TEXTO         N�o
	 VAR_            NUM_               CODIGO            VAR_NUM_CODIGO�         Sim

Exemplo pr�tico ...
<form name="forminsert" action="../_database/athInsertToDB.asp" method="POST">
 <input type="hidden" name="DEFAULT_TABLE" value="RV_REVISTA">
 <input type="hidden" name="DEFAULT_DB" value="[database.mdb]">
 <input type="hidden" name="FIELD_PREFIX" value="DBVAR_">
 <input type="hidden" name="RECORD_KEY_NAME" value="COD_REVISTA">
 <input type="hidden" name="DEFAULT_LOCATION" value="../modulo_revista/update.asp">
 <input type="hidden" name="DBVAR_AUTODATE_DT_CRIACAO" value="">
...	

**** LEGENDA ***
Esta p�gina precisa receber os seguintes valores do formul�rio que a chama:
DEFAULT_TABLE = Tabela a ser feita a dele��o
DEFAULT_DB = Vari�vel do banco de dados incluso no arquivo config.inc (CFG_DB ou CFG_DB)
FIELD_PREFIX = Prefixo do nome do campo do formul�rio (ex: nome: DBVAR_NUM_COD_CLI prefixo: DBVAR_)
RECORD_KEY_NAME = Nome do campo chave da tabela a ser inserido o registro (usado para redirecionar para o �ltimo registro)
DEFAULT_LOCATION = P�gina e par�metros para o redirecionamento
Obs: DEFAULT_LOCATION ir� redirecionar para a p�gina que est� em seu value, para continuar na mesma p�gina,
insira o link da pr�pria p�gina em que est�


RECORD_KEY_SELECT = Nome de um campo extra (usado para o redirecionamento correto quando se insere imagens)
RECORD_KEY_NAME_EXTRA = Nome de um campo extra se for necess�rio
RECORD_KEY_VALUE_EXTRA = Valor de um campo chave extra se for necess�rio
*****************************************************************************************************************************
*/
  
define("DEFAULT_TABLE"   , request("DEFAULT_TABLE"));
define("FIELD_PREFIX"    , request("FIELD_PREFIX"));
define("RECORD_KEY_NAME" , request("RECORD_KEY_NAME"));
define("DEFAULT_LOCATION", request("DEFAULT_LOCATION"));

define("RECORD_KEY_NAME_EXTRA" , request("RECORD_KEY_NAME_EXTRA"));
define("RECORD_KEY_VALUE_EXTRA", request("RECORD_KEY_VALUE_EXTRA"));
define("RECORD_KEY_SELECT"     , request("RECORD_KEY_SELECT"));

$strAux = $_POST;

if($strAux == array()){
	$strAux = $_GET;
}
	
/*
 //Debug dos "fields" e seus respectivos "values" e "types" recebidos 
 
$strDebug  = "<BR>DEFAULT_TABLE:    " .  DEFAULT_TABLE;
$strDebug .= "<BR>FIELD_PREFIX:     " .  FIELD_PREFIX;
$strDebug .= "<BR>DEFAULT_LOCATION: " .  DEFAULT_LOCATION;
$strDebug .= "<BR><BR>AUXSTR:       " .  print_r($strAux) . "<BR><BR>";
 
 exit($strDebug);
//*/

$strMyTbFields  = "";
$strMyTbValues  = "";
$boolMyFRequired = true;

foreach($strAux as $strCampo => $strValor){ 
	
	$strAuxValue = prepStr($strValor);
	
	if(strpos($strCampo,FIELD_PREFIX) === 0){
		$strCampo = str_replace(FIELD_PREFIX,"",$strCampo);
		$strAuxType  = substr($strCampo,0,strpos($strCampo,"_"));
		$strAuxField = substr($strCampo,strpos($strAuxType,"_") + strlen($strAuxType) + 1);
		
		if(strpos($strAuxField,"�") !== false) {
			$strAuxField = str_replace("�","",$strAuxField);
			/*******************************************************************\
			/   Em vez de fazer atrav�s de um eval a valida��o dos campos, 	    \
			/   agora � feita com uma compara��o de cada campo com o resultado  \
			/   da compara��o do campo anterior                                 \
			/*******************************************************************/
			$boolMyFRequired = ( $boolMyFRequired && (request(FIELD_PREFIX  . $strAuxType . "_" . $strAuxField . "�") != "") );
		}
		
		/*
		//Substitui todos os caracteres especiais pelo respectivo c�digo HTML
		$strAuxValue = ReturnCodigo($strAuxValue)
		$strAuxValue = Replace($strAuxValue, "'", "''")
		//*/
		switch(strtolower($strAuxType)){
			case "num": 	(($strAuxValue == "") || (! is_numeric($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = ("'" . $strAuxValue . "'");
							 break;
								
			case "str":		($strAuxValue == "") ? $strAuxValue = "NULL" : $strAuxValue = ("'" . $strAuxValue . "'");
							 break;
								
			case "autodate": $strAuxValue = ("current_timestamp");
							 break;
								
			case "bool":	 ($strAuxValue == "") ? $strAuxValue = ("false") : NULL;
							 break;
								
			case "cripto": 	 (($strAuxValue == "")) ? $strAuxValue = "NULL" : $strAuxValue = "'" . md5($strAuxValue) . "'";
							 break;
								
			case "date": 	 $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 (($strAuxValue == "") || (!is_date($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = "'" . $strAuxValue . "'";
							 break;
							 
			case "datetime": $strAuxValue = cDate(CFG_LANG, $strAuxValue, true);
							 (($strAuxValue == "") || (!is_date($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = "'" . $strAuxValue . "'";
							 break;
							 
			case "moeda":    if(($strAuxValue == "")){
							   $strAuxValue = "NULL";
							 }
							 else{
							   $strAuxValue = str_replace(".","",$strAuxValue);
							   $strAuxValue = str_replace(",",".",$strAuxValue);
							 }
							 break;
			case "moeda4cd": if($strAuxValue == ""){
							   $strAuxValue = "NULL";
							 }
							 else{
							   //$strAuxValue = number_format($strAuxValue,4,",","");
							   $strAuxValue = str_replace(".","",$strAuxValue);
							   $strAuxValue = str_replace(",",".",$strAuxValue);
							 }
							 break;
			case "status": //"A" para ativar e "I"para inativar
								if($strAuxValue == "I") 
									$strAuxValue = ("current_timestamp"); 
								else 
									$strAuxValue = "NULL"; 
							 break;
			case "captcha":  
							 $objCaptcha = new securimage();
							 
							 if (!$objCaptcha->check($strCode)){
								mensagem("err_dados_titulo","err_dados_captcha","","javascript:history.back()","erro",1);
								die();
							 }
							 break;
		}
	  
		/*	  
		//Debug dos "fields" e seus respectivos "values" e "types" recebidos 
		$strDebug  = "TYPE:  " . $strAuxType  . "<br>";
		$strDebug .= "FIELD: " . $strAuxField . "<br>";
		$strDebug .= "VALUE: " . $strAuxValue . "<br>";
		die($strDebug);
		//*/
		
		$strAuxValue = str_replace("\\","\\\\",$strAuxValue);
		
		$strMyTbFields .= $strAuxField . ",";
		$strMyTbValues .= $strAuxValue . ",";
	} elseif (preg_match("/^captcha.*/i",$strCampo)) {
		 $objCaptcha = new securimage();
		 
		 $strAuxValue = request($strCampo);
		 
		 if (!$objCaptcha->check($strAuxValue) && ($strAuxValue != "" || preg_match("/.*�$/i",$strCampo))) {
			mensagem("err_dados_titulo","err_dados_captcha","","javascript:history.back()","erro",1);
			die();
		 }
	}
	
}

$objConnInsertToDB = abreDBConn(CFG_DB);
$objConnInsertToDB->beginTransaction();
	
if(!$boolMyFRequired) {
	mensagem("err_dados_titulo","err_dados_submit_desc","","javascript:history.back();","aviso",1);
	die();
}
else {
	try{
		$strSqlInsertToDB = "INSERT INTO " . DEFAULT_TABLE . " (" . $strMyTbFields . ") VALUES (" . $strMyTbValues . ")";
		$strSqlInsertToDB = trim(str_replace(",)",")",$strSqlInsertToDB));
		//die("<br> DEBUG: \$strSqlInsertToDB<BR><BR>" . ($strSqlInsertToDB));
		$objConnInsertToDB->query($strSqlInsertToDB);
		$objConnInsertToDB->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConnInsertToDB->rollBack();
		die();
	}
}

//sendEmail("no-reply@proevento.com.br", "", "", "ath.atendimento@gmail.com", "PROEVENTO STUDIO - (" . getsession(CFG_SYSTEM_NAME . "_modulo_atual") . " - INS)", "Teste", true);
	
/*****************************************************
 Seleciona o �ltimo inserido...
 e repassa para o local indicado via "var_chavereg"
******************************************************/
try{
	$strSQL = "SELECT Max(" . RECORD_KEY_NAME . ") AS COD FROM " . DEFAULT_TABLE;
	$objRS = $objConnInsertToDB->query($strSQL)->fetch();
	$strCodigo = $objRS["cod"];
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
/*
//Debug CODIGO e REDIRECT
$strDebug  = $strCodigo . "<BR>";
$strDebug .= DEFAULT_LOCATION . "?var_chavereg=" .  $strCodigo;
$strDebug .= $strSQL . "<BR>";
die($strDebug);
//*/

$objRS = NULL;
$objConnInsertToDB = NULL;

if(DEFAULT_LOCATION != "") {
	if(strpos(DEFAULT_LOCATION, "?") > 0){
		redirect(DEFAULT_LOCATION . "&var_chavereg=" . $strCodigo);
	}
	else{
		redirect(DEFAULT_LOCATION . "?var_chavereg=" . $strCodigo);
	}
}
else {
	echo($strCodigo);
}
 ?>