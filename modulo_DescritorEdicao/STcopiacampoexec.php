<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strPopulate = request("var_populate");                             //Flag de verifica��o se necessita popular o session ou n�o
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos �tens do m�dulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));            	  //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_CAMPO"); //Verifica��o de acesso do usu�rio corrente

$intCodigo = request("RECORD_KEY_VALUE");

$objConn = abreDBConn(CFG_DB);

if($intCodigo != ""){

	try{
		
		$objConn->beginTransaction();
		$objStatement = $objConn->prepare("SELECT sp_copia_campo(:in_cod_descr_campo);");
		$objStatement->bindParam(":in_cod_descr_campo",$intCodigo);
		$objStatement->execute();
		
		$objConn->commit();
		
		echo "<script>window.parent.frames[\"".CFG_SYSTEM_NAME . "_left\"].document.formeditor_000.submit();</script>";
		
		
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
		$objConn->rollBack();
		die();
	}
}


?>