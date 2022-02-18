<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
 
 $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
 verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
 
?>
<html>
<head>
	<title>PROEVENTO STUDIO</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<style>
		a{
			background-image:url(../img/icon_dir.gif);
			background-repeat:no-repeat;
			height:20px;
			padding:5px 0px 0px 20px;
		}
	</style>
</head>
<body style="margin:0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_filtro.jpg">
 <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
	<td width="24" valign="top"><img id="img_collapse" src="../img/collapse_open.gif" onClick="swapwidth(250,'<?php echo(CFG_SYSTEM_THEME); ?>');" style="cursor:pointer"></td>
	<td valign="top" style="padding-top:10px;">
		<?php
			include_once("_includemenu.php");
			echo("<br><br>");
			include_once("_includepastas_ST.php");
		?>
	</td>
  </tr>
 </table>
</body>
</html>