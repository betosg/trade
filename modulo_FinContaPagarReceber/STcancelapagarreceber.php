<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "CANCEL");

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_chavereg");

try{
	$strSQL = " SELECT
				  t4.nome AS conta
				, t1.tipo
				, t1.codigo
				, t2.nome AS centro_custo
				, t3.cod_reduzido || ' - ' || t3.nome AS plano_conta
				, t1.vlr_conta
				, t1.tipo_documento
				, t1.num_documento
				, t1.nosso_numero
				, t1.dt_emissao
				, t1.dt_vcto
				, t1.historico
				, t1.obs
				, t1.situacao
				, t1.pagar_receber
				FROM fin_conta_pagar_receber t1
				   , fin_centro_custo t2
				   , fin_plano_conta t3
				   , fin_conta t4
				WHERE t1.cod_conta_pagar_receber = " . $intCodDado . "
				AND t1.cod_centro_custo = t2.cod_centro_custo
				AND t1.cod_plano_conta = t3.cod_plano_conta
				AND t1.cod_conta = t4.cod_conta ";

		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objRS !== array()){ 
	if (getValue($objRS,"situacao") != "aberto") {
		mensagem("err_sql_titulo","alert_titulo_cancel_titulo_desc","","","aviso",1);
		die();
	}
	
	$strVLR_CONTA = number_format((double) getValue($objRS,"vlr_conta"), 2);
	$strVLR_CONTA = str_replace(",", "", $strVLR_CONTA);
	$strVLR_CONTA = str_replace(".", ",", $strVLR_CONTA);
	
	if (getValue($objRS,"pagar_receber") != false) {
		$strTITULO = getTText("conta_pagar",C_TOUPPER);
		$strROTULO = getTText("pagar_para",C_NONE);
		$strCOR = "#FF0000";
	}
	else {
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

	<?php if(getsession($strSesPfx . "_field_detail") != '') { 	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php }	?>
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",$strTITULO . " - " . getTText("cancelar",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="STcancelapagarreceberexec.php" method="post">
		   <input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>">
		   <input type="hidden" name="var_button_action" value="">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"conta")); ?></td>
						</tr> 
						<tr bgcolor="#FAFAFA">
							<td align="right" style="color:<?php echo($strCOR); ?>;">*<b><?php echo($strROTULO); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:5px;" valign="middle">
										<?php 
										echo(getValue($objRS,"codigo") . " - ");
										if(getValue($objRS,"tipo") == 'cad_pf') echo(getTText("pessoa_fisica", C_UCWORDS)); 
										if(getValue($objRS,"tipo") == 'cad_pj') echo(getTText("pessoa_juridica", C_UCWORDS)); 
										?>
										</td>
									</tr>
								</table>				
							</td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr>
										<td style="padding-right:3px;" valign="middle"><?php echo(getValue($objRS,"centro_custo")); ?></td> 			
									</tr>
								</table>
							</td>
						</tr> 	
						<tr bgcolor="#FAFAFA">
							<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle">
								<table border="0px" cellpadding="0px" cellspacing="0px">
									<tr valign="middle">
										<td style="padding-right:3px;"><?php echo(getValue($objRS,"plano_conta")); ?></td>
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
							<td><?php echo($strVLR_CONTA); ?></td>
						</tr>
						<tr>
							<td align="right">*<b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td>
							<?php
							if(getValue($objRS,"tipo_documento") == 'BOLETO')              echo(getTText("boleto",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'BOLETO_SINDICAL')     echo(getTText("boleto_sindical",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'BOLETO_ASSISTENCIAL') echo(getTText("boleto_assistencial",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'EXTRATO')             echo(getTText("extrato",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'FATURA')              echo(getTText("fatura",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'HOLERITE')            echo(getTText("holerite",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'NOTA_FISCAL')         echo(getTText("nota_fiscal",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'TARIFA')              echo(getTText("tarifa",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'RECIBO')              echo(getTText("recibo",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'TED')                 echo(getTText("ted",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'CARTAO_VISA')         echo(getTText("cartao_visa",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'CARTAO_MASTERCARD')   echo(getTText("cartao_mastercard",C_TOUPPER));
							if(getValue($objRS,"tipo_documento") == 'CARTAO_AMEX')         echo(getTText("cartao_amex",C_TOUPPER));
							?>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td width="100" align="right">*<b><?php echo(getTText("nosso_numero",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><?php echo(getValue($objRS,"nosso_numero")); ?></td>
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
						<tr bgcolor="#FAFAFA">
							<td align="right"><b>Situação:&nbsp;</b></td>
							<td>
							<?php
							echo((getValue($objRS,"situacao") == "aberto") ? "ABERTO" : "");
							echo((getValue($objRS,"situacao") == "lcto_parcial") ? "LCTO PARCIAL" : "");
							echo((getValue($objRS,"situacao") == "lcto_total") ? "LCTO TOTAL" : "");
							echo((getValue($objRS,"situacao") == "cancelado") ? "CANCELADO" : "");
							?>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
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
							<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
						</tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm('ok');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="window.history.back();return false;">
								<?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								<!--location.href='
								<?php echo(getsession($strSesPfx . "_grid_default")); ?>';return false;-->
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
}
?>