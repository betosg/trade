<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strPopulate = request("var_populate");                             //Flag de verificação se necessita popular o session ou não
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd()))); //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA"); //Verificação de acesso do usuário corrente

$intCodigo = request("var_chavereg");

$objConn = abreDBConn(CFG_DB);

$boolGerado = true;

if($intCodigo != ""){
	try{
		$objConn->beginTransaction();
		
		$strSQL  = " UPDATE sys_cria_app SET status = 'EM_LOTE' ";
		$strSQL .= " WHERE cod_cria_app = " . $intCodigo;
		$strSQL .= " AND status = 'EM_EDICAO' ";
		$objConn->query($strSQL); 
		
		$strSQL = " SELECT * FROM sp_gera_modulo(".$intCodigo.", ".getsession(CFG_SYSTEM_NAME . "_cod_usuario").", '".getsession(CFG_SYSTEM_NAME . "_grp_user")."', '".CFG_SYSTEM_NAME."') ";
		$objConn->query($strSQL); 
		
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
		$objConn->rollBack();
		die();
	}
	
	try{
		$strSQL = "SELECT dir_app FROM sys_cria_app WHERE cod_cria_app = " . $intCodigo;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
		
		$nomeModulo = getValue($objRS,"dir_app");
		
		if($nomeModulo != ""){
			$nomeModulo = $nomeModulo."/";
			//verifica se a pasta ja existe
			if(!is_dir("../".$nomeModulo)) {
				//seta o caminho dos fontes originais do kernelps e abre o diretorio dos fontes
				$dir = $_SERVER["DOCUMENT_ROOT"] . "/_" . CFG_SYSTEM_NAME . "/_fontes/";
				$openDir = opendir($dir);
				
				//cria o diretorio com nome do modulo, se conseguir copia arquivo da página INDEX
				$mkDir = mkdir("../".$nomeModulo);
				if($mkDir){
					$arq = "index.php";
					if(!copy($arq,"../".$nomeModulo.$arq)) {
						mensagem("err_sql_titulo","err_sql_desc",getTText("erro_copia_arquivo",C_NONE).":&nbsp;<strong>".$arq."</strong>","","erro",0);
						$boolGerado = false;
					}
				}
			}
		}
	} 
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
		die();
	}
	
	if ($boolGerado) mensagem("info_modulo_gerado_titulo","info_modulo_gerado_desc",getTText("msg_modulo_criado",C_NONE),"","info",1);
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc",$e->getMessage(),"","erro",1);
	die();
}

?>