<?php
include_once("../_database/athdbconn.php");

$strAction  = request("var_action"); 
$strIndice  = request("var_indice"); 
$strValor   = request("var_valor");

$strValor = (request("var_delete") == "sim") ? null : $strValor;

setsession($strIndice,$strValor);

redirect($strAction);
?>