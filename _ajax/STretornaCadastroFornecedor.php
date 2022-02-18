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

$id_mercado	= getsession(CFG_SYSTEM_NAME."_id_mercado");


//$strCodigo = str_pad($strCodigo, 6, "0", STR_PAD_LEFT);

/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
//Busca dados do cadastro
try {
echo	$strSQL = " SELECT 
					  cod_pj_fornec,
					  razao_social,
					  cnpj,
					  insc_est,
					  insc_munic,
					  end_cep,
					  end_logradouro,
					  end_numero,
					  end_complemento,
					  end_bairro,
					  end_cidade,
					  end_estado,
					  end_pais,
					  end_fone1  
					FROM 
					  cad_pj_fornec 
					WHERE   cod_pj_fornec = '".$intCodigo."'";
//die();
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
		. getValue($objRS,"cod_pj_fornec")."|"
		. getValue($objRS,"razao_social")."|"
		. getValue($objRS,"cnpj")."|"
		. getValue($objRS,"insc_est")."|"
		. getValue($objRS,"insc_munic")."|"
		. getValue($objRS,"end_cep")."|"
		. getValue($objRS,"end_logradouro")."|"
		. getValue($objRS,"end_numero")."|"
		. getValue($objRS,"end_complemento")."|"			
		. getValue($objRS,"end_bairro")."|"
		. getValue($objRS,"end_cidade")."|"				
		. getValue($objRS,"end_estado")."|"
		. getValue($objRS,"end_pais")."|"		
		. getValue($objRS,"end_fone1"));		
	$intI++;
}
?>