<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado = request("var_chavereg");
	
	// Verificação de ACESSO
	// Carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// Verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_FAST");
	
	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// LOCALIZA O RESPONSÁVEL, PARA FLAG NO ID_FROM
	try{
		$strSQL = "
			SELECT 
				  id_responsavel
				, id_ult_executor 
				, titulo
				, cod_atividade
				, sys_usuario_responsavel.grp_user AS grupo_responsavel
				, sys_usuario_executor.grp_user AS grupo_executor
				, prioridade
			FROM  tl_todolist
			LEFT JOIN sys_usuario sys_usuario_responsavel ON (sys_usuario_responsavel.id_usuario = tl_todolist.id_responsavel)
			LEFT JOIN sys_usuario sys_usuario_executor ON (sys_usuario_executor.id_usuario = tl_todolist.id_ult_executor)
			WHERE cod_todolist = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	if((getsession(CFG_SYSTEM_NAME."_id_usuario") != getValue($objRS,"id_ult_executor")) && (getsession(CFG_SYSTEM_NAME."_id_usuario") != getValue($objRS,"id_responsavel"))){
		mensagem("err_sql_titulo","tarefa_nao_te_pertence","","STifrrespostas.php?var_chavereg=".$intCodDado,"aviso",1,"1");
		die();
	}
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css"></style>
		<script type="text/javascript">
			var strLocation = null;
			
			function validaCampos(){
				// Esta função faz uma pré-validação via JS dos campos marcados com asterisco
				var strMSG  = "";
				strMSG += (
						   (document.getElementById('var_todo_prioridade').value 	  	== "")|| 
						   (document.getElementById('var_todo_id_to').value  			== "")|| 
						   (document.getElementById('var_todo_resposta').value 	  		== "")||
						   (document.getElementById('var_todo_horas_1').value 	  		== "")||
						   (document.getElementById('var_todo_horas_2').value 	  		== "")
						  ) ? "\n\nDADOS DA RESPOSTA:" : "";
				strMSG += (document.getElementById('var_todo_id_to').value 		  		== "") ? "\nDestinatário" 		: "";
				strMSG += (document.getElementById('var_todo_prioridade').value 		== "") ? "\nPrioridade" 		: "";
				strMSG += (document.getElementById('var_todo_resposta').value 		  	== "") ? "\nResposta"			: "";
				strMSG += (document.getElementById('var_todo_horas_1').value  	 	 	== "") ? "\nHoras (Parte Um)"	: "";
				strMSG += (document.getElementById('var_todo_horas_2').value   			== "") ? "\nHoras (Parte Dois)"	: "";
				if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:'+strMSG); return(false); }
				else { return(true); }
			}
			
			function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
				strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
				AbreJanelaPAGE(strLink, "570", "270");
			}
			
			function ajaxDetailDataLoader(prSQL,prFuncao,prID,prFuncExtra,prIDLoader,prEquipe){
				var objAjax;
				var strReturnValue;
				var objLoader = document.getElementById(prIDLoader);
	
				objAjax = createAjax();
				
				if(objLoader != null){
					objLoader.innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
				}
				
				if(prEquipe == 'equipe'){
					prSQL = "SELECT id_usuario, id_usuario FROM bs_equipe WHERE cod_atividade = <?php echo(getValue($objRS,"cod_atividade"));?>";
				}
				
				objAjax.onreadystatechange = function() {
					if(objAjax.readyState == 4) {
						if(objAjax.status == 200) {
							strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
							//alert(strReturnValue);
							switch (prFuncao) {
								case "ajaxMontaCombo":  ajaxMontaCombo(prID, strReturnValue);
									if(prFuncExtra != '') eval(prFuncExtra);
								break;
								case "ajaxMontaEdit":   ajaxMontaEdit(prID, strReturnValue);
									if(prFuncExtra != '') eval(prFuncExtra);
								break;
							}
							if(objLoader != null){
								objLoader.innerHTML = "";
							}
						} else {
							alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET","../_ajax/returndados.php?var_sql="+prSQL,true); 
				objAjax.send(null); 
			}
			
			function ok() {
				if(validaCampos()){
					strLocation = "../modulo_Todolist/STifrrespostas.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				} else{
					return(false);
				}
			}

			function cancelar() {
				document.location.href = "../modulo_Todolist/STifrrespostas.php?var_chavereg=<?php echo($intCodDado);?>";
			}
			
			function aplicar() {
				if(validaCampos()){
					strLocation = "../modulo_Todolist/STinsresposta.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				} else{
					return(false);
				}
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
		</script>
	</head>
<body style="margin:10px;background-color:#FFFFFF;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("todolist",C_TOUPPER)." - (".getTText("insercao_de_resposta",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STinsrespostaexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" value="" />
	<input type="hidden" name="var_todo_cod_todo_list" value="<?php echo($intCodDado);?>" />
	<input type="hidden" name="var_todo_id_from" value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="690" bgcolor="#FFFFFF" class="table_master" style="border:1px solid #BBB;">
		<tr><td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td></tr>
		<tr>
		  <td align="left" valign="top" style="padding:10px 75px 10px 75px;">
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<!-- DIALOG INSERT -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_da_resposta",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("codigo_da_tarefa",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo($intCodDado);?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("titulo_da_tarefa",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRS,"titulo"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("id_from",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>
							<?php if(getsession(CFG_SYSTEM_NAME."_id_usuario") == getValue($objRS,"id_responsavel")){?>
								<?php echo(" - (".getTText("responsavel",C_NONE).")");?>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong>*<?php echo(getTText("id_to",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<select name="var_grupo_usuario_to" id="var_grupo_usuario_to" style="width:80px;" onChange="limpaSelect('var_todo_id_to');ajaxDetailDataLoader((this.value != '') ? 'SELECT DISTINCT id_usuario, id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = \'' + this.value + '\' ORDER BY id_usuario' : '','ajaxMontaCombo','var_todo_id_to','','loader_ajax_responsavel',this.value);">
							<?php if(getValue($objRS,"cod_atividade") != ""){?>
								<option value="equipe"><?php echo(getTText("equipe",C_TOUPPER));?></option>
							<?php } else{ ?>
								<?php if(getsession(CFG_SYSTEM_NAME."_id_usuario") == getValue($objRS,"id_responsavel")){?>
									<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario ORDER BY grp_user","grp_user","grp_user",getValue($objRS,"grupo_executor")));?>
								<?php } else{ ?>
									<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario ORDER BY grp_user","grp_user","grp_user",getValue($objRS,"grupo_responsavel")));?>
								<?php }?>
							<?php }?>
							</select>
							&nbsp;
							<select name="var_todo_id_to" id="var_todo_id_to" style="width:100px;">
							<?php if(getValue($objRS,"cod_atividade") != ""){?>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM bs_equipe WHERE dtt_inativo IS NULL AND cod_atividade = ".getValue($objRS,"cod_atividade")." ORDER BY id_usuario","id_usuario","id_usuario",getValue($objRS,"id_ult_executor")));?>
							<?php } else{ ?>
								<?php if(getsession(CFG_SYSTEM_NAME."_id_usuario") == getValue($objRS,"id_responsavel")){?>
									<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = '".getValue($objRS,"grupo_executor")."' ORDER BY id_usuario","id_usuario","id_usuario",getValue($objRS,"id_ult_executor")));?>
								<?php } else{ ?>
									<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = '".getValue($objRS,"grupo_responsavel")."' ORDER BY id_usuario","id_usuario","id_usuario",getValue($objRS,"id_responsavel")));?>
								<?php }?>
							<?php }?>
							</select>
							&nbsp;<span id="loader_ajax_responsavel"></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<select name="var_todo_prioridade" id="var_todo_prioridade" style="width:150px;">
								<option value="normal" <?php echo((getValue($objRS,"prioridade") == "normal") ? "selected='selected'" : "");?> ><?php echo(getTText("normal",C_TOUPPER));?></option>
								<option value="baixa" <?php echo((getValue($objRS,"prioridade") == "baixa") ? "selected='selected'" : "");?> ><?php echo(getTText("baixa",C_TOUPPER));?></option>
								<option value="media" <?php echo((getValue($objRS,"prioridade") == "media") ? "selected='selected'" : "");?> ><?php echo(getTText("media",C_TOUPPER));?></option>
								<option value="alta" <?php echo((getValue($objRS,"prioridade") == "alta") ? "selected='selected'" : "");?> ><?php echo(getTText("alta",C_TOUPPER));?></option>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("resposta",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><textarea name="var_todo_resposta" id="var_todo_resposta" style="width:350px;height:150px;"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("sigiloso",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<textarea name="var_todo_sigiloso" id="var_todo_sigiloso" style="width:350px;height:075px;"></textarea>
							<br /><span class="comment_med"><?php echo(getTText("obs_campo_sigiloso",C_NONE));?></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("anexo",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<input type="text" name="var_todo_arquivo_anexo" id="var_todo_arquivo_anexo" value="" size="50" readonly="true" title="<?php echo(getTText("anexo",C_NONE));?>">
							<input type="button" name="btn_uploader" value="<?php echo(getTText("procurar_reticencias",C_NONE));?>" class="inputclean" onClick="window.open('../modulo_Principal/athuploader.php?var_formname=formstatic&var_fieldname=var_todo_arquivo_anexo&var_dir=/<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/','popup_upload_resp','width=570,height=270,top=30,left=30,scrollbars=1,resizable=yes,status=yes');">
							&nbsp;
							<span class="comment_med" onClick="document.getElementById('var_todo_arquivo_anexo').value = '';" style="font-weight:bold;cursor:pointer;"><?php echo(getTText("obs_limpar_campo",C_NONE));?></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("horas",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<select name="var_todo_horas_1" id="var_todo_horas_1" style="width:45px;">
								<?php for($auxCounter = 0; $auxCounter <= 23; $auxCounter++){?>
								<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." h" : $auxCounter." h");?></option>
								<?php }?>
							</select>
							&nbsp;
							<select name="var_todo_horas_2" id="var_todo_horas_2" style="width:55px;">
								<?php for($auxCounter = 5; $auxCounter <= 55; $auxCounter++){?>
								<?php if(($auxCounter % 5) == 0){?>
								<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." min" : $auxCounter." min");?></option>
								<?php }?>
								<?php }?>
							</select>
						</td>
					</tr>
					<!-- DIALOG INSERT -->
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<!--td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td><td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td-->
									<td width="35%" align="right">
										<button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button>
										<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
										<button onClick="aplicar();return false;"><?php echo(getTText("aplicar",C_UCWORDS));?></button>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>			
			</td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>