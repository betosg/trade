<?php
if(!isset($_SESSION["shop_db_name"]) || !isset($_SESSION["shop_cod_evento"])) {
	$intCodEvento  = $_REQUEST["var_cod_evento"];
	$strDirCliente = $_REQUEST["var_client_name"];
	$strDB         = "prostudio_" . $strDirCliente;
	//$strDB = "proeventostudio1";
	
	if($intCodEvento != "" && $strDB != ""){
		setsession("shop_cod_evento",$intCodEvento);
		setsession("shop_db_name"   ,$strDB       );
		setsession("shop_dir_name"  ,$strDirCliente);
		setsession("shop_tipo_exibicao","categoria");
		setsession("shop_novo_cadastro",true);
	}
	else {
		die("A sess�o atual expirou ou n�o foram passados os par�metros corretos.");
	}
}
else{
	$strDirCliente = getsession("shop_dir_name");
}
?>