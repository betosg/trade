<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// INCLUDES
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// REQUESTS
$dateDtEmissaoIni = request("var_dt_emissao_ini");
$dateDtEmissaoFim = request("var_dt_emissao_fim");
$dateDtVctoIni = request("var_dt_vcto_ini");
$dateDtVctoFim = request("var_dt_vcto_fim");
$strHistorico = request("var_historico");
$strTipoDocumento = request("var_tipo_documento");
$strValor		  = request("var_valor");
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin: 0px; background-color:#FFFFFF; } 
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
</style>
<script language="javascript" type="text/javascript">
function cancelar() {
	document.location.href = "STgeraarqRemessaPasso1.php";	
}

function gerar(){
	var var_msg = "";
	
	if ((document.formeditor.var_dt_emissao_ini.value == '') && (document.formeditor.var_dt_emissao_fim.value == '') && (document.formeditor.var_dt_vcto_ini.value == '') && (document.formeditor.var_dt_vcto_fim.value == '')) {
		var_msg += "Informe um período de data\n";
	}
	else {
		if (((document.formeditor.var_dt_emissao_ini.value == '') && (document.formeditor.var_dt_emissao_fim.value != '')) || ((document.formeditor.var_dt_emissao_ini.value != '') && (document.formeditor.var_dt_emissao_fim.value == '')))
			var_msg += "Informe data de início e fim do período de emissão\n";
		if (((document.formeditor.var_dt_vcto_ini.value == '') && (document.formeditor.var_dt_vcto_fim.value != '')) || ((document.formeditor.var_dt_vcto_ini.value != '') && (document.formeditor.var_dt_vcto_fim.value == '')))
			var_msg += "Informe data de início e fim do período de vencimento\n";
	}
	
	if (var_msg != '') {
		alert(var_msg);
	}else{
		document.formeditor.submit();
	}
}

</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_gerar_remessa",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
        <form name="formeditor" action="STgeraarqRemessaPasso2exec.php" method="post">
		<input type="hidden" name="var_dt_emissao_ini" value="<?php echo($dateDtEmissaoIni); ?>">
		<input type="hidden" name="var_dt_emissao_fim" value="<?php echo($dateDtEmissaoFim); ?>">
		<input type="hidden" name="var_dt_vcto_ini"    value="<?php echo($dateDtVctoIni); ?>">
		<input type="hidden" name="var_dt_vcto_fim"    value="<?php echo($dateDtVctoFim); ?>">
		<input type="hidden" name="var_historico"      value="<?php echo($strHistorico); ?>">
		<input type="hidden" name="var_tipo_documento" value="<?php echo($strTipoDocumento); ?>">
        <input type="hidden" name="var_valor"          value="<?php echo($strValor); ?>">
        
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr><td width="30%"></td><td width="70%"></td></tr>
					<tr><td align="left" style="padding-left:5px;" colspan="2"><img src="../img/remessa_passo02.gif"></td></tr>
					<tr>
						<td colspan="2"><iframe src="STlistaTitulos.php?var_valor=<?php echo($strValor);?>&var_dt_emissao_ini=<?php echo($dateDtEmissaoIni); ?>&var_dt_emissao_fim=<?php echo($dateDtEmissaoFim); ?>&var_dt_vcto_ini=<?php echo($dateDtVctoIni); ?>&var_dt_vcto_fim=<?php echo($dateDtVctoFim); ?>&var_historico=<?php echo $strHistorico; ?>&var_tipo_documento=<?php echo $strTipoDocumento; ?>" width="700" height="250" frameborder="0"></iframe></td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
							<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
								<button onClick="gerar();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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