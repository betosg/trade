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
			        , t1.vlr_lcto
					, t1.num_lcto
					, t1.dt_lcto
					, t1.historico
					, t1.obs
					, t1.operacao
					, t2.nome AS nome_conta
					, t3.nome AS centro_custo
					, t4.cod_reduzido || ' - ' || t4.nome AS plano_conta
				FROM fin_lcto_em_conta AS t1
				     LEFT OUTER JOIN fin_conta AS t2        ON (t1.cod_conta = t2.cod_conta)
				     LEFT OUTER JOIN fin_centro_custo AS t3 ON (t1.cod_centro_custo = t3.cod_centro_custo)
				     LEFT OUTER JOIN fin_plano_conta AS t4  ON (t1.cod_plano_conta = t4.cod_plano_conta)
				WHERE cod_lcto_em_conta = " . $intCodDado;
		$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}


if($objRS = $objResult->fetch()) { 
	$dblVlrConta = number_format((double) getValue($objRS,"vlr_lcto"), 2);
	$dblVlrConta = str_replace(",", "", $dblVlrConta);
	$dblVlrConta = str_replace(".", ",", $dblVlrConta);
	
	if (strtoupper(getValue($objRS,"operacao")) == "SAIDA") {
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
window.onload = function(){
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>').style.height = document.body.scrollHeight;
			
			if(window.parent.document.frmSizeBody){	
				var codAvo = window.parent.document.frmSizeBody.codAvo.value;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_'+codAvo).style.height = window.parent.document.body.scrollHeight;
			}
		}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top" height="1%">
	<?php athBeginFloatingBox("725","none",getTText("lcto_em_conta",C_NONE),CL_CORBAR_GLASS_1); ?>
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
										if (getValue($objRS,"tipo") == "cad_pf")        $strSQL .= " WHERE cod_pf = "        . getValue($objRS,"cod_entidade");
										if (getValue($objRS,"tipo") == "cad_pj")        $strSQL .= " WHERE cod_pj = "        . getValue($objRS,"cod_entidade");
										if (getValue($objRS,"tipo") == "cad_pj_fornec") $strSQL .= " WHERE cod_pj_fornec = " . getValue($objRS,"cod_entidade");
										
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
							<td align="right">*<b><?php echo(getTText("vlr_lcto",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td><?php echo($dblVlrConta); ?></td>
						</tr>
						<tr bgcolor="#FFFFFF"> 
							<td width="120px" align="right">*<b><?php echo(getTText("dt_lcto",C_UCWORDS)); ?></b>:&nbsp;</td>
							<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false)); ?></td>
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
						<tr><td height="10" colspan="3"></td></tr>
					</table>
				</td>
			</tr>
		</table>
	<?php athEndFloatingBox(); ?>
	<br><br>
   </td>
  </tr>
</table>
</body>
</html>
<?php } ?>