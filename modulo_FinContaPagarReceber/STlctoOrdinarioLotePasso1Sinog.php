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

$objConn = abreDBConn(CFG_DB); // ABERTURA DO BANCO DE DADOS

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function ok(){
	var var_msg = "";
	
	if (document.formeditor.var_situacao.value == '') var_msg += "Informe situação\n";
	//if (document.formeditor.var_cod_segmento.value == '') var_msg += "Informe segmento\n";
	//if (document.formeditor.var_categoria.value == '') var_msg += "Informe categoria\n";
	//if (document.formeditor.var_cod_categoria_extra.value == '') var_msg += "Informe categoria extra\n";
	if ((document.formeditor.var_dt_vcto_ini.value == '') || (document.formeditor.var_dt_vcto_fim.value == '')) var_msg += "Informe período de vencimento\n";
	
	if ((document.formeditor.var_opcao_pgto_1.checked == false) && (document.formeditor.var_opcao_pgto_2.checked == false) && (document.formeditor.var_opcao_pgto_3.checked == false)) {
		var_msg += "Selecione opções de pagamento\n";
	}
	else {
		if (document.formeditor.var_opcao_pgto_2.checked && (document.formeditor.var_vlr_pgto.value == "")) var_msg += "Informe valor se escolheu pagar com o valor digitado\n";
	}
	
	if (var_msg != '') {
		alert(var_msg);
	}
	else {
		document.formeditor.submit();
	}
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
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_lcto_ordinario_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
        <form name="formeditor" action="STlctoOrdinarioLotePasso2Sinog.php" method="post">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><img src="../img/lcto_ordinario_lote_passos01.png"></td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><?php echo(getTText("preparacao_lcto_ordinario_lote_passo01",C_NONE)); ?></td>
					</tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="100" align="right"></td>
                        <td width="450" align="left" class="destaque_gde"><strong><?php echo(getTText("selecao",C_TOUPPER));?></strong></td>
                    </tr>
					<tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong>* Receita / Despesa:</strong></td>
						<td align="left" style="padding-left:5px;">
							<select name="var_situacao" id="var_situacao" size="1" style="width:80px;">								
								<option value="receita" selected="selected">RECEITA</option>
								<option value="despesa" selected="selected">DESPESA</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong>*<?php echo(getTText("situacao",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
							<select name="var_situacao" id="var_situacao" size="1" style="width:80px;">
								<option value="aberto" selected="selected">ABERTO</option>
							</select>
						</td>
					</tr>
					<!--tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong>*<?php echo(getTText("segmento",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
                            <select id="var_cod_segmento" name="var_cod_segmento" size="1" style="width:210px; vertical-align:middle;">
                                <option value="" selected="selected"></option>
                                <?php echo(montaCombo($objConn, " SELECT cod_segmento, nome FROM cad_segmento ORDER BY nome ", "cod_segmento", "nome", "", "")); ?>
                            </select>
						</td>
					</tr//-->
					<!--tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong>*<?php echo(getTText("categoria",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
							<select id="var_categoria" name="var_categoria" size="1" style="width:160px; vertical-align:middle;">
                                <option value="" selected="selected"></option>
                                <?php echo(montaCombo($objConn, " SELECT DISTINCT categoria FROM cad_pj WHERE categoria IS NOT NULL AND categoria <> '' ORDER BY categoria ", "categoria", "categoria", "", "")); ?>
                            </select>
						</td>
					</tr//-->
					<!--tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong>*<?php echo(getTText("categoria_extra",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
                            <select id="var_cod_categoria_extra" name="var_cod_categoria_extra" size="1" style="width:160px; vertical-align:middle;">
                                <option value="" selected="selected"></option>
                                <?php echo(montaCombo($objConn, " SELECT cod_categoria, nome FROM cad_categoria ORDER BY nome ", "cod_categoria", "nome", "", "")); ?>
                            </select>
						</td>
					</tr//-->

					<tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
                            <select id="var_cod_plano_conta" name="var_cod_plano_conta" size="1" style="width:250px; vertical-align:middle;">
                                <option value="" selected="selected"></option>
                                <?php echo(montaCombo($objConn," SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo","")); ?>
                            </select>
						</td>
					</tr>


					<tr>
						<td align="left" style="padding-left:5px;" nowrap="nowrap"><strong><?php echo(getTText("tipo_documento",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
                            <select id="var_tipo_documento" name="var_tipo_documento" size="1" style="width:160px; vertical-align:middle;">
                                <option value="" selected="selected"></option>
                                <?php echo(montaCombo($objConn, " SELECT DISTINCT tipo_documento, tipo_documento FROM fin_conta_pagar_receber ORDER BY tipo_documento ", "tipo_documento", "tipo_documento", "", "")); ?>
                            </select>
						</td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;"><strong>*<?php echo(getTText("vcto",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;"><?php echo(getTText("de",C_NONE)); ?>&nbsp;<input name="var_dt_vcto_ini" id="var_dt_vcto_ini" value="" style="width:80px;" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />&nbsp;<?php echo(getTText("a",C_NONE)); ?>&nbsp;<input name="var_dt_vcto_fim" id="var_dt_vcto_fim" value="" style="width:80px;" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" /></td>
					</tr>
                    <tr bgcolor="#FFFFFF">
                        <td align="right"></td>
                        <td align="left" class="destaque_gde"><strong><?php echo(getTText("registrar",C_TOUPPER));?></strong></td>
                    </tr>
					<tr>
						<td align="left" style="padding-left:5px;"><strong><?php echo(getTText("pgto",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;"><input name="var_dt_pgto" id="var_dt_pgto" value="" style="width:80px;" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />&nbsp;<?php echo(getTText("msg_campo_vazio_dt_pgto",C_NONE)); ?></td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" valign="top"><strong><?php echo(getTText("pagar",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                        	<td width="1%"><input type="radio" name="var_opcao_pgto" id="var_opcao_pgto1" value="valor_saldo" checked="checked" style="border:none;" /></td>
                            <td width="99%"><?php echo(getTText("msg_pagar_valor_saldo",C_NONE)); ?></td>
                        </tr>
                        <tr>
                        	<td width="1%"><input type="radio" name="var_opcao_pgto" id="var_opcao_pgto2" value="valor_digitado" style="border:none;" /></td>
                            <td width="99%"><?php echo(getTText("msg_pagar_valor_digitado",C_NONE)); ?>&nbsp;<input name='var_vlr_pgto' value='' type='text' class='edtext' style='width:80px;' maxlength='15' onKeyPress='return(validateFloatKeyNew(this,event));'></td>
                        </tr>
                        <tr>
                        	<td width="1%"><input type="radio" name="var_opcao_pgto" id="var_opcao_pgto3" value="saldo_desconto" style="border:none;" /></td>
                            <td width="99%"><?php echo(getTText("msg_pagar_saldo_desconto",C_NONE)); ?></td>
                        </tr>
                        </table>
						</td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></td>
						<td align="left" style="padding-left:5px;"><input name="var_obs" id="var_obs" value="" style="width:280px;" maxlength="250"></td>
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
<?php
$objConn = NULL;
?>