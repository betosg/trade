<!-- DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" //-->
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
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

// Abertura de Conexão com o BD
$objConn = abreDBConn(CFG_DB);
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function gerar(opcao)
{
	strExibirMsg = 0;
	
	if ((document.getElementById("var_identificador").value == "")){
		strExibirMsg = 1;
	}
	
	
	if (strExibirMsg  == 0)	{
	document.formeditalote.submit();
	}
	else {alert('Escolha um identificador para excluir.');}
}

function cancelar() {
	document.location.href = "index.php";	
}
</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("510","none","<b>".getTText("titulo_deleta_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" name="var_dialog" cellpadding="0" cellspacing="0" style="width:490px; border:1px solid #A6A6A6; display:block; background-color:#FFFFFF;">
        <form name="formeditalote" action="STdeletaTituloLotePasso2.php" method="post">
		<tr> 
			<td valign="top" style="text-align:center;" align="center">

				<table cellspacing="0" cellpadding="4" style="width:480px; border:0px solid #F00;" align="center">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><img src="../img/titulo_lote_passo01.gif"></td>
					</tr>					
                    <tr>
						<td align="left" style="padding-left:5px;" colspan="2"><strong><?php echo(getTText("preparacao_deleta_titulo",C_NONE)); ?></strong></td>
					</tr>
					<tr><td height="22" colspan="2"></td></tr>
					<tr>
						<td width="34%" colspan="2"><?php echo(getTText("explicacao_delecao_lote",C_NONE)); ?></td>							
					</tr>
					<tr>							
						<td colspan="2"><hr></td>
					</tr> 
					<tr>
						<td width="34%" align="right" style="padding-right:10px;">* Identificador</td>
						<td  align="left" >
							<select name="var_identificador" id="var_identificador" class="edtext" style="width:150px;">
								<option value=""></option>
								<?php echo(montaCombo($objConn," select distinct identificador_lote, identificador_lote || ' ('|| to_char(sys_dtt_ins,'dd/mm/yyyy') || ')' as info  from fin_conta_pagar_receber  where identificador_lote is not null and  sys_dtt_cancel is null and fin_conta_pagar_receber.situacao = 'aberto' ","identificador_lote","info",'')); ?>									
							</select>
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
										<button onClick="gerar(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
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
