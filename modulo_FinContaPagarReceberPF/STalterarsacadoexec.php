<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  	= request("DBVAR_NUM_COD_PF");	// cod_pf
	$strNome	 	= request("DBVAR_STR_NOME");
	$strLogradouro 	= request("DBVAR_STR_LOGRADOURO");
	$intNumero		= request("DBVAR_STR_NUMERO");
	$strComplemento = request("DBVAR_STR_COMPLEMENTO");
	$intCep 		= (request("DBVAR_STR_CEP") != "") ? " CEP: ".request("DBVAR_STR_CEP") : "";
	$strBairro		= request("DBVAR_STR_BAIRRO");
	$strCidade		= request("DBVAR_STR_CIDADE");
	$strEstado 		= request("DBVAR_STR_ESTADO");
	$strLocation  	= request("DEFAULT_LOCATION");
	
	// Remove seleção ESTADO caso o RESTANTE DO ENDEREÇO VENHA VAZIO
	if(($strLogradouro == "") && ($strComplemento == "") && ($intNumero == "") && ($intCep == "") && ($strBairro == "") && 
	   ($strCidade == "")&&($strEstado != "")){
	   $strEstado = "";
	}
	
	$strERRMsg  = "";
	$strERRMsg .= ($strNome == "") ? "&bull;&nbsp;NO MÍNIMO o campo NOME deve ser preenchido" : "";
	if($strERRMsg != ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",
				 $strERRMsg,"STalterarsacado.php?var_chavereg=".$intCodDado,"erro",1,"","");
		die();
	}

	// Monta STRING para sacado
	$strSACADO  = "";
	$strSACADO .= $strNome."<br>";
	$strSACADO .= $strLogradouro." ".$intNumero." ".$strComplemento." ";
	$strSACADO .= $intCep."<br>";
	$strSACADO .= $strBairro." ".$strCidade." ".$strEstado;
	

	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// UPDATE NO CAD_PF PARA INSERÇÃO DO DADOS_SACADO
	try{
		$strSQL = "UPDATE cad_pf SET dados_sacado = '".prepStr($strSACADO)."' WHERE cod_pf = ".$intCodDado;
		$objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Redirect para local correto
	if($strLocation == ""){ echo("<script type='text/javascript' language='javascript'>window.close();</script>"); }
	else{ redirect($strLocation); }
?>