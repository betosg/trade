<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");	// COD_HOMOLOGACAO
	
	// CARREGA PREFIX PARA SESSION
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("cod_homo_null",C_NONE),"","erro","1");
		die();
	}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
		
	// LOCALIZA TODOS OS DOCUMENTOS EMITIDOS
	// PARA A HOMOLOGAÇÃO ATUAL, NEW NEW NEW
	try{
		$strSQL = "
			SELECT 
				  ag_agenda.cod_agenda
				, ag_agenda.titulo
				, ag_agenda.descricao
				, ag_agenda.prev_dtt_ini
				, ag_agenda.prev_dtt_fim
				, sd_homologacao.situacao
			FROM 
				  ag_agenda
			INNER JOIN
				  sd_homologacao ON (ag_agenda.tipo = 'sd_homologacao' AND ag_agenda.codigo = sd_homologacao.cod_homologacao AND ag_agenda.codigo = ".$intCodDado.")";

		//die($strSQL);
		$objResult  = $objConn->query($strSQL);
		$objRS      = $objResult->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// LOCALIZA DADOS DO TITULO
	try{
		$strSQL = "
			SELECT dt_vcto, cod_conta_pagar_receber FROM fin_conta_pagar_receber INNER JOIN sd_homologacao ON (sd_homologacao.cod_pedido = fin_conta_pagar_receber.cod_pedido)
			WHERE sd_homologacao.cod_homologacao = ".$intCodDado;

		//die($strSQL);
		$objResultF  = $objConn->query($strSQL);
		$objRSF      = $objResultF->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// INICIALIZA VAR PARA TROCAR DE COR LINHA
	$strColor = CL_CORLINHA_2;
	
	// FUNÇÃO PARA TROCA DE COR DE LINHAS
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
				var strMSG = "";
				if(
					(document.getElementById("var_new_prev_dtt_ini").value 	== "")||
					(document.getElementById("var_new_prev_dtt_fim").value 	== "")||
					(document.getElementById("var_hr_prev_ini").value 		== "")||
					(document.getElementById("var_hr_prev_fim").value 		== "")
				  ){ strMSG += "\n\nDADOS DA NOVA PREVISÃO:";}
				if(document.getElementById("var_new_prev_dtt_ini").value 	== "") { strMSG += "\nData de Previsão Inicial"; }
				if(document.getElementById("var_new_prev_dtt_fim").value 	== "") { strMSG += "\nData de Previsão Final"; }
				if(document.getElementById("var_hr_prev_ini").value 	 	== "") { strMSG += "\nHora de Previsão Inicial"; }
				if(document.getElementById("var_hr_prev_fim").value 	 	== "") { strMSG += "\nHora de Previsão Final"; }
				if(
					(document.getElementById("var_flag_alterar_titulo").checked == true)&&
					(document.getElementById("var_dt_vcto_tit").value == "")
				  ){ strMSG += "\n\nDATA DE VENCIMENTO:";}
				if((document.getElementById("var_flag_alterar_titulo").checked == true)&&(document.getElementById("var_dt_vcto_tit").value == "")) { strMSG += "\nData de Vencimento"; }
				if(strMSG != ""){
					alert("Verifique os Campos obrigatórios:" + strMSG);
				} else{
					strLocation = "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>";
					submeterForm();
				}
			}

			function cancelar() {
				document.location.href = "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>";
			}
			
			function aplicar() {
				var strMSG = "";
				if(
					(document.getElementById("var_new_prev_dtt_ini").value 	== "")||
					(document.getElementById("var_new_prev_dtt_fim").value 	== "")||
					(document.getElementById("var_hr_prev_ini").value 		== "")||
					(document.getElementById("var_hr_prev_fim").value 		== "")
				  ){ strMSG += "\n\nDADOS DA NOVA PREVISÃO:";}
				if(document.getElementById("var_new_prev_dtt_ini").value 	== "") { strMSG += "\nData de Previsão Inicial"; }
				if(document.getElementById("var_new_prev_dtt_fim").value 	== "") { strMSG += "\nData de Previsão Final"; }
				if(document.getElementById("var_hr_prev_ini").value 	 	== "") { strMSG += "\nHora de Previsão Inicial"; }
				if(document.getElementById("var_hr_prev_fim").value 	 	== "") { strMSG += "\nHora de Previsão Final"; }
				if(
					(document.getElementById("var_flag_alterar_titulo").checked == true)&&
					(document.getElementById("var_dt_vcto_tit").value == "")
				  ){ strMSG += "\n\nDATA DE VENCIMENTO:";}
				if((document.getElementById("var_flag_alterar_titulo").checked == true)&&(document.getElementById("var_dt_vcto_tit").value == "")) { strMSG += "\nData de Vencimento"; }
				if(strMSG != ""){
					alert("Verifique os Campos obrigatórios:" + strMSG);
				} else{
					strLocation = "../modulo_SdHomologacao/STreagendarhomologacao.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				}
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
			
			function selectDoc(){
				var strComboValue = document.getElementById("var_html_ressalva_declaracao").value;
				document.getElementById("var_html_texto").innerHTML = strComboValue;
			}	
			
			function showtable(){
				if(document.getElementById("var_flag_alterar_titulo").checked == true){
					document.getElementById("table_tit").style.display = 'block'; 
				}else{
					document.getElementById("table_tit").style.display = 'none';
				}
			}
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px;" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<!-- USO -->
<center>
<?php
	// TESTE PARA MENSAGEM DE VAZIO
	if((getValue($objRS,"cod_agenda") == "") || (getValue($objRS,"situacao") != "aberto")){
		if(getValue($objRS,"cod_agenda") == ""){
			mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("homologacao_sem_agenda",C_NONE),"data.php","aviso",1,"","");
		}else if(getValue($objRS,"situacao") != "aberto"){
			mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("homologacao_nao_aberta",C_NONE),"data.php","aviso",1,"","");
		}
	} else{
	?>
<?php athBeginFloatingBox("720","",getTText("reagendar_agenda",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="700" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
			<strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 80px 10px 80px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
			<form name="formstatic" id="formstatic" action="STreagendarhomologacaoexec.php" method="post">
				<input type="hidden" name="var_chavereg" value="<?php echo(getValue($objRS,"cod_agenda"));?>" />
				<input type="hidden" name="var_titulo" value="<?php echo(getValue($objRSF,"cod_conta_pagar_receber"));?>" />
				<input type="hidden" name="DEFAULT_LOCATION" value="" />
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_agenda",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_agenda",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_agenda"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"titulo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"descricao"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("prev_dtt_ini",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("prev_dtt_fim",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true));?></td>
				</tr>
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_reagendamento",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("nova_prev_dtt_ini",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="text" name="var_new_prev_dtt_ini" id="var_new_prev_dtt_ini" size="12" maxlength="10" value="<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),false));?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
						&nbsp;<input type="text" name="var_hr_prev_ini" id="var_hr_prev_ini" size="8" maxlength="5" value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true),11,5));?>" onkeyPress="FormataInputHoraMinuto(this,event);"/>
						&nbsp;<span class="comment_peq">(Formato dd/mm/aaaa HH:MM - 4 dígitos)</span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("nova_prev_dtt_fim",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="text" name="var_new_prev_dtt_fim" id="var_new_prev_dtt_fim" size="12" maxlength="10" value="<?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),false));?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
						&nbsp;<input type="text" name="var_hr_prev_fim" id="var_hr_prev_fim" size="8" maxlength="5" value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true),11,5));?>" onkeyPress="FormataInputHoraMinuto(this,event);"/>
						&nbsp;<span class="comment_peq">(Formato dd/mm/aaaa HH:MM - 4 dígitos)</span>
					</td>
				</tr>
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				<?php if(getValue($objRSF,"cod_conta_pagar_receber") != ""){?>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("alterar_vcto_titulo",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><input type="checkbox" name="var_flag_alterar_titulo" id="var_flag_alterar_titulo" value="TRUE" class="inputclean" onClick="showtable();" /></td>
					<td width="77%" align="left">Alterar Data de Vencimento do Título?</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td colspan="2">
						<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_tit" style="display:none;">
						<tr>
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("data_vcto",C_UCWORDS));?>:</strong></td>
							<td width="77%" align="left">&nbsp;
								<input type="text" name="var_dt_vcto_tit" id="var_dt_vcto_tit" size="12" maxlength="10" value="<?php echo(dDate(CFG_LANG,getValue($objRSF,"dt_vcto"),false));?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />&nbsp;<span class="comment_peq">(Formato dd/mm/aaaa)</span>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan="2" height="5">&nbsp;</td></tr>
				<?php }?>
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				<tr>
					<td colspan="2" align="right">
						<button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						<button onClick="aplicar();return false;"><?php echo(getTText("aplicar",C_UCWORDS));?></button>
					</td>
				</tr>
			</form>			
			</table>			
		</td>
	</tr>
</table>
<?php athEndFloatingBox();?>
	<?php }?>
</center>

</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>