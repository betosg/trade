<?php
/***************************************************************/
/**        SCRIPTS PHP PARA AJAX - INSTRU��ES B�SICAS         **/
/***************************************************************/
/** 1- Deve-se retirar qualquer caracter que estiver fora     **/
/**    da marca��o PHP (<?php | ?>), pois afetar� no retorno  **/
/**    de dados para o AJAX, inclusive espa�os e caracteres   **/
/**    invis�veis                                             **/
/**                                                           **/
/** 2- O separador de dados padr�o em coluna � o pipe "|"     **/
/**    e o de linhas � a quebra de linha "\n"     			  **/
/**                                                           **/
/** 3- Os cabe�alhos HTTP devem ser usados conforme o caso.   **/
/**    Por padr�o inicial, ele n�o poe em cache os dados mas  **/
/**    pode ser modificado de acordo com a especifica��o do   **/
/**    script                                                 **/
/**                                                           **/
/** 4- Os tratamentos de erros podem ser customizados mas     **/
/**    OBRIGATORIAMENTE precisa ter ACIMA da sa�da de dados a **/
/**    linha "header("HTTP/1.0 500 Server internal error");"  **/
/**                                                           **/
/***************************************************************/
/** Sugest�o:                                                 **/
/** Ap�s a leitura das instru��es remova esse coment�rio do   **/
/** novo script                                               **/
/***************************************************************/

/***          DEFINI��O DE CABE�ALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

/***              DEFINI��O DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***            DEFINI��O DE PAR�METROS            ***/
/*****************************************************/
$strSQL = request("var_sql");
$strSystem     = (request("var_db") == "") ? getsession("tradeunion_db_name") : request("var_db");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn($strSystem); 

/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
try {
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	/***               TRATAMENTO DE ERRO              ***/
	/*****************************************************/
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

/***         RETORNO DOS DADOS PARA O AJAX         ***/
/*****************************************************/
if($objResult->rowCount() > 0){
	$objRS = $objResult->fetch(); 
	echo (getValue($objRS,"cod_pj_contabil") . "|" . getValue($objRS,"razao_social") . "|" . getValue($objRS,"end_cep") . "|" . getValue($objRS,"email") . "|" . getValue($objRS,"contato") . "|" . getValue($objRS,"end_logradouro") . "|" . getValue($objRS,"end_numero") . "|" . getValue($objRS,"end_complemento") . "|" . getValue($objRS,"end_bairro") . "|" . getValue($objRS,"end_cidade") . "|" . getValue($objRS,"end_estado"). "|" . getValue($objRS,"end_fone1"). "|" . getValue($objRS,"end_fone2"));
}
?>