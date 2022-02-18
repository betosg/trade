<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/STathutils.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_chavereg");

$intCodConta = getVarEntidade($objConn, "nfentrada_cod_conta_padrao");
$intCodPlanoConta = ""; //getVarEntidade($objConn, "nfentrada_cod_plano_conta_padrao");
$intCodCentroCusto = getVarEntidade($objConn, "nfentrada_cod_centro_custo_padrao");
$intCodCFGBoleto = getVarEntidade($objConn, "nfentrada_cod_cfg_boleto_padrao");
$intCodJob = ""; //getVarEntidade($objConn, "nfentrada_cod_job");

try {
	$strSQL = " SELECT t1.razao_social AS razao_nota, t1.vlr_total_nota, t1.titulo_gerado
	                 , t2.cod_pj_fornec, t2.razao_social AS razao_fornec
				FROM fin_nf_entrada t1, cad_pj_fornec t2
				WHERE t1.cod_pj_fornec = t2.cod_pj_fornec 
				AND t1.cod_nf_entrada = ".$intCodDado;
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$intCOD_PJ_FORNEC = getValue($objRS,"cod_pj_fornec");
$strRAZAO_NOTA = getValue($objRS,"razao_nota");
$strRAZAO_FORNEC = getValue($objRS,"razao_fornec");
$dblVLR_TOTAL_NOTA = getValue($objRS,"vlr_total_nota");
$strTITULO_GERADO = (getValue($objRS,"titulo_gerado") == true) ? "T" : "";

$objResult->closeCursor();

$dblVLR_TOTAL_NOTA = number_format((double) $dblVLR_TOTAL_NOTA, 2);
$dblVLR_TOTAL_NOTA = str_replace(",", "", $dblVLR_TOTAL_NOTA);
$dblVLR_TOTAL_NOTA = str_replace(".", ",", $dblVLR_TOTAL_NOTA);

if ($strTITULO_GERADO == "T") {
	mensagem("err_sql_titulo","err_sql_desc",getTText("msg_titulo_ja_gerado",C_NONE),"","aviso",1);
	die();
}

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

function searchModulo(prType, prComponente) {
	strModulo = "";
	if(prType == "centrocusto") strModulo = "FinCentroCusto";
	if(prType == "planoconta") strModulo = "FinPlanoConta";
	
	if (strModulo != "")
		AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + prComponente + "&var_formname=formconf","800","600");
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("conta_pagar",C_TOUPPER) . " - " . getTText("insercao",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="STinscontapagarnfentradaexec.php" method="post">
		  <input type="hidden" name="var_cod_nf_entrada" value="<?php echo $intCodDado; ?>">
		  <input type="hidden" name="var_cod_pj_fornec" value="<?php echo $intCOD_PJ_FORNEC; ?>">
		  <input type="hidden" name="var_obs" value="">
		  <input type="hidden" name="var_button_action" value="">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr bgcolor="#FFFFFF"> 
							<td align="right" valign="middle"><b><?php echo(getTText("codigo_nf_entrada",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo $intCodDado; ?></td>
						</tr> 
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle"><b><?php echo(getTText("razao_social",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo $strRAZAO_NOTA; ?></td>
						</tr> 
						<tr bgcolor="#FFFFFF"> 
							<td align="right" valign="middle"><b><?php echo(getTText("fornecedor",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo($intCOD_PJ_FORNEC." - ".$strRAZAO_FORNEC); ?></td>
						</tr> 
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle"><b><?php echo(getTText("vlr_total",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo $dblVLR_TOTAL_NOTA; ?></td>
						</tr> 
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("dados_titulo",C_NONE)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right">*<b><?php echo(getTText("vlr_conta",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><input name="var_vlr_conta" type="text" class="edtext" style="width:80px;" maxlength="15" onKeyPress="return(validateFloatKeyNew(this,event));" value="<?php echo $dblVLR_TOTAL_NOTA; ?>"></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right">*<b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td valign="middle">
								<input name="var_dt_emissao" id="var_dt_emissao" class="edtext" value="<?php echo(date('d/m/Y'));?>" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);">
							</td>					
						</tr>
						<tr> 
							<td align="right">*<b><?php echo(getTText("historico",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td valign="middle">
								<input name="var_historico" id="var_historico" class="edtext" value="" type="text" maxlength="250" style="width:280px;">
							</td>					
						</tr>
						<tr bgcolor="#FFFFFF"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_conta" class="edtext" style="width:200px;">
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY nome ","cod_conta","nome",$intCodConta)); ?>
								</select>
							</td>
						</tr> 
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr valign="middle">
										<td style="padding-right:3px;">
											<select name="var_cod_plano_conta" class="edtext" style="width:280px;">
												<?php echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",$intCodPlanoConta)); ?>
											</select>	
										</td>
										<td><input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('planoconta','var_cod_plano_conta')" class="inputclean"></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:3px;" valign="middle">
											<select name="var_cod_centro_custo" class="edtext" style="width:280px;">
												<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY nome ","cod_centro_custo","nome",$intCodCentroCusto)); ?>
											</select>
										</td>
										<td valign="middle">
											<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('centrocusto','var_cod_centro_custo')" class="inputclean">
										</td> 			
									</tr>
								</table>
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="middle"><b><?php echo(getTText("job",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<select name="var_cod_job" class="edtext" style="width:200px;">
									<option value=""></option>
									<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",$intCodJob)); ?>
								</select>	
							</td>
						</tr>
						<tr bgcolor="#FFFFFF"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("cod_cfg_boleto",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_cfg_boleto" class="edtext" style="width:200px;">
									<?php echo(montaCombo($objConn," SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY ordem, descricao ","cod_cfg_boleto","descricao",$intCodCFGBoleto,"")); ?>
								</select>
							</td>
						</tr> 
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("opcoes_para_varios_titulos",C_NONE)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td align="right"><b><?php echo(getTText("apenas_um_titulo",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
							<table width="95%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="1%"><input type="radio" name="var_varios_titulos" id="var_varios_titulos_opcao_a" value="A" checked="checked" style="border:none;"></td>
								<td width="1%" nowrap="nowrap"><?php echo(getTText("dt_vcto",C_UCWORDS)); ?>:</td>
								<td width="98%"><input name="var_dt_vcto" id="var_dt_vcto" class="edtext" value="" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><b><?php echo(getTText("varios_titulos",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
							<table width="95%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="1%"><input type="radio" name="var_varios_titulos" id="var_varios_titulos_opcao_b" value="B" style="border:none;"></td>
								<td width="1%" nowrap="nowrap"><?php echo(getTText("dt_prim_vcto",C_UCWORDS)); ?>:</td>
								<td width="98%"><input name="var_dt_prim_vcto" id="var_dt_prim_vcto" class="edtext" value="" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"></td>
							</tr>
							<tr>
								<td width="1%"></td>
								<td width="1%" nowrap="nowrap"><?php echo(getTText("parcelas",C_UCWORDS)); ?>:</td>
								<td width="98%"><input name="var_parcelas" class="edtext" value="2" type="text" maxlength="3" style="width:25px; text-align:center;" onFocus="this.value='';" onKeyPress="Javascript:return validateNumKey(event);"></td>
							</tr>
							<tr>
								<td width="1%"></td>
								<td width="1%" nowrap="nowrap"><?php echo(getTText("frequencia",C_UCWORDS)); ?>:</td>
								<td width="98%">
									<select name="var_frequencia" class="edtext" size="1">
										<option value="DIARIA"><?php echo(getTText("diaria",C_UCWORDS)); ?></option>
										<option value="SEMANAL"><?php echo(getTText("semanal",C_UCWORDS)); ?></option>
										<option value="QUINZENAL"><?php echo(getTText("quinzenal",C_UCWORDS)); ?></option>
										<option value="MENSAL" selected="selected"><?php echo(getTText("mensal",C_UCWORDS)); ?></option>
										<option value="BIMESTRAL"><?php echo(getTText("bimestral",C_UCWORDS)); ?></option>
										<option value="TRIMESTRAL"><?php echo(getTText("trimestral",C_UCWORDS)); ?></option>
										<option value="SEMESTRAL"><?php echo(getTText("semestral",C_UCWORDS)); ?></option>
										<option value="ANUAL"><?php echo(getTText("anual",C_UCWORDS)); ?></option>
									</select>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><b><?php echo(getTText("varios_titulos",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
							<table width="95%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="1%"><input type="radio" name="var_varios_titulos" id="var_varios_titulos_opcao_c" value="C" style="border:none;"></td>
								<td width="1%" nowrap="nowrap"><?php echo(getTText("dt_base_vcto",C_NONE)); ?>:</td>
								<td width="98%"><input name="var_dt_base_vcto" id="var_dt_base_vcto" class="edtext" value="" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"></td>
							</tr>
							<tr>
								<td width="1%"></td>
								<td width="1%" nowrap="nowrap" valign="top"><?php echo(getTText("prz_vctos",C_NONE)); ?>:</td>
								<td width="98%"><input type="text" style="width:120px;" name="var_prz_vctos" id="var_prz_vctos" value="">&nbsp;<br><span class="comment_med"><?php echo(getTText("msg_prz_vctos",C_NONE)); ?></span></td>
							</tr>
							<tr>
								<td width="1%"></td>
								<td width="1%" nowrap="nowrap" valign="top"><?php echo(getTText("vlr_vctos",C_NONE)); ?>:</td>
								<td width="98%"><input type="text" style="width:220px;" name="var_vlr_vctos" id="var_vlr_vctos" value="">&nbsp;<br><span class="comment_med"><?php echo(getTText("msg_vlr_vctos",C_NONE)); ?></span></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("opcoes_quitacao",C_UCWORDS)); ?></b></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="top"><b><?php echo(getTText("criar_titulos",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="1%"><input type="radio" name="var_opcoes_quitacao" id="var_opcoes_quitacao_aberto" value="A" checked="checked" style="border:none;"></td>
									<td width="99%" align="left"><?php echo(getTText("em_aberto",C_UCWORDS)); ?></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="top"><b><?php echo(getTText("criar_titulos",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td>
							<table width="95%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="1%"><input type="radio" name="var_opcoes_quitacao" id="var_opcoes_quitacao_quitado" value="B" style="border:none;"></td>
									<td width="99%" colspan="2" align="left"><?php echo(getTText("ja_quitados",C_NONE)); ?></td>
								</tr>
								<tr>
									<td width="1%"></td>
									<td width="1%" nowrap="nowrap" align="left"><?php echo(getTText("dt_lcto",C_UCWORDS)); ?>:&nbsp;</td>
									<td width="98%">
										<input name="var_dt_lcto" id="var_dt_lcto" class="edtext" value="" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);">
									</td>
								</tr>
								<tr>
									<td width="1%"></td>
									<td width="1%" nowrap="nowrap" align="left"><?php echo(getTText("conta",C_UCWORDS)); ?>:&nbsp;</td>
									<td width="98%">
										<select name="var_cod_conta_quitacao" class="edtext" style="width:200px;">
											<option value=""></option>
											<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY nome ","cod_conta","nome","")); ?>
										</select>
									</td>
								</tr>
								<tr>
									<td width="1%"></td>
									<td width="1%" nowrap="nowrap" align="left"><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:&nbsp;</td>
									<td width="98%">
										<select name="var_cod_plano_conta_quitacao" class="edtext" style="width:200px;">
											<option value=""></option>
											<?php echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo","")); ?>
										</select>
										<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('planoconta','var_cod_plano_conta_quitacao')" class="inputclean">
									</td>
								</tr>
								<tr>
									<td width="1%"></td>
									<td width="1%" nowrap="nowrap" align="left"><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:&nbsp;</td>
									<td width="98%">
										<select name="var_cod_centro_custo_quitacao" class="edtext" style="width:200px;">
											<option value=""></option>
											<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY nome ","cod_centro_custo","nome","")); ?>
										</select>
										<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('centrocusto','var_cod_centro_custo_quitacao')" class="inputclean">
									</td>
								</tr>
								<tr>
									<td width="1%"></td>
									<td width="1%" nowrap="nowrap" align="left"><?php echo(getTText("job",C_UCWORDS)); ?>:&nbsp;</td>
									<td width="98%">
										<select name="var_cod_job_quitacao" class="edtext" style="width:200px;">
											<option value=""></option>
											<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome","")); ?>
										</select>	
									</td>
								</tr>
							</table>
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
								<!-- button onClick="submeterForm('aplicar');"><?php //echo(getTText("aplicar",C_UCWORDS)); ?></button -->
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