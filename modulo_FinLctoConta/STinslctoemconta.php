<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn   = abreDBConn(CFG_DB);

$intCodDado = request("var_cod_conta");
$strOper 	= request("var_oper");

$strLabelEnt = ($strOper == "receita") ? "receber_de" : "pagar_para";

$intCodJob = getVarEntidade($objConn, "fin_cod_job");

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
var dbCCheck = false;

function submeterForm(prAcao) {
	if (!dbCCheck) {
		dbCCheck = true;
		document.formconf.DEFAULT_LOCATION.value = (prAcao == "ok") ? "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>" : "../modulo_FinLctoConta/STinslctoemconta.php?var_cod_conta=" + document.formconf.dbvar_num_cod_conta.value + "&var_oper=<?php echo($strOper); ?>";
	    document.formconf.submit();
		return true;
	} else {
		alert("DuploClick detectado! ATENÇÃO - não utilizar clique duplo.\n(O sistema tentará enviar o formulário apenas uma vez...)");
		return false; 
	}
}

function searchModulo(prType){
	/*if(prType == "pessoa"){
		combo         = "cad_pj";
		strModulo     = "CadPJ";
		strComponente = "dbvar_num_codigo";
	}*/
	if(prType == "pessoa"){
		var combo     = document.forms[0].dbvar_str_tipoô;
		// strModulo     = (combo.options[combo.selectedIndex].value == "cad_pf") ? "CadPF" : "CadPJ";
		strModulo     = combo.options[combo.selectedIndex].value;
		switch(strModulo){
			case "cad_pf" :
			strModulo = "CadPF";
			break;
			
			case "cad_pj" :
			strModulo = "CadPJ";
			break;
			
			case "cad_pj_fornec" :
			strModulo = "CadPJFornec";
			break;
		}
		strComponente = "dbvar_num_codigoô_000";
	}
	else if(prType == "centrocusto"){
		strModulo     = "FinCentroCusto";
		strComponente = "dbvar_num_cod_centro_custo";
	}
	else if(prType == "planoconta"){
		strModulo     = "FinPlanoConta";
		strComponente = "dbvar_num_cod_plano_conta";
	}

	AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + strComponente + "&var_formname=formconf","800", "600");
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("lcto_conta",C_TOUPPER) . " - " . getTText("insercao_" . $strOper,C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="../_database/athinserttodb.php" method="post">
		   <input type="hidden" name="DEFAULT_TABLE" value="fin_lcto_em_conta">
		   <input type="hidden" name="DEFAULT_DB" value="<?php echo(CFG_DB); ?>">
		   <input type="hidden" name="FIELD_PREFIX" value="dbvar_">
		   <input type="hidden" name="RECORD_KEY_NAME" value="cod_lcto_em_conta">
		   <input type="hidden" name="DEFAULT_LOCATION" value="">
		   <input type="hidden" name="dbvar_autodate_sys_dtt_ins" value="">
		   <input type="hidden" name="dbvar_str_sys_usr_ins" value="<?php echo(getsession(CFG_SYSTEM_NAME . "_id_usuario")); ?>">
		   <!--input type="hidden" name="dbvar_str_tipo" value="cad_pj" -->
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("operacao",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php $strColor = ($strOper == "receita") ? "#027C02" : "#FF0000"; ?>
								<span style="color:<?php echo($strColor); ?>; font-weight:bold;"> <?php echo(getTText($strOper,C_UCWORDS)); ?> </span>
								<input name="dbvar_str_operacao" id="dbvar_str_operacao" type="hidden" value="<?php echo($strOper); ?>">
							</td>
						</tr>	 	
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="dbvar_num_cod_conta" class="edtext" style="width:230px;">
									<option value="" <?php if ($intCodDado == "") echo("selected='selected'"); ?>></option>
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY nome ","cod_conta","nome",$intCodDado)); ?>
								</select>
							</td>
						</tr> 
						<tr> 
							<td align='right' valign="middle">*<b><?php echo(getTText($strLabelEnt,C_NONE)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:5px;" valign="middle"><input name='dbvar_num_codigoô' id='dbvar_num_codigoô_000' class='edtext' type='text' maxlength='10' value="" onKeyPress="Javascript:return validateNumKey(event);" style="width:40px;"></td>
										<td style="padding-right:3px;" valign="middle">
											<select name="dbvar_str_tipoô" id="dbvar_str_tipoô_000" class="edtext" size="1" style="width:185px;">
												<option value="cad_pf"><?php echo(getTText("pessoa_fisica", C_UCWORDS)); ?></option>
												<option value="cad_pj"><?php echo(getTText("pessoa_juridica", C_UCWORDS))?></option>
												<option value="cad_pj_fornec"><?php echo(getTText("fornecedor", C_UCWORDS))?></option>
											</select>
										</td>
										<td valign="middle">
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('pessoa');" class="inputclean">
										</td>
									</tr>
								</table>
							</td>
						</tr>	
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:3px;" valign="middle">
											
											<select name="dbvar_num_cod_centro_custo" class="edtext" style="width:230px;">
												<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY nome ","cod_centro_custo","nome","")); ?>
											</select>
										</td>
										<td valign="middle">
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('centrocusto');" class="inputclean">
										</td> 			
									</tr>
								</table>
							</td>
						</tr> 	
						<tr>
							<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr valign="middle">
										<td style="padding-right:3px;">
											<select name="dbvar_num_cod_plano_conta" class="edtext" style="width:307px;">
												<?php echo(montaCombo($objConn," SELECT cod_plano_conta, nome FROM fin_plano_conta ORDER BY nome ","cod_plano_conta","nome","")); ?>
											</select>	
										</td>
										<td>
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('planoconta');" class="inputclean">
										</td>
									</tr>
								</table>
							</td>
						</tr> 
						<tr> 
							<td align="right" valign="middle"><b><?php echo(getTText("job",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:3px;" valign="middle">
											<select name="dbvar_num_cod_job" class="edtext" style="width:230px;">
												<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",$intCodJob)); ?>
											</select>
										</td>
									</tr>
								</table>
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
							<td valign="middle"><input name='dbvar_date_dt_lcto' id='VAR_DT_LCTO' class='edtext' value='' type='text' maxlength='10' style='width:70px;' onKeyPress='Javascript:return validateNumKey(event);' onKeyUp='Javascript:FormataInputData(this,2);'><span class="texto_corpo_peq"></span></td>
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
								<button class="inputcleanActionOk" onClick="submeterForm('ok'); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button class="inputcleanActionCancelar" onClick="document.location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>'; return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								<!--button onClick="submeterForm('aplicar'); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button-->
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