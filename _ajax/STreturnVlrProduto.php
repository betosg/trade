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
$intI = 0;
/***              DEFINI��O DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 

/***            DEFINI��O DE PAR�METROS            ***/
/*****************************************************/
$intCodProd        = request("var_codigo");

if($intCodProd == ""){
	echo("0,00;vazio");
	die();
}

/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
try {
	$strSQL = " 
	SELECT DISTINCT t1.cod_produto, t1.rotulo || ' (R$ ' || t1.valor || ')' AS rotulo_valor, case when (t1.valor is null OR t1.valor = 0) then '0,00' else to_char(t1.valor , '99999999999999D99') end as valor
		FROM prd_produto t1
		WHERE t1.cod_produto = ".$intCodProd;
	
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
	echo((($intI != 0) ? "\n" : "") . getValue($objRS,"valor") . ";" . getValue($objRS,"rotulo_valor")); 
	$intI++;
}
?>