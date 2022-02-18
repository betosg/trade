<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$intCodDado = request("var_chavereg");   // Código chave da página

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), 'VIE');

$objConn = abreDBConn(CFG_DB);

try{
	$strSQL = "SELECT cod_pj, razao_social, cnpj, sys_usr_ins, sys_dtt_ins FROM cad_pj WHERE cod_pj =" . $intCodDado;
    $objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
} 
$objRS = $objResult->fetch();
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  	<!-- <tr><td valign="top" height="35%"><img src="../img/system_logo.gif" border="0" hspace="10" vspace="10"><td></tr> -->
	  		<tr>
				<td align="center" valign="top">
				<?php athBeginFloatingBox("600","","PJ (Seleção)",CL_CORBAR_GLASS_1); ?>
					<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
					<form name="formeditor" action="STactivateexec.php" method="post">
						<input type="hidden" name="var_cod_pj" value="<?php echo(getValue($objRS,"cod_pj"));?>">
						<input type="hidden" name="var_razao_social" value="<?php echo(getValue($objRS,"razao_social"));?>">
						<tr>
						  <td align="center" valign="top"> 
							  <table width="570" cellpadding="0" cellspacing="0">
								<tr><td height="3" colspan="2"></td></tr>	
								<tr>
									<td colspan="3" nowrap="nowrap">
										<div style="padding-left:10px;"></div>
								  </td>
								</tr>
								<tr><td height="5" colspan="2"></td></tr>			
								<tr bgcolor="#FAFAFA" height="22px">
									<td nowrap="nowrap" align="right"><b>Código: </b></td>
									<td width="432"><div style="padding-left:10px"><?php echo(getValue($objRS,"cod_pj"));?></div></td>
								</tr>
								<tr bgcolor="#FFFFFF" height="22px">
									<td nowrap="nowrap" align="right"><b>Razão Social: </b></td>
									<td><div style="padding-left:10px"><?php echo(getValue($objRS,"razao_social"));?></div></td>
								</tr>
								<tr bgcolor="#FAFAFA" height="22px">
									<td nowrap="nowrap" align="right"><b>CNPJ: </b></td>
									<td><div style="padding-left:10px"><?php echo(getValue($objRS,"cnpj"));?></div></td>
								</tr>							
								<tr bgcolor="#FFFFFF" height="22px">
									<td align="right"><b>Usuário que Criou: </b></td>
									<td><div style="padding-left:10px"><?php echo(getValue($objRS,"sys_usr_ins"));?></div></td>
								</tr>
								<tr bgcolor="#FAFAFA" height="22px">
									<td align="right"><b>Data que foi Criado: </b></td>
									<td><div style="padding-left:10px"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"), true));?></div></td>
								</tr>										
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
								<tr><td height="5" colspan="2"></td></tr>
								<tr> 
								  <td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
									  <tr>
										<td width="11%" align="right" style="padding-bottom:10px;"><img src="../img/mensagem_info.gif" border="0"></td>
										<td width="54%" align="left" style="padding-bottom:10px; padding-left:20px; padding-right:20px;">
											<?php echo(getTText("aviso_selecionar_txt",C_NONE));?>									  	</td>

										<td width="35%" colspan="2" align="right" style="padding-bottom:10px;">
											<button onClick="document.formeditor.submit(); return false;"><?php echo(getTText("ok",C_NONE)); ?></button>	
											<button onClick="javascript:history.back(); return false;"><?php echo( getTText("cancelar",C_NONE)); ?></button>
									  	</td>
									  </tr>
									</table>
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
<?php 
$objResult->closeCursor();
$objConn = NULL;
?>