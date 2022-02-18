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
	$intCodAgenda   = request("var_chavereg");	 // cod_agenda para o qual irá ser update da agenda
	$strFlagRedir   = request("var_flag_redir"); // flag para redirecionar o action depois para a agenda [VIEW_AGENDA]
	$dtEventoInicio = request("var_dt_ini");     // Data inicial do evento
	$dtEventoFim    = request("var_dt_fim");     // Data final do evento
	$strTitulo  	= request("var_titulo");	 // Título do Evento
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
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
			
			#img_drop_dt_ini{ cursor:pointer; display:none }
						
			#img_drop_dt_fim{ cursor:pointer; display:none }
			
			#lst_dt_ini{ width:250px;height:100px;overflow:scroll;display:none }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				//strLocation = "../modulo_Agenda/STdatascheduler.php";
				strLocation = "../modulo_Agenda/";
				submeterForm();
			}

			function cancelar() {
				//document.location.href = "../modulo_Agenda/STdatascheduler.php";
				document.location.href = "../modulo_Agenda/";
			}
			
			function aplicar() {
				// RESETA VALUE PARA NAO FAZER REDIRECT PARA AGENDA
				// document.formstatic.var_flag_redir.value = "";
				strLocation = "../modulo_Agenda/STinsevent.php?var_dt_ini=<?php echo($dtEventoInicio);?>&var_dt_fim=<?php echo($dtEventoFim);?>";
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
<?php athBeginFloatingBox("710","",getTText("insert_event",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STinseventexec.php" method="post">
<input type="hidden" name="DEFAULT_LOCATION" value="" />
<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong>
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
					<td width="23%" align="right"><strong><?php echo(getTText("criador_responsavel",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong><?php echo(getTText("data_criacao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<?php echo(dDate(CFG_LANG,now(),true));?>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><input type="text" name="var_titulo" id="var_titulo" size="60" maxlength="250" value="<?php echo($strTitulo);?>" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><textarea name="var_descricao" rows="6" cols="60"></textarea></td>
				</tr>

				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("citados",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_grp_user" style="width:120px;"/>
							<!-- option value="" selected="selected"></option -->
							<?php 
							$strDefaultGrp = ""; //?????
							echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario WHERE grp_user <> 'SU'",'grp_user','grp_user',$strDefaultGrp,'admin')); 
							?>
						</select>&nbsp;
						<span class="comment_med"><?php echo(getTText("descricao_citados",C_NONE));?></span>
					</td>
				</tr>
				
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_categoria" style="width:120px;">
							<option value="REUNIAO"    	><?php echo(getTText("reuniao",C_TOUPPER));?></option>
							<option value="ENCONTRO"   	><?php echo(getTText("encontro",C_TOUPPER));?></option>
							<option value="CONFERENCIA"	><?php echo(getTText("conferencia",C_TOUPPER));?></option>
							<option value="ALMOCO"     	><?php echo(getTText("almoco",C_TOUPPER));?></option>
							<option value="HOMOLOGACAO"	><?php echo(getTText("homologacao",C_TOUPPER));?></option>
							<option value="JANTAR"     	><?php echo(getTText("jantar",C_TOUPPER));?></option>
							<option value="VISITA"		><?php echo(getTText("visita",C_TOUPPER));?></option>
							<option value="VIAGEM"		><?php echo(getTText("viagem",C_TOUPPER));?></option>
							<option value="ANIVERSARIO"	><?php echo(getTText("aniversario",C_TOUPPER));?></option>
							<option value="COMEMORACAO"	><?php echo(getTText("comemoracao",C_TOUPPER));?></option>
							<option value="FERIADO"		><?php echo(getTText("feriado",C_TOUPPER));?></option>
							<option value="OUTROS"		><?php echo(getTText("outros",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_prioridade" style="width:120px;">
							<option value="BAIXA"><?php echo(getTText("baixa",C_TOUPPER));?></option>
							<option value="NORMAL"><?php echo(getTText("normal",C_TOUPPER));?></option>
							<option value="MEDIA"><?php echo(getTText("media",C_TOUPPER));?></option>
							<option value="ALTA"><?php echo(getTText("alta",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("previsao_ini",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="text" name="var_dt_prev_ini" size="12" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" value="<?php echo(dDate(CFG_LANG,$dtEventoInicio,false));?>" />
						&nbsp;de&nbsp;
						<input type="text" name="var_hr_prev_ini" size="8" maxlength="5" value="07:00" onkeyPress="FormataInputHoraMinuto(this,event);" style="margin-bottom:0px;"/>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("previsao_fim",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="text" name="var_dt_prev_fim" size="12" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" value="<?php echo(dDate(CFG_LANG,$dtEventoFim,false));?>" />
						&nbsp;até&nbsp;
						<input type="text" name="var_hr_prev_fim" size="8" maxlength="5" value="20:00" onkeyPress="FormataInputHoraMinuto(this,event);"  style="margin-bottom:0px;"/>
					</td>
				</tr>
				<!-- DADOS AGENDA -->
				
				<!-- CASO SEJA NECESSÁRIO FUTURAMENTE SER INCLUÍDO A EDIÇÃO DE CITADOS E    --> 
				<!-- AS RESPOSTAS PARA ESTA AGENDA ENTAO COPIAR DA PÁGINA STDELETEEVENT.PHP -->
				
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td>
				</tr>
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td width="%" align="right"><button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button></td>
                <td width="10%" align="left"><button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button></td>
                <td width="27%" align="left" style="padding-right:25px;"><button onClick="aplicar();return false;"><?php echo(getTText("aplicar",C_NONE));?></button></td>
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