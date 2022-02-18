<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
 
 $objConn = abreDBConn(CFG_DB);
	
 if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) == "normal") {
	$strFramePage = "STmenulateralAdmin.php";
 }
 else {
		$strFramePage = "STmenulateral.php";
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TRADEUNION MAPA (GUEST)</title>
</head>
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="2" bgcolor="#D5D5D5">
    <div style="padding-left:5px;">
	<table width="240" height="98%" cellpadding="0" cellspacing="0" border="0">
	  <tr><td colspan="2" height="10" background="../img/bmap_header.gif"></td></tr>
	  <tr>
		<td width="26" valign="top" align="right" background="../img/bmap_bgTabs.gif"><img src='../img/bmap_TabARNetUnico.gif' border='0' /></td>
		<td valign="top" align="center" background="../img/bmap_bgContent.gif" style="vertical-align:t">
		 <?php
		   //Como o nome do DB tem de ter como sufixo o nome da pasta do cliente ("datawide_abrh", por exemplo)
		   //então pegamos esse sufixo para descobrir o nome da imagem de LogoMarca do cliente que conforme 
		   //nossa padronização segue a regra de nome: LogoMarca_[nome_cliente].jpg
		   $strAux = getsession(CFG_SYSTEM_NAME."_db_name"); 
		   $strAux = str_replace(CFG_SYSTEM_NAME,"",$strAux);
		 ?>
		 <img src="../img/LogoMarca<?php echo(strtoupper($strAux));?>.gif" vspace="5" border="0">
		</td>
	  </tr>
	  <tr><td colspan="2" height="10" background="../img/bmap_footer.gif"></td></tr>
	  <tr><td colspan="2"><br></td></tr><!-- para garantir a margem inferior -->
	</table>
	</div>
	<map name="Map" id="Map">
		<area shape="rect" coords="7,14,21,72" href="mapaAdmin.php" />      
		<area shape="rect" coords="7,105,23,178" href="mapaGeral.php" />
	</map>
</body>
</html><br />
<script type="text/javascript" language="javascript">
 parent.swapMenu();
</script>