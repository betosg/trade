<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<html>
<head>
<title>PROEVENTO STUDIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(){
	document.formconf.submit();
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="middle">
	<?php athBeginFloatingBox("600","none","Assistente de Lotes - PROEVENTO STUDIO","#AFD987"); ?>
		<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="wizardlotesp2.php" method="post">
			<tr>
				<td width="1%"><img src="wizard_lotes.jpg"></td>
				<td align="center" valign="top">
					<table width="99%" border="0" cellspacing="0" cellpadding="4">	
						<tr>
							<td height="5" colspan="3" valign="top"><h1>Bem-Vindo ao Assistente de Lotes.</h1></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<table width="95%" border="0" cellspacing="0" cellpadding="4">
						<tr><td height="5" colspan="3"></td></tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick=""><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								<button onClick="submeterForm();">Avan�ar >></button>
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