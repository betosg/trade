<?php
include_once("../_database/athdbconn.php");
	
	$sForm = request("htmlarea"); //Replace(Replace(Request.Form("htmlarea"), """", "'"), VbCrLf, "")
	
	$sForm = str_replace("\"", "'", $sForm);
	//$sForm = str_replace(VbCrLf, "", $sForm);
	
	$strTextBoxName = request("var_TextBoxName"); //Server.HTMLEncode()
	$strIndexForm   = request("var_IndexForm"); //Server.HTMLEncode()

?>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<script language="JavaScript">
			function LocalTrim(str)
			{
				while (str.charAt(0) == " ")
				str = str.substr(1,str.length-1);
				
				while (str.charAt(str.length-1) == " ")
				str = str.substr(0,str.length-1);
				
				return str;
			}
			function Carrega()
			{
				window.opener.document.forms[<?php echo($strIndexForm); ?>].<?php echo($strTextBoxName); ?>.value = LocalTrim("<?php echo($sForm); ?>");
				self.close();
			}
		</script>
	</head>
	<body onLoad="javascript:Carrega();"></body>
</html>