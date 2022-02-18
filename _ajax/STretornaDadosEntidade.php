<?php
/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***            DEFINIÇÃO DE PARÂMETROS            ***/
/*****************************************************/
$strCod  = request("var_codcadastro");
$strTipo = strtolower(request("var_tipo"));

/***            VALIDAÇÃO DOS PARÂMETROS           ***/
/*****************************************************/
if(($strCod != "") and ($strTipo!="")){
	/***           ABERTURA DO BANCO DE DADOS          ***/
	/*****************************************************/
	$objConn = abreDBConn(CFG_DB);
		
	/***            CONSULTA FONTE DOS DADOS           ***/
	/*****************************************************/
	try{
		if(strcmp($strTipo,"cad_pj") == 0){
			$strSQL = "SELECT COALESCE(cnpj,'') AS doc, COALESCE(razao_social,'') AS nome FROM " .$strTipo. " WHERE cod_pj =".$strCod;
		}else if(strcmp($strTipo,"cad_pj_fornec") == 0){
			$strSQL = "SELECT COALESCE(cnpj,'') AS doc, COALESCE(razao_social,'') AS nome FROM " .$strTipo. " WHERE cod_pj_fornec =".$strCod;	
		}else {
			$strSQL = "SELECT COALESCE(cpf,'') AS doc, COALESCE(nome,'') AS nome FROM cad_pf WHERE cod_pf=".$strCod;
		}
		
		$objResult = $objConn->query($strSQL);
		
		/***               TRATAMENTO DE ERRO              ***/
		/*****************************************************/
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	
	/***         RETORNO DOS DADOS PARA O AJAX         ***/
	/*****************************************************/
	foreach($objResult as $objRS){
		echo(getValue($objRS,"nome")."|".getValue($objRS,"doc"));
	}
	
	/***         FECHAMENTO DO BANCO DE DADOS          ***/
	/*****************************************************/
	$objResult->closeCursor();
}
?>