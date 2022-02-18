<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$strLocation = request("var_location");
	
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
				, situacao
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
	
	// SE O USUARIO NAO FOR O RESPONSAVEL OU O EXECUTOR DA TAREFA, ENTAO MOSTRA MSG
	if((getsession(CFG_SYSTEM_NAME."_id_usuario") != getValue($objRS,"id_responsavel")) && (getsession(CFG_SYSTEM_NAME."_id_usuario") != getValue($objRS,"id_ult_executor"))){
		mensagem("err_sql_titulo","tarefa_nao_te_pertence","","","aviso",1);
		die();
	}
	
	// SE A SITUAÇÃO ESTIVER MARCADA COMO FECHADA, ENTÃO EXIBE MSG
	if(getValue($objRS,"situacao") == "fechado"){
		mensagem("err_sql_titulo","aviso_tarefa_ja_fechada","",$strLocation,"aviso",1);
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
		<script type="text/javascript">
			var strLocation = null;
			
			function validaCampos(){
				// Esta função faz uma pré-validação via JS dos campos marcados com asterisco
				var strMSG  = "";
				strMSG += (
						   (document.getElementById('var_todo_resposta').value 	  	== "")|| 
						   (document.getElementById('var_todo_horas_1').value 	  	== "")||
						   (document.getElementById('var_todo_horas_2').value 	  	== "")||
						   (document.getElementById('var_todo_dt_realizado').value	== "")
						  ) ? "\n\nDADOS DA RESPOSTA:" : "";
				strMSG += (document.getElementById('var_todo_resposta').value 		== "") ? "\nResposta" 			: "";
				strMSG += (document.getElementById('var_todo_horas_1').value  	 	== "") ? "\nHoras (Parte Um)"	: "";
				strMSG += (document.getElementById('var_todo_horas_2').value   		== "") ? "\nHoras (Parte Dois)"	: "";
				strMSG += (document.getElementById('var_todo_dt_realizado').value   == "") ? "\nData Realizado"		: "";
				if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:'+strMSG); return(false); }
				else { return(true); }
			}
			
			function ok() {
				if(validaCampos()){
					<?php if($strLocation != ""){?>
					strLocation = "<?php echo($strLocation);?>";
					<?php } else{?>
					strLocation = "../modulo_Todolist/data.php";
					<?php }?>
					submeterForm();
				} else{
					return(false);
				}
			}

			function cancelar() {
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					document.location.href = "../modulo_Todolist/data.php";
				<?php }?>
			}
			
			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("todolist",C_TOUPPER)." - (".getTText("finalizar_tarefa",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STfinalizartarefaexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" value="" />
	<input type="hidden" name="var_todo_cod_todo_list" value="<?php echo($intCodDado);?>" />
	<input type="hidden" name="var_todo_id_from" value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
	<input type="hidden" name="var_todo_id_to" value="<?php echo(getValue($objRS,"id_responsavel"));?>" />
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
							<span style="font-weight:bold;font-style:italic;color:#777;">
							<?php echo(getValue($objRS,"id_responsavel"));?>
							</span>
							<?php echo(" (".getTText("responsavel",C_NONE).")");?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("resposta",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><textarea name="var_todo_resposta" id="var_todo_resposta" style="width:350px;height:150px;"></textarea></td>
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
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("dt_realizado",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_todo_dt_realizado" id="var_todo_dt_realizado" value="<?php echo(dDate(CFG_LANG,now(),false));?>" size="12" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
							&nbsp;<span class="comment_med"><?php echo(getTText("obs_hoje",C_NONE));?></span>
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