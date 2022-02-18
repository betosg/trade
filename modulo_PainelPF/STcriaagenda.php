<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strNomeUsuario = request("var_chavereg"); // Nome de usuário
$strOperacao	= request("var_oper"); // Operação a ser realizada
$strExec 		= request("var_exec"); // Executor externo (fora do kernel)
$strPopulate 	= request("var_populate"); // Flag para necessidade de popular o session ou não
$strAcao 		= request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
				location.href="STpainelPJ.php";
				//window.history.back();
			}
			
			function copyValor(prObject, prValue){
				var objID, auxValue;
				objID    = prObject;
				auxValue = prValue;
				document.getElementById(objID).value = auxValue;
				//copyValor(document.dbvar_str_id_ult_executor, this.value);
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("cria_agenda",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="../_database/athinserttodb.php" method="post">
		<input type="hidden" name="DEFAULT_TABLE" value="ag_agenda" />
		<input type="hidden" name="RECORD_KEY_NAME" value="cod_agenda" />
		<input type="hidden" name="FIELD_PREFIX" value="dbvar_" />
		<input type="hidden" name="DEFAULT_LOCATION" value="../modulo_PainelPJ/STcriaagenda.php?var_chavereg=<?php echo($strNomeUsuario);?>" />
		<input type="hidden" name="dbvar_str_id_responsavel" value="<?php echo($strNomeUsuario);?>" />
		<input type="hidden" name="dbvar_str_id_citados" value="<?php echo($strNomeUsuario);?>" />
		<input type="hidden" name="dbvar_autodate_sys_dtt_ins" value="" />
		<input type="hidden" name="dbvar_str_sys_usr_ins" value="<?php echo($strNomeUsuario);?>" />
		<input type="hidden" name="dbvar_str_id_ult_executor" id="dbvar_str_id_ult_executor" value="" />
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("confirme_dados",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 70px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("titulo_agenda",C_NONE)); ?></strong>						</td>
					  <td width="70%"><input type="text" name="dbvar_str_tituloô" id="dbvar_str_tituloô" maxlength="254" value="" size="60"></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("situacao",C_NONE)); ?></strong>
						</td>
						<td>
							<select name="dbvar_str_situacaoô" id="dbvar_str_situacaoô" style="width:120px;">
								<option value="" selected="selected"></option>
								<option value="status_img_aberto">ABERTO</option>
								<option value="status_img_executando">EXECUTANDO</option>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("ag_categoria",C_NONE)); ?></strong>
						</td>
						<td>
							<select name="dbvar_num_cod_categoriaô" id="dbvar_num_cod_categoriaô" style="width:120px;">
								<option value="" selected="selected"></option>
							<?php echo montaCombo($objConn,"SELECT cod_categoria, nome FROM ag_categoria WHERE nome ILIKE '%geral%'","cod_categoria","nome","");?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("prioridade",C_NONE)); ?></strong>						</td>
						<td>
							<select name="dbvar_str_prioridadeô" id="dbvar_str_prioridadeô" style="width:120px;">
								<option value="" selected="selected"></option>
								<option value="status_img_baixa"><?php echo(getTText("baixa",C_TOUPPER)) ?></option>
								<option value="status_img_baixa"><?php echo(getTText("normal",C_TOUPPER)) ?></option>
								<option value="status_img_media"><?php echo(getTText("media",C_TOUPPER)) ?></option>
								<option value="status_img_alta"><?php echo(getTText("alta",C_TOUPPER)) ?></option>
							</select>						
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("responsavel",C_NONE)); ?></strong>						</td>
						<td><label for="dbvar_str_id_responsavel"><em><?php echo($strNomeUsuario);?></em></label></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="30%"> 
							<strong><?php echo(getTText("citado_para",C_NONE)); ?></strong>
						</td>
						<td>
							<select name="dbvar_str_id_citados" id="dbvar_str_id_citados" style="width:120px" onblur="javascript:copyValor('dbvar_str_id_ult_executor', this.value);">
								<option value="<?php echo($strNomeUsuario);?>" selected="selected"><?php echo($strNomeUsuario);?></option>
								<?php echo montaCombo($objConn,"SELECT id_usuario FROM sys_usuario WHERE grp_user = 'ADMIN' AND dtt_inativo IS NULL","id_usuario","id_usuario",""); ?>
							</select>
							
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("dtt_previsao_ini",C_NONE)); ?></strong>
						</td>
						<td><input type="text" name="dbvar_datetime_prev_dtt_iniô" id="dbvar_datetime_dtt_prev_iniô" size="15" maxlength="20" onKeyUp="FormataInputData(this)" onKeyPress="return validateNumKey(event);"/>
						<div class="comment_peq" style="display:inline;"><?php echo(getTText("formato_dtt_ini",C_NONE));?></div>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("prev_horas",C_NONE)); ?></strong>
						</td>
						<td><input type="text" size="8" maxlength="5" name="dbvar_str_prev_horas" id="dbvar_str_prev_horas" /></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("dtt_realizado",C_NONE)); ?></strong>
						</td>
						<td><input type="text" name="dbvar_datetime_dtt_realizado" id="dbvar_datetime_dtt_realizado" size="20" onKeyUp="FormataInputData(this)" onKeyPress="return validateNumKey(event);"/>
						<div class="comment_peq" style="display:inline;"><?php echo(getTText("formato_dtt_realizado",C_NONE))?></div>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" valign="top">
							<strong><?php echo(getTText("ag_descricao",C_NONE)); ?></strong>
						</td>
						<td><textarea name="dbvar_str_descricao" id="dbvar_str_descricao" rows="5" cols="60"></textarea></td>
					</tr>
					<tr><td class="destaque_med" colspan="5" style="padding-top:15px;"><?php echo(getTText("campos_obrig",C_NONE));?></td></tr>
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td width="1%" align="right" style="padding:10px 10px 10px 10px;" nowrap>
									<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
									<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
									<button onClick="ok(); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
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
	<?php athEndFloatingBox(); ?>
	</td>
</tr>
</table>
</body>
</html>
<?php 
	$objConn = NULL; 
?>