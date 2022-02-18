<?php
//header("Content-Type:text/html; charset=iso-8859-1");
//header("Cache-Control:no-cache, must-revalidate");
//header("Pragma:no-cache");

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
  		case "2":	$strTexto = "2<br>Cidade com logradouro �nico" .
								 "<br>".$strResultadoBusca['cidade'].
								 "<br>".$strResultadoBusca['uf'];
					break;
  		case "1":	$strTexto = "1<br>".$strResultadoBusca['tipo_logradouro'] .
								 "<br>".$strResultadoBusca['logradouro'] .
								 "<br>".$strResultadoBusca['bairro'] .
								 "<br>".$strResultadoBusca['cidade'] .
								 "<br>".$strResultadoBusca['uf'];
					break;
		default:	$strTexto = " ";
					break;
	}
	//OBSERVA��O: O AJAX BUSCA INFORMA��ES 
	//DAQUILO QUE EST� ESCRITO EM P�GINA!
	//TODO O C�DIGO (INCLUSIVE TAGS HTML) VEM
	//NA REQUISI��O QUE O AJAX FAZ
	echo $strTexto;
} else {
	//A base de ceps atual n�o suporta o caracter '-', ent�o 
	//formatamos a string corretamente para que seja aceita
	//$strCep = explode("-",$strCep);
	//$strCep = $strCep[0].$strCep[1];
	
	//Roda o sql que far� a busca do cep na base local
	try {
		$strSQL = "SELECT tipo, logradouro, bairro, municipio, estado FROM lc_logradouro WHERE cep = '".$strCep."'"; 
		$objResult = $objConn->query($strSQL);
	} catch (PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	//fetch do resultado
	$objRS = $objResult->fetch();
	//ajusta o campo tipo de logradouro, que na base est� como 'AV', 'R', etc.
	$strTipoLog = (getValue($objRS,"tipo") == "R") ? "Rua" : getValue($objRS,"tipo");
	
	//caso o resultado seja uma cidade de logradouro �nico
	if(getValue($objRS,"tipo") == "") {
		$strTexto = "2<br>Cidade com logradouro �nico".
					 "<br>".ucwords(strtolower(getValue($objRS,"municipio"))).
			 		 "<br>".getValue($objRS,"estado");
	} else {
		$strTexto = "1<br>Tipo de Logradouro: ".$strTipoLog.
					 "<br>".ucwords(strtolower(getValue($objRS,"logradouro"))).
					 "<br>".ucwords(strtolower(getValue($objRS,"bairro"))).
					 "<br>".ucwords(strtolower(getValue($objRS,"municipio"))).
		 			 "<br>".getValue($objRS,"estado");
	}
	echo $strTexto;
}
$objConn = NULL;
?>