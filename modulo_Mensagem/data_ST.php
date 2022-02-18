<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente

$strPasta = request("var_pasta");
$strAcao  = request("var_acao");
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="margin:10px 0px 10px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<center><?php 
  athBeginWhiteBox("98%");
  echo("<iframe id=\"" . CFG_SYSTEM_NAME . "_gridmsg\" scrolling=\"auto\" src=\"datamsg.php?var_pasta=" . $strPasta . "&var_acao=" . $strAcao . "\" frameborder=\"0\" width=\"100%\" height=\"150\"></iframe>");
  athEndWhiteBox(); 
  echo("<br><br>");
  athBeginWhiteBox("98%");
  echo("<iframe id=\"" . CFG_SYSTEM_NAME . "_viewmsg\" scrolling=\"auto\" src=\"msgview.php\" frameborder=\"0\" width=\"100%\" height=\"295\"></iframe>");
  athEndWhiteBox(); 
 ?></center>
 </body>
</html>