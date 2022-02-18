<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPedido = request("var_chavereg");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = "";
if ($intCodPedido == "") $strMsg = "Informar código do pedido";

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

$objConn = abreDBConn(CFG_DB);

$strBGColor1 = CL_CORLINHA_1;
$strBGColor2 = CL_CORLINHA_2;

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function verifica(){
	var var_msg = "";
	
	/*
	if ((!document.formeditor.var_opcao_a.checked) && 
	    (!document.formeditor.var_opcao_b.checked) && 
		(!document.formeditor.var_opcao_c.checked)) var_msg += "\nSelecionar para quem será gerado pedido";
	if ((document.formeditor.var_opcao_a.checked) && (document.formeditor.var_cod_pj.value == "")) var_msg += "\nInformar código da empresa";
	if (document.formeditor.var_cod_produto.value == "") var_msg += "\nCobrança";
	if (document.formeditor.var_gerar_b.checked) {
		if (document.formeditor.var_dt_vcto.value == "") var_msg += "\nVencimento";
		if (document.formeditor.var_cod_conta.value == "") var_msg += "\nConta banco";
		if (document.formeditor.var_cod_plano_conta.value == "") var_msg += "\nPlano de Contas";
		if (document.formeditor.var_cod_centro_custo.value == "") var_msg += "\nCentro de Custos";
		if (document.formeditor.var_cod_cfg_boleto.value == "") var_msg += "\nBoleto";
		if (document.formeditor.var_historico.value == "") var_msg += "\nHistórico";
	}
	*/
	if (var_msg == "")
		document.formeditor.submit();
	else
		alert("Favor verificar campos:\n" + var_msg);
}

function cancelar(){
	window.history.back();
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","PEDIDO ( Deleção )",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
		<form name="formeditor" action="STDelPedidoexec.php" method="post">
		<input type="hidden" name="var_chavereg" value="<?php echo $intCodPedido; ?>">
		<tr><td height="22" style="padding:10px"><b><?php echo getTText("rotulo_dialog",C_NONE); ?></b></td></tr>
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
				<tr bgcolor="<?php echo($strBGColor1)?>">
					<td align="right" width="25%"><label for="var_cod_pedido"><strong><?php echo(getTText("cod_pedido",C_NONE)); ?>:</strong></label></td>
					<td width="99%"><?php echo $intCodPedido; ?></td>
				</tr>
				<tr bgcolor="<?php echo($strBGColor2)?>">
					<td align="right" width="25%"><label for="var_obs"><strong>*<?php echo(getTText("obs_motivo",C_NONE)); ?>:</strong></label></td>
					<td width="99%"><textarea name="var_obs_delecao" id="var_obs_delecao" cols="60" rows="7"></textarea>
					<br><span class="comment_peq"><?php echo(getTText("msg_obs_delecao",C_NONE))?></span></td>
				</tr>
				<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px">
					<span class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></span>
				</td></tr>
				<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
				<tr>
					<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
							<tr>
								<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_aviso.gif"></td>
								<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_delete_txt",C_NONE))?></td>
								<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
									<button onClick="verifica(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
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
<?php
$objConn = NULL;
?>
