<?php
	header("Content-Type:text/html; charset=iso-8859-1");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// include dbfiles
	include_once("../_database/athdbconn.php");
	
	// verificaчуo de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	// abertura de conexуo com o BD
	$objConn = abreDBConn(CFG_DB);
	
	// busca todos os direitos que o usuario
	// corrente da sessуo tem sobre o modulo
	try{
		$strSQL = "
			SELECT sys_app_direito.id_direito FROM sys_app_direito
			INNER JOIN sys_app_direito_usuario ON sys_app_direito_usuario.cod_app_direito = sys_app_direito.cod_app_direito
			INNER JOIN sys_app ON sys_app.cod_app = sys_app_direito.cod_app
			WHERE sys_app_direito_usuario.cod_usuario = ".getsession(CFG_SYSTEM_NAME . "_cod_usuario")." 
			AND sys_app.cod_app = ".getsession($strSesPfx . "_chave_app");
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// inciializa variсvel que serс utilizada pelo retorno
	$strReturn = '';
	
	// escreve na tela os direitos que o usuario corrente possui
	if($objResult->rowCount() > 0){
		foreach($objResult as $objRS){
			$strReturn = $strReturn . getValue($objRS,"id_direito") . ",";
		}
		echo($strReturn);
	}
?>