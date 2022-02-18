<?php
 function request($prParam){
 	(isset($_REQUEST[$prParam])) ? $retValue = $_REQUEST[$prParam] : $retValue = "";
	return($retValue);
 }
 //$strDB	  = (!isset($_POST["var_db"]))   ? strtolower("tradeunion_".basename(getcwd())) : $_POST["var_db"];
 //$strUser = (!isset($_POST["var_user"])) ? "" : $_POST["var_user"];
 //$strPass = (!isset($_POST["var_pass"])) ? "" : $_POST["var_pass"];
 $strDB	  = request("var_db");
 $strUser = request("var_user");
 $strPass = request("var_pass");

 //Se o banco não vemn por parãmetro monta o memso com o nome da pasta do cliente
 if ($strDB == "") { $strDB = strtolower("tradeunion_".basename(getcwd()));  }

 //Para aceitar tbm "user" e não só var_user como parâmetro
 if ($strUser == "" ) { $strUser = request("user");  }

 //Quando o var_pass não vem, assumimos para tentar com a senha "guest"
 if (($strPass == "" ) && ($strUser != "" ) ) { $strPass = ("guest"); }
?>
<html>
<head>
<title>TRADEUNION</title>
<meta name="TITLE"         content="">
<meta name="DESCRIPTION"   content="">
<meta name="ABSTRACT"      content="">
<meta name="comment"       content="">
<meta name="KEYWORDS"      content="">
<meta name="REVISIT-AFTER" content="30 days">
<meta name="LANGUAGE"      content="PT-BR">
<meta name="COPYRIGHT"     content="PROEVENTO - Athenas Software & Systems">
<meta name="audience"      content="all">

<meta name="robots"          content="noindex">
<meta name="googlebot"       content="noindex">
<meta name="bingbot"         content="noindex">
<meta name="msnbot"          content="noindex">
<meta name="adsbot-google"   content="noindex">
<meta name="twitterbot"      content="noindex">
<meta name="yahoo-mmcrawler" content="noindex">
<meta name="slurp|gigabot"   content="noindex">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
	<frameset rows="*,0" framespacing="0" frameborder="NO" border="0">
  	<frame src="login.php?var_db=<?php echo($strDB);?>&var_user=<?php echo($strUser);?>&var_pass=<?php echo($strPass);?>" name="tradeunion_mainFrame" scrolling="yes">
</html>