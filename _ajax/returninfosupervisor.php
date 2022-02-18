<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strIDUsr  = request("var_id_usuario");
$strSenha  = request("var_senha");

$objConn = abreDBConn(getsession(CFG_SYSTEM_NAME . "_db_name"));

try {
	$strSQL  = " SELECT cod_usuario, id_usuario ";
	$strSQL .= "   FROM sys_usuario ";
	$strSQL .= "  WHERE id_usuario ilike '" . $strIDUsr . "'";
	$strSQL .= "    AND senha = md5('" . $strSenha . "') ";
	$strSQL .= "    AND (grp_user = 'SU' OR grp_user = 'ADMIN') ";
	$strSQL .= "    AND dtt_inativo IS NULL ";

	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

if($objRS = $objResult->fetch()) { 
  $strRetorno = getValue($objRS,"cod_usuario") . "|" . getValue($objRS,"id_usuario"); 
} else { 
  $strRetorno = ''; 
}

$objResult->closeCursor();

echo(trim($strRetorno));
?>