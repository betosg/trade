<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

$intCodDado = request("var_chavereg");

try{
	$strSQL = " SELECT t1.cod_lcto_transf
					 , t1.num_lcto
					 , t1.vlr_lcto
					 , t1.dt_lcto
					 , t1.historico
					 , t1.obs
				     , t2.nome AS conta_origem
					 , t3.nome AS conta_destino
				FROM fin_lcto_transf AS t1
				     LEFT OUTER JOIN fin_conta AS t2 ON (t1.cod_conta_orig = t2.cod_conta)
				     LEFT OUTER JOIN fin_conta AS t3 ON (t1.cod_conta_dest = t3.cod_conta)
				WHERE t1.cod_lcto_transf = " . $intCodDado;
		$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objRS = $objResult->fetch()) { 
	$dblVlrTransf = number_format((double) getValue($objRS,"vlr_lcto"), 2);
	$dblVlrTransf = str_replace(",", "", $dblVlrTransf);
	$dblVlrTransf = str_replace(".", ",", $dblVlrTransf);
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
	<?php athBeginFloatingBox("725","none",getTText("lcto_transf",C_NONE),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="20"></td></tr> 	
						<tr> 
							<td width="120" align="right" valign="middle">*<b><?php echo(getTText("conta_origem",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"conta_origem")); ?></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("conta_destino",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"> <?php echo(getValue($objRS,"conta_destino")); ?></td>
						</tr> 
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					    <tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("numero",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"numero")); ?></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("valor",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo($dblVlrTransf); ?></td>
						</tr>
						<tr> 
							<td align="right" valign="middle">*<b><?php echo(getTText("data",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false)); ?></td>
						</tr>
						<tr bgcolor="#FAFAFA"> 
							<td align="right" valign="middle">*<b><?php echo(getTText("historico",C_UCWORDS)); ?>:</b>&nbsp;</td>
							<td valign="middle"><?php echo(getValue($objRS,"historico")); ?></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
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