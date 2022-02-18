<?php
include_once("../_database/athdbconn.php");
 
$strPasta  = request("var_pasta");
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

setsession($strSesPfx . "_select",str_replace("caixa_entrada",$strPasta,getsession($strSesPfx . "_select_orig")));

redirect(getsession($strSesPfx . "_grid_default"). "?var_pasta=" . $strPasta); 
?>