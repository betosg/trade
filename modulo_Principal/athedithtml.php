<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");

	// REQUESTS
	$intCHAVE  = request("var_chavereg");
 	$strCHNOME = request("var_chavename");
 	$strTABELA = request("var_table");
 	$strCAMPO  = request("var_field");
 	$strVALOR  = request("var_value");
 	$strLOCAL  = request("var_location");
	$intCODRESIZE = (request("var_cod_resize")=="") ? $intCHAVE : request("var_cod_resize");
	
	// CARREGA PREFIX DOS SESSIONS
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
  body {font:11px verdana,arial,sans-serif;background-attachment:fixed; }
  a {color:#0000cc;font-size:xx-small; }
</style>
<!-- STEP 1: Include the Editor js file -->
<script type="text/javascript" language="javascript" src="../_class/Editor/scripts/innovaeditor.js"></script>
</head>
<body bgcolor="#FFFFFF" <?php echo((request("var_cod_resize") == "") ? 'background="../img/bgFrame_'.CFG_SYSTEM_THEME.'_main.jpg"' : "");?>>
	<form action="../_database/athupdatetodb.php" id="formeditor" method="post" >
	<input type="hidden" name="DEFAULT_TABLE" 		value="<?php echo($strTABELA);?>">
  	<input type="hidden" name="DEFAULT_DB" 			value="<?php echo(CFG_DB);?>">
	<input type="hidden" name="FIELD_PREFIX"		value="DBVAR_">
	<input type="hidden" name="RECORD_KEY_NAME"		value="<?php echo($strCHNOME);?>">
	<input type="hidden" name="RECORD_KEY_VALUE"	value="<?php echo($intCHAVE);?>">
	<input type="hidden" name="DEFAULT_LOCATION"	value="<?php echo($strLOCAL);?>">
	<table width="100%" height="580" cellpadding="2" cellspacing="2" border="0">
	<tr>
    	<td align="center" valign="bottom">
	 		<div style="width:700px;padding-right:30px;font-size:8px;height:100%;text-align:right;">
	   		<?php echo(strtoupper(CFG_DB." / ".$strTABELA." / CODIGO ".$intCHAVE." / <b>".$strCAMPO."</b>"));?>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center"> 
	    <textarea id="DBVAR_STR_<?php echo($strCAMPO);?>" name="DBVAR_STR_<?php echo($strCAMPO);?>" rows=4 cols=25><?php echo(html_entity_decode($strVALOR));?></textarea>
		<script type="text/javascript" language="javascript">
		// STEP 2: Replace the textarea (txtContent)
			var oEdit1 = new InnovaEditor("oEdit1");
			oEdit1.width		= 690;
			oEdit1.height		= 460;
			oEdit1.toolbarMode	= 1; // Set toolbar mode: 0: standard, 1: tab toolbar, 2: group toolbar 
			oEdit1.mode			= "HTMLBody"; //Editing mode. Possible values: "HTMLBody" (default), "XHTMLBody", "HTML", "XHTML"
			oEdit1.css		    = "../_css/default.css"; //Specify external css file here
			oEdit1.cmdAssetManager = "modalDialogShow('/tradeunion/_tradeunion/_class/Editor/assetmanager/assetmanager.php',680,510);"; //Command to open the Asset Manager add-on.
			
			//oEdit1.cmdInternalLink = "modelessDialogShow('links.htm',365,270)"; //Command to open your custom link lookup page.
			//oEdit1.cmdCustomObject = "modelessDialogShow('objects.htm',365,270)"; //Command to open your custom content lookup page.
	
			//------------------------
			//useDIV useBR Line Break |
			//------------------------
			//True	 False	<DIV>	  |
			//False	 False	<P>		  |	
			//True	 True 	<BR>	  | 
			//False	 True 	<BR>	  | 
			//------------------------
			oEdit1.useDIV			 = true;
			oEdit1.useBR			 = true;
			oEdit1.mode="XHTMLBody"; //Editing mode. Possible values: "HTMLBody" (default), "XHTMLBody", "HTML", "XHTML"
	
			// Parecemm não funcionar :-P ------------------------------------------------------------------------
			oEdit1.cmdPublishingPath = "http//www.athcsm4.com.br/<%=local_CFG_PATH%>/"
			oEdit1.btnSpellCheck	 = false;
			oEdit1.bReturnAbsolute	 = false; 
			// ----------------------------------------------------------------------------------------------------
			
	
			/*
			// ADDING CUSTOM BUTTONS
			oEdit1.arrCustomButtons = [
			["CustomName1","alert('Command 1 here.')","Caption 1 here","btnCustom1.gif"],
			["CustomName2","alert(\"Command '2' here.\")","Caption 2 here","btnCustom2.gif"],
			["CustomName3","alert('Command \"3\" here.')","Caption 3 here","btnCustom3.gif"]
			]
		 
			// RECONFIGURE TOOLBAR BUTTONS
			oEdit1.tabs=[
			["tabHome", "Home", ["grpEdit", "grpFont", "grpPara", "grpInsert", "grpTables"]],
			["tabStyle", "Objects", ["grpResource", "grpMedia", "grpMisc", "grpCustom"]]
			];
		 
			oEdit1.groups=[
			["grpEdit",     "", ["Undo", "Redo", "FullScreen", "RemoveFormat", "BRK", "Cut", "Copy", "Paste", "PasteWord", "PasteText", "XHTMLSource"]],
			["grpFont",     "", ["FontName", "FontSize", "Styles", "BRK", "Bold", "Italic", "Underline", "Strikethrough", "Superscript", "ForeColor", "BackColor"]],
			["grpPara",     "", ["Paragraph", "Indent", "Outdent", "StyleAndFormatting", "BRK", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyFull", "Numbering", "Bullets"]],
			["grpInsert",   "", ["Hyperlink", "Bookmark", "BRK", "Image"]],
			["grpTables",   "", ["Table", "BRK", "Guidelines"]],
			["grpResource", "", ["InternalLink", "BRK", "CustomObject"]],
			["grpMedia",    "", ["Media", "BRK", "Flash"]],
			["grpMisc",     "", ["Characters", "Line", "Absolute", "BRK", "CustomTag"]],
			["grpCustom",   "", ["CustomName1","CustomName2", "BRK","CustomName3"]]
			];
		 
			// OTHER SETTINGS
			oEdit1.arrCustomTag=[["First Name","{%first_name%}"],["Last Name","{%last_name%}"],["Email","{%email%}"]];//Define custom tag selection
			oEdit1.customColors=["#ff4500","#ffa500","#808000","#4682b4","#1e90ff","#9400d3","#ff1493","#a9a9a9"];//predefined custom colors
			*/
			
	
			oEdit1.REPLACE("DBVAR_STR_<?php echo($strCAMPO);?>");	//Specify the id of the textarea here
	  	</script>
		</td>
	</tr>
	<tr>
		<td align="center"> 
		  <div style="width:700px; padding-right:30px; font-size:8px; height:30px; text-align:right;">
			  <input type="submit" value="Salvar">
			  <input type="button" value="Cancelar" onclick="history.back();">
		  </div>
		</td>
	</tr>
</table>
</form>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCODRESIZE);?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>