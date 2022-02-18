<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "SEND");
?>
<html>
<head>
<title><?php echo(getTText("send",C_TOUPPER)); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
  _editor_url = "../modulo_HTMLArea/";
  _editor_lang = "en";
</script>

<!-- load the main HTMLArea file, this will take care of loading the CSS and
    other required core scripts. -->
<script type="text/javascript" src="../modulo_HTMLArea/htmlarea.js"></script>

<!-- load the plugins -->
<script type="text/javascript">
<!--
HTMLArea.loadPlugin("FullPage");
HTMLArea.loadPlugin("ContextMenu");
HTMLArea.loadPlugin("ListType");
HTMLArea.loadPlugin("CharacterMap");

var editor = null;

function initEditor() {

  // create an editor for the "ta" textbox
  editor = new HTMLArea("var_mensagem");

  // register the FullPage plugin
  editor.registerPlugin(FullPage);

  // register the ListType plugin
  editor.registerPlugin(ListType);

  // add a contextual menu
  editor.registerPlugin("ContextMenu");

  editor.generate();
  return false;
}

HTMLArea.onload = initEditor;


function callUploader(prFormName, prFieldName, prDir){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}

function submeter(prAction){
	switch(prAction){
		case "ok":
			document.formeditor.default_location.value = "window.close()";
			editor.execCommand("htmlmode");
			document.formeditor.submit();
			document.formeditor.onsubmit();
			break;
		case "cancelar":
			window.close();
			break
		case "aplicar":
			document.formeditor.default_location.value = "msgsend.php";
			editor.execCommand("htmlmode");
			document.formeditor.submit();
			document.formeditor.onsubmit();
			break;
	}
}
//-->
</script>
</head>
<body style="margin:10px 0px 10px 0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" onload="HTMLArea.init();">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER)." (".getTText("send",C_UCWORDS).")",CL_CORBAR_GLASS_1); ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	   <form name="formeditor" action="msgsendexec.php" method="post">
		<input type="hidden" name="default_location" value="">
		<tr><td height="22" style="padding:10px"><b></b></td></tr>
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
				<tr>
					<td width="1%" align="right" valign="top"><?php echo(getTText("para",C_UCWORDS)); ?>:&nbsp;</td>
					<td>
						<input type="text" name="var_para" size="30">
						<br><small class="copyright"><i>&nbsp;&nbsp;<?php echo(getTText("dica_para",C_NONE)); ?></i></small>
					</td>
				</tr>
				<tr bgcolor="#FAFAFA">
					<td width="1%" align="right" valign="top"><?php echo(getTText("assunto",C_UCWORDS)); ?>:&nbsp;</td>
					<td><input type="text" name="var_assunto" size="70"></td>
				</tr>
				<tr>
					<td width="1%" align="right" valign="top"><?php echo(getTText("mensagem",C_UCWORDS)); ?>:&nbsp;</td>
					<td><textarea id="var_mensagem" name="var_mensagem" rows="22" cols="80" style="width:100%"></textarea></td>
				</tr>
				<tr><td height="1" colspan="2" bgcolor="#CCCCCC"></td></tr>
				</tr>
				  <td colspan="2" align="right" style="padding:10px 0px 10px 10px;">
					<button onClick="javascript:submeter('ok');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
					<button onClick="javascript:submeter('cancelar');"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
					<button onClick="javascript:submeter('aplicar');"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
				  </td>
				</tr>
			</table>
		  </td>
		</tr>
	   </form>
	  </table>
	<?php athEndFloatingBox(); ?>
   </td>
 </tr>
</table>
</body>
</html>		