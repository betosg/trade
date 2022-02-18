<?php
include_once("../_database/athdbconn.php");

$strTypeName = request("var_typename");

setsession(CFG_SYSTEM_NAME . "_theme",$strTypeName);

redirect("setcolor.php");
?>