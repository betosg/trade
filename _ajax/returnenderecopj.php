<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");
include_once("../_database/athdbconn.php");

$intCodPJ = request("var_chavereg");
$intCodTipoEndereco = request("var_cod_tipo_endereco");

$objConn  = abreDBConn(CFG_DB);

try {
	$strSQL  = " SELECT cod_endereco";
	$strSQL .= "      , cod_tipo_endereco";
	$strSQL .= "      , cep";
	$strSQL .= "      , logradouro";
	$strSQL .= "      , numero";
	$strSQL .= "      , complemento";
	$strSQL .= "      , endereco";
	$strSQL .= "      , bairro";
	$strSQL .= "      , cidade";
	$strSQL .= "	  , estado";
	$strSQL .= "	  , pais";
	$strSQL .= "	  , ddd";
	$strSQL .= "	  , ddi";
	$strSQL .= "	  , fone";
	$strSQL .= "	  , ddd_extra1";
	$strSQL .= "	  , ddi_extra1";
	$strSQL .= "	  , fone_extra1";
	$strSQL .= "	  , ddd_extra2";
	$strSQL .= "	  , ddi_extra2";
	$strSQL .= "	  , fone_extra2";
	$strSQL .= "	  , ddd_extra3";
	$strSQL .= "	  , ddi_extra3";
	$strSQL .= "	  , fone_extra3";
	$strSQL .= "	  , email";
	$strSQL .= "      , email_extra";
	$strSQL .= "      , homepage";
	$strSQL .= " FROM cad_endereco_pj ";
	$strSQL .= " WHERE cod_pj = " . $intCodPJ;
	$strSQL .= "   AND cod_tipo_endereco = " . $intCodTipoEndereco;
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

if($objRS = $objResult->fetch()){
	$strRetorno = getValue($objRS,"cep") . "|" . getValue($objRS,"logradouro") . "|" . getValue($objRS,"numero") . "|" . getValue($objRS,"complemento") . "|" .
	getValue($objRS,"bairro") . "|" . getValue($objRS,"cidade") . "|" . getValue($objRS,"estado") . "|" . getValue($objRS,"pais") . "|" .
	getValue($objRS,"ddi") . "|" . getValue($objRS,"ddd") . "|" . getValue($objRS,"fone") . "|" .
	getValue($objRS,"ddi_extra1") . "|" . getValue($objRS,"ddd_extra1") . "|" . getValue($objRS,"fone_extra1") . "|" .
	getValue($objRS,"ddi_extra2") . "|" . getValue($objRS,"ddd_extra2") . "|" . getValue($objRS,"fone_extra2") . "|" .
	getValue($objRS,"ddi_extra3") . "|" . getValue($objRS,"ddd_extra3") . "|" . getValue($objRS,"fone_extra3") . "|" .
	getValue($objRS,"homepage") . "|" . getValue($objRS,"email") . "|" . getValue($objRS,"email_extra");   
}	
else{
	$strRetorno = NULL;
}

echo(trim($strRetorno));

$objResult->closeCursor();
?>