<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

/***         FUNÇÕES AUXILIARES - OPCIONAL        ***/
/****************************************************/
function getLineColor(&$prColor) {
	$prColor = (isset($prColor) && $prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
	return $prColor;
}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
			<!--
			
			/****** Funções de ação dos botões - Início ******/
			function ok() {
				submeterForm();
			}

			function cancelar() {
				window.close();
			}

			function submeterForm() {
				document.formstatic.submit();
			}
			/****** Funções de ação dos botões - Fim ******/
			
			//-->
		</script>
	</head>
	<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
			<tr>
				<td align="center" valign="top">
					<?php athBeginFloatingBox("400","none",getTText("lingua",C_TOUPPER) . " (" . getTText("alterar",C_TOLOWER) . ")",CL_CORBAR_GLASS_1); ?>
						<table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="4" width="300">
										<form name="formstatic" action="STchangelangexec.php" method="post">
											<input type="hidden" name="var_action" value="">
										<tr><td height="22" style="padding:10px"><strong><?php echo(getTText("rotulo_dialog_lang",C_NONE)); ?></strong></td></tr>
										<tr> 
											<td align="center" valign="top">
												<table width="300" border="0" cellspacing="0" cellpadding="4">
													<tr bgcolor="<?php echo(getLineColor($strBgColor))?>">
														<td width="1%" align="right" valign="top"><strong><?php echo(getTText("lingua",C_NONE)); ?>:</strong>&nbsp;</td>
														<td>
															<select name="var_lang">
																<option value="ptb"<?php echo((CFG_LANG == "ptb") ? " selected" : "")?>>Português (Brasil)</option>
																<option value="en"<?php echo((CFG_LANG == "en") ? " selected" : "")?>>English</option>
																<option value="es"<?php echo((CFG_LANG == "es") ? " selected" : "")?>>Español</option>
															</select>
														</td>
													</tr>
													<tr align="left">
														<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
													</tr>
													<tr><td colspan="2" class="linedialog"></td></tr>
													<tr>
														<td colspan="2">
															<table border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td width="99%">
																		<!--table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td width="1%"><img src="../img/mensagem_aviso.gif" border="0" hspace="10"></td>
																				<td><!-- Aqui texto de apoio - -></td>
																			</tr>
																		</table-->
																	</td>
																	<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
																		<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
																		<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
																	</td>
																</tr>
															</table>
														</td>
													</tr> 
													</form>
												</table>
											</td>
										</tr>
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