<?php
/***************************************************************/
/**        SCRIPTS PHP PARA AJAX - INSTRUÇÕES BÁSICAS         **/
/***************************************************************/
/** 1- Deve-se retirar qualquer caracter que estiver fora     **/
/**    da marcação PHP (<?php | ?>), pois afetará no retorno  **/
/**    de dados para o AJAX, inclusive espaços e caracteres   **/
/**    invisíveis                                             **/
/**                                                           **/
/** 2- O separador de dados padrão em coluna é o pipe "|"     **/
/**    e o de linhas é a quebra de linha "\n"     			  **/
/**                                                           **/
/** 3- Os cabeçalhos HTTP devem ser usados conforme o caso.   **/
/**    Por padrão inicial, ele não poe em cache os dados mas  **/
/**    pode ser modificado de acordo com a especificação do   **/
/**    script                                                 **/
/**                                                           **/
/** 4- Os tratamentos de erros podem ser customizados mas     **/
/**    OBRIGATORIAMENTE precisa ter ACIMA da saída de dados a **/
/**    linha "header("HTTP/1.0 500 Server internal error");"  **/
/**                                                           **/
/***************************************************************/
/** Sugestão:                                                 **/
/** Após a leitura das instruções remova esse comentário do   **/
/** novo script                                               **/
/***************************************************************/

/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");
$intI = 0;
/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 

/***            DEFINIÇÃO DE PARÂMETROS            ***/
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