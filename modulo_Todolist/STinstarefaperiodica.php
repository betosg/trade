<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verifica��o de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));      
	
	// INICIALIZA MODULO
	initModuloParams(basename(getcwd()));    
	
	// verifica��o de acesso do usu�rio corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_PER");

	// REQUESTS
	$intCodAtividade = request("var_cod_atividade");	 // COD_ATIVIDADE / BS
	$strLocation = request("var_location");
	
	// abre objeto para manipula��o com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// fun��o para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// TRATAMENTO PARA DATAS
	$arrDateIni = explode("/",dDate(CFG_LANG,dateNow(),false));
	$arrDateFim = explode("/",dDate(CFG_LANG,dateAdd("d",7,dateNow()),false));
	
	// print_r($arrDateFim);
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #CCC;
				border-right: 1px solid #CCC;
				border-bottom:1px solid #CCC;
				border-left:  1px solid #CCC;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
			
			#img_drop_dt_ini{ cursor:pointer; display:none }
						
			#img_drop_dt_fim{ cursor:pointer; display:none }
			
			#lst_dt_ini{ width:250px;height:100px;overflow:scroll;display:none }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			
			function validaCampos(){
				// Esta fun��o faz uma pr�-valida��o via JS dos campos marcados com asterisco
				var strMSG  = "";
				strMSG += (
						   (document.getElementById('var_todo_titulo').value 		  == "")|| 
						   (document.getElementById('var_todo_situacao').value 		  == "")||
						   (document.getElementById('var_todo_categoria').value 	  == "")||
						   (document.getElementById('var_todo_prioridade').value 	  == "")|| 
						   (document.getElementById('var_todo_id_responsavel').value  == "")|| 
						   (document.getElementById('var_todo_id_ult_executor').value == "")|| 
						   (document.getElementById('var_todo_descricao').value 	  == "")
						   <?php if($intCodAtividade != ""){?>
						   ||(document.getElementById('var_todo_prev_dt_ini').value   == "")
						   ||(document.getElementById('var_todo_prev_hr_ini_1').value == "")
						   ||(document.getElementById('var_todo_prev_hr_ini_2').value == "")
						   ||(document.getElementById('var_todo_prev_horas_1').value  == "")
						   ||(document.getElementById('var_todo_prev_horas_2').value  == "")
						   <?php }?>
						  ) ? "\n\nDADOS DA TAREFA:" : "";
				strMSG += (document.getElementById('var_todo_titulo').value 		  == "") ? "\nT�tulo" 		: "";
				strMSG += (document.getElementById('var_todo_situacao').value 		  == "") ? "\nSitua��o" 	: "";
				strMSG += (document.getElementById('var_todo_categoria').value 		  == "") ? "\nCategoria"	: "";
				strMSG += (document.getElementById('var_todo_prioridade').value  	  == "") ? "\nPrioridade"	: "";
				strMSG += (document.getElementById('var_todo_id_responsavel').value   == "") ? "\nRespons�vel"	: "";
				strMSG += (document.getElementById('var_todo_id_ult_executor').value  == "") ? "\nExecutor"		: "";
				strMSG += (document.getElementById('var_todo_descricao').value  	  == "") ? "\nDescri��o"	: "";
				<?php if($intCodAtividade != ""){?>
				strMSG += (document.getElementById('var_todo_prev_dt_ini').value 	  == "") ? "\nPrevis�o de In�cio (Data)" 			: "";
				strMSG += (document.getElementById('var_todo_prev_hr_ini_1').value 	  == "") ? "\nParte Um - Hora Previs�o de In�cio" 	: "";
				strMSG += (document.getElementById('var_todo_prev_hr_ini_2').value 	  == "") ? "\nParte Dois - Hora Previs�o de In�cio"	: "";
				strMSG += (document.getElementById('var_todo_prev_horas_1').value  	  == "") ? "\nParte Um - Previs�o de Horas"			: "";
				strMSG += (document.getElementById('var_todo_prev_horas_2').value     == "") ? "\nParte Dois - Previs�o de Horas"		: "";
				<?php }?>
				if(strMSG != ""){ alert('Os seguintes campos n�o foram preenchidos:'+strMSG); return(false); }
				else { return(true); }
			}
			
			function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
				strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
				AbreJanelaPAGE(strLink, "570", "270");
			}
			
			function ajaxDetailDataLoader(prSQL,prFuncao,prID,prFuncExtra,prIDLoader){
				var objAjax;
				var strReturnValue;
				var objLoader = document.getElementById(prIDLoader);
	
				objAjax = createAjax();
				
				if(objLoader != null){
					objLoader.innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
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
							alert("Erro no processamento da p�gina: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET","../_ajax/returndados.php?var_sql="+prSQL,true); 
				objAjax.send(null); 
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
				document.location.href = "../modulo_Todolist/index.php";
				<?php }?>
			}
			
			function aplicar() {
				if(validaCampos()){
					strLocation = "../modulo_Todolist/STinstarefa.php";
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
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("todolist",C_UCWORDS)." - (".getTText("insercao_de_tarefas_periodicas",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STinstarefaperiodicaexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" id="DEFAULT_LOCATION" value="" />
	<input type="hidden" name="var_todo_cod_atividade" id="var_todo_cod_atividade" value="<?php echo($intCodAtividade);?>" />
	<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<!-- DADOS AGENDA -->
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><img src="../img/iconmx_tarefa_periodo.gif" border="0" /></td>
					<td width="77%" align="left"><?php echo(getTText("obs_insercao_periodica",C_NONE));?></td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				
				<!-- PASSO UM: PERIODO DO AGENDAMENTO -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("passo_um",C_TOUPPER).": ".getTText("periodo_do_agendamento",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo("*".getTText("inicio",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<input type="text" name="var_todo_periodo_inicio" id="var_todo_periodo_inicio" size="12" maxlength="10" value="<?php echo(dDate(CFG_LANG,dateNow(),false));?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
						&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo("*".getTText("fim",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<input type="text" name="var_todo_periodo_fim" id="var_todo_periodo_fim" size="12" maxlength="10" value="<?php echo(dDate(CFG_LANG,dateAdd("d",7,dateNow()),false));?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
						&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				
				<!-- PASSO DOIS: DEFINICAO DOS MESES DA TAREFA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("passo_dois",C_TOUPPER).": ".getTText("definicao_dos_meses",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("observacao",C_UCWORDS));?>:</strong></td>
					<td align="left"><span class="comment_med"><?php echo(getTText("obs_definicao_de_meses",C_NONE));?></span></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo("*".getTText("meses",C_UCWORDS));?>:</strong></td>
					<td align="left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<?php for($auxCounter = 1; $auxCounter <= 12; $auxCounter++){?>
								<?php if($auxCounter == 7){?>
								</td></tr><tr><td>
								<?php } else{?>
								<td>
								<?php }?>
								<input type="checkbox" name="var_todo_periodo_meses[]" id="var_todo_periodo_meses" class="inputclean" value="<?php echo($auxCounter);?>" <?php echo((($auxCounter == $arrDateIni[1]) || ($auxCounter == $arrDateFim[1])) ? "checked='checked'" : "");?> />
								<?php echo(ucwords(substr(getMesExtensoFromMes($auxCounter),0,3)));?>&nbsp;&nbsp;
								</td>
							<?php }?>
						</tr>
						<tr><td colspan="12" height="10"></td></tr>
						<tr>
							<td colspan="12">
								<input type="checkbox" name="var_todo_flag_todos_meses" id="var_todo_flag_todos_meses" class="inputclean" value="S" />
								<?php echo(getTText("todos_os_meses",C_NONE));?>
								&nbsp;<span class="comment_med"><?php echo(getTText("obs_marcacao_todos_meses",C_NONE));?></span>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				
				<!-- PASSO 3: DEFINI��O DOS DIAS -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("passo_tres",C_TOUPPER).": ".getTText("definicao_dos_dias",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><input type="radio" name="var_todo_opcao_dias" id="var_todo_opcao_dias" value="DIA" class="inputclean" checked="checked" /></td>
					<td align="left" valign="top">
						<?php echo(getTText("todo_dia",C_NONE));?>
						<select name="var_todo_dias" id="var_todo_dias" style="width:40px;">
							<?php for($auxCounter = 1; $auxCounter <= 31; $auxCounter++){?>
							<option value="<?php echo($auxCounter);?>"><?php echo($auxCounter);?></option>
							<?php }?>
						</select>
						<?php echo(getTText("do_mes",C_NONE));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><input type="radio" name="var_todo_opcao_dias" id="var_todo_opcao_dias" value="SEM" class="inputclean" /></td>
					<td align="left" valign="top">
						<?php echo(getTText("toda",C_NONE));?>
						<?php for($auxCounter = 1; $auxCounter <= 07; $auxCounter++){?>
						<input type="checkbox" name="var_todo_semana[]" id="var_todo_semana" value="<?php echo(getWeekDayFromNumber($auxCounter));?>" class="inputclean" />
						<?php echo(ucwords(substr(getWeekDayFromNumber($auxCounter),0,3)));?>&nbsp;
						<?php }?>
						<?php echo(getTText("da_semana",C_NONE));?>
						<br />
						<br />
						<input type="checkbox" name="var_todo_flag_todos_dias" id="var_todo_flag_todos_dias" class="inputclean" value="S" />
						<?php echo(getTText("todos_os_dias_da_semana",C_NONE));?>
						&nbsp;<span class="comment_med"><?php echo(getTText("obs_marcacao_todos_meses",C_NONE));?></span>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				
				<!-- DADOS DA TAREFA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_tarefa",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<!-- PARA LIGA��O DA ATIVIDADE / BS -->
				<?php if($intCodAtividade != ""){?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_atividade_bs",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($intCodAtividade);?></td>
				</tr>
				<?php }?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td align="left"><input type="text" name="var_todo_titulo" id="var_todo_titulo" value="" maxlength="250" size="60" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("situacao",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_todo_situacao" id="var_todo_situacao" style="width:150px;">
							<option value="aberto"><?php echo(getTText("aberto",C_TOUPPER));?></option>
							<option value="executando"><?php echo(getTText("executando",C_TOUPPER));?></option>
							<option value="fechado"><?php echo(getTText("fechado",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_todo_categoria" id="var_todo_categoria" style="width:150px;">
							<?php echo(montaCombo($objConn,"SELECT tl_categoria.cod_categoria, UPPER(tl_categoria.cod_categoria||' - '||tl_categoria.nome) AS nome FROM tl_categoria WHERE dtt_inativo IS NULL","cod_categoria","nome",""));?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_todo_prioridade" id="var_todo_prioridade" style="width:150px;">
							<option value="normal"><?php echo(getTText("normal",C_TOUPPER));?></option>
							<option value="baixa"><?php echo(getTText("baixa",C_TOUPPER));?></option>
							<option value="media"><?php echo(getTText("media",C_TOUPPER));?></option>
							<option value="alta"><?php echo(getTText("alta",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("responsavel",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_grupo_usuario_responsavel" id="var_grupo_usuario_responsavel" style="width:80px;" onChange="limpaSelect('var_todo_id_responsavel');ajaxDetailDataLoader((this.value != '') ? 'SELECT DISTINCT id_usuario, id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = \'' + this.value + '\' ORDER BY id_usuario' : '','ajaxMontaCombo','var_todo_id_responsavel','','loader_ajax_responsavel');">
							<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario ORDER BY grp_user","grp_user","grp_user",getsession(CFG_SYSTEM_NAME."_grp_user")));?>
						</select>
						&nbsp;
						<select name="var_todo_id_responsavel" id="var_todo_id_responsavel" style="width:100px;">
							<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = '".getsession(CFG_SYSTEM_NAME."_grp_user")."' ORDER BY id_usuario","id_usuario","id_usuario",getsession(CFG_SYSTEM_NAME."_id_usuario")));?>
						</select>
						&nbsp;<span id="loader_ajax_responsavel"></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("executor",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_grupo_usuario_executor" id="var_grupo_usuario_executor" style="width:80px;" onChange="limpaSelect('var_todo_id_ult_executor');ajaxDetailDataLoader((this.value != '') ? 'SELECT DISTINCT id_usuario, id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = \'' + this.value + '\' ORDER BY id_usuario' : '','ajaxMontaCombo','var_todo_id_ult_executor','','loader_ajax_executor');">
							<option value=""><?php echo(getTText("grupo_reticencias",C_NONE));?></option>
							<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario ORDER BY grp_user","grp_user","grp_user",""));?>
						</select>
						&nbsp;
						<select name="var_todo_id_ult_executor" id="var_todo_id_ult_executor" style="width:100px;">
							<option value=""><?php echo(getTText("usuario_reticencias",C_NONE));?></option>
							<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = '".getsession(CFG_SYSTEM_NAME."_grp_user")."' ORDER BY id_usuario","id_usuario","id_usuario",""));?>
						</select>
						&nbsp;<span id="loader_ajax_executor"></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(($intCodAtividade != "") ? "*".getTText("prev_dt_ini_horas",C_UCWORDS) : getTText("prev_dt_ini_horas",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_todo_prev_hr_ini_1" id="var_todo_prev_hr_ini_1" style="width:45px;">
							<?php for($auxCounter = 0; $auxCounter <= 23; $auxCounter++){?>
							<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." h" : $auxCounter." h");?></option>
							<?php }?>
						</select>
						&nbsp;
						<select name="var_todo_prev_hr_ini_2" id="var_todo_prev_hr_ini_2" style="width:55px;">
							<?php for($auxCounter = 0; $auxCounter <= 55; $auxCounter++){?>
							<?php if(($auxCounter % 5) == 0){?>
							<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." min" : $auxCounter." min");?></option>
							<?php }?>
							<?php }?>
						</select>
						&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data_hora_somente_hora",C_NONE));?></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(($intCodAtividade != "") ? "*".getTText("prev_horas",C_UCWORDS) : getTText("prev_horas",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_todo_prev_horas_1" id="var_todo_prev_horas_1" style="width:45px;">
							<?php for($auxCounter = 0; $auxCounter <= 23; $auxCounter++){?>
							<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." h" : $auxCounter." h");?></option>
							<?php }?>
						</select>
						&nbsp;
						<select name="var_todo_prev_horas_2" id="var_todo_prev_horas_2" style="width:55px;">
							<?php for($auxCounter = 0; $auxCounter <= 55; $auxCounter++){?>
							<?php if(($auxCounter % 5) == 0){?>
							<option value="<?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter : $auxCounter);?>"><?php echo((strlen($auxCounter) < 2) ? "0".$auxCounter." min" : $auxCounter." min");?></option>
							<?php }?>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("anexo",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<input type="text" name="var_todo_arquivo_anexo" id="var_todo_arquivo_anexo" value="" size="50" readonly="true" title="<?php echo(getTText("anexo",C_NONE));?>">
						<input type="button" name="btn_uploader" value="<?php echo(getTText("procurar_reticencias",C_NONE));?>" class="inputclean" onClick="callUploader('formstatic','var_todo_arquivo_anexo','/<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/','','');">
						&nbsp;
						<span class="comment_med" onClick="document.getElementById('var_todo_arquivo_anexo').value = '';" style="font-weight:bold;cursor:pointer;"><?php echo(getTText("obs_limpar_campo",C_NONE));?></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong>*<?php echo(getTText("descricao_da_tarefa",C_UCWORDS));?>:</strong></td>
					<td align="left"><textarea name="var_todo_descricao" id="var_todo_descricao" style="width:350px;height:120px;"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="%" align="right">
						<button onClick="ok('formstatic');">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
					<td width="10%" align="left">
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button>
					</td>
					<td width="27%" align="left" style="padding-right:25px;">
						<button onClick="aplicar('formstatic');"><?php echo(getTText("aplicar",C_NONE));?></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOT�ES -->
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
</html>