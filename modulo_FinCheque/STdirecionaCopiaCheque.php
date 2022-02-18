<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body><?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
$intCodDado = request("var_chavereg");

setsession(CFG_SYSTEM_NAME."_codcheque", $intCodDado);

?>
<script language="javascript">
window.location = "../modulo_ASLWRelatorio/execaslw.php?var_chavereg=144"
</script>
</body>

</html>