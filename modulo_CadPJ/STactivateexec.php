<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/STathutils.php");

$intCodPJ = request("var_cod_pj");
$strNomePJ = request("var_razao_social");

$objConn = abreDBConn(CFG_DB);

getDadosPJSelected($objConn, $intCodPJ, "");

$objConn = NULL;

?>
<html>
<body>
 <script>
   location = "../modulo_PainelPJ/STindex.php";
   //location = "../modulo_Principal/frames.php";
 </script>
</body>
</html>
