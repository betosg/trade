<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/STathutils.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$strOper = request("var_oper");

$strROTULO = getTText($strOper,C_NONE);
$strCOR = ($strOper == "receber_de") ? "#027C02" : "#FF0000";

$intCodCFGBoleto = getVarEntidade($objConn, "cod_cfg_boleto_padrao");
$intCodJob = getVarEntidade($objConn, "fin_cod_job");
$intCodCentroCusto = getVarEntidade($objConn, "fin_cod_centro_custo_default");
$intCodPlanoConta = getVarEntidade($objConn, "fin_cod_plano_conta_default");

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(prAcao){
	document.formconf.var_button_action.value = prAcao;
	document.formconf.submit();
}

function searchModulo(prType){
	if(prType == "pessoa"){
		var combo     = document.forms[0].var_tipo;
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
		strComponente = "var_codigo";
	}
	else if(prType == "centrocusto"){
		strModulo     = "FinCentroCusto";
		strComponente = "var_cod_centro_custo";
	}
	else if(prType == "planoconta"){
		strModulo     = "FinPlanoConta";
		strComponente = "var_cod_plano_conta";
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
	<?php athBeginFloatingBox("600","none",getTText(($strOper == "receber_de") ? "conta_receber" : "conta_pagar",C_TOUPPER) . " - " . getTText("insercao",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="STinspagarreceberexec.php" method="post">
		   <input type="hidden" name="var_button_action" value="">
		   <input type="hidden" name="var_pagar_receber" value="<?php echo(($strOper == "pagar_para") ? "true" : "false"); ?>">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_conta" class="edtext" style="width:230px;">
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY nome ","cod_conta","nome","")); ?>
								</select>
							</td>
						</tr> 
						<tr bgcolor="#FAFAFA"> 
							<td align="right" style="color:<?php echo($strCOR); ?>; vertical-align:text-top; padding-top:8px;">*<b><?php echo($strROTULO); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:5px;" valign="middle"><input name='var_codigo' id='var_codigo' class='edtext' type='text' maxlength='10' value="" style="width:40px;"
																						onKeyPress="Javascript:return validateNumKey(event);" 
																						onblur="javascript:document.getElementById('div_entidade_nome').innerHTML = ''; 
																										   buscanomeentidade('div_entidade_nome','formconf','var_codigo','var_tipo');"></td>
										<td style="padding-right:3px;" valign="middle">
											<select name="var_tipo" class="edtext" size="1" style="width:185px;" onBlur="javascript:document.getElementById('div_entidade_nome').innerHTML = ''; 
																																    buscanomeentidade('div_entidade_nome','formconf','var_codigo','var_tipo');">
												<option value="cad_pf"><?php echo(getTText("pessoa_fisica", C_UCWORDS)); ?></option>
												<option value="cad_pj"><?php echo(getTText("pessoa_juridica", C_UCWORDS)); ?></option>
												<option value="cad_pj_fornec"><?php echo(getTText("fornecedor", C_UCWORDS)); ?></option>
											</select>
										</td>
										<td valign="middle">
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" class="inputclean"
											onClick="javascript:document.getElementById('var_codigo').focus(); 
													 			searchModulo('pessoa'); return false; 
													 			release();">
										</td>
									</tr>
									<tr>
										<td colspan="3"><span id="div_entidade_nome"></span></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:3px;" valign="middle">
											<select name="var_cod_centro_custo" class="edtext" style="width:230px;">
												<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY nome ","cod_centro_custo","nome",$intCodCentroCusto)); ?>
											</select>
										</td>
										<td valign="middle">
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('centrocusto')" class="inputclean">
										</td> 			
									</tr>
								</table>
							</td>
						</tr> 	
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr valign="middle">
										<td style="padding-right:3px;">
											<select name="var_cod_plano_conta" class="edtext" style="width:307px;">
												<?php //echo(montaCombo($objConn," SELECT cod_plano_conta, coalesce(cod_reduzido,NULL,'') || ' ' || coalesce(nome,NULL,'') AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",$intCodPlanoConta)); ?>
                                   				<?php echo(montaCombo($objConn," SELECT cod_plano_conta, coalesce(nome,NULL,'') || ' (' || coalesce(cod_reduzido,NULL,'') || ')' AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY rotulo,cod_reduzido ","cod_plano_conta","rotulo",$intCodPlanoConta)); ?>
											</select>	
										</td>
										<td>
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('planoconta')" class="inputclean">
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
											<select name="var_cod_job" class="edtext" style="width:230px;">
												<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",$intCodJob)); ?>
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr> 	
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("dados",C_UCWORDS)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right">*<b><?php echo(getTText("vlr_conta",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><input name="var_vlr_conta" type="text" class="edtext" style="width:80px;" maxlength="15" onKeyPress="return(validateFloatKeyNew(this,event));" value=""></td>
						</tr>
						<tr>
							<td align="right">*<b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<select name="var_tipo_documento" class="edtext"  style="width:200px;">
									<option value="BOLETO"><?php echo(getTText("boleto",C_TOUPPER)); ?></option>
									<option value="BOLETO_SINDICAL"><?php echo(getTText("boleto_sindical",C_TOUPPER)); ?></option>
									<option value="BOLETO_ASSISTENCIAL"><?php echo(getTText("boleto_assistencial",C_TOUPPER)); ?></option>
									<option value="EXTRATO"><?php echo(getTText("extrato",C_TOUPPER)); ?></option>
									<option value="FATURA"><?php echo(getTText("fatura",C_TOUPPER)); ?></option>
									<option value="HOLERITE"><?php echo(getTText("holerite",C_TOUPPER)); ?></option>
									<option value="NOTA_FISCAL"><?php echo(getTText("nota_fiscal",C_TOUPPER)); ?></option>
									<option value="TARIFA"><?php echo(getTText("tarifa",C_TOUPPER)); ?></option>
									<option value="RECIBO"><?php echo(getTText("recibo",C_TOUPPER)); ?></option>
                                    <option value="TED"><?php echo(getTText("ted",C_TOUPPER)); ?></option>
									<option value="CARTAO_VISA"><?php echo(getTText("cartao_visa",C_TOUPPER)); ?></option>
									<option value="CARTAO_MASTERCARD"><?php echo(getTText("cartao_mastercard",C_TOUPPER)); ?></option>
									<option value="CARTAO_AMEX"><?php echo(getTText("cartao_amex",C_TOUPPER)); ?></option>
								</select>
							</td>
						</tr>
						<!--
						<tr bgcolor="#FAFAFA">
							<td align="right">*<b><?php //echo(getTText("num_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><input name="var_num_documento" class="edtext" type="text" style="width:115;" value=""></td>
						</tr>
						-->
						<tr> 
							<td align="right">*<b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<table border="0px" cellpadding="0px" cellspacing="0px" width="100%">
									<tr>
										<td width="90px"><input name='var_dt_emissao' id='var_dt_emissao' class='edtext' value='<?php echo(date('d/m/Y'));?>' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"><span class="texto_corpo_peq"></span></td>
										<td width="120px" align="right">*<b><?php echo(getTText("dt_vcto",C_UCWORDS)); ?></b>:&nbsp;</td>
										<td align="left"><input name='var_dt_vcto' id='var_dt_vcto' class='edtext' value='' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"><span class="texto_corpo_peq"></span></td>
									</tr>
								</table>		
							</td>					
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right">*<b><?php echo(getTText("historico",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><input name="var_historico" type="text" class="edtext" maxlength="250" style="width:307px;"></td>
						</tr>
						<!--tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("peridiocidade",C_UCWORDS)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="top"><b><?php echo(getTText("parcelas",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td>
								<div style="padding-left:13px;">
									<?php echo(getTText("avisoins_parte1",C_NONE)); ?>
									<span style="padding-right:7px; padding-left:12px;">
										<input name="var_parcelas" class="edtext" value="1" type="text" maxlength="3" style="width:25px; text-align:center;" onFocus="this.value='';" onKeyPress="Javascript:return validateNumKey(event);">
									</span>
									<?php echo(getTText("avisoins_parte2",C_NONE)); ?>
								</div>
								<div style="padding-top:5px;">
									<?php echo(getTText("avisoins_parte3",C_NONE)); ?> &nbsp;
									<select name="var_frequencia" class="edtext" size="1">
										<option value="UMA_VEZ" selected><?php echo(getTText("uma_vez_apenas",C_UCWORDS)); ?></option>
										<option value="DIARIA"><?php echo(getTText("diaria",C_UCWORDS)); ?></option>
										<option value="SEMANAL"><?php echo(getTText("semanal",C_UCWORDS)); ?></option>
										<option value="QUINZENAL"><?php echo(getTText("quinzenal",C_UCWORDS)); ?></option>
										<option value="MENSAL"><?php echo(getTText("mensal",C_UCWORDS)); ?></option>
										<option value="BIMESTRAL"><?php echo(getTText("bimestral",C_UCWORDS)); ?></option>
										<option value="TRIMESTRAL"><?php echo(getTText("trimestral",C_UCWORDS)); ?></option>
										<option value="SEMESTRAL"><?php echo(getTText("semestral",C_UCWORDS)); ?></option>
										<option value="ANUAL"><?php echo(getTText("anual",C_UCWORDS)); ?></option>
									</select>
								</div>
							</td>
						</tr-->
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr> 
							<td align="right" valign="top"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><textarea name="var_obs" class="edtext" rows="7" style="width:357px;"></textarea></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle"><b><?php echo(getTText("cod_cfg_boleto",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_cfg_boleto" class="edtext" style="width:160px;">
									<option value=""></option>
									<?php echo(montaCombo($objConn," SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY ordem, descricao ","cod_cfg_boleto","descricao",$intCodCFGBoleto,"")); ?>
								</select>
							</td>
						</tr> 
						<tr bgcolor="#FFFFFF">
							<td width="100" align="right"><b><?php echo(getTText("ano_vcto",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><input name='var_ano_vcto' id='var_ano_vcto' class='edtext' value="" type='text' maxlength='4' style='width:50px;' onKeyPress="Javascript:return validateNumKey(event);"></td>
						</tr>
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("peridiocidade",C_UCWORDS)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="top"><b><?php echo(getTText("parcelas",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td>
								<div style="padding-left:1px;"><?php echo(getTText("avisoins_parte1",C_NONE)); ?><span style="padding-right:3px; padding-left:3px;"><input name="var_parcelas" class="edtext" value="" type="text" maxlength="3" style="width:25px; text-align:center;" onFocus="this.value='';" onKeyPress="Javascript:return validateNumKey(event);">
								</span><?php 
								if ($strOper == "pagar_para") echo(getTText("avisoins_parte2pagar",C_NONE)); 
								if ($strOper == "receber_de") echo(getTText("avisoins_parte2receber",C_NONE)); 
								?>
								</div>
								<div style="padding-top:5px;">
									<?php echo(getTText("avisoins_parte3",C_NONE)); ?>
									<select name="var_frequencia" class="edtext" size="1">
										<option value="" selected="selected"></option>
										<option value="DIARIA"><?php echo(getTText("diaria",C_UCWORDS)); ?></option>
										<option value="SEMANAL"><?php echo(getTText("semanal",C_UCWORDS)); ?></option>
										<option value="QUINZENAL"><?php echo(getTText("quinzenal",C_UCWORDS)); ?></option>
										<option value="MENSAL"><?php echo(getTText("mensal",C_UCWORDS)); ?></option>
										<option value="BIMESTRAL"><?php echo(getTText("bimestral",C_UCWORDS)); ?></option>
										<option value="TRIMESTRAL"><?php echo(getTText("trimestral",C_UCWORDS)); ?></option>
										<option value="SEMESTRAL"><?php echo(getTText("semestral",C_UCWORDS)); ?></option>
										<option value="ANUAL"><?php echo(getTText("anual",C_UCWORDS)); ?></option>
									</select>
								</div>
							</td>
						</tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr>
							<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
						</tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm('ok');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>';return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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