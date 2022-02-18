<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script>
<!--
	function imprimir(){
		parent.frames[0].focus();
		parent.frames[0].print();
	}
	/*
	function exportarWord(){
		parent.window.frames[1].document.frmRelatorio.var_acao.value = '.doc';
		parent.window.frames[1].document.frmRelatorio.submit();
	}
	
	function exportarExcel(){
		parent.window.frames[1].document.frmRelatorio.var_acao.value = '.xls';
		parent.window.frames[1].document.frmRelatorio.submit();
	}
	
	function exportarAdobe(){
		parent.window.frames[1].document.frmRelatorio.var_acao.value = '.pdf';
		parent.window.frames[1].document.frmRelatorio.submit();
	}
	*/
//-->
</script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="formAcao" action="" target="frm_resulaslw_detail">
	<input type="hidden" name="var_acao" value="">
</form>
<table width="100%" height="22" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="100%" valign="middle" background="../img/bgFooterLeft.jpg">
	  <table width="100%" height="22" cellpadding="0" cellspacing="0" border="0">
	    <tr>
			<td align="left" valign="middle" class="texto_corpo_peq">
			<?php 
			echo("&nbsp;" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . ":&nbsp;" . getsession(CFG_SYSTEM_NAME . "_grp_user"));
			if (getsession(CFG_SYSTEM_NAME . "_su_passwd")) echo("*");
			?>
			<span class="copyright">(<?php echo("SID: " . session_id()); ?>)</span>
			</td>
			<td align="right" valign="middle"><a href="http://www.athenas.com.br" target="_blank" class="copyright">Copyright Athenas Software &amp; Systems&nbsp;</a></td>
		</tr>
	  </table>
	</td>
  </tr>
</table>
</body>
</html>