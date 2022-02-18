<?php
    include_once("../_database/athdbconn.php");
error_reporting(E_ALL);
	$strMAINFRAME	= CFG_SYSTEM_NAME . "_mainFrame";
	$strCLIENTE		= getsession(CFG_SYSTEM_NAME."_dir_cliente");
	$strLOGINAWAY	= getsession(CFG_SYSTEM_NAME."_login_away");
	
	//Configs. de evento
	/*
	setsession(CFG_SYSTEM_NAME . "_cod_evento"	 , NULL);
	setsession(CFG_SYSTEM_NAME . "_nome_evento"	 , NULL);
	setsession("dt_inicio_evento", NULL);
	setsession("dt_fim_evento"   , NULL);
	setsession("cod_status_preco", NULL);
	setsession("cod_status_cred" , NULL);
	*/
	
	//Configs. de usuário
	setsession(CFG_SYSTEM_NAME . "_cod_usuario" , NULL);
	setsession(CFG_SYSTEM_NAME . "_id_usuario"  , NULL);
	setsession(CFG_SYSTEM_NAME . "_nome_usuario", NULL);
	setsession(CFG_SYSTEM_NAME . "_foto_usuario", NULL);
	setsession(CFG_SYSTEM_NAME . "_lang"	    , NULL);
	setsession(CFG_SYSTEM_NAME . "_grp_user"    , NULL);
	setsession(CFG_SYSTEM_NAME . "_email"		, NULL);
	setsession(CFG_SYSTEM_NAME . "_dtt_login"	, NULL);
	setsession(CFG_SYSTEM_NAME . "_dir_default" , NULL);
	setsession(CFG_SYSTEM_NAME . "_db_name"	 	, NULL);
	setsession(CFG_SYSTEM_NAME . "_su_passwd"	, NULL);
	setsession(CFG_SYSTEM_NAME . "_login_away"  , NULL);
	
	//Configs. de sistema
	//setsession(CFG_SYSTEM_NAME . "_theme"	         		 , NULL);
	setsession(CFG_SYSTEM_NAME . "_dir_cliente"		  		, NULL);
	setsession(CFG_SYSTEM_NAME . "_cli_dir_physical_path"	, NULL);
	setsession(CFG_SYSTEM_NAME . "_cli_dir_logical_path"	, NULL);
	setsession(CFG_SYSTEM_NAME . "_db_user"					, NULL);

	setsession(CFG_SYSTEM_NAME . "_pj_selec_codigo"     , NULL);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_nome"       , NULL);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_foto"       , NULL);
	setsession(CFG_SYSTEM_NAME . "_pj_selec_cod_usuario", NULL);

	session_unset(); 
    session_destroy(); 
	
	//Caso a sessão não seja 'morta' ao menos garante que a mesma não será mais usada
	session_regenerate_id(false);
?>
<!DOCTYPE html >
  <body>
    <form name="formlogout" action="../../<?php echo($strCLIENTE);?>/index.php" target="<?php echo($strMAINFRAME); ?>">
		<input type="hidden" name="var_loginaway" value="<?php echo($strLOGINAWAY); ?>">
	</form>
  </body>
</html>
<script>
 document.formlogout.submit(); 
 if(parent.frames[0].winpopup_profx != null) { parent.frames[0].winpopup_profx.close(); }
</script>