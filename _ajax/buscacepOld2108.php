<?php
//header("Content-Type:text/html; charset=iso-8859-1");
//header("Cache-Control:no-cache, must-revalidate");
//header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$objConn  = abreDBConn(CFG_DB);

//coleta o cep enviado pela página
//coleta o metodo de busca definido
//pela constante CFG_BUSCA_CEP
 $strCep 	= request("var_cep");
$boolOpcao 	= CFG_BUSCA_CEP;

if($boolOpcao == "web"){
	//envia o cep para o destino abaixo e a 
	//saída gerada é posta na variável $strResultado
	$strResultado = @file_get_contents("http://republicavirtual.com.br/web_cep.php?cep=" . urlencode($strCep) . "&formato=query_string");
	
	parse_str($strResultado,$strResultadoBusca);

	switch($strResultadoBusca["resultado"]){
  		case "2":	$strTexto = "2<br>Cidade com logradouro único" .
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
	//OBSERVAÇÃO: O AJAX BUSCA INFORMAÇÕES 
	//DAQUILO QUE ESTÁ ESCRITO EM PÁGINA!
	//TODO O CÓDIGO (INCLUSIVE TAGS HTML) VEM
	//NA REQUISIÇÃO QUE O AJAX FAZ
	echo $strTexto;
} else {
	//A base de ceps atual não suporta o caracter '-', então 
	//formatamos a string corretamente para que seja aceita
	//$strCep = explode("-",$strCep);
	//$strCep = $strCep[0].$strCep[1];
	
	//Roda o sql que fará a busca do cep na base local
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
	//ajusta o campo tipo de logradouro, que na base está como 'AV', 'R', etc.
	$strTipoLog = (getValue($objRS,"tipo") == "R") ? "Rua" : getValue($objRS,"tipo");
	
	//caso o resultado seja uma cidade de logradouro único
	if(getValue($objRS,"tipo") == "") {
		$strTexto = "2<br>Cidade com logradouro único".
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