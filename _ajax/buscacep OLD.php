<?php
header("Content-Type:text/html; charset=iso-8859-1");

include_once("../_database/athdbconn.php");

$objConn  = abreDBConn(CFG_DB);

//coleta o cep enviado pela p�gina
//coleta o metodo de busca definido
//pela constante CFG_BUSCA_CEP
$strCep 	= request("var_cep");
$boolOpcao 	= CFG_BUSCA_CEP;

if($boolOpcao == "web"){
	//envia o cep para o destino abaixo e a 
	//sa�da gerada � posta na vari�vel $strResultado
	$strResultado = @file_get_contents("http://republicavirtual.com.br/web_cep.php?cep=" . urlencode($strCep) . "&formato=query_string");
	
	parse_str($strResultado,$strResultadoBusca);

	switch($strResultadoBusca["resultado"]){
  		case '2':	$texto = "Cidade com logradouro �nico" .
					 "<br>Cidade: ".$strResultadoBusca['cidade'].
					 "<br>UF: ".$strResultadoBusca['uf'];
		break;
  		case '1':	$texto = "Tipo de Logradouro: ".$strResultadoBusca['tipo_logradouro'] .
					 "<br>Logradouro: ".$strResultadoBusca['logradouro'] .
					 "<br>Bairro: ".$strResultadoBusca['bairro'] .
					 "<br>Cidade: ".$strResultadoBusca['cidade'] .
					 "<br>UF: ".$strResultadoBusca['uf'];
		break;
		default:	$texto = " ";
		break;
	}
	//OBSERVA��O: O AJAX BUSCA INFORMA��ES 
	//DAQUILO QUE EST� ESCRITO EM P�GINA!
	//TODO O C�DIGO (INCLUSIVE TAGS HTML) VEM
	//NA REQUISI��O QUE O AJAX FAZ
	echo $texto;
}
else{
	//A base de ceps atual n�o suporta o caracter '-', ent�o 
	//formatamos a string corretamente para que seja aceita
	//$strCep = explode("-",$strCep);
	//$strCep = $strCep[0].$strCep[1];
	
	//Roda o sql que far� a busca do cep na base local
	try {
		$strSQL = "SELECT tipo, logradouro, bairro, municipio, estado FROM lc_logradouro WHERE cep = '".$strCep."'"; 
		$objResult = $objConn->query($strSQL);
	}catch (PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//fetch do resultado
	$objRS = $objResult->fetch();
	//ajusta o campo tipo de logradouro, que na base est� como 'AV', 'R', etc.
	$strTipoLog = (getValue($objRS,"tipo") == "R") ? "Rua" : getValue($objRS,"tipo");
	
	//caso o resultado seja uma cidade de logradouro �nico
	if(getValue($objRS,"tipo") == '') {
		$texto = "Cidade com logradouro �nico".
		"<br>Cidade: ".ucwords(strtolower(getValue($objRS,"municipio"))).
 		"<br>UF: ".getValue($objRS,"estado");
	}
	else{
	$texto = "Tipo de Logradouro: ".$strTipoLog.
		"<br>Logradouro: ".ucwords(strtolower(getValue($objRS,"logradouro"))).
		"<br>Bairro: ".ucwords(strtolower(getValue($objRS,"bairro"))).
		"<br>Cidade: ".ucwords(strtolower(getValue($objRS,"municipio"))).
 		"<br>UF: ".getValue($objRS,"estado");
	}
	echo $texto;
}
$objConn = NULL;
?>