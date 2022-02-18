<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 $strSystem     = (request("var_db") == "") ? getsession(CFG_SYSTEM_NAME."_db_name") : request("var_db");
 $intCodigo     =  request("var_coditem");
 $strFieldName  =  request("var_fieldname");
 $strDialogGrp  =  request("var_dialog_grp");
 $strRelatTitle =  request("var_relat_title");
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE . " - " . getTText("relatorio_aslw",C_UCWORDS)); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset cols="225,*" rows="*" frameborder="no" border="0" framespacing="0">
  <frame name="frm_resulaslw_header" src="STresultaslwfiltro.php?var_chavereg=<?php echo($intCodigo); ?>&var_fieldname=<?php echo($strFieldName); ?>&var_dialog_grp=<?php echo($strDialogGrp); ?>&var_relat_title=<?php echo($strRelatTitle); ?>&var_db=<?php echo($strSystem);?>" scrolling="no">
  <frame name="frm_resulaslw_detail" src="STresultaslwdetail.php?var_db=<?php echo($strSystem);?>">
</frameset><noframes></noframes>
</html>