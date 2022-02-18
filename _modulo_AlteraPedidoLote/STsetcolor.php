<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "UPD");

$intCodDado = request("var_chavereg");
?>
<html>
<head>
 <title><?php echo(CFG_SYSTEM_TITLE); ?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
 <style> .moldura { border:1px dashed #CCCCCC; padding:10px; cursor:pointer; }  </style>
 <script>
	var strLocation = null;
	function ok() {
		strLocation = "STdata.php";
		submeterForm();
	}

	function cancelar() {
		document.location.href = "STdata.php";
	}

	function submeterForm() {
		document.formstatic.var_action.value = strLocation;
		document.formstatic.submit();
	}
 </script>
</head>
<body style="margin:10px 0px 10px 0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("config",C_TOUPPER)." (".getTText(strtolower("selecionar_fundo"),C_UCWORDS).")",CL_CORBAR_GLASS_1); ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	   <form name="formstatic" action="STupdateexec.php" method="post">
		<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>">
		<input type="hidden" name="var_action" value="">
		<input type="hidden" name="var_indice" value="@CFG_SYSTEM_THEME">
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
				<tr>
					<td valign="top" align="right"><strong><?php echo(getTText("selecione",C_UCWORDS)); ?></strong>:&nbsp;</td>
					<td>
						<?php
							if($resHandle = opendir("../img/")){
								$intI = 1;
								echo("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
									  <tr>");
								
								while(($strFile = readdir($resHandle)) !== false){
									if(preg_match("/^bgFrame(.*)thumb\.(.*)$/",$strFile)){
										$strTitle = preg_replace("/^bgFrame_cor|^bgFrame_img|_thumb\.(.*)/i","",$strFile);
										$strTypeName = preg_replace("/^bgFrame_|_thumb\.(.*)/i","",$strFile);
										
										(strtolower(CFG_SYSTEM_THEME) == strtolower($strTypeName)) ? $strChecked = "checked" : $strChecked = "";
										
										echo("<td align=\"center\">
												<label for=\"var_valor\">
												<img src=\"../img/" . $strFile . "\" title=\"" . strtolower($strTitle) . "\" onClick=\"//AbreJanelaPAGE('viewimg.php?type=" . $strTypeName . "',10,10);\">&nbsp;&nbsp;<br>
												</label>
											  	<input type=\"radio\" name=\"var_valor\" value='\"" . $strTypeName . "\"' class=\"inputclean\" " . $strChecked . ">
											</td>");
										
										if($intI % 4 == 0){ echo("</tr><tr>"); }
										$intI++;
									}
								}
								
								while($intI % 4 == 0){
									echo("<td>&nbsp;</td>");
									if($intI % 4 == 0){ echo("</tr><tr>"); }
									$intI++;
								}
								echo("</table>");
								
							}
						?>
					</td>
				</tr>
				<tr><td height="5" colspan="2"></td></tr>
				<tr><td colspan="2" class="linedialog"></td></tr>
				<tr>
					<td align="right" colspan="2" style="padding:10px 0px 10px 10px;">
						<button onClick="ok();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="cancelar();"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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