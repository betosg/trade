<?php
include_once("../_database/athdbconn.php");
	
    $strTextBoxName = request("var_TextBoxName"); //Server.HTMLEncode
	$strIndexForm   = request("var_IndexForm");
?>
<html>
  <head>
    <title>Editor de HTML</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script type="text/javascript">
      _editor_url = "htmlarea/";
      _editor_lang = "pt_br";
    </script>
    <script type="text/javascript" src="htmlarea/htmlarea.js"></script>
    <script type="text/javascript" src="htmlarea/dialog.js"></script>
    <script type="text/javascript" src="htmlarea/popupwin.js"></script>
    <!-- <script type="text/javascript" src="htmlarea/popupdiv.js"></script> -->
    <script type="text/javascript">
      HTMLArea.loadPlugin("ContextMenu");
      //HTMLArea.loadPlugin("FullPage");
      //HTMLArea.loadPlugin("SpellChecker");
      HTMLArea.loadPlugin("TableOperations");

      var editor = null;
	  
	  function setValue()
	  {
	 	formhtmlarea.htmlarea.value = window.opener.document.forms[<?php echo($strIndexForm); ?>].<?php echo($strTextBoxName); ?>.value;
	  }
	  
      function initDocument() {
        editor = new HTMLArea("editor");
        editor.registerPlugin(ContextMenu);
        //editor.registerPlugin(FullPage);
        //editor.registerPlugin(SpellChecker);
        editor.registerPlugin(TableOperations);
        editor.generate();
		setValue();
      }
    </script>
  <link rel="stylesheet" href="htmlarea/htmlarea.css" type="text/css">
  </head>
  <body onLoad="initDocument()" topmargin="0" leftmargin="0">
  <form name="formhtmlarea" action="sampleposteddata.php" method="post" target="">
	<input type="hidden" name="var_TextBoxName" value="<?php echo($strTextBoxName); ?>">
	<input type="hidden" name="var_IndexForm" value="<?php echo($strIndexForm); ?>">    
    <textarea id="editor" name="htmlarea" style="height:33em;"></textarea>
    <hr/>
	<div align="right" style="padding-right:15px;">
	    <!--input type="image" src="../img/bt_enviar.gif"-->
	</div>
  </form>
  </body>
</html>
