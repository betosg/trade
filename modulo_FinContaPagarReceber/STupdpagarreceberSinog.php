<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_chavereg");
$strOper 	= request("var_oper");

try{
	$strSQL = " SELECT 
					  cod_conta
					, tipo
			        , codigo
			        , cod_centro_custo
			        , cod_plano_conta
					, cod_job
			        , vlr_conta
					, vlr_pago
					, vlr_mora_multa
					, vlr_outros_acresc
					, tipo_documento
					, num_documento
					, nosso_numero
					, dt_emissao
					, dt_vcto
					, ano_vcto
					, historico
					, obs
					, situacao
					, pagar_receber
					, cod_cfg_boleto
					, arquivo1
					, exibir_ar
				FROM fin_conta_pagar_receber
				WHERE cod_conta_pagar_receber = " . $intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$intCodJob = getVarEntidade($objConn, "fin_cod_job");

if($objRS !== array()) { 
	if (getValue($objRS,"situacao") != "aberto") {
		//mensagem("err_sql_titulo","alert_titulo_edit_titulo_desc",getTText("err_titulo_diff_aberto",C_NONE),"","aviso",1);
		//die();
		$readOnly = "readonly'";
	}
	
	$strVLR_CONTA = number_format((double) getValue($objRS,"vlr_conta"), 2);
	$strVLR_CONTA = str_replace(",", "", $strVLR_CONTA);
	$strVLR_CONTA = str_replace(".", ",", $strVLR_CONTA);
	
	$strVLR_MORA_MULTA = number_format((double) getValue($objRS,"vlr_mora_multa"), 2);
	$strVLR_MORA_MULTA = str_replace(",", "", $strVLR_MORA_MULTA);
	$strVLR_MORA_MULTA = str_replace(".", ",", $strVLR_MORA_MULTA);
	
	$strVLR_OUTROS_ACRESC = number_format((double) getValue($objRS,"vlr_outros_acresc"), 2);
	$strVLR_OUTROS_ACRESC = str_replace(",", "", $strVLR_OUTROS_ACRESC);
	$strVLR_OUTROS_ACRESC = str_replace(".", ",", $strVLR_OUTROS_ACRESC);
	
	if (getValue($objRS,"pagar_receber") != false) {
		$strTITULO = getTText("conta_pagar",C_TOUPPER);
		$strROTULO = getTText("pagar_para",C_NONE);
		$strCOR = "#FF0000";
	} else {
		$strTITULO = getTText("conta_receber",C_TOUPPER);
		$strROTULO = getTText("receber_de",C_NONE);
		$strCOR = "#027C02";
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

function searchModulo(prType) {
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
	} else if(prType == "centrocusto") {
		strModulo     = "FinCentroCusto";
		strComponente = "var_cod_centro_custo";
	} else if(prType == "planoconta") {
		strModulo     = "FinPlanoConta";
		strComponente = "var_cod_plano_conta";
	}
	
	AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + strComponente + "&var_formname=formconf","800", "600");
}

<?php if(getsession($strSesPfx . "_field_detail") != '') { 	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php }	?>

//window.onload = function(){
//	window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php //echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
//	window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php //echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
//}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",$strTITULO . " - " . getTText("edicao",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="STupdpagarreceberexecSinog.php" method="post">
		   <input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>">
		   <input type="hidden" name="var_button_action" value="">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_conta" <?php echo($readOnly);?> class="edtext" style="width:230px;">
									<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY nome ","cod_conta","nome",getValue($objRS,"cod_conta"))); ?>
								</select>
							</td>
						</tr> 
						<tr bgcolor="#FAFAFA">
							<td align="right" style="color:<?php echo($strCOR); ?>; vertical-align:text-top; padding-top:8px;">*<b><?php echo($strROTULO); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:5px;" valign="middle">
										  <input name='var_codigo' value='<?php echo(getValue($objRS,"codigo")); ?>' <?php echo($readOnly);?> class='edtext' type='text' maxlength='10' style="width:40px;"
										  onKeyPress="Javascript:return validateNumKey(event);"
										  onblur="javascript:document.getElementById('div_entidade_nome').innerHTML = ''; 
														     buscanomeentidade('div_entidade_nome','formconf','var_codigo','var_tipo');">
										</td>
										<td style="padding-right:3px;" valign="middle">
											<select name="var_tipo" class="edtext" size="1" style="width:185px;" <?php echo($readOnly);?> onBlur="javascript:document.getElementById('div_entidade_nome').innerHTML = ''; 
																															        buscanomeentidade('div_entidade_nome','formconf','var_codigo','var_tipo');">
												<option value="cad_pf"        <?php if(getValue($objRS,"tipo") == 'cad_pf') echo('selected'); ?>><?php echo(getTText("pessoa_fisica", C_UCWORDS)); ?></option>
												<option value="cad_pj"        <?php if(getValue($objRS,"tipo") == 'cad_pj') echo('selected'); ?>><?php echo(getTText("pessoa_juridica", C_UCWORDS))?></option>
												<option value="cad_pj_fornec" <?php if(getValue($objRS,"tipo") == 'cad_pj_fornec') echo('selected'); ?>><?php echo(getTText("fornecedor", C_UCWORDS))?></option>
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
												<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY nome ","cod_centro_custo","nome",getValue($objRS,"cod_centro_custo"))); ?>
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
												<?php 
												  //No chamado - 27161 - Alterações Plano de conta e Nota de Debitos.
												  //Solicitou-se a inversão da exibição do PLano de contas no combo, pediram para mudar 
												  //mostrando o NOME primeiro ante do COD RESUMIDO
												  
												  //Obs.: 
												  //Particularmente acredito que essa alteração não possa ser mantida desta forma 
												  //(na exibição dos pl.contas no combo), porque dentre outras coisas, isso interfere 
												  //em outros clientes (outros sindicatos), além de que, por definição de lógica do 
												  //sistema e contábil o que nos foi passado em início do projeto, é que a referenciação 
												  //e o encontro de um PL.CONTAS bem organizado se dá mais fácil e logicamente pelo seu código reduzido.

												  //ORIGINAL	
												  echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",getValue($objRS,"cod_plano_conta"))); 

												  //NOVO (Cabrera)
												  //echo(montaCombo($objConn," SELECT cod_plano_conta, nome || ' ' || cod_reduzido AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY nome, cod_reduzido, ordem ","cod_plano_conta","rotulo",getValue($objRS,"cod_plano_conta"))); 
 												?>
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
									<tr valign="middle">
										<td style="padding-right:3px;">
											<select name="var_cod_job" class="edtext" style="width:230px;">
												<option value=""></option>
												<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",getValue($objRS,"cod_job"))); ?>
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
							<td>
							<?php 
							if (getValue($objRS,"situacao") == "aberto") { 
								echo("<input name='var_vlr_conta' value='" . $strVLR_CONTA . "' type='text' ");
								echo("class='edtext' style='width:80px;' maxlength='15' onKeyPress='return(validateFloatKeyNew(this,event));'>");
							}
							else {
								echo($strVLR_CONTA);
							}
							?>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right">*<b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<table border="0px" cellpadding="0px" cellspacing="0px" width="100%">
									<tr>
										<td width="120">
											<select name="var_tipo_documento" <?php echo($readOnly);?> class="edtext" style="width:200px;">
												<option value="BOLETO"              <?php if(getValue($objRS,"tipo_documento") == 'BOLETO')              echo("selected='selected'"); ?>><?php echo(getTText("boleto",C_TOUPPER)); ?></option>
												<option value="BOLETO_SINDICAL"     <?php if(getValue($objRS,"tipo_documento") == 'BOLETO_SINDICAL')     echo("selected='selected'"); ?>><?php echo(getTText("boleto_sindical",C_TOUPPER)); ?></option>
												<option value="BOLETO_ASSISTENCIAL" <?php if(getValue($objRS,"tipo_documento") == 'BOLETO_ASSISTENCIAL') echo("selected='selected'"); ?>><?php echo(getTText("boleto_assistencial",C_TOUPPER)); ?></option>
												<option value="EXTRATO"             <?php if(getValue($objRS,"tipo_documento") == 'EXTRATO')             echo("selected='selected'"); ?>><?php echo(getTText("extrato",C_TOUPPER)); ?></option>
												<option value="FATURA"              <?php if(getValue($objRS,"tipo_documento") == 'FATURA')              echo("selected='selected'"); ?>><?php echo(getTText("fatura",C_TOUPPER)); ?></option>
												<option value="HOLERITE"            <?php if(getValue($objRS,"tipo_documento") == 'HOLERITE')            echo("selected='selected'"); ?>><?php echo(getTText("holerite",C_TOUPPER)); ?></option>
												<option value="NOTA_FISCAL"         <?php if(getValue($objRS,"tipo_documento") == 'NOTA_FISCAL')         echo("selected='selected'"); ?>><?php echo(getTText("nota_fiscal",C_TOUPPER)); ?></option>
												<option value="TARIFA"              <?php if(getValue($objRS,"tipo_documento") == 'TARIFA')              echo("selected='selected'"); ?>><?php echo(getTText("tarifa",C_TOUPPER)); ?></option>
												<option value="RECIBO"              <?php if(getValue($objRS,"tipo_documento") == 'RECIBO')              echo("selected='selected'"); ?>><?php echo(getTText("recibo",C_TOUPPER)); ?></option>
                                                <option value="TED"                 <?php if(getValue($objRS,"tipo_documento") == 'TED')                 echo("selected='selected'"); ?>><?php echo(getTText("ted",C_TOUPPER)); ?></option>
												<option value="CARTAO_VISA"         <?php if(getValue($objRS,"tipo_documento") == 'CARTAO_VISA')         echo("selected='selected'"); ?>><?php echo(getTText("cartao_visa",C_TOUPPER)); ?></option>
												<option value="CARTAO_MASTERCARD"   <?php if(getValue($objRS,"tipo_documento") == 'CARTAO_MASTERCARD')   echo("selected='selected'"); ?>><?php echo(getTText("cartao_mastercard",C_TOUPPER)); ?></option>
												<option value="CARTAO_AMEX"         <?php if(getValue($objRS,"tipo_documento") == 'CARTAO_AMEX')         echo("selected='selected'"); ?>><?php echo(getTText("cartao_amex",C_TOUPPER)); ?></option>
											</select>
										</td>
									</tr>
								</table>		
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td align="right"><b>Anexo</b>:&nbsp;</td>
							<td >
								<input type="text" name="dbvar_str_arquivo_1" id="dbvar_str_arquivo_1" value="<?php echo(getValue($objRS,"arquivo1"));?>" readonly="true" title="Arquivo (1)" style="width:80px;">
								<input type="button" name="btn_uploader" value="Upload" class="inputclean" onclick="callUploader('formconf','dbvar_str_arquivo_1','\\<?php echo(getSession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspj\\','','');">
								<span class="comment_med">&nbsp;
									<img src="../img/icon_wrong.gif" alt="Remover Arquivo" title="Remover Arquivo" onclick="Javascript:document.getElementById('dbvar_str_arquivo_1').value='';" style="cursor:pointer;">
								</span>&nbsp;
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td width="100" align="right">*<b><?php echo(getTText("nosso_numero",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><?php echo(getValue($objRS,"nosso_numero")); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td width="100" align="right">*<b><?php echo(getTText("num_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><?php echo(getValue($objRS,"num_documento")); ?></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right">*<b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<table border="0px" cellpadding="0px" cellspacing="0px" width="100%">
									<tr>
										<td width="90px"><input name='var_dt_emissao' id='var_dt_emissao' <?php echo($readOnly);?> class='edtext' value="<?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false)); ?>" type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"  ><span class="texto_corpo_peq"></span></td>
										<td width="120px" align="right">*<b><?php echo(getTText("dt_vcto",C_UCWORDS)); ?></b>:&nbsp;</td>
										<td align="left"><input name='var_dt_vcto' id='var_dt_vcto' <?php echo($readOnly);?> class='edtext' value="<?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false)); ?>" type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"  ><span class="texto_corpo_peq"></span></td>
									</tr>
								</table>		
							</td>					
						</tr>
						<tr bgcolor="#FFFFFF">
							<td align="right"><b><?php echo(getTText("situacao",C_UCWORDS)); ?>:&nbsp;</b></td>
							<td>
								<select name="var_situacao" style="width:180px;" <?php echo($readOnly);?>>
									<option value="aberto" <?php echo((getValue($objRS,"situacao") == "aberto") ? "selected" : ""); ?>>ABERTO</option>
									<option value="lcto_parcial" <?php echo((getValue($objRS,"situacao") == "lcto_parcial") ? "selected" : ""); ?>>LCTO PARCIAL</option>
									<option value="lcto_total" <?php echo((getValue($objRS,"situacao") == "lcto_total") ? "selected" : ""); ?>>LCTO TOTAL</option>
									<option value="cancelado" <?php echo((getValue($objRS,"situacao") == "cancelado") ? "selected" : ""); ?>>CANCELADO</option>
								</select>
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td align="right">*<b><?php echo(getTText("historico",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><input name="var_historico" value="<?php echo(getValue($objRS,"historico")); ?>" type="text" class="edtext" maxlength="250" style="width:307px;"></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr> 
							<td align="right" valign="top"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><textarea name="var_obs" class="edtext" rows="7" style="width:357px;"><?php echo(getValue($objRS,"obs")); ?></textarea></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle"><b><?php echo(getTText("cod_cfg_boleto",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> 
								<select name="var_cod_cfg_boleto" class="edtext" style="width:190px;" <?php echo($readOnly);?>>
									<?php echo(montaCombo($objConn," SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY ordem, descricao ","cod_cfg_boleto","descricao",getValue($objRS, "cod_cfg_boleto"),"")); ?>
								</select>
							</td>
						</tr> 
						<tr bgcolor="#FFFFFF">
							<td width="100" align="right"><b><?php echo(getTText("ano_vcto",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><input name='var_ano_vcto' id='var_ano_vcto' class='edtext' <?php echo($readOnly);?> value="<?php echo(getValue($objRS,"ano_vcto")); ?>" type='text' maxlength='4' style='width:50px;' onKeyPress="Javascript:return validateNumKey(event);"></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td width="100" align="right"><b>Exibir Ar</b>:&nbsp;</td>
							<td align="left">
									<input type="checkbox" value="true" <?php if  ( getValue($objRS,"exibir_ar") ){echo("checked");} ?> name="var_exibir_ar">
								</td>
						</tr>
						<tr>
							<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
						</tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm('ok');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="document.location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>'; return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<script language="javascript" type="text/javascript">
	document.getElementById('div_entidade_nome').innerHTML = ''; 
	buscanomeentidade('div_entidade_nome','formconf','var_codigo','var_tipo');
</script>
<?php
}
?>
