<?php
	// INCLUDES
	include_once("../../../_database/STconfiginc.php");
	$bReturnAbsolute=false;

	$sBaseVirtual0="/imgdin";  //Assuming that the path is http://yourserver/Editor/assets/ ("Relative to Root" Path is required)
	
	// DEFINE PATH
	$sBase0="http://10.1.20.9/tradeunion/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/imgdin"; //The real path
	// $sBase0="c:/inetpub/wwwroot/Editor/assets"; //The real path
	// $sBase0="/home/yourserver/web/Editor/assets"; //example for Unix server

	$sName0="IMAGENS";

	$sBaseVirtual1="";
	$sBase1="";
	$sName1="";

	$sBaseVirtual2="";
	$sBase2="";
	$sName2="";

	$sBaseVirtual3="";
	$sBase3="";
	$sName3="";
?>