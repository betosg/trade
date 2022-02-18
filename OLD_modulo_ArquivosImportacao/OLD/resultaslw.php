<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 
 $intCodigo    = request("var_coditem");
 $strFieldName = request("var_fieldname");
 $strDialogGrp = request("var_dialog_grp");
 $strRelatTitle = request("var_relat_title");
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE . " - " . getTText("relatorio_aslw",C_UCWORDS)); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset cols="225,*" rows="*" frameborder="no" border="0" framespacing="0">
  <frame name="frm_resulaslw_header" src="resultaslwfiltro.php?var_chavereg=<?php echo($intCodigo); ?>&var_fieldname=<?php echo($strFieldName); ?>&var_dialog_grp=<?php echo($strDialogGrp); ?>&var_relat_title=<?php echo($strRelatTitle); ?>" scrolling="no">
  <frame name="frm_resulaslw_detail" src="resultaslwdetail.php">
</frameset>
</html>