<?php
include_once("../_database/athdbconn.php");

error_reporting(E_ALL);

$intCodDado = request("var_chavereg");
$strAction  = request("var_action"); 
$strIndice  = request("var_indice"); 
$strValor   = request("var_valor");

$arrArquivo = file("../_database/STconfiginc.php");

if($strValor != "") {
	$arrArquivo[$intCodDado-1] = ((strpos($strIndice,"@") === 0) ? "@" : "") . "define(\"" . str_replace("@","",$strIndice) . "\"," . $strValor . ");" . chr(10);
}
else {
	mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
	die();
}

$resArquivo = fopen("../_database/STconfiginc.php","wb");

foreach($arrArquivo as $strLine) {
	fwrite($resArquivo,$strLine);
}

fclose($resArquivo);

//header("Location:" . $strAction);
?>
<script>
	<?php if($strIndice == "@CFG_SYSTEM_THEME") { ?> parent.frames[0].location.reload(); <?php } ?>
	location.href = "<?php echo($strAction); ?>";
</script>