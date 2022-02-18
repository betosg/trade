<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_cod_conta");

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(prAcao){
	document.formconf.DEFAULT_LOCATION.value = (prAcao == "ok") ? "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>" : "../modulo_FinConta/STinslctotransf.php?var_cod_conta=" + document.formconf.dbvar_num_cod_conta_origô.value;
	document.formconf.submit();
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("lcto_transf",C_TOUPPER) . " (" . getTText("insercao",C_UCWORDS) . ")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="../_database/athinserttodb.php" method="post">
		   <input type="hidden" name="DEFAULT_TABLE" value="fin_lcto_transf">
		   <input type="hidden" name="DEFAULT_DB" value="<?php echo(CFG_DB); ?>">
		   <input type="hidden" name="FIELD_PREFIX" value="dbvar_">
		   <input type="hidden" name="RECORD_KEY_NAME" value="cod_lcto_transf">
		   <input type="hidden" name="DEFAULT_LOCATION" value="">
		   <input type="hidden" name="dbvar_autodate_sys_dtt_ins" value="">
		   <input type="hidden" name="dbvar_str_sys_usr_ins" value="<?php echo(getsession(CFG_SYSTEM_NAME . "_id_usuario")); ?>">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr> 	
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta_origem",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="dbvar_num_cod_conta_origô" class="edtext" style="width:230px;">
									<option><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY nome ","cod_conta","nome",$intCodDado)); ?>
								</select>
							</td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta_dest",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="dbvar_num_cod_conta_destô" class="edtext" style="width:230px;">
									<option><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY nome ","cod_conta","nome","")); ?>
								</select>
							</td>
						</tr> 
					    <tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("numero",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><input name="dbvar_str_num_lcto" type="text" class="edtext" style="width:125px;" maxlength="50"></td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("valor",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><input name="dbvar_moeda_vlr_lcto" type="text" class="edtext" style="width:105px;" maxlength="15" onKeyPress="return(validateFloatKeyNew(this,event));"></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("data",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><input name='dbvar_date_dt_lcto' id='dbvar_date_dt_lcto' class='edtext' value='' type='text' maxlength='10' style='width:70px;' onKeyPress='Javascript:return validateNumKey(event);' onKeyUp='Javascript:FormataInputData(this);'><span class="texto_corpo_peq"></span></td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("historico",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><input name="dbvar_str_historico" type="text" class="edtext" maxlength="50" style="width:357px;"></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="top"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><textarea name="dbvar_str_obs" class="edtext" rows="7" style="width:357px;"></textarea></td>
						</tr>
						<tr>
							<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
						</tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm('ok');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								<button onClick="submeterForm('aplicar');"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
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