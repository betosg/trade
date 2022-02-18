<?php
header("Content-Type:text/html; charset=iso-8859-1");
include_once("../_database/athdbconn.php");
// Com o include acima, é disparado session_start()... 
// desta forma a cada chamda desta págia a session acaba 
// sendo renovada, e o teste abaixo SEMPRE retornará algum 
// valor, indicando que a session esta ativa.
// **ou seja, indiretamnte estamos sempre renovando a session
// se essa página for chamada via em pooling via ajax como 
// no mxmenu.php

//Retorna o valor do campo da session solicitado
if(isset($_SESSION[request("var_sesfield")])) 
  { echo(getsession(request("var_sesfield"))); }
else 
  { echo(""); }
?>
