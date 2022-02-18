<?php
/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");
header("Content-Type:text/html; charset=iso-8859-1");

/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 

/***            DEFINIÇÃO DE PARÂMETROS            ***/
/*****************************************************/
$intCodigo	= request("var_chavereg");

/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
//Busca dados do cadastro
try {
	$strSQL = " SELECT cod_produto
					 , rotulo
					 , descricao
					 , obs
					 , valor
					FROM prd_produto 
					WHERE cod_produto = ".$intCodigo;
	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	/***               TRATAMENTO DE ERRO              ***/
	/*****************************************************/
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}
//fim da busca dos dados do cadastro

/***         RETORNO DOS DADOS PARA O AJAX         ***/
/*****************************************************/
$intI=0;
foreach($objResult as $objRS) {
	echo((($intI != 0) ? "\n" : "")
		. getValue($objRS,"cod_produto")."|"
		. getValue($objRS,"rotulo")."|"
		. getValue($objRS,"descricao")."|"
		. getValue($objRS,"obs")."|"
		. getValue($objRS,"valor"));		
	$intI++;
}
?>