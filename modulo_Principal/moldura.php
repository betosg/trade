<?php include_once("../_database/athdbconn.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
var winpopup_prostudio = null;

function AbreJanelaPAGE(prpage, prwidth, prheight){ 
  var auxstr;

  auxstr  = 'width=' + prwidth;
  auxstr  = auxstr + ',height=' + prheight;
  auxstr  = auxstr + ',top=30,left=30,scrollbars=1,resizable=yes,status=yes';

  if (winpopup_prostudio != null){
    winpopup_prostudio.close();
  }
  winpopup_prostudio = window.open(prpage, 'winpopup_prostudio', auxstr);
}
</script>
</head>
<frameset cols="250,*,0" frameborder="no" name="<?php echo(CFG_SYSTEM_NAME . "_principal"); ?>" id="<?php echo(CFG_SYSTEM_NAME . "_principal"); ?>" border="0" framespacing="0" rows="*"> 
  <frame name="<?php echo(CFG_SYSTEM_NAME . "_left"); ?>" id="<?php echo(CFG_SYSTEM_NAME . "_left"); ?>" src="info.php">
  <frame name="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>" id="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>" src="painel.php">
<noframes>
<body text="#000000">
</body>
</noframes>
</html>