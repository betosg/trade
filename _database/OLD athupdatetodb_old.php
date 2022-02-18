<?php
include_once("athdbconn.php");
include_once("athsendmail.php");
include_once("../_class/multi-language/multilang.php");
include_once("../_class/multi-language/functions.inc.php");

/*
************************	************************	************************	************************	************************	************************
*    VERSÃO 1.3.2PHP   *	*    VERSÃO 1.3.1PHP   *	*    VERSÃO 1.3.0PHP   *	*    VERSÃO 1.2.0PHP   *	*    VERSÃO 1.1.0PHP   *	*    VERSÃO 1.0.5PHP   *
*      29/02/2007      *	*      28/01/2007      *	*      21/12/2007      *	*      22/10/2007	   *	*      12/09/2007      *	*      31/08/2007      *
************************	************************	************************	************************	************************	************************

************************	************************	************************
*    VERSÃO 1.3.5PHP   *	*    VERSÃO 1.3.4PHP   *	*    VERSÃO 1.3.3PHP   *
*      16/04/2009      *	*      22/01/2009      *	*      23/05/2007      *
************************	************************	************************

*************************************************************************************
*************************** Nome de Campos de Formulários ***************************
*************************************************************************************
1° - Crie um prefixo - ex: DBVAR_
2° - Escolha o tipo de dados que a tabela recebe para este campo:
STR - Texto e Memo
NUM - Número inteiro
MOEDA - Número em ponto flutuante com 2 casas decimais
MOEDA4CD - Número em ponto flutuante com 4 casas decimais
AUTODATE - Data/Hora (obs: o valor para este campo deve ser vazio)
BOOL - Sim/Não
DATE - Data
DATETIME - Data e hora
STATUS - Status de ativo/inativo
3° - Escreva o nome do campo na tabela
4° - Se o campo for requerido adicione "ô" ao final de seu nome

Obs: Sempre adicione _ após o Prefixo e o Tipo_campo_tabela

Ex:  Prefixo   Tipo_campo_Tabela    Nome_campo_Tabela   Nome_campo_formulário  É Requerido
	 DBVAR_          STR_               TEXTO             DBVAR_STR_TEXTO         Não
	 VAR_            NUM_               CODIGO            VAR_NUM_CODIGOô         Sim

Exemplo prático ...
<form name="forminsert" action="../_database/athUpdateToDB.asp" method="POST">
 <input type="hidden" name="DEFAULT_TABLE" value="RV_REVISTA">
 <input type="hidden" name="DEFAULT_DB" value="[database.mdb]">
 <input type="hidden" name="FIELD_PREFIX" value="DBVAR_">
 <input type="hidden" name="RECORD_KEY_NAME" value="COD_REVISTA">
 <input type="hidden" name="DEFAULT_LOCATION" value="../modulo_revista/update.asp">
 <input type="hidden" name="DBVAR_AUTODATE_DT_CRIACAO" value="">
...	

**** LEGENDA ***
Esta página precisa receber os seguintes valores do formulário que a chama:
DEFAULT_TABLE = Tabela a ser feita a deleção
DEFAULT_DB = Variável do banco de dados incluso no arquivo config.inc (CFG_DB ou CFG_DB)
FIELD_PREFIX = Prefixo do nome do campo do formulário (ex: nome: DBVAR_NUM_COD_CLI prefixo: DBVAR_)
RECORD_KEY_NAME = Nome do campo chave da tabela a ser inserido o registro (usado para redirecionar para o último registro)
DEFAULT_LOCATION = Página e parâmetros para o redirecionamento
Obs: DEFAULT_LOCATION irá redirecionar para a página que está em seu value, para continuar na mesma página,
insira o link da própria página em que está


RECORD_KEY_SELECT = Nome de um campo extra (usado para o redirecionamento correto quando se insere imagens)
RECORD_KEY_NAME_EXTRA = Nome de um campo extra se for necessário
RECORD_KEY_VALUE_EXTRA = Valor de um campo chave extra se for necessário
*****************************************************************************************************************************
*/  
  
define("DEFAULT_TABLE"   , request("DEFAULT_TABLE"));
define("FIELD_PREFIX"    , request("FIELD_PREFIX"));
define("RECORD_KEY_NAME" , request("RECORD_KEY_NAME"));
define("RECORD_KEY_VALUE", request("RECORD_KEY_VALUE"));
define("RECORD_KEY_TYPE" , request("RECORD_KEY_TYPE"));
define("EXIBE_MENSAGEM"  , request("EXIBE_MENSAGEM"));
define("DEFAULT_LOCATION", request("DEFAULT_LOCATION"));
 
//die(RECORD_KEY_VALUE . "<br>". RECORD_KEY_NAME);
 
$strAux = $_POST;
	
if($strAux == array()){
	$strAux = $_GET;
}
	
/*
 //--Debug dos "fields" e seus respectivos "values" e "types" recebidos 
	 
$strDebug  = "<BR>DEFAULT_TABLE:    " .  DEFAULT_TABLE;
$strDebug .= "<BR>FIELD_PREFIX:     " .  FIELD_PREFIX;
$strDebug .= "<BR>DEFAULT_LOCATION: " .  DEFAULT_LOCATION;
$strDebug .= "<BR><BR>AUXSTR:       " .  print_r($strAux) . "<BR><BR>";
	 
die($strDebug);
//*/

$strMyTbFields  = "";
$strMyTbValues  = "";
$strMyTbSetFields = "";
$boolMyFRequired = true;
 
foreach($strAux as $strCampo => $strValor){ 
	
	$strAuxValue = str_replace("'","''",$strValor);
	
	if(strpos($strCampo,FIELD_PREFIX) === 0){
		$strCampo    = str_replace(FIELD_PREFIX,"",$strCampo);
		$strAuxType  = substr($strCampo,0,strpos($strCampo,"_"));
		$strAuxField = substr($strCampo,strpos($strAuxType,"_") + strlen($strAuxType) + 1);
		
		if(strpos($strAuxField,"ô") !== false) {
			$strAuxField = str_replace("ô","",$strAuxField);
			//$strMyFRequired .= "(\$_REQUEST['" . FIELD_PREFIX . $strAuxField . "ô']=\"\")||";
			/*******************************************************************\
			/   Em vez de fazer através de um eval a validação dos campos, 	    \
			/   agora é feita com uma comparação de cada campo com o resultado  \
			/   da comparação do campo anterior                                 \
			/*******************************************************************/
			$boolMyFRequired = $boolMyFRequired && (request(FIELD_PREFIX  . $strAuxType . "_" . $strAuxField . "ô") != "");
		}
		
		/*
		//Substitui todos os caracteres especiais pelo respectivo código HTML
		$strAuxValue = ReturnCodigo($strAuxValue)
		$strAuxValue = Replace($strAuxValue, "'", "''")
		//*/
		switch(strtolower($strAuxType)){
			case "num": 	 (($strAuxValue == "") || (! is_numeric($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = ("'" . $strAuxValue . "'");
							 break;
						
			case "str":		 ($strAuxValue == "") ? $strAuxValue = "NULL" : $strAuxValue = ("'" . $strAuxValue . "'");
							 break;
						
			case "autodate": $strAuxValue = ("current_timestamp");
							 break;
						
			case "bool":	 ($strAuxValue == "") ? $strAuxValue = ("FALSE") : NULL;
							 break;
								
			case "cripto": 	 //TESE: Se vier 20 caracteres significa que possivelmente não foi 
			                 //alterada a senha se vier menos de 20, é porque algo foi digitado
							 (($strAuxValue == "")) ? $strAuxValue = "NULL" : $strAuxValue = "'" . md5($strAuxValue) . "'";
							 break;
							 
			case "date": 	 $strAuxValue = cDate(CFG_LANG, $strAuxValue, false);
							 (($strAuxValue == "") || (!is_date($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = "'" . $strAuxValue . "'";
							 break;
							 
			case "datetime": $strAuxValue = cDate(CFG_LANG, $strAuxValue, true);
							 (($strAuxValue == "") || (!is_date($strAuxValue))) ? $strAuxValue = "NULL" : $strAuxValue = "'" . $strAuxValue . "'";
							 break;
							 
			case "moeda":    if($strAuxValue == ""){
							   $strAuxValue = "NULL";
							 }
							 else{
							   //$strAuxValue = number_format($strAuxValue,2);
							   $strAuxValue = str_replace(".","",$strAuxValue);
							   $strAuxValue = str_replace(",",".",$strAuxValue);
							 }
							 break;
			case "moeda4cd": if($strAuxValue == ""){
							   $strAuxValue = "NULL";
							 }
							 else{
							   //$strAuxValue = number_format($strAuxValue,4);
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
		}
	  
		/*	  
		//Debug dos "fields" e seus respectivos "values" e "types" recebidos 
		$strDebug  = "TYPE:  " . $strAuxType  . "<br>";
		$strDebug .= "FIELD: " . $strAuxField . "<br>";
		$strDebug .= "VALUE: " . $strAuxValue . "<br>";
		exit($strDebug);
		//*/
		
		$strAuxValue = str_replace("\\","\\\\",$strAuxValue);
		
		/*$strMyTbFields = $strMyTbFields . $strAuxField . ",";
		$strMyTbValues = $strMyTbValues . $strAuxValue . ",";*/
		$strMyTbSetFields .= "," . ($strAuxField . "=" . $strAuxValue);
	}
}

$objConnUpdateToDB = abreDBConn(CFG_DB);
$objConnUpdateToDB->beginTransaction();

		
if(!$boolMyFRequired) {
	mensagem("err_dados_titulo","err_dados_submit_desc","","javascript:history.back();","aviso",1);
	die();
}
else{
	if($strMyTbSetFields != ""){
		try{
			// Está assim porque há só um tipo de chave no sistema que é o SERIAL (AUTO-INCREMENTO)
			$strSqlUpdateToDB = "UPDATE " . DEFAULT_TABLE . " SET " . $strMyTbSetFields . " WHERE " .  RECORD_KEY_NAME . "=" . RECORD_KEY_VALUE ;
			
			$strSqlUpdateToDB = trim(str_replace("SET ,","SET ",$strSqlUpdateToDB));
			
			//die("<br> DEBUG: \$strSqlUpdateToDB<BR><BR>" . ($strSqlUpdateToDB));
			$objConnUpdateToDB->query($strSqlUpdateToDB);
			$objConnUpdateToDB->commit();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConnUpdateToDB->rollBack();
			die();
		}
	}
}

//sendEmail("no-reply@proevento.com.br", "", "", "ath.atendimento@gmail.com", "PROEVENTO STUDIO - (" . getsession(CFG_SYSTEM_NAME . "_modulo_atual") . " - UPD)", "Teste", true);
 
if(EXIBE_MENSAGEM != 0){
	mensagem("Mensagem", "Seu cadastro foi alterado com sucesso", "", DEFAULT_LOCATION, "standardinfo", 1);
}
else{
	if(DEFAULT_LOCATION != "") {
		redirect(DEFAULT_LOCATION);
	}
	else {
		echo(RECORD_KEY_VALUE);
	}
} 
?>