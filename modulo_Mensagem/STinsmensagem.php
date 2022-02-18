<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodAgenda	= request("var_chavereg");		// cod_agenda para o qual irá ser update da agenda
	$strPopulate 	= "yes";
	
	// if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
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
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				strLocation = "";
				submeterForm();
			}

			function cancelar() {
				window.close();
			}

			function aplicar() {
				strLocation = "../modulo_PainelAdmin/STinsmensagem.php";
				submeterForm();
			}

			function submeterForm() {
				document.forminsert.DEFAULT_LOCATION.value = strLocation;
				document.forminsert.submit();
			}
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<!-- body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;" -->

<!-- USO -->
<center>
<?php athBeginFloatingBox("520","",getTText("ins_mensagem",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="315" width="500" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_dados",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
				<!-- DADOS NOVA MENSAGEM -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_resposta",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<form name="forminsert" action="STinsmensagemexec.php" method="post">
				<input type="hidden" name="DBVAR_STR_SYS_USR_INS" 
				 value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
				<input type="hidden" name="DBVAR_STR_ID_USUARIO_REMETENTE" 
				 value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>"/>
				<input type="hidden" name="DBVAR_DATE_DTT_ENVIO" value="<?php echo(dDate(CFG_LANG,now(),true));?>" />
				<input type="hidden" name="DEFAULT_LOCATION" 	 value="" />
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right"><strong>*<?php echo(getTText("hoje",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(dDate(CFG_LANG,now(),true));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong>*<?php echo(getTText("de",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%" align="left"><?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("para",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="DBVAR_STR_ID_USUARIO_DESTINATARIO" style="width:150px;">
							<option value=""></option>
							<?php echo(montaCombo($objConn,"SELECT id_usuario FROM sys_usuario 
							 					   WHERE dtt_inativo IS NULL OR oculto = false",
												   "id_usuario","id_usuario",""));?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("assunto",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><input type="text" name="DBVAR_STR_ASSUNTO" maxlength="250" size="60" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top">
						<strong>*<?php echo(getTText("sua_mensagem",C_UCWORDS));?>:</strong>
					</td>
					<td width="77%" align="left"><textarea name="DBVAR_STR_MENSAGEM" cols="60" rows="8"></textarea></td>
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