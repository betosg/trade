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

$arrArquivo = file("../_database/STconfiginc.php");

$intIndex = $intCodDado - 1;
$strLine  = $arrArquivo[$intIndex];

$strLine  = preg_replace("/define\(|\)\;(.*)/i","",$strLine);
$arrLine  = explode(",",$strLine);

$strIndice = trim(str_replace("\"","",$arrLine[0]));
$strValor  = trim($arrLine[1]);

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "DEL");

?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
<!--
//****** Fun��es de a��o dos bot�es - In�cio ******
function ok() {
	document.formstatic.var_action.value = "STdata.php";
	document.formstatic.submit();
}

function cancelar() {
	document.location.href = "STdata.php";
}

function aplicar() {
	/* N�o Utilizada */
}
//****** Fun��es de a��o dos bot�es - Fim ******
//-->
</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","CONFIG (Dele��o)",CL_CORBAR_GLASS_1); ?>
    <table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    <form name="formstatic" action="STdeleteexec.php" method="post">
			<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>">
			<input type="hidden" name="var_action" value="">
		<tr><td height="22" style="padding:10px"><strong>Preencha os campos abaixo</strong></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr bgcolor="#FFFFFF">
						<td width="1%" align="right"><strong>*Linha:</strong>&nbsp;</td>
						<td><?php echo($intCodDado); ?></td>
					</tr>
					<tr bgcolor="#FAFAFA">
						<td width="1%" align="right"><strong>*�ndice:</strong>&nbsp;</td>
						<td><?php echo($strIndice); ?></td>
					</tr>
					<tr bgcolor="#FFFFFF">
						<td width="1%" align="right" valign="top"><strong>*Valor:</strong>&nbsp;</td>
						<td><?php echo($strValor); ?></td>
					</tr>
					<tr align="left">
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
					</tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="padding:10px 0px 10px 10px;">
										  <table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="1%"><img src="../img/mensagem_aviso.gif" border="0" hspace="10"></td>
												<td>
													ATEN��O! Voc� est� prestes a remover o registro que est� sendo visualizado. 
													Para confirmar essa dele��o clique no bot�o [ok], para desistir clique em [cancelar] 
												</td>
											</tr>
										  </table>
									</td>
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
										<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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