<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

// Este teste serve para verificar se dentro de um memso navegador 
// (tipo caixa de areia) foi efetuado o longi em um ouro banco, 
// desta forma a sessão deste teria expirado pois o banco mudou
// ------------------------------------------------------- by Aless 
$strOrigDB = request("var_orig_db");
if ($strOrigDB == "") $strOrigDB = CFG_DB;
if ($strOrigDB != CFG_DB) { 
	?>
	<script>
		alert('Sessão expirou.');
		parent.parent.parent.location.reload();
    </script>
	<?php
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo(CFG_SYSTEM_TITLE);?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#C6C6C6" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="formrefresh" id="formrefresh" target="_self" action="mxcheckdb.php">
	<input type="hidden" name="var_orig_db" value="<?php echo $strOrigDB; ?>">
</form>
<script type="application/javascript" language="javascript">
  // De tanto em tanto tempo submete este formulário enviando o DB que estava 
  // quando a página foi aberta, logo (lá em cima) se o banco que esta na sessão 
  // for diferente do banco recebido, isso significa que numa mesma sessão 
  // (navegar tipo sandbox) outro banco foi acionado e desta formao o atual 
  // deve sinalizar que a sessão expirou.
  // ------------------------------------------------------- by Aless 
  setTimeout(function(){document.formrefresh.submit();},60000);
</script>
</body>
</html>