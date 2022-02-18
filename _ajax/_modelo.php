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

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 

/***            DEFINI��O DE PAR�METROS            ***/
/*****************************************************/
$intCodPJ        = request("var_cod_pj");
$strNomeFantasia = request("var_nome_fantasia");
$strRazaoSocial  = request("var_razao_social");
$strNomeDoc      = request("var_nome_doc");
$strValorDoc     = request("var_valor_doc");

/***            VALIDA��O DOS PAR�METROS           ***/
/*****************************************************/
if($intCodPJ != "") {
	$strSQLAux = "    pj.cod_pj  =  " . $intCodPJ;
} elseif($strNomeFantasia != "" || $strNomeFantasia != "") {
	$strSQLAux = "    pj.nome_fantasia  =  '" . $strRazaoSocial . "' OR  pj.razao_social  =  '" . $strRazaoSocial . "'";
} elseif($strNomeDoc != "" && $strValorDoc != "") {
	$strSQLAux = "    dpj.nome  = '" .$strNomeDoc . "'
				  AND dpj.valor = '" . $strValorDoc . "'";
}

/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
try {
	$strSQL = " SELECT pj.cod_pj
					 , pj.razao_social
					 , pj.nome_fantasia
				  FROM cad_pj AS pj
					   " . (($strNomeDoc != "" && $strValorDoc != "") ? "LEFT OUTER JOIN cad_doc_pj AS dpj ON (pj.cod_pj = dpj.cod_pj)" : "") . "
					WHERE " . $strSQLAux . "
				 ORDER BY pj.cod_pj ";
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
foreach($objResult as $objRS) { 
	echo((($intI != 0) ? "\n" : "") . getValue($objRS,"cod_pj") . "|" . getValue($objRS,"razao_social") . "|" . getValue($objRS,"nome_fantasia")); 
	$intI++;
}
?>