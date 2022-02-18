<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");       // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$strPopulate = "yes";
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
// verificação de ACESSO
// carrega o prefixo das sessions
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
// verificação de acesso do usuário corrente
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function gerar(){
	var var_msg = "";
	
	//if ((document.formfatura.var_dt_ini.value == '') || (document.formfatura.var_dt_fim.value == '')) var_msg = "Informe intervalo de datas";
	//if ((document.formfatura.var_dt_ini.value != '') && (document.formfatura.var_dt_fim.value == '')) var_msg = "Informe intervalo de datas corretamente";
	
	if (var_msg != '') {
		alert(var_msg);
	}
	else {
		document.formfatura.submit();
	}
}

</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_gerar_faturamento",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
        <form name="formfatura" action="STgerafatPasso1exec.php" method="post">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="3"><img src="../img/faturamento_passo01.gif"></td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="3"><?php echo(getTText("preparacao_faturamento",C_NONE)); ?></td>
					</tr>
					<tr><td height="22" colspan="3"></td></tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"></td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2">
						<?php echo(getTText("pedidos_dentro_periodo",C_NONE)); ?>&nbsp;<input type="text" name="var_ped_ini" id="var_ped_ini" style="width:70px" maxlength="10" value="" onKeyPress="return validateNumKey(event);">&nbsp;<?php echo(getTText("a",C_NONE)); ?>&nbsp;<input type="text" name="var_ped_fim" id="var_ped_fim" style="width:70px" maxlength="10" value="" onKeyPress="return validateNumKey(event);">
						</td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr align="left">
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
					</tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										<button onClick="proximo(); return false;"><?php echo(getTText("proximo",C_UCWORDS)); ?></button>
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
$objConn = NULL;
?>