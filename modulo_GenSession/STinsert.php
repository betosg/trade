<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

$strOperacao  = request("var_oper");       // Opera��o a ser realizada
$intCodDado   = request("var_chavereg");   // C�digo chave da p�gina
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou n�o
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade � exibida normalmente.

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos �tens do m�dulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "INS");
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript">
	<!--
		//****** Fun��es de a��o dos bot�es - In�cio ******
		var strLocation = null;
		function ok() {
			strLocation = "STdata.php";
			submeterForm();
		}

		function cancelar() {
			document.location.href = "STdata.php";
		}

		function aplicar() {
			strLocation = "STinsert.php";
			submeterForm();
		}

		function submeterForm() {
			document.formstatic.var_action.value = strLocation;
			document.formstatic.submit();
		}
		//****** Fun��es de a��o dos bot�es - Fim ******
	//-->
	</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","CONFIG (Inser��o)",CL_CORBAR_GLASS_1); ?>
    <table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    <form name="formstatic" action="STsessionexec.php" method="post">
			<input type="hidden" name="var_action" value="">
		<tr><td height="22" style="padding:10px"><strong><?php echo(getTText("rotulo_dialog",C_NONE)); ?></strong></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr bgcolor="#FAFAFA">
						<td width="1%" align="right" valign="top"><strong><?php echo(getTText("indice",C_NONE)); ?>:</strong>&nbsp;</td>
						<td>
							<input type="text" name="var_indice" value="" size="64"><br>
							<span class="comment_med"><?php echo(getTText("indice_desc_ins1",C_NONE) . "<strong>" . CFG_SYSTEM_NAME . "</strong>" . getTText("indice_desc_ins2",C_NONE)); ?>.</span>
						</td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td width="1%" align="right" valign="top"><strong><?php echo(getTText("valor",C_NONE)); ?>:</strong>&nbsp;</td>
						<td>
							<textarea name="var_valor" cols="60" rows="6"></textarea><br>
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
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
										<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
										<button onClick="aplicar(); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
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
<?php $objConn = NULL; ?>