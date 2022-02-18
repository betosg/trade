<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// recebe o cod_usuario
	$intCodDado  = request("var_chavereg");
	
	// prefixo dos values do input - caso o id nao seja
	// enviado, setamos com COPY
	$strInputPrefix = (request("var_value_prefix")=="") ? "COPY" : request("var_value_prefix");
	
	// verifica direitos de acesso para usuario corrente
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME. "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_USR");
	
	// abre conexão com o banco de dados
	$objConn   = abreDBConn(CFG_DB);
	
	// faz a busca dos dados de usuario com base no
	// codigo enviado, acrescentando o prefixo setado
	// na constante acima
	try{
		$strSQL = "
			SELECT 	cod_usuario, id_usuario, grp_user, codigo, tipo, nome, obs, email,
					email_extra, lang, dir_default, foto, dtt_inativo
			FROM sys_usuario
			WHERE cod_usuario = " . $intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// fetch dos dados encontrados
	$objRS 			= $objResult->fetch();
	$intCodUsuario 	= getValue($objRS,"cod_usuario");
	$strIDUsuario 	= getValue($objRS,"id_usuario");
	$strGrpUser 	= getValue($objRS,"grp_user");
	$intCodigo 		= getValue($objRS,"codigo");
	$strTipo 		= getValue($objRS,"tipo");
	$strNome 		= getValue($objRS,"nome");
	$strSenha 		= getValue($objRS,"senha");
	$strObs 		= getValue($objRS,"obs");
	$strEmail 		= getValue($objRS,"email");
	$strEmailExtra 	= getValue($objRS,"email_extra");
	$strLang 		= getValue($objRS,"lang");
	$strDirDefault 	= getValue($objRS,"dir_default");
	$strFoto 		= getValue($objRS,"foto");
	$dtDttInativo 	= getValue($objRS,"dtt_inativo");
	
	// seta cores para as linhas
	$strBGColor1 = CL_CORLINHA_1;
	$strBGColor2 = CL_CORLINHA_2;
	
?>
<html>
	<head>
		<title><?php echo(getTText(CFG_SYSTEM_NAME,C_UCWORDS)); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="JavaScript" type="text/javascript">
		<!--
			function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
				strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + 		prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
				AbreJanelaPAGE(strLink, "570", "270");
			}
 
			function AbreJanelaPAGE_LOCAL(pr_link, pr_extra){
  				var auxStrToChange, rExp, auxNewExtra, auxNewValue;
  				if (pr_extra != ""){
   					rExp = /:/gi;
   					auxNewExtra = pr_extra
   					if(pr_extra.search(rExp) != -1){
    					 auxStrToChange = pr_extra.split(":");
					     auxStrToChange = auxStrToChange[1];
					     rExp = eval("/:" + auxStrToChange + ":/gi");
					     auxNewValue = eval("document.formeditor_000." + auxStrToChange + ".value");
					     auxNewExtra = pr_extra.replace(rExp, auxNewValue);
				    }
					pr_link = pr_link + auxNewExtra;
				}
  				AbreJanelaPAGE(pr_link, "680", "350");
			}

			function setFormField(formname, fieldname, valor){
				if ((formname != "") && (fieldname != "") && (valor != "")){
    				eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  				}
			}
		
			function submeter(){
				document.formeditor_000.submit();
			}

			function cancelar(){
				window.history.back();
			}

		//-->
		</script>
	</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
		<td align="center" valign="top">
		<?php athBeginFloatingBox(CFG_DIALOG_WIDTH, "", getTText("usuario",C_TOUPPER) ." (". getTText("copia_usuario",C_UCWORDS). ")", CL_CORBAR_GLASS_1); ?>
      		<table width="100%" cellpadding="0" cellspacing="0" class="kernel_dialog">
	   			<form name="formeditor_000" action="STcopyusuarioexec.php" method="post">
				<input type="hidden" name="dbvar_str_lang_000" value="<?php echo($strLang);?>" />
				<input type="hidden" name="dbvar_str_tipo_000" value="<?php echo($strTipo);?>" />
				<input type="hidden" name="dbvar_str_senha_000" value="<?php echo($strSenha);?>" />
				<input type="hidden" name="dbvar_num_cod_usuario_000" value="<?php echo($intCodUsuario);?>" />
				<input type="hidden" name="dbvar_str_old_id_usuario_000" value="<?php echo($strIDUsuario);?>" />
				<tr><td height="22" style="padding:10px" align="left"><strong><?php echo(getTText("preencher_dados",C_NONE)); ?></strong>
				</td></tr>
				<tr> 
		  			<td align="center" valign="top">
						<table width="<?php echo(CFG_DIALOG_CONTENT_WIDTH); ?>" border="0" cellspacing="0" cellpadding="3">
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_num_codigo_000"><?php echo("*".getTText("codigo",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="text" maxlength="15" size="8" name="dbvar_num_codigo_000" id="dbvar_num_codigo_000" value="<?php echo($intCodigo);?>" />
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2);?>">	
								<td class="coluna_label">
									<label for="dbvar_str_id_usuario_000"><?php echo("*".getTText("id_usuario",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="text" maxlength="150" size="50" name="dbvar_str_id_usuario_000" id="dbvar_str_id_usuario_000" value="<?php echo($strInputPrefix."_".$strIDUsuario);?>" />
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_str_nome_000"><?php echo("*".getTText("nome",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="text" maxlength="150" size="60" name="dbvar_str_nome_000" id="dbvar_str_nome_000" value="<?php echo($strInputPrefix."_".$strNome);?>" />
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2);?>">
								<td class="coluna_label">
									<label for="dbvar_str_grp_user_000"><?php echo("*".getTText("grp_user",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<select name="dbvar_str_grp_user_000" id="dbvar_str_grp_user_000">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario WHERE grp_user <> 'SU' OR grp_user = '".getsession(CFG_SYSTEM_NAME."_grp_user")."'","grp_user","grp_user",$strGrpUser));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_str_dir_default_000"><?php echo("*".getTText("dir_default",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<select name="dbvar_str_dir_default_000" id="dbvar_str_dir_default_000">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT DISTINCT dir_default FROM sys_usuario","dir_default","dir_default",$strDirDefault));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2);?>">
								<td class="coluna_label">
									<label for="dbvar_str_var_foto_000"><?php echo(getTText("foto",C_UCWORDS).":");?>
								</td>
								<td class="coluna_valor">
									<input type="text" name="dbvar_str_var_foto_000" id="dbvar_str_var_foto_000" size="50"  value="<?php echo($strFoto);?>" readonly="true" title="Foto"><input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_var_foto_000','\\<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/fotosusuario\\','','');">
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_str_email_000"><?php echo(getTText("email",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="text" maxlength="50" size="40" name="dbvar_str_email_000" id="dbvar_str_email_000" value="<?php echo($strEmail);?>" />
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2);?>">
								<td class="coluna_label">
									<label for="dbvar_str_email_extra_000"><?php echo(getTText("email_extra",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="text" maxlength="50" size="40" name="dbvar_str_email_extra_000" id="dbvar_str_email_extra_000" value="<?php echo($strEmailExtra);?>" />
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label><?php echo(getTText("lang",C_UCWORDS).":");?></label>
								</td>
								<td class="coluna_valor"><?php echo($strLang);?></td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2);?>">
								<td class="coluna_label" valign="top">
									<label for="dbvar_str_obs_000"><?php echo(getTText("obs",C_NONE).":");?></label>
								</td>
								<td class="coluna_valor">
									<textarea name="dbvar_str_obs_000" id="dbvar_str_obs_000" rows="5" cols="50"><?php echo($strObs);?></textarea>
								</td>
							</tr>
                            
                            <tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_str_tp_user_refdir_000"><?php echo(getTText("tp_user_refdir",C_UCWORDS).":");?></label>
								</td> 
								<td class="coluna_valor">
									<select name="dbvar_str_tp_user_refdir_000" id="dbvar_tp_user_refdir_000">
										<option value=""></option>
										<option value="normal">NORMAL</option>
                                        <option value="modelo">MODELO</option>
									</select><br /><span class='comment_med'>&nbsp;<br>Tipo de usuario: normal ou modelo, caso seja do tipo modelo, o mesmo poderá ser utilizado para refêrencia em outros usuarios.</span>&nbsp;
								</td>
							</tr>
                            
                            <tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_int_cod_user_refdir_000"><?php echo(getTText("cod_user_refdir",C_UCWORDS).":");?></label>
								</td> 
								<td class="coluna_valor">
									<select name="dbvar_int_cod_user_refdir_000" id="dbvar_int_cod_user_refdir_000">
										<option value="0"></option>
										<?php echo(montaCombo($objConn,"SELECT cod_usuario, id_usuario FROM sys_usuario WHERE tp_user_refdir = 'modelo'","cod_usuario","id_usuario",$strDirDefault));?>
									</select><br /><span class='comment_med'>&nbsp;<br>Usuario modelo a ser utilizado para referencia nos direitos de acesso aos módulos.</span>&nbsp;	
								</td>
							</tr>
                            
							<tr bgcolor="<?php echo($strBGColor1);?>">
								<td class="coluna_label">
									<label for="dbvar_str_dtt_inativo_000_A"><?php echo("<strong>".getTText("situacao",C_NONE).":");?></label>
								</td>
								<td class="coluna_valor">
									<input type="radio" name="dbvar_str_dtt_inativo_000" id="dbvar_str_dtt_inativo_000_A" value="A" <?php echo(($dtDttInativo == "") ? "checked='checked'" : "")?> class="inputclean" /><?php echo(getTText("ativa",C_UCWORDS));?>
									<input type="radio" name="dbvar_str_dtt_inativo_000" value="I" <?php echo(($dtDttInativo != "") ? "checked='checked'" : "")?> class="inputclean" /><?php echo(getTText("inativa",C_UCWORDS));?>
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px" align="left">campos com (*) são obrigatórios</td></tr>
							<tr><td class="linedialog" colspan="2"></td></tr>
							<tr>
								<td colspan="2">
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td><img src="../img/mensagem_aviso.gif" style="float:left" /><?php echo(getTText("aviso_copia",C_NONE));?></td>
											<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
												<button onClick="submeter(); return false;">Ok</button>
												<button onClick="cancelar(); return false;">Cancelar</button>
											</td>							
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</form>
			</table>
			<?php athEndFloatingBox();?>
		</td>
	</tr>
</table>
<?php
	$objResult->closeCursor();
	$objConn = NULL;
?>