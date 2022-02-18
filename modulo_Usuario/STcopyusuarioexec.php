<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	//include_once("../_scripts/scripts.js");
	//include_once("../_scripts/STscripts.js");
	
	//print_r($_REQUEST);
	
	//recebe campos do formulário anterior
	$strOldIDUsuario 	= request("dbvar_str_old_id_usuario_000");
	$intCodUsuario   	= request("dbvar_num_cod_usuario_000");
	$strIDUsuario  	 	= request("dbvar_str_id_usuario_000");
	$strGrpUser  	 	= request("dbvar_str_grp_user_000");
	$intCodigo 		 	= request("dbvar_num_codigo_000");
	$strTipo 		 	= request("dbvar_str_tipo_000");
	$strNome 		 	= request("dbvar_str_nome_000");
	$strSenha		 	= request("dbvar_str_senha_000");
	$strObs 		 	= request("dbvar_str_obs_000");
	$strEmail 		 	= request("dbvar_str_email_000");
	$strEmailExtra 	 	= request("dbvar_str_email_extra_000");
	$strLang 		 	= request("dbvar_str_lang_000");
	$strDirDefault 	 	= request("dbvar_str_dir_default_000");
	$strFoto 		 	= request("dbvar_str_var_foto_000");
	$dtDttInativo 	 	= request("dbvar_str_dtt_inativo_000");
	$intCodUserRefdir 	= request("dbvar_int_cod_user_refdir_000");
	$strTpUserRefdir  	= request("dbvar_str_tp_user_refdir_000");
	
	// verifica direitos de acesso para usuario corrente
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME. "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_USR");
	
	// abre conexão com o banco de dados
	$objConn   = abreDBConn(CFG_DB);
	
	// procura por uma mesma ID ja cadastrada, para tratamento de erro
	try{
		$strSQL 	= "SELECT cod_usuario, id_usuario FROM sys_usuario WHERE id_usuario = '".$strIDUsuario."'";
		$objResult  = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();	
	}
	
	
	// Inicialização da variável que receberá os
	// campos que estiverem incorretamente preenchidos.
	$strErro = "";
	
	//Formatação dos parâmetros antes de ser enviado para a procedure
	//id_usuario igual ao antigo
	if(($strOldIDUsuario == $strIDUsuario) ||(($objResult->rowCount()) > 0)){ $strErro .= "&bull;".getTText("outro_usuario", C_UCWORDS)."<br />"; }
	// cod_pf relacionado
	if($intCodigo     == ""){ $strErro .= "&bull;".getTText("codigo"     ,C_UCWORDS)."<br />";
	}else{ $intCodigo     = addslashes($intCodigo); }
	// nome
	if($strNome       == ""){ $strErro .= "&bull;".getTText("nome"       ,C_UCWORDS)."<br />";
	}else{ $strNome       = addslashes($strNome); }
	// id_usuario
	if($strIDUsuario  == ""){ $strErro .= "&bull;".getTText("id_usuario" ,C_UCWORDS)."<br />";
	}else{ $strIDUsuario  = addslashes($strIDUsuario); }	
	// diretorio_padrao
	if($strDirDefault == ""){ $strErro .= "&bull;".getTText("dir_default",C_UCWORDS)."<br />";
	}else{ $strDirDefault = addslashes($strDirDefault); }
	// grupo de usuario
	if($strGrpUser    == ""){ $strErro .= "&bull;".getTText("grp_user"  ,C_UCWORDS)."<br />";
	}else{ $strGrpUser    = addslashes($strGrpUser); }
	// obs
	if($strObs        == ""){ $strObs         = NULL;
	}else{ $strObs        = addslashes($strObs); }
	// email
	if($strEmail      == ""){ $strEmail 	  = NULL; 
	}else{ $strEmail      = addslashes($strEmail); }
	// email extra
	if($strEmailExtra == ""){ $strEmailExtra  = NULL; 
	}else{ $strGrupo      = addslashes($strGrupo); }
	// foto
	if($strFoto       == ""){ $strFoto  	  = NULL; 
	}else{ $strFoto       = addslashes($strFoto); }
	// inativo
	if($dtDttInativo  == "A"){ $dtDttInativo	  = NULL; 
	}elseif($dtDttInativo  == "I"){ $dtDttInativo  = addslashes(dDate(CFG_LANG,now(),true)); }
	//CodUserRefdir
	if($intCodUserRefdir       == ""){ $intCodUserRefdir  	  = NULL; 
	}else{ $intCodUserRefdir       = addslashes($intCodUserRefdir); }	
	//TpUserRefdir
	if($strTpUserRefdir       == ""){ $strTpUserRefdir  	  = 0; 
	}else{ $strTpUserRefdir       = addslashes($strTpUserRefdir); }	
	//die($intCodUserRefdir);
	if($strErro == ""){
		try{
			$objConn->beginTransaction();
			$objStatement = $objConn->prepare("SELECT sp_copia_usuario(:in_cod_base, :in_nome, :in_id_usuario, :in_grupo, :in_senha, :in_email, :in_email_extra, :in_lang, :in_dir_default, :in_dtt_inativo, :in_obs, :in_usr_ins, :in_foto,:in_tp_user_refdir,:in_cod_user_refdir);");
			$objStatement->bindParam(":in_cod_base"    		,$intCodUsuario);
			$objStatement->bindParam(":in_nome"        		,$strNome);
			$objStatement->bindParam(":in_id_usuario"  		,$strIDUsuario);
			$objStatement->bindParam(":in_grupo"       		,$strGrpUser);
			$objStatement->bindParam(":in_senha"       		,$strSenha);
			$objStatement->bindParam(":in_email"       		,$strEmail);
			$objStatement->bindParam(":in_email_extra" 		,$strEmailExtra);
			$objStatement->bindParam(":in_lang"        		,$strLang);
			$objStatement->bindParam(":in_dir_default" 		,$strDirDefault);
			$objStatement->bindParam(":in_dtt_inativo" 		,$dtDttInativo);
			$objStatement->bindParam(":in_obs"         		,$strObs);
			$objStatement->bindParam(":in_usr_ins"     		,getsession(CFG_SYSTEM_NAME."_id_usuario"));
			$objStatement->bindParam(":in_foto"        		,$strFoto);
			$objStatement->bindParam(":in_tp_user_refdir"	,$strTpUserRefdir);
			$objStatement->bindParam(":in_cod_user_refdir" 	,$intCodUserRefdir);
			$objStatement->execute();
			
			$objRS = $objStatement->fetch();
			
			$objConn->commit();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConn->rollBack();
			die();
		}
	}
	else{
		mensagem("err_dados_titulo","err_dados_submit_desc",$strErro,"","erro",1);
		die();
	}
	redirect("../modulo_Usuario/");
?>