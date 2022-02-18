<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_DIR");

$objConn = abreDBconn(CFG_DB);

$strComboUsuarios = montaCombo($objConn,"SELECT cod_usuario, id_usuario, tp_user_refdir FROM sys_usuario ORDER BY tp_user_refdir, id_usuario","cod_usuario","id_usuario","","tp_user_refdir");
//montaCombo($prObjConn, $prSQL, $prValor, $prCampo, $prSearch, $prGroup="")
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css" />
		<script language="javascript" type="text/javascript">
			function submeter(prAction){
				if(document.formcopydireitos.var_de.value != document.formcopydireitos.var_para.value){
					switch(prAction){
						case "Ok": document.formcopydireitos.var_location.value = "data.php";
						break;
						case "Aplicar": document.formcopydireitos.var_location.value = "STcopydireitos.php";
						break;
					}
					document.formcopydireitos.submit();
				} else {
					alert("Atenção! Deve-se escolher dois usuários diferentes!");
				}
			}
		</script>
	</head>
	<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
		 <tr>
		   <td align="center" valign="top">
			<?php athBeginFloatingBox(CFG_DIALOG_WIDTH,"none",getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER) . " (" . getTText("copia_direitos",C_UCWORDS) . ")",CL_CORBAR_GLASS_1); ?>
				<table width="100%" class="kernel_dialog">
					<tr>
						<td align="center" valign="top">
							<table width="<?php echo(CFG_DIALOG_CONTENT_WIDTH); ?>" border="0" cellspacing="0" cellpadding="4">
							  <form name="formcopydireitos" action="STcopydireitosexec.php" method="post">
								<input type="hidden" name="var_location" value="">
								<tr><td colspan="2" align="left"><strong><?php echo(getTText("preencher_dados",C_NONE)); ?></strong></td></tr>
								<tr><td colspan="2" height="10"></td></tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_1); ?>">
									<td class="coluna_label">
										<label for="var_de"><?php echo(getTText("copiar_de",C_UCWORDS)); ?>:</label>
									</td>
									<td class="coluna_valor">
										<select name="var_de">
											<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?></option>
											<?php echo($strComboUsuarios); ?>
										</select>
									</td>
								</tr>
								<tr bgcolor="<?php echo(CL_CORLINHA_2); ?>">
									<td class="coluna_label">
										<label for="var_para"><?php echo(getTText("para",C_UCWORDS)); ?>:</label>
									</td>
									<td class="coluna_valor">
										<select name="var_para">
											<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?></option>
											<?php echo($strComboUsuarios); ?>
										</select>
									</td>
								</tr>
								<tr><td colspan="2"></td></tr>
								<tr><td colspan="2" class="linedialog"></td></tr>
								<tr>
									<td align="right" colspan="2" style="padding:10px 0px 10px 10px;">
										<button onClick="submeter('Ok'); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>									
										<button onClick="location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>'; return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
										<button onClick="submeter('Aplicar'); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
									</td>
								</tr>
							  </form>
							</table>
						</td>
					</tr>
				</table>
			<?php athEndFloatingBox(); ?>
			</td>
		 </tr>
		</table>
	</body>
</html>