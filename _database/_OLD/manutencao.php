<?php
 include_once("../_database/athdbconn.php");
 
 $StrMSG = request("var_mensagem");
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
</head>
<!--#CFCFCF-->
<body style="margin:10px;" bgcolor="#FFFFFF">
 <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
	<td valign="top" style="padding-top:10px;">
		<?php mensagem("MANUTENÇÃO: Não foi possível carregar este módulo.", $StrMSG , "" , "" , "standardinfo", 0); ?>
	</td>
  </tr>
 </table>
</body>
</html>