<?php
include_once("../_database/athdbconn.php");

error_reporting(E_ALL);

$intCodDado     = request("var_chavereg");
$strAction      = request("var_action"); 
$strIndice      = request("var_indice"); 
$strValor       = request("var_valor");
$strComentario  = request("var_comentario");
$strLocalLinhas = request("var_local_linhas");
$intNumLinhas   = (request("var_linhas_branco") != "") ? request("var_linhas_branco") : 0;

$strLinha = "";
$strLinhasBranco = "";
$strLinhasBrancoAntes = "";
$strLinhasBrancoDepois = "";

$arrArquivo = file("../_database/STconfiginc.php");

if($strIndice != "") { 
	if($strValor != "") {
		$strLinha .= ((strpos($strIndice,"@") === 0) ? "@" : "") . "define(\"" . str_replace("@","",$strIndice) . "\"," . $strValor . ");  "; 
	}
	else {
		mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
		die();
	}
}

if($strComentario != "") { $strLinha .= "//" . $strComentario; }

for($intI=0;$intI < intval($intNumLinhas);$intI++) { $strLinhasBranco .= chr(10); }

($strLocalLinhas == "a") ? $strLinhasBrancoAntes = $strLinhasBranco : $strLinhasBrancoDepois = $strLinhasBranco;
$intIndex = intval(count($arrArquivo));
$arrArquivo[$intIndex-1] = $strLinhasBrancoAntes . $strLinha . chr(10) . $strLinhasBrancoDepois . $arrArquivo[$intIndex-1];

$resArquivo = fopen("../_database/STconfiginc.php","wb");

foreach($arrArquivo as $strLine) { fwrite($resArquivo,$strLine); }

fclose($resArquivo);

header("Location:" . $strAction);
?>