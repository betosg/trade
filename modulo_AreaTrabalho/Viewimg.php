<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<html>
<head>
<script language="JavaScript" type="text/javascript">
function NewTamPos(prImgWidth, prImgHeight){
  var auxWidth  = prImgWidth  + 25; //Diferença para Scrollbar
  var auxHeight = prImgHeight + 70; //Diferença para o texto abaixo da imagem
  self.resizeTo(auxWidth,auxHeight); 
  self.moveTo( (screen.width/2)-(auxWidth/2) , (screen.height/2)-(auxHeight/2) );
}
</script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" scroll="no">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td height="100%" align="center" valign="middle"><?php 
    if( file_exists( "../img/bgFrame_" . trim(request("type")) . "_filtro.jpg" ) ) { ?>
    <div align="center" style="padding-left:2px; padding-right:2px;"><img src="../img/bgFrame_<?php echo(trim(request("type"))); ?>_filtro.jpg" onload="NewTamPos(this.width, this.height);"><br><font face="Arial" size="-1"><?php echo(trim(request("extra"))); ?></font></div><?php
	}
	else{
	?><script>self.moveTo(10,10);</script><?php
	}
	?></td></tr>
</table>
</body>
</html>
