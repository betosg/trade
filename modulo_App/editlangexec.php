<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
 
$arrPost = $_POST;
 
global $php_errormsg;
ini_set("track_errors",true);
 
$strIdioma   = request("var_lang�");
$strDir      = request("var_dir�");
$strLocation = request("var_location�");
 
(strpos($strLocation,"?") !== false) ? $strLocation .= "&var_lang�=" . $strIdioma : NULL ; 
 
if($resArq = @fopen("../" . $strDir . "/lang/" . $strIdioma . ".lang","w")){
	foreach($arrPost as $strIndex => $strValue){
		if(strpos($strIndex,"�") === false && strpos($strIndex,"&ecirc;") === false){
			fwrite($resArq,">" . $strIndex . "  '" . str_replace("'","\\'",$strValue) . "'\r\n");
		}
	}
	fclose($resArq);
}
else{
	mensagem("err_stream_titulo","err_stream_desc",$php_errormsg,"","erro",1);
	die();
}
 
redirect($strLocation);
?>