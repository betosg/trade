<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	echo(CFG_EMAIL_SENDER);
emailNotify("ola teste","assunto teste","gabriel.schunck@gmail.com",CFG_EMAIL_SENDER);



?>
