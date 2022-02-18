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
$codigo         = request("var_codigo");
$tipoEntidade   = strtolower(request("var_tipo"));
$tipoEntidade   = ($tipoEntidade == "") ? "cad_pj" : $tipoEntidade; //caso não receba uma entidade utiliza a default que é cad_pj - By Vini 19.03.2013
$strUsuario    	= getsession(CFG_SYSTEM_NAME."_id_usuario");
/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
try {
	if($tipoEntidade == "cad_pj") {
		$strSQL = "SELECT cad_pj.razao_social as razao
		                 ,cad_pj.endprin_cep
						 ,cad_pj.endprin_logradouro
						 ,cad_pj.endprin_numero
						 ,cad_pj.endprin_complemento
						 ,cad_pj.endprin_bairro
						 ,cad_pj.endprin_cidade
						 ,cad_pj.endprin_estado
						 ,cad_pj.endprin_pais
						 ,cad_pj.cnpj as cnpj_cpf						 						 
					FROM cad_pj WHERE cad_pj.cod_pj = '".$codigo."'";
	}else if($tipoEntidade == "cad_pf") {
		$strSQL = "SELECT cad_pf.nome as razao
		                 ,cad_pf.endprin_cep
						 ,cad_pf.endprin_logradouro
						 ,cad_pf.endprin_numero
						 ,cad_pf.endprin_complemento
						 ,cad_pf.endprin_bairro
						 ,cad_pf.endprin_cidade
						 ,cad_pf.endprin_estado
						 ,cad_pf.endprin_pais
						 ,cad_pf.cpf  as cnpj_cpf						 
					FROM cad_pf WHERE cad_pf.cod_pf = '".$codigo."';";		
	}
				//echo $strSQL;
	//die($strSQL);	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	/***               TRATAMENTO DE ERRO              ***/
	/*****************************************************/
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$strOBS = utf8_decode("Observações: (1) A presente Nota de Débito não está sujeita a retenção de Imposto de Renda na Fonte.");
/***         RETORNO DOS DADOS PARA O AJAX         ***/
/*****************************************************/
$intI=0;
foreach($objResult as $objRS) { 
	echo( (($intI != 0) ? "\n" : "") 
	      .getValue($objRS,"razao").";"
		  .getValue($objRS,"endprin_cep").";"
		  .getValue($objRS,"endprin_logradouro").";"
		  .getValue($objRS,"endprin_numero").";"
		  .getValue($objRS,"endprin_complemento").";"
		  .getValue($objRS,"endprin_bairro") .";"
		  .getValue($objRS,"endprin_cidade").";"
		  .getValue($objRS,"endprin_estado").";"
		  .getValue($objRS,"endprin_pais").";"
		  .getValue($objRS,"cnpj_cpf") .";"
		  . $strOBS);
	$intI++;
}
?>