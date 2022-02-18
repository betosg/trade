<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
 
$objConn = abreDBConn(CFG_DB);
 
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
 
$intCodigo 	  = request("var_chavereg");
$strCampoRet  = request("var_fieldname");
$strDialogGrp = request("var_dialog_grp");
$strRelatTitle = request("var_relat_title");
 
try{
	$strSQL = " SELECT nome, search_dbcamporet, search_dbcampolabel, search_query, search_label, search_acao
					FROM sys_descritor_campos_edicao
					WHERE cod_app = '" . getsession($strSesPfx . "_chave_app") . "'
						AND dtt_inativo IS NULL 
						AND cod_descr_campo = " . $intCodigo;
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
 
foreach($objResult as $objRS){
	$strNome       = getValue($objRS,"nome");
	$strSQLSearch  = getValue($objRS,"search_query");
	$strDBCampoRet = getValue($objRS,"search_dbcamporet");
	$strDBCampoLbl = getValue($objRS,"search_dbcampolabel");
	$strLabel      = getValue($objRS,"search_label");
	$strAcao       = getValue($objRS,"search_acao");
}
 
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script>
			function submeteForm(){
				var strSQL      = "<?php echo(preg_replace("/\n|\r|\t/"," ",$strSQLSearch)); ?>";
				var strDado     = document.formbusca.var_dado.value;
				var strSQLField = document.formbusca.var_strparam;
				
				if(strDado != ""){
					strSQL = strSQL.replace(/\?/g,strDado);
					
					strSQLField.value = strSQL;
					document.formbusca.submit();
				}
				else{
					alert("<?php echo(getTText("preencher_campo",C_NONE)); ?>");
				}
			}
		</script>
	</head>
	<body style="margin:10px 0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_filtro.jpg">
		<center>
		<?php athBeginFloatingBox("205","",getTText("filtrar_por",C_UCWORDS) . "...",CL_CORBAR_GLASS_2); ?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center">
						<table border="0" cellpadding="0" cellspacing="0" width="185">
							<tr>
								<form name="formbusca" action="resultaslwdetail.php" method="post" target="frm_resulaslw_detail" onSubmit="submeteForm();">
								<td align="left">	
									<div style="padding:5px;">
										<input type="hidden" name="var_strparam" value="">
										<input type="hidden" name="var_acaogrid" value="<?php echo($strAcao); ?>">
										<input type="hidden" name="var_nome"     value="<?php echo($strNome); ?>">
										<input type="hidden" name="var_camporet" value="<?php echo($strCampoRet); ?>">
										<input type="hidden" name="var_dbcamporet" value="<?php echo($strDBCampoRet); ?>">
										<input type="hidden" name="var_dbcampolbl" value="<?php echo($strDBCampoLbl); ?>">
										<input type="hidden" name="var_dialog_grp" value="<?php echo($strDialogGrp); ?>">
										<input type="hidden" name="var_relat_title" value="<?php echo($strRelatTitle); ?>">
										<label><?php echo(getTText($strLabel,C_UCWORDS)); ?>:</label>&nbsp;&nbsp;<br>
										<input type="text" name="var_dado" size="35">&nbsp;&nbsp;<br>
									</div>
								</td>
								</form>
							</tr>
							<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
							<tr><td align="right" style="padding:5px 0px;"><button onClick="submeteForm();" align="baseline"><?php echo(getTText("ok",C_UCWORDS)); ?></button></td></tr>
						</table>
					</td>
				</tr>
			</table>
		<?php athEndFloatingBox(); ?>
		</center>
	</body>
</html>
<?php
 $objResult->closeCursor();
 $objConn = NULL;
?>