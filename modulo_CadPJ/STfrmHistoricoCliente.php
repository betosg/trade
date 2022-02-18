<?php 
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 
 $intCodigo = request("var_chavereg");
 
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE . " - " . getTText("relatorio_aslw",C_UCWORDS)); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset rows="*,23" cols="*" framespacing="0" frameborder="no" border="0">
  <frame name="frm_historico_cliente_detail" src="SThistoricoCliente.php?var_chavereg=<?php echo $intCodigo;?>">
  <frame frameborder="1" name="frm_historico_cliente_footer" src="SThistoricoClienteFooter.php" scrolling="no">
</frameset>
</html>