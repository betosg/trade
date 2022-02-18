<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
$strIdUsuario = request("var_id_usuario");

($strIdUsuario != "" && $strIdUsuario != getsession(CFG_SYSTEM_NAME . "_id_usuario")) ? verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), 1, "UPD") : $strIdUsuario = getsession(CFG_SYSTEM_NAME . "_id_usuario");
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script>
		function submeterForm(){
			if(document.formtrocasenha.var_senha.value == document.formtrocasenha.var_conf_senha.value){
				document.formtrocasenha.submit();
			}
			else{
				alert("<?php echo(getTText("senhas_diferentes",C_NONE)); ?>");
			}
		}
		
		function cancelar(){
			(window.opener == null || window.opener == "undefined") ? location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>" : window.close();
		}
	</script>
</head>
<body style="margin:7px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td align="center" valign="top">
			  <form name="formtrocasenha" action="STtrocasenhaexec.php" method="post">
			    <input type="hidden" id="var_id_usuario" name="var_id_usuario" value="<?php echo($strIdUsuario) ?>"> 
				<?php athBeginFloatingBox("450","",getTText("usuario",C_TOUPPER) . " (" . getTText("nova_senha",C_UCWORDS) . ") - <b>" . $strIdUsuario . "</b>",CL_CORBAR_GLASS_1); ?>					
					<table border="0" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" width="100%" style="border:1px #A6A6A6 solid;">
						<tr>
							<td align="center">
								<table border="0" cellpadding="0" cellspacing="0" width="400">
									<tr><td colspan="2" height="10"></td></tr>									
									<tr><td colspan="2"><b><?php echo(getTText("troca_senha_txt",C_NONE)); ?></b></td></tr>
									<tr><td colspan="2" height="10"></td></tr>
									<tr>
										<td align="right" width="1%" nowrap><?php echo(getTText("nova_senha",C_UCWORDS)); ?>:&nbsp;</td>
										<td><input type="password" name="var_senha" size="30" maxlength="50"></td>
									</tr>
									<tr>
										<td align="right" width="1%" nowrap><?php echo(getTText("confirma_senha",C_UCWORDS)); ?>:&nbsp;</td>
										<td><input type="password" name="var_conf_senha" size="30" maxlength="50"></td>
									</tr>
									<tr><td colspan="2" height="7"></td></tr>
									<tr><td colspan="2" height="1" bgcolor="#DBDBDB"></td></tr>
									<tr>
										<td colspan="2" align="right" style="padding:7px 0px 10px 10px;">
											<button onClick="submeterForm(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
											<button onClick="location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>'; return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				<?php athEndFloatingBox(); ?>
			  </form>
			</td>
		</tr>
	</table>
</body>
</html>