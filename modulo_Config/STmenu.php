<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
 
 $strPopulate = request("var_populate");     //Flag de verificação se necessita popular o session ou não
 if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
 
 $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
 verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
 
 $objConn = abreDBConn(CFG_DB);
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script>
		function scrolling(e) { 
				if(!e) { 
					e = window.event; 
				} 
				
				if(e.keyCode == 38){
					window.scrollBy(0,-10);
				}
				else if(e.keyCode == 40){
					window.scrollBy(0,10);
				}
			}
			
			document.onkeydown = scrolling;
			var submitForm = '<?php echo request('var_redirect') ?>';
			
			window.onload = function(){
				if(submitForm == ''){
					document.formeditor_000.submit();
				}
			}
	</script>
</head>
<!--#CFCFCF-->
<body style="margin:0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') { ?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_filtro.jpg" <?php } ?>>
 <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
	<td width="24" valign="top"><img id="img_collapse" src="../img/collapse_open.gif" onClick="swapwidth(250,'<?php echo(CFG_SYSTEM_THEME); ?>');" style="cursor:pointer"></td>
	<td valign="top" style="padding-top:10px;">
		<?php
		 include_once("_STincludemenu.php");
		 /*
		 echo("<br><br>");
		 include_once("_includebookmark.php");
		 */
		?>
	</td>
  </tr>
 </table>
</body>
</html>
<?php $objConn = NULL; ?>