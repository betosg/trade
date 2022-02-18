<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strPopulate = request("var_populate");                             //Flag de verificação se necessita popular o session ou não
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));            	  //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_CAMPO"); //Verificação de acesso do usuário corrente

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