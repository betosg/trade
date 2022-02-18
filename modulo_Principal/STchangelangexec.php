<?php 
include_once("../_database/athdbconn.php");

$strLang = request("var_lang");

setsession(CFG_SYSTEM_NAME . "_lang",$strLang);
?>
<script type="text/javascript">
	window.opener.document.getElementById("lingua").innerHTML = "<?php echo($strLang); ?>".toUpperCase();
	window.close();
</script>