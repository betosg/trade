<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodAgenda	= request("var_chavereg");		// cod_agenda para o qual irá ser update da agenda
	$strFlagClose   = request("var_flag_close");    // flag para fechar a JANELA caso aberta em POP-UP, usado btn CANCELAR
	$strPopulate 	= "yes";
	
	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'','erro','1');
		die();
	}

	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// busca dados sobre a agenda para confirmação antes de DEL
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
	
	// localiza o número de respostas 
	// referentes a agenda corrente b
	try{
		$strSQL = "
			SELECT 
  				  id_usuario
  				, resposta
 				, dtt_resposta
  				, sys_usr_ins
  				, sys_dtt_ins
			FROM ag_resposta 
			WHERE cod_agenda = ".$intCodAgenda."
			ORDER BY sys_dtt_ins DESC";
		$objResult 		= $objConn->query($strSQL);
		$objResultFetch	= $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
			
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
		<title><?php echo(strtoupper(CFG_SYSTEM_TITLE));?></title>
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
			function ok() {
				strLocation = "../modulo_Agenda/STrespostas.php?var_chavereg=<?php echo($intCodAgenda);?>";
				submeterForm();
			}

			function cancelar() {
				<?php if($strFlagClose == ""){?>
				document.location.href = "../modulo_Agenda/STrespostas.php?var_chavereg=<?php echo($intCodAgenda);?>";
				<?php } else{?>
				window.close();
				<?php }?>
			}

			function aplicar() {
				strLocation = "../modulo_Agenda/STinsresposta.php?var_chavereg=<?php echo($intCodAgenda);?>";
				submeterForm();
			}

			function submeterForm() {
				document.formstatic_resp.DEFAULT_LOCATION.value = strLocation;
				document.formstatic_resp.submit();
			}
			
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
			    resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
				return true;
			}
			
		</script>
	</head>
<body bgcolor="#FFFFFF"  style="margin:10px 0px 10px 0px;">
<!-- body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;"  -->

<!-- USO -->
<center>
<?php athBeginFloatingBox("520","",getTText("event_ins_resp",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="315" width="500" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_dados_resp",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
				
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("resumo_agenda",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("cod_agenda",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_agenda"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right">
						<strong><?php echo(getTText("criador_responsavel",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="25%" align="left">
									<?php echo(getValue($objRS,"id_responsavel"));?>
								</td>
								<td width="30%" align="right">
									<strong><?php echo(getTText("data_criacao",C_UCWORDS));?>:</strong>
								</td>
								<td width="40%" align="left" style="padding-left:5px;">
									<?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),false));?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"titulo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php 
							$strReticencias = (strlen(getValue($objRS,"descricao")) >= 60) ? "..." : "";
							echo("<span alt='".getValue($objRS,"descricao")."' title='".getValue($objRS,"descricao")."'
								   style='cursor:default;'>".
								   substr(getValue($objRS,"descricao"),0,60).$strReticencias."</span>");
						?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td width="77%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="25%" align="left">
									<?php echo(getValue($objRS,"categoria"));?>
								</td>
								<td width="30%" align="right">
									<strong><?php echo(getTText("prioridade",C_UCWORDS));?>:</strong>
								</td>
								<td width="40%" align="left" style="padding-left:5px;">
									<?php echo(getValue($objRS,"prioridade"));?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("previsao_ini_abrev",C_UCWORDS));?>:</strong></td>
					<td width="77%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td width="30%" align="left">
									<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true));?>
								</td>
								<td width="30%" align="right">
									<strong><?php echo(getTText("previsao_fim_abrev",C_UCWORDS));?>:</strong>
								</td>
								<td width="40%" align="left" style="padding-left:5px;">
									<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true));?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<!-- DADOS AGENDA -->
				
				
				
				<tr><td colspan="2">&nbsp;</td></tr>
				
				
				
				<!-- RESPOSTAS -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">
						<span id="img_respostas">
							<img src="../img/icon_tree_plus.gif" onClick="showHide('');" style="cursor:pointer;">
						</span>
					</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("respostas",C_TOUPPER)." (".$objResult->rowCount().")");?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<tr>
					<td colspan="2">
						<table cellpadding="1" cellspacing="1" border="0" width="100%" id="respostas" style="display:none;">
							<?php if($objResult->rowCount() <= 0){?>
							<tr bgcolor="#FFFFFF">
								<td colspan="2" class="td_no_resp">
									<?php echo(getTText("nenhuma_resposta",C_UCWORDS));?>
								</td>
							</tr>
							<?php 
								} else { 
								foreach($objResultFetch as $objRSResp){
							?>
							<tr bgcolor="#FFFFFF">
								<td colspan="2" class="td_resp" align="center">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr><td colspan="2" class="td_resp_cabec">
											<?php echo(getTText("resposta",C_UCWORDS));?>
										</td></tr>
										<tr>
											<td width="20%" class="td_resp_conte">
												<?php echo("<strong>".getTText("por",C_UCWORDS).":</strong> ");?>
												<?php echo(getValue($objRSResp,"id_usuario"));?>
											</td>
											<td width="80%" class="td_resp_conte">
												<?php echo("<strong>".getTText("data",C_UCWORDS).":</strong> ");?>
												<?php echo(dDate(CFG_LANG,getValue($objRSResp,"dtt_resposta"),false));?>
											</td>
										</tr>
										<tr>
											<td colspan="2" class="td_resp_conte" style="padding-top:0px;">
												<?php echo("<span class='comment_peq'>".getTText("registrado_por",C_NONE));?>
												<?php echo(" <i><b>".getValue($objRSResp,"sys_usr_ins")."</b></i>");?>
												<?php echo(" ".getTText("em",C_NONE)." ");?>
												<?php echo(dDate(CFG_LANG,getValue($objRSResp,"sys_dtt_ins"),true)."</span>");?>
											</td>
										</tr>
										<tr height="2"><td colspan="2"></td></tr>
										<tr><td colspan="2" class="td_resp_conte">
											<table cellpadding="0" cellspacing="0" border="0" width="95%">
												<tr>
													<td valign="top" width="8%">
													<?php echo("<strong>".getTText("texto",C_UCWORDS).":</strong> ");?>
													</td>
													<td class="td_text_resp" width="%92">
													<?php echo(getValue($objRSResp,"resposta"));?>
													</td>
												</tr>
											</table>
										</td></tr>
									</table>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<?php }} ?>
						</table>
					</td>
				</tr>
				<!-- RESPOSTAS -->
				
				
				<tr><td colspan="2">&nbsp;</td></tr>
				
				
				<!-- DADOS NOVA RESPOSTA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_resposta",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				
				<form name="formstatic_resp" action="STinsrespostaexec.php" method="post">
				<input type="hidden" name="var_chavereg"    value="<?php echo($intCodAgenda);?>" />
				<input type="hidden" name="var_sys_usr_ins" value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
				<input type="hidden" name="DEFAULT_LOCATION" 
				value="../modulo_Agenda/STrespostas.php?var_chavereg=<?php echo($intCodAgenda);?>" />
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("por",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_id_usuario" style="width:150px;">
						<option value=""><?php echo(getTText("selec_citado",C_NONE));?></option>
						<?php echo(montaCombo($objConn,"
											  SELECT id_usuario 
											  FROM ag_agenda_citado 
											  WHERE cod_agenda = ".$intCodAgenda,"id_usuario","id_usuario",
											  getsession(CFG_SYSTEM_NAME."_id_usuario")));?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong>*<?php echo(getTText("dtt_ins_resposta",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%" align="left"><input type="text" size="15" maxlength="10" 
												  onKeyPress="return validateNumKey(event);"
												  onKeyDown ="FormataInputData(this,event);"
												  name="var_dtt_ins_resposta"
												  value="<?php echo(dDate(CFG_LANG,now(),false));?>"/>
												 <span class="comment_peq"><?php echo(getTText("obs_data_resp",C_NONE));?></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("texto",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<textarea name="var_resposta" 
						onFocus="if(this.value == '<?php echo(getTText("sua_resp_aqui",C_NONE));?>'){javascript:this.value='';}"
						onClick="if(this.value == '<?php echo(getTText("sua_resp_aqui",C_NONE));?>'){javascript:this.value='';}"
						rows="6" cols="60"><?php echo(getTText("sua_resp_aqui",C_NONE));?></textarea>
					</td>
				</tr>
				</form>
				<!-- DADOS NOVA RESPOSTA -->
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
					<td width="20%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr><td align="right" width="23%" style="padding-right:8px;"></td></tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="35%" align="right">
						<button onClick="ok();">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
					<td width="20%" align="left" >
						<button onClick="cancelar('STrespostas.php?var_chavereg=<?php echo($intCodAgenda);?>');return false;">
							<?php echo(getTText("cancelar",C_NONE));?>
						</button>
					</td>
					<td width="25%" align="left" >
						<button onClick="aplicar();">
							<?php echo(getTText("aplicar",C_NONE));?>
						</button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>