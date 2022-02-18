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

/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***            DEFINIÇÃO DE PARÂMETROS            ***/
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