<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), 41,"INS");
?> 
<html>
<head>
<title>PROEVENTO STUDIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(){
	document.formchamado.submit();
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("chamado",C_TOUPPER) . " (" . getTText("inserir",C_UCWORDS) .  ")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formchamado" action="inserirchamadoexec.php" method="post">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr>
							<td align="right" width="100"><?php echo(getTText("titulo",C_UCWORDS)); ?>:&nbsp;</td>
							<td><input type="text" name="var_titulo" size="60"></td>
						</tr>
						<tr>
							<td align="right" width="100"><?php echo(getTText("descricao",C_UCWORDS)); ?>:&nbsp;</td>
							<td><textarea name="var_descricao" rows="6" cols="60"></textarea></td>
						</tr>
						<tr><td height="5" colspan="3"></td></tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="submeterForm();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="window.close();"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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