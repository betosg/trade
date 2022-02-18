<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_chavereg");
$strOper 	= request("var_oper");

try{
	$strSQL = " SELECT 
					  t1.cod_conta
					, t1.tipo
			        , t1.codigo AS cod_entidade
			        , t1.vlr_conta
					, t1.vlr_pago
					, t1.tipo_documento
					, t1.num_documento
					, t1.dt_emissao
					, t1.dt_vcto
					, t1.historico
					, t1.obs
					, t1.situacao
					, t1.pagar_receber
					, t2.nome AS nome_conta
					, t3.nome AS centro_custo
					, t4.cod_reduzido || ' - ' || t4.nome AS plano_conta
				FROM fin_conta_pagar_receber AS t1
				     LEFT OUTER JOIN fin_conta AS t2        ON (t1.cod_conta = t2.cod_conta)
				     LEFT OUTER JOIN fin_centro_custo AS t3 ON (t1.cod_centro_custo = t3.cod_centro_custo)
				     LEFT OUTER JOIN fin_plano_conta AS t4  ON (t1.cod_plano_conta = t4.cod_plano_conta)
				WHERE cod_conta_pagar_receber = " . $intCodDado;
		$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}


if($objRS = $objResult->fetch()) { 
	$dblVlrConta = number_format((double) getValue($objRS,"vlr_conta"), 2);
	$dblVlrConta = str_replace(",", "", $dblVlrConta);
	$dblVlrConta = str_replace(".", ",", $dblVlrConta);
	
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
		combo         = document.forms[0].dbvar_str_tipo;
		strModulo     = (combo.options[combo.selectedIndex].value == "cad_pf") ? "CadPF" : "CadPJ";
		strComponente = "dbvar_num_codigo";
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
   <td align="center" valign="top" height="1%">
	<?php athBeginFloatingBox("725","none",getTText("conta_pagar_receber",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"nome_conta")); ?></td>
						</tr> 
						<tr bgcolor="#FAFAFA">
							<td align="right" style="color:<?php echo($strCOR); ?>;">*<b><?php echo($strROTULO); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<?php 
									try {
										$strSQL =  " SELECT ";
										$strSQL .= ((getValue($objRS,"tipo") == "cad_pf") ? " nome " : " nome_fantasia ") . " AS nome ";
										$strSQL .= " FROM " . getValue($objRS,"tipo");
										$strSQL .= " WHERE " . ((getValue($objRS,"tipo") == "cad_pf") ? " cod_pf " : " cod_pj ") . " = " . getValue($objRS,"cod_entidade");
										$objResultAux = $objConn->query($strSQL);
										
										$objRSAux = $objResultAux->fetch();
										$strNomeEntidade = getValue($objRSAux,"nome");
											
										$objResultAux->closeCursor();
									} catch(PDOException $e) {
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
										die();
									}
									
									echo(getValue($objRS,"cod_entidade") . " - " . $strNomeEntidade);
								?>			
							</td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"centro_custo")); ?></td>
						</tr> 	
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<?php echo(getValue($objRS,"plano_conta")); ?>
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
							<td><?php echo($dblVlrConta); ?></td>
						</tr>
						<tr>
							<td align="right">*<b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<table border="0px" cellpadding="0px" cellspacing="0px" width="100%">
									<tr>
										<td width="120">
											<?php echo(getValue($objRS,"tipo_documento")); ?>
										</td>
									</tr>
								</table>		
							</td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td width="100" align="right">*<b><?php echo(getTText("num_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><?php echo(getValue($objRS,"num_documento")); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF"> 
							<td align="right">*<b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
								<table border="0px" cellpadding="0px" cellspacing="0px" width="100%">
									<tr>
										<td width="90px"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false)); ?></td>
										<td width="120px" align="right">*<b><?php echo(getTText("dt_vcto",C_UCWORDS)); ?></b>:&nbsp;</td>
										<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false)); ?></td>
									</tr>
								</table>		
							</td>					
						</tr>
						<tr>
							<td align="right"><b>Situação:&nbsp;</b></td>
							<td><?php echo(getValue($objRS,"situacao")); ?></td>
						</tr>
						<tr bgcolor="#FAFAFA">
							<td align="right">*<b><?php echo(getTText("historico",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><?php echo(getValue($objRS,"historico")); ?></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr> 
							<td align="right" valign="top"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"obs")); ?></td>
						</tr>
						<tr>
							<td height="10" colspan="2"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php athEndFloatingBox(); ?>
	<br><br>
   </td>
  </tr>
  <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","Lançamentos",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
						<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
						<tr><td>
							<table align="center" cellpadding="0" cellspacing="1" style="width:100%">
								<tr>
									<td>
										<table border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
											<tr height="22">
												<td width="19%" nowrap>&nbsp;Plano de Conta</td>
												<td width="10%" nowrap>&nbsp;Lançamento</td>
												<td width="10%" nowrap>&nbsp;Data</td>
												<td width="15%" nowrap>&nbsp;Ocorrência</td>
												<td width="10%" nowrap>&nbsp;Usuario</td>
												<td width="10%" nowrap>&nbsp;Tipo</td>
												<td width="5%"  nowrap>&nbsp;Histórico</td>
												<td width="20%" align="center" nowrap>&nbsp;Info Extra</td>
												<td width="1%" align="right">&nbsp;</td>
											</tr>
								<?php
									try {
										$strSQL = "	SELECT 
														lct.cod_lcto_ordinario,
														lct.cod_conta_pagar_receber,
														lct.vlr_lcto,
														lct.dt_lcto,
														lct.historico,
														pcont.cod_reduzido,
														lct.obs,
														lct.sys_dtt_ins,
														lct.sys_usr_ins,
														lct.tipo_documento,
														lct.extra_documento
													FROM fin_lcto_ordinario AS lct
														 INNER JOIN fin_conta_pagar_receber AS cont ON (lct.cod_conta_pagar_receber = cont.cod_conta_pagar_receber AND cont.cod_conta_pagar_receber = " . $intCodDado . ")
														 LEFT OUTER JOIN fin_plano_conta AS pcont ON (lct.cod_plano_conta = pcont.cod_plano_conta)
													ORDER BY cod_conta_pagar_receber DESC, dt_lcto DESC";
										$objResultLct = $objConn->query($strSQL);
									} catch(PDOException $e) {
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
										die();
									}
									
									$dblValotTotal = 0;
									$strColor = "";
									$strTituloAnt = "";
									$boolShowResult = true;

									foreach($objResultLct as $objRSLct) {
										$strColor = ($strColor != CL_CORLINHA_1) ? CL_CORLINHA_1 : CL_CORLINHA_2;
								?>	
											<tr height="22" bgcolor="<?php echo($strColor) ?>">	
												<td style="vertical-align:middle;">&nbsp;<?php echo(getValue($objRSLct,"cod_reduzido")); ?></td>
												<td style="vertical-align:middle;"><?php echo(number_format((double) getValue($objRSLct,"vlr_lcto"),2,",","")); ?></td>
												<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRSLct,"dt_lcto"),false)); ?></td>
												<td style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRSLct,"sys_dtt_ins"),true)); ?></td>
												<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRSLct,"sys_usr_ins")); ?></td>
												<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRSLct,"tipo_documento")); ?></td>
												<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRSLct,"historico")); ?></td>
												<td style="vertical-align:middle;" align="left"><?php echo(getValue($objRSLct,"extra_documento")); ?></td>
												<td valign="middle" align="center">
													<?php if(getValue($objRSLct,"obs") != "") { ?>
														<img src="../img/icon_obs.gif" title="<?php echo(getValue($objRSLct,"obs")); ?>" border="0" vspace="5" hspace="5" style="cursor: pointer;">
													<?php } ?>
												</td>
											</tr>
										<?php
									}
									
									$objResultLct->closeCursor();
									?>
										</table>
									</td>
								</tr>
							</table>
						</td></tr>
						<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					</table>	
				</td>
			</tr>
		</table>
	<?php athEndFloatingBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>
<?php } ?>