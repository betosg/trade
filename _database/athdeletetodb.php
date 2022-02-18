<?php
include_once("athdbconn.php");
include_once("athsendmail.php");
include_once("../_class/multi-language/multilang.php");
include_once("../_class/multi-language/functions.inc.php");
/*
************************	************************
*    VERS�O 1.1.0PHP   *	*    VERS�O 1.0.3PHP   *
*      31/08/2007      *	*      29/08/2007      *
************************	************************
	
  
************************* Nome de Campos de Formul�rios *************************
*********************************************************************************
1� - Crie um prefixo - ex: DBVAR_
2� - Escolha o tipo de dados que a tabela recebe para este campo:
STR - Texto e Memo
NUM - N�mero
AUTODATE - Data/Hora (obs: o valor para este campo deve ser vazio)
BOOL - Sim/N�o
DATE - Data
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
  
define("DEFAULT_TABLE"   	 	, request("DEFAULT_TABLE"));
define("RECORD_KEY_NAME" 	 	, request("RECORD_KEY_NAME"));
define("RECORD_KEY_VALUE"	 	, request("RECORD_KEY_VALUE"));
define("RECORD_KEY_NAME_EXTRA"  , request("RECORD_KEY_NAME_EXTRA"));
define("RECORD_KEY_VALUE_EXTRA" , request("RECORD_KEY_VALUE_EXTRA"));
define("RECORD_KEY_TYPE" 	 	, request("RECORD_KEY_TYPE"));
define("BOOL_EXIBE_MENSAGEM"	, request("EXIBE_MENSAGEM"));
define("DEFAULT_LOCATION"	 	, request("DEFAULT_LOCATION"));

$strAux = $_POST;

if($strAux == ""){
$strAux = GET;
}

/*
//--Debug dos "fields" e seus respectivos "values" e "types" recebidos 
 
$strDebug  = "<BR>DEFAULT_TABLE:    " .  DEFAULT_TABLE;
$strDebug .= "<BR>FIELD_PREFIX:     " .  FIELD_PREFIX;
$strDebug .= "<BR>DEFAULT_LOCATION: " .  DEFAULT_LOCATION;
$strDebug .= "<BR><BR>AUXSTR:       " .  print_r($strAux) . "<BR><BR>";
 
exit($strDebug);
//*/

$objConnInsertToDB = abreDBConn(CFG_DB);
$objConnInsertToDB->beginTransaction();
	
try{
	if((RECORD_KEY_NAME != "" && RECORD_KEY_VALUE != "")){
		$strSqlInsertToDB = "DELETE FROM " . DEFAULT_TABLE . " WHERE " . RECORD_KEY_NAME . " IN (" . RECORD_KEY_VALUE . ")";
	   
		if(RECORD_KEY_NAME_EXTRA != ""){
			if(!is_numeric(RECORD_KEY_VALUE_EXTRA)){
				$strSqlInsertToDB = $strSqlInsertToDB . " AND " . RECORD_KEY_NAME_EXTRA . " = '" .  RECORD_KEY_VALUE_EXTRA . "'";
			}
			else{
				$strSqlInsertToDB = $strSqlInsertToDB . " AND " . RECORD_KEY_NAME_EXTRA . " = " .  RECORD_KEY_VALUE_EXTRA;
			}
		}
		
		// exit("<br> DEBUG: $strSqlInsertToDB<BR><BR>" & (StrSql_InsertToDB));
		$objConnInsertToDB->query($strSqlInsertToDB);
		$objConnInsertToDB->commit();
	}
	else{
		//mensagem("err_dados_titulo","err_dados_submit_desc","","javascript:history.back();","aviso",1);
		messageDlg(C_MSG_AVISO,"Mensagem / Message",getTText("Campos obrigatorios",C_NONE),"","javascript:history.back()",1);
		die();
	}
}
catch(PDOException $e){
	//mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	messageDlg(C_MSG_ERRO,"Mensagem / Message","",$e->getMessage() . "<br>SQL: ".$objConnInsertToDB,"",1);
	$objConnInsertToDB->rollBack();
	die();
}

//sendEmail("no-reply@proevento.com.br", "", "", "ath.atendimento@gmail.com", "PROEVENTO STUDIO - (" . getsession(CFG_SYSTEM_NAME . "_modulo_atual") . " - DEL)", "Teste", true);
  
if(BOOL_EXIBE_MENSAGEM != 0){
	//mensagem("Mensagem","Seu cadastro foi alterado com sucesso", "", DEFAULT_LOCATION, "standardinfo", 1);
	messageDlg(C_MSG_INFO,"Mensagem / Message",getTText("Cadastro deletado",C_NONE),"",DEFAULT_LOCATION,1);
}
else{
	redirect(DEFAULT_LOCATION);
} 
?>