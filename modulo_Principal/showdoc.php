<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

$codPJ = request("var_chavereg");

try{
	$strSQL = "SELECT documento_1, documento_2, documento_3 FROM cad_pj WHERE cod_pj =".$codPJ;
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	</head>
	
	<body bgcolor="#CFCFCF" background="../img/bgFrame_CorGray_collapsed.jpg">
		<center>
		<?php athBeginFloatingBox("250","","<center class='padrao_gde'><b>Documentos</b></center>",CL_CORBAR_GLASS_2); ?>
			 <table width="100%" border="0" cellpadding="0" cellspacing="0">
			  <?php
			  	if(getvalue($objRS,'documento_1') != ''){
					$nomeArquivo = end(explode("_",getvalue($objRS,'documento_1')));
			  ?>
			  	<tr>
					<td>
						<a href="arqdownload.php?arquivo=../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/<?php echo(getvalue($objRS,'documento_1')); ?>&nome=<?php echo($nomeArquivo); ?>"><?php echo($nomeArquivo); ?></a>
					</td>
				</tr>
			  <?php
				}
			  ?>
			   <?php
			  	if(getvalue($objRS,'documento_2') != ''){
					$nomeArquivo = end(explode("_",getvalue($objRS,'documento_2')));
			  ?>
			  	<tr>
					<td>
						<a href="arqdownload.php?arquivo=../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/<?php echo(getvalue($objRS,'documento_2')); ?>&nome=<?php echo($nomeArquivo); ?>"><?php echo($nomeArquivo); ?></a>
					</td>
				</tr>
			  <?php
				}
			  ?>
			   <?php
			  	if(getvalue($objRS,'documento_3') != ''){
					$nomeArquivo = end(explode("_",getvalue($objRS,'documento_3')));
			  ?>
			  	<tr>
					<td>
						<a href="arqdownload.php?arquivo=../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/<?php echo(getvalue($objRS,'documento_3')); ?>&nome=<?php echo($nomeArquivo); ?>"><?php echo($nomeArquivo); ?></a>
					</td>
				</tr>
			  <?php
				}
			  ?>	
			 </table> 
		<?php athEndWhiteBox(); ?>
		</center>
	</body>
</html>