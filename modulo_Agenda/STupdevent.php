<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");

	// REQUESTS
	$intCodAgenda = request("var_chavereg");	 // cod_agenda para o qual irá ser update da agenda
	$strFlagRedir = request("var_flag_redir");   // flag para redirecionar o action depois para a agenda [VIEW_AGENDA]
	
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'','erro','1');
		die();
	}

	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// busca dados sobre a agenda para 
	// popular campos do update form
	try{
		$strSQL = "
			SELECT 
				  ag_agenda.cod_agenda 
				, ag_agenda.id_responsavel
  				, ag_agenda.categoria
				, ag_agenda.titulo 
				, ag_agenda.descricao
				, ag_agenda.prioridade 
				, ag_agenda.prev_dtt_ini 
				, ag_agenda.prev_dtt_fim
				, ag_agenda.dtt_realizado 
				, ag_agenda.sys_dtt_ins 
				, ag_agenda.sys_dtt_upd 
				, ag_agenda.sys_usr_ins 
				, ag_agenda.sys_usr_upd 
			FROM
				ag_agenda
			WHERE ag_agenda.cod_agenda = ".$intCodAgenda;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// fetch dos dados localizados
	$objRS = $objResult->fetch();
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
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
				border-top:   1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				border-bottom:1px solid #E9E9E9;
				border-left:  1px solid #E9E9E9;
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
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function showHide(){
				// Esta função exibe ou faz hide na
				// table que contém as respostas, para
				// que a página não abra totalmente
				if (document.getElementById('respostas').style.display == 'block'){
					document.getElementById('respostas').style.display = 'none';
					document.getElementById('img_respostas').innerHTML = "<img src='../img/icon_tree_plus.gif' onclick=\"showHide('respostas');\" style='cursor:pointer;'>";
				}else if(document.getElementById('respostas').style.display == 'none'){
					document.getElementById('respostas').style.display = 'block';
					document.getElementById('img_respostas').innerHTML = "<img src='../img/icon_tree_minus.gif' onclick=\"showHide('respostas');\" style='cursor:pointer;'>";
				}
				return true;
			}
			
			function showCitados(){
				// Esta função exibe ou faz hide na
				// table que contém os citados, para
				// que a página não abra totalmente
				if (document.getElementById('citados').style.display == 'block'){
					document.getElementById('citados').style.display = 'none';
					document.getElementById('img_citados').innerHTML = "<img src='../img/icon_tree_plus.gif' onclick=\"showCitados('citados');\" style='cursor:pointer;'>";
				}else if(document.getElementById('citados').style.display == 'none'){
					document.getElementById('citados').style.display = 'block';
					document.getElementById('img_citados').innerHTML = "<img src='../img/icon_tree_minus.gif' onclick=\"showCitados('citados');\" style='cursor:pointer;'>";
				}
				return true;
			}
	
			function focusField(prIDField){
				// OBS: Esta funcao seta o focus
				// para um campo de id especifico
				// informado como parametro
				strIDField = prIDField;
				document.getElementById(strIDField).focus();
			}

			function ok() {
				strLocation = "../modulo_Agenda/";
				submeterForm();
			}

			function cancelar() {
				<?php if($strFlagRedir == ""){?>
				document.location.href = "../modulo_Agenda/";
				<?php } else{?>
				document.location.href = "../modulo_Agenda/STdatascheduler.php";
				<?php }?>
			}
			
			function aplicar() {
				// RESETA VALUE PARA NAO FAZER REDIRECT PARA AGENDA
				document.formstatic.var_flag_redir.value = "";
				strLocation = "../modulo_Agenda/STupdevent.php?var_chavereg=<?php echo($intCodAgenda);?>";
				submeterForm();
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
<?php athBeginFloatingBox("710","",getTText("update_event",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STupdeventexec.php" method="post">
<input type="hidden" name="var_chavereg" 	 value="<?php echo($intCodAgenda);?>" />
<input type="hidden" name="DEFAULT_LOCATION" value="" />
<input type="hidden" name="var_flag_redir" 	 value="<?php echo($strFlagRedir);?>" />
<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_antes_upd",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_agenda",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("cod_agenda",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_agenda"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("criador_responsavel",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"id_responsavel"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("data_criacao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="text" name="var_titulo" id="var_titulo" size="60" maxlength="250" 
						 value="<?php echo(getValue($objRS,"titulo"));?>" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%" align="left">
						<textarea name="var_descricao" rows="6" cols="60"><?php echo(getValue($objRS,"descricao"));?></textarea>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_categoria" style="width:120px;">
							<option value="REUNIAO"     <?php echo(getValue($objRS,"categoria") == "REUNIAO") ? "selected='selected'" : "";?>><?php echo(getTText("reuniao",C_TOUPPER));?></option>
							<option value="ENCONTRO"    <?php echo(getValue($objRS,"categoria") == "ENCONTRO") ? "selected='selected'" : "";?>><?php echo(getTText("encontro",C_TOUPPER));?></option>
							<option value="CONFERENCIA" <?php echo(getValue($objRS,"categoria") == "CONFERENCIA") ? "selected='selected'" : "";?>><?php echo(getTText("conferencia",C_TOUPPER));?></option>
							<option value="ALMOCO" 		<?php echo(getValue($objRS,"categoria") == "ALMOCO") ? "selected='selected'" : "";?>><?php echo(getTText("almoco",C_TOUPPER));?></option>
							<option value="HOMOLOGACAO"	<?php echo(getValue($objRS,"categoria") == "HOMOLOGACAO") ? "selected='selected'" : "";?>><?php echo(getTText("homologacao",C_TOUPPER));?></option>
							<option value="JANTAR" 		<?php echo(getValue($objRS,"categoria") == "JANTAR") ? "selected='selected'" : "";?>><?php echo(getTText("jantar",C_TOUPPER));?></option>
							<option value="VISITA" 		<?php echo(getValue($objRS,"categoria") == "VISITA") ? "selected='selected'" : "";?>><?php echo(getTText("visita",C_TOUPPER));?></option>
							<option value="VIAGEM" 		<?php echo(getValue($objRS,"categoria") == "VIAGEM") ? "selected='selected'" : "";?>><?php echo(getTText("viagem",C_TOUPPER));?></option>
							<option value="ANIVERSARIO" <?php echo(getValue($objRS,"categoria") == "ANIVERSARIO") ? "selected='selected'" : "";?>><?php echo(getTText("aniversario",C_TOUPPER));?></option>
							<option value="COMEMORACAO" <?php echo(getValue($objRS,"categoria") == "COMEMORACAO") ? "selected='selected'" : "";?>><?php echo(getTText("comemoracao",C_TOUPPER));?></option>
							<option value="FERIADO" 	<?php echo(getValue($objRS,"categoria") == "FERIADO") ? "selected='selected'" : "";?>><?php echo(getTText("feriado",C_TOUPPER));?></option>
							<option value="OUTROS" 		<?php echo(getValue($objRS,"categoria") == "OUTROS") ? "selected='selected'" : "";?>><?php echo(getTText("outros",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_prioridade" style="width:120px;">
							<option value="<?php echo(getTText("baixa",C_TOUPPER));?>"
							<?php if(getValue($objRS,"prioridade")==getTText("baixa",C_TOUPPER)) echo("selected='selected'");?>>
							<?php echo(getTText("baixa",C_TOUPPER));?>
							</option>
							<option value="<?php echo(getTText("normal",C_TOUPPER));?>"
							<?php 
								if((getValue($objRS,"prioridade")==getTText("normal",C_TOUPPER))||
								   (getValue($objRS,"prioridade")=="")) 
								echo("selected='selected'");
							?>>
								<?php echo(getTText("normal",C_TOUPPER));?>
							</option>
							<option value="<?php echo(getTText("media",C_TOUPPER));?>"<?php 
							if(getValue($objRS,"prioridade")==getTText("media",C_TOUPPER)) echo("selected='selected'");?>>
								<?php echo(getTText("media",C_TOUPPER));?>
							</option>
							<option value="<?php echo(getTText("alta",C_TOUPPER));?>"
							<?php if(getValue($objRS,"prioridade")==getTText("alta",C_TOUPPER)) echo("selected='selected'");?>>
								<?php echo(getTText("alta",C_TOUPPER));?>
							</option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("previsao_ini",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="20%">
									<input type="text" name="var_dt_prev_ini" size="12" maxlength="10"
						 			 onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);"
						 			 value="<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),false));?>" />
						 		</td>
								<td width="80%" align="left">
									<input type="text" name="var_hr_prev_ini" size="8" maxlength="5"
									 value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),11,15));?>" 
									 onkeyPress="FormataInputHoraMinuto(this,event);"/>
									<span class="comment_peq">
										<?php echo(getTText("obs_hora_minutos",C_NONE));?>
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("previsao_fim",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="20%">
									<input type="text" name="var_dt_prev_fim" size="12" maxlength="10"
						 			 onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);"
						 			 value="<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),false));?>" />
						 		</td>
								<td width="80%" align="left">
									<input type="text" name="var_hr_prev_fim" size="8" maxlength="5"
									 value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true),11,15));?>" 
									 onkeyPress="FormataInputHoraMinuto(this,event);"/>
									<span class="comment_peq">
										<?php echo(getTText("obs_hora_minutos",C_NONE));?>
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<!-- DADOS AGENDA -->
				
				<tr><td colspan="2">&nbsp;</td></tr>
				
				<?php if(getValue($objRS,"id_responsavel") == getsession(CFG_SYSTEM_NAME."_id_usuario")){?>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_realizado",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("realizado_em",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="20%">
									<input type="text" name="var_dt_realizado" size="12" maxlength="10"
						 			 onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);"
						 			 value="<?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_realizado"),false));?>" />
						 		</td>
								<td width="80%" align="left">
									<input type="text" name="var_hr_realizado" size="8" maxlength="5"
									 value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"dtt_realizado"),true),11,5));?>" 
									 onkeyPress="FormataInputHoraMinuto(this,event);"/>
									<span class="comment_peq">
										<?php echo(getTText("obs_hora_minutos",C_NONE));?>
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php }?>
				
				<!-- CASO SEJA NECESSÁRIO FUTURAMENTE SER INCLUÍDO A EDIÇÃO DE CITADOS E    --> 
				<!-- AS RESPOSTAS PARA ESTA AGENDA ENTAO COPIAR DA PÁGINA STDELETEEVENT.PHP -->
				
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
                <td width="%" align="right"><button onClick="ok('formstatic');return false;"><?php echo(getTText("ok",C_NONE));?></button></td>
                <td width="10%" align="left"><button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button></td>
                <td width="27%" align="left" style="padding-right:25px;"><button onClick="aplicar('formstatic');return false;"><?php echo(getTText("aplicar",C_NONE));?></button></td>
            </tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
</form>
<?php athEndFloatingBox();?>
</center>
<script type="text/javascript">
	document.getElementById('var_titulo').focus();
</script>
</body>
</html>