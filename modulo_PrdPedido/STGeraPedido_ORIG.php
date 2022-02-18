<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPJ = request("var_chavereg");

$objConn = abreDBConn(CFG_DB);

$intCodConta 		= getVarEntidade($objConn, "pedido_cod_conta_banco_padrao");;
$intCodPlanoConta 	= getVarEntidade($objConn, "pedido_cod_plano_conta_padrao");;
$intCodCentroCusto 	= getVarEntidade($objConn, "pedido_cod_centro_custo_padrao");;
$intQtdeDiasVcto 	= getVarEntidade($objConn, "pedido_qtde_dias_vcto_padrao");;
$intCodCFGBoleto 	= getVarEntidade($objConn, "cod_cfg_boleto_padrao");;
$intCodJob          = getVarEntidade($objConn, "fin_cod_job");;

if ($intQtdeDiasVcto == "") $intQtdeDiasVcto = "30";
$dtVcto = dateAdd("d", $intQtdeDiasVcto, date("Y-m-d"), false);
$dtVcto = dDate(CFG_LANG, $dtVcto, false);

$strBGColor1 = CL_CORLINHA_1;
$strBGColor2 = CL_CORLINHA_2;

$strOpcao = "B";
if ($intCodPJ != "") $strOpcao = "A";

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
	
	if ((!document.formeditor.var_opcao_a.checked) && 
		(!document.formeditor.var_opcao_c.checked) &&
		(!document.formeditor.var_opcao_d.checked)) var_msg += "\nSelecionar para quem será gerado pedido";
	if ((document.formeditor.var_opcao_a.checked) && (document.formeditor.var_cod_pj.value == "")) var_msg += "\nInformar código da empresa";
	if (document.formeditor.var_opcao_c.checked) {
		if ((document.formeditor.var_socio_opcao_c.value == "") && (document.formeditor.var_categoria_opcao_c.value == "") && (document.formeditor.var_porte_opcao_c.value == "") && (document.formeditor.var_segmento_opcao_c.value == "") && (document.formeditor.var_categoria_extra_opcao_c.value == "")) var_msg += "\nSelecione um dos combos";
	}
	if (document.formeditor.var_opcao_d.checked) {
		if ((document.formeditor.var_socio_opcao_d.value == "") && (document.formeditor.var_categoria_opcao_d.value == "") && (document.formeditor.var_porte_opcao_d.value == "") && (document.formeditor.var_segmento_opcao_d.value == "") && (document.formeditor.var_categoria_extra_opcao_d.value == "")) var_msg += "\nSelecione um dos combos";
	}
	
	if (document.formeditor.var_cod_produto.value == "") var_msg += "\nProduto";
	
	if ((document.formeditor.var_gerar_b.checked)||(document.formeditor.var_gerar_c.checked)) {
		if (document.formeditor.var_dt_vcto.value == "") var_msg += "\nVencimento";
		if (document.formeditor.var_cod_conta.value == "") var_msg += "\nConta banco";
		if (document.formeditor.var_cod_plano_conta.value == "") var_msg += "\nPlano de Contas";
		if (document.formeditor.var_cod_centro_custo.value == "") var_msg += "\nCentro de Custos";
		if (document.formeditor.var_cod_cfg_boleto.value == "") var_msg += "\nBoleto";
		if (document.formeditor.var_tipo_documento.value == "") var_msg += "\nTipo de Documento";
		if (document.formeditor.var_historico.value == "") var_msg += "\nHistórico";
	}
	
	if (var_msg == ""){
		document.getElementById("butOK").style.display = 'none';
		document.formeditor.action = 'STGeraPedidoexec.php';		
		document.formeditor.submit();
	}else
		alert("Favor verificar campos:\n" + var_msg);
}

function cancelar(){
	window.history.back();
}

function AlteraOpcoesGerar() {
	if (document.formeditor.var_opcao_a.checked) {
		document.formeditor.var_gerar_a.checked = false;
		document.formeditor.var_gerar_a.disabled = false;
		
		document.formeditor.var_gerar_b.checked = true;
		document.formeditor.var_gerar_b.disabled = false;
	}
	else /*if (document.formeditor.var_opcao_b.checked)*/{
		document.formeditor.var_gerar_a.checked = false;
		document.formeditor.var_gerar_a.disabled = true;
		
		document.formeditor.var_gerar_b.checked = true;
		document.formeditor.var_gerar_b.disabled = false;
	}
}

function abreJanelaPageLocal(pr_link, pr_extra){
	var auxStrToChange, rExp, auxNewExtra, auxNewValue;
	if (pr_extra != ""){
		rExp = /:/gi;
		auxNewExtra = pr_extra
		if(pr_extra.search(rExp) != -1){
		    auxStrToChange = pr_extra.split(":");
		    auxStrToChange = auxStrToChange[1];
		    rExp = eval("/:" + auxStrToChange + ":/gi");
		    auxNewValue = eval("document.formeditor." + auxStrToChange + ".value");
		    auxNewExtra = pr_extra.replace(rExp, auxNewValue);
		}
		pr_link = pr_link + auxNewExtra;
	}
	AbreJanelaPAGE(pr_link, "800", "600");
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","PEDIDO ( Geração )",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<form name="formeditor" action="" method="post">
		<tr><td height="22" style="padding:10px"><b><?php echo getTText("rotulo_dialog",C_NONE); ?></b></td></tr>
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
							<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>A QUEM SE DESTINA O PEDIDO</strong></td>
							</tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="var_gerar"><strong>*<?php echo(getTText("para",C_NONE)); ?>:</strong></label></td>
								<td>
								<table width="100%" cellpadding="0" cellspacing="2" border="0">
								<tr>
									<td width="1%" valign="top"><input type="radio" value="uma_empresa" name="var_opcao" id="var_opcao_a" style="border-style:none;" onClick="AlteraOpcoesGerar();" <?php if ($strOpcao == "A") echo("checked='checked'"); ?>></td>
									<td width="99%">a empresa de código <input name="var_cod_pj" id="var_cod_pj" value="<?php echo($intCodPJ); ?>" type="text" size="6" maxlength="10" title="Empresa">
									<a href="javascript:abreJanelaPageLocal('../modulo_CadPJ/?var_acao=single&var_fieldname=var_cod_pj&var_formname=formeditor','');"><img src="../img/icon_search.gif" border="0" hspace="5" align="absmiddle"></a></td>
								</tr>
								<tr><td colspan="2" height="5"></tr>
								<tr>
									<td width="1%" valign="top"><input type="radio" value="varias_empresas" name="var_opcao" id="var_opcao_c" style="border-style:none;" onClick="AlteraOpcoesGerar();" <?php if ($strOpcao == "C") echo("checked='checked'"); ?>></td>
									<td width="99%">todas as empresas que são
									<select id="var_socio_opcao_c" name="var_socio_opcao_c" size="1" style="width:110px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT socio FROM cad_pj WHERE socio IS NOT NULL AND socio <> '' ORDER BY socio ", "socio", "socio", "", "")); ?>
									</select><br>da categoria
									<select id="var_categoria_opcao_c" name="var_categoria_opcao_c" size="1" style="width:150px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT categoria FROM cad_pj WHERE categoria IS NOT NULL AND categoria <> '' ORDER BY categoria ", "categoria", "categoria", "", "")); ?>
									</select>&nbsp;do porte
									<select id="var_porte_opcao_c" name="var_porte_opcao_c" size="1" style="width:120px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT porte FROM cad_pj WHERE porte IS NOT NULL AND porte <> '' ORDER BY porte ", "porte", "porte", "", "")); ?>
									</select><br>do segmento
									<select id="var_segmento_opcao_c" name="var_segmento_opcao_c" size="1" style="width:180px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT cod_segmento, nome FROM cad_segmento ORDER BY nome ", "nome", "nome", "", "")); ?>
									</select><br>da categoria extra
									<select id="var_categoria_extra_opcao_c" name="var_categoria_extra_opcao_c" size="1" style="width:155px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT cod_categoria, nome FROM cad_categoria ORDER BY nome ", "nome", "nome", "", "")); ?>
									</select>
									<br>com data de fundação menor ou igual a&nbsp;<input name="var_dt_fundacao_opcao_c" id="var_dt_fundacao_opcao_c" value="" size="10" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />
									</td>
								</tr>
								<tr><td colspan="2" height="5"></tr>
								<tr>
									<td width="1%" valign="top"><input type="radio" value="nao_possuem" name="var_opcao" id="var_opcao_d" style="border-style:none;" onClick="AlteraOpcoesGerar();" <?php if ($strOpcao == "D") echo("checked='checked'"); ?>></td>
									<td width="99%">para as empresas que NÃO POSSUEM um pedido/uma cobrança<br>com esse produto mas que são
									<select id="var_socio_opcao_d" name="var_socio_opcao_d" size="1" style="width:110px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT socio FROM cad_pj WHERE socio IS NOT NULL AND socio <> '' ORDER BY socio ", "socio", "socio", "", "")); ?>
									</select><br>da categoria
									<select id="var_categoria_opcao_d" name="var_categoria_opcao_d" size="1" style="width:150px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT categoria FROM cad_pj WHERE categoria IS NOT NULL AND categoria <> '' ORDER BY categoria ", "categoria", "categoria", "", "")); ?>
									</select>&nbsp;do porte
									<select id="var_porte_opcao_d" name="var_porte_opcao_d" size="1" style="width:120px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT DISTINCT porte FROM cad_pj WHERE porte IS NOT NULL AND porte <> '' ORDER BY porte ", "porte", "porte", "", "")); ?>
									</select><br>do segmento
									<select id="var_segmento_opcao_d" name="var_segmento_opcao_d" size="1" style="width:180px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT cod_segmento, nome FROM cad_segmento ORDER BY nome ", "nome", "nome", "", "")); ?>
									</select><br>da categoria extra
									<select id="var_categoria_extra_opcao_d" name="var_categoria_extra_opcao_d" size="1" style="width:100px; vertical-align:middle;">
										<option value=""></option>
										<?php echo(montaCombo($objConn, " SELECT cod_categoria, nome FROM cad_categoria ORDER BY nome ", "nome", "nome", "", "")); ?>
									</select>
									<br>com data de fundação menor ou igual a&nbsp;<input name="var_dt_fundacao_opcao_d" id="var_dt_fundacao_opcao_d" value="" size="10" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO PRODUTO</strong></td>
							</tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
								<label for="var_cod_produto"><strong>*<?php echo(getTText("produto",C_NONE)); ?>:</strong></label></td>
								<td nowrap align="left" width="99%">
								<select name="var_cod_produto" id="var_cod_produto" style="width:360px" size="1" title="Produto" onChange="
                                 ajaxDetailData((this.value != '') ? ' SELECT valor 						FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_valor', 'document.formeditor.var_valor.value = FloatToMoeda(document.formeditor.var_valor.value);');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_cod_conta_banco   FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_cod_conta', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_cod_plano_conta   FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_cod_plano_conta', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_cod_centro_custo  FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_cod_centro_custo', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_cod_job           FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_cod_job', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_cod_cfg_boleto    FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_cod_cfg_boleto', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_tipo_doc          FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_tipo_documento', '');
                                 ajaxDetailData((this.value != '') ? ' SELECT tit_default_historico         FROM prd_produto WHERE cod_produto = '+this.value : '', 'ajaxMontaEdit', 'var_historico', '');
                                ">
								<option value="" selected="selected"></option>
								<?php
								try{
									$strSQL  = " SELECT cod_produto, rotulo, valor, descricao ";
									$strSQL .= " FROM prd_produto ";
									$strSQL .= " WHERE dtt_inativo IS NULL ";
									$strSQL .= " AND ((tipo <> 'card' AND tipo <> 'homo') OR (tipo IS NULL)) ";
									$strSQL .= " AND CURRENT_DATE BETWEEN dt_ini_val_produto AND dt_fim_val_produto ";
									$strSQL .= " ORDER BY rotulo ";
									
									$objResult = $objConn->query($strSQL);
								} catch(PDOException $e){
									mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
									die();
								}
								//$objRS = $objResult->fetch();
								
								if($objResult->rowCount() > 0)
									foreach($objResult as $objRS){
										$dblValor = number_format((double) getValue($objRS,"valor"), 2);
										$dblValor = str_replace(",","",$dblValor);
										$dblValor = str_replace(".",",",$dblValor);
										
										echo("<option value='" . getValue($objRS,"cod_produto") . "'>");
										echo(getValue($objRS,"rotulo") . " - " . getValue($objRS,"descricao") . " (R$ " . $dblValor . ")</option>");
									}
								$objResult->closeCursor();
								?>
								</select><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right" width="20%"><label for="var_valor"><strong>*<?php echo(getTText("valor",C_NONE)); ?>:</strong></label></td>
								<td><input name="var_valor" id="var_valor" value="" size="10" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this, event);" /></td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td align="right" width="20%"></td>
								<td><span class="comment_med">Após faturar um pedido que tenha um "perfil de evento" o site oficial levará 4h até atualizar a lista de eventos da empresa promotora.</span></td>
							</tr>
							<!--
							<tr bgcolor="<?php //echo($strBGColor2)?>">
								<td align="right"><label for="var_texto"><strong></strong></label></td>
								<td><textarea name="var_texto" id="var_texto" cols="60" rows="3"></textarea></td>
							</tr>
							-->
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO PEDIDO</strong></td>
							</tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><label for="var_obs_pedido"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></label></td>
								<td><textarea name="var_obs_pedido" id="var_obs_pedido" cols="60" rows="3"></textarea></td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_gerar"><strong>*<?php echo(getTText("gerar",C_NONE)); ?>:</strong></label></td>
								<td>
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="1%"><input type="radio" value="apenas_pedido" name="var_gerar" id="var_gerar_a" style="border-style:none"></td>
									<td width="99%">apenas o pedido</td>
								</tr>
								<tr>
									<td width="1%"><input type="radio" value="pedido_e_titulo" name="var_gerar" id="var_gerar_b" style="border-style:none" checked="checked"></td>
									<td width="99%">o pedido e mais o título</td>
								</tr>
								<?php 
								    //Opção mostrada apenas para Ubrafe e Sindiprom. By Lumertz 23.05.2013
									$strDirCliente = getsession(CFG_SYSTEM_NAME . "_dir_cliente");																																				
								?>	
								<tr <?php if(($strDirCliente != "ubrafe") and ($strDirCliente != "sindiprom")) echo " style=\"display:none;\" "; ?>>
									<td width="1%"><input type="radio" value="ped_tit_e_nfdeb" name="var_gerar" id="var_gerar_c" style="border-style:none"></td>
									<td width="99%">o pedido, o título e a Nota de Débito</td>
								</tr>																
								</table>
								</td>
							</tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO TÍTULO</strong></td>
							</tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><label for="var_dt_vcto"><strong>*<?php echo(getTText("dt_vcto",C_NONE)); ?>:</strong></label></td>
								<td><input name="var_dt_vcto" id="var_dt_vcto" value="<?php echo($dtVcto); ?>" size="10" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" /></td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td align="right"><label for="var_cod_conta"><strong>*<?php echo(getTText("conta_banco",C_NONE)); ?>:</strong></label></td>
								<td>
								<select name="var_cod_conta" id="var_cod_conta" size="1" style="width:180px;">
								<?php echo(montaCombo($objConn, " SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome ", "cod_conta", "nome", $intCodConta, "")); ?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><label for="var_cod_plano_conta"><strong>*<?php echo(getTText("plano_conta",C_NONE)); ?>:</strong></label></td>
								<td>
								<select name="var_cod_plano_conta" id="var_cod_plano_conta" size="1" style="width:240px;">
								<?php echo(montaCombo($objConn, " SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_plano_conta", "rotulo", $intCodPlanoConta, "")); ?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td align="right"><label for="var_cod_centro_custo"><strong>*<?php echo(getTText("centro_custo",C_NONE)); ?>:</strong></label></td>
								<td>
								<select name="var_cod_centro_custo" id="var_cod_centro_custo" size="1" style="width:160px;">
								<?php echo(montaCombo($objConn, " SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_centro_custo", "nome", $intCodCentroCusto, "")); ?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><label for="var_cod_job"><strong><?php echo(getTText("job",C_NONE)); ?>:</strong></label></td>
								<td>
								<select name="var_cod_job" id="var_cod_job" size="1" style="width:160px;">
								<option value=""></option>
								<?php echo(montaCombo($objConn, " SELECT cod_job, nome AS rotulo FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ", "cod_job", "rotulo", $intCodPlanoConta, "")); ?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td align="right" style="vertical-align:top"><label for="var_cod_cfg_boleto"><strong>*<?php echo(getTText("boleto",C_NONE)); ?>:</strong></label></td>
								<td>
									<select name="var_cod_cfg_boleto" id="var_cod_cfg_boleto" size="1" style="width:160px;">
										<option value="" selected="selected"></option>
										<?php echo(montaCombo($objConn, " SELECT cod_cfg_boleto, cod_cfg_boleto||' - '||descricao AS descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY ordem, descricao ", "cod_cfg_boleto", "descricao", "", "")); ?>
									</select>
									<br><span class="comment_peq"><?php echo(getTText("obs_boleto_default",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><b>*<?php echo(getTText("tipo_documento",C_UCWORDS)); ?>:</b></td>
								<td>
									<select name="var_tipo_documento" class="edtext"  style="width:200px;">
										<option value="BOLETO"><?php echo(getTText("boleto",C_TOUPPER)); ?></option>
										<option value="BOLETO_SINDICAL"><?php echo(getTText("boleto_sindical",C_TOUPPER)); ?></option>
										<option value="BOLETO_ASSISTENCIAL"><?php echo(getTText("boleto_assistencial",C_TOUPPER)); ?></option>
										<option value="EXTRATO"><?php echo(getTText("extrato",C_TOUPPER)); ?></option>
										<option value="FATURA"><?php echo(getTText("fatura",C_TOUPPER)); ?></option>
										<option value="HOLERITE"><?php echo(getTText("holerite",C_TOUPPER)); ?></option>
										<option value="NOTA_FISCAL"><?php echo(getTText("nota_fiscal",C_TOUPPER)); ?></option>
										<option value="TARIFA"><?php echo(getTText("tarifa",C_TOUPPER)); ?></option>
										<option value="RECIBO"><?php echo(getTText("recibo",C_TOUPPER)); ?></option>
										<option value="TED"><?php echo(getTText("ted",C_TOUPPER)); ?></option>
										<option value="CARTAO_VISA"><?php echo(getTText("cartao_visa",C_TOUPPER)); ?></option>
										<option value="CARTAO_MASTERCARD"><?php echo(getTText("cartao_mastercard",C_TOUPPER)); ?></option>
										<option value="CARTAO_AMEX"><?php echo(getTText("cartao_amex",C_TOUPPER)); ?></option>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor2)?>">
								<td align="right" style="vertical-align:top"><label for="var_historico"><strong>*<?php echo(getTText("historico",C_NONE)); ?>:</strong></label></td>
								<td><input name="var_historico" id="var_historico" value="" size="60" /></td>
							</tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right"><label for="var_obs_titulo"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></label></td>
								<td><textarea name="var_obs_titulo" id="var_obs_titulo" cols="60" rows="3"></textarea></td>
							</tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("peridiocidade",C_UCWORDS)); ?></b></td>
							</tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr bgcolor="<?php echo($strBGColor1)?>">
								<td align="right" valign="top"><b><?php echo(getTText("parcelas",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td>
									<div style="padding-left:0px;"><?php echo(getTText("avisoins_parte1",C_NONE)); ?><span style="padding-right:3px; padding-left:3px;"><input name="var_parcelas" class="edtext" value="" type="text" maxlength="3" style="width:25px; text-align:center;" onFocus="this.value='';" onKeyPress="Javascript:return validateNumKey(event);">
									</span><?php echo(getTText("avisoins_parte2",C_NONE)); ?></div>
									<div style="padding-top:5px;">
										<?php echo(getTText("avisoins_parte3",C_NONE)); ?>
										<select name="var_frequencia" class="edtext" size="1" style="width:90px;">
											<option value="" selected="selected"></option>
											<option value="DIARIA"><?php echo(getTText("diaria",C_UCWORDS)); ?></option>
											<option value="SEMANAL"><?php echo(getTText("semanal",C_UCWORDS)); ?></option>
											<option value="QUINZENAL"><?php echo(getTText("quinzenal",C_UCWORDS)); ?></option>
											<option value="MENSAL"><?php echo(getTText("mensal",C_UCWORDS)); ?></option>
											<option value="BIMESTRAL"><?php echo(getTText("bimestral",C_UCWORDS)); ?></option>
											<option value="TRIMESTRAL"><?php echo(getTText("trimestral",C_UCWORDS)); ?></option>
											<option value="SEMESTRAL"><?php echo(getTText("semestral",C_UCWORDS)); ?></option>
											<option value="ANUAL"><?php echo(getTText("anual",C_UCWORDS)); ?></option>
										</select>
									</div>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr><td colspan="2" align="left"><img src="../img/line_dialog.jpg" border="0"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><span class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></span></td></tr>
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
						</table>
				</td>
				</tr>
				<tr>
					<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
						<button id="butOK" name="butOK" onClick="verifica();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<script type="text/javascript" language="javascript">
	AlteraOpcoesGerar();
</script>
<?php
$objConn = NULL;
?>
