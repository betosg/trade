<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$strPopulate = request("var_populate");   // Flag para necessidade de popular o session ou não
	
	//if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), $strOperacao);

	// ABRE CONEXÃO COM BANCO
	$objConn = abreDBConn(CFG_DB);

	try{
		$strSQL = " 
			SELECT   
				  prd_pedido.cod_pedido
				, prd_pedido.valor
				, prd_pedido.qtde_parc
				, prd_pedido.obs
				, prd_pedido.it_descricao
				, prd_pedido.it_cod_pf
				, prd_pedido.it_tipo
				, prd_pedido.it_dtt_agendamento
				, prd_produto.descricao AS prod_descricao
				, prd_pedido.cod_pj
				, prd_pedido.cli_nome_fantasia
				, prd_pedido.cli_razao_social
				, prd_pedido.cli_vlr_doc
				, prd_pedido.cli_estado
				, cad_pf.nome   		AS pf_nome
				, cad_pf.cpf    		AS pf_cpf
				, cad_pf.email  		AS email_pf
				, cad_pj.email  		AS email_pj
				, cad_pj.endprin_fone1 	AS fone1_pj
				, cad_pj.endprin_fone2 	AS fone2_pj
				, cad_pf.endprin_fone1	AS fone1_pf
				, cad_pf.endprin_fone2	AS fone2_pf
				, EXTRACT('year' FROM prd_pedido.it_dt_fim_val_produto) AS ano_vcto
			FROM prd_pedido
			LEFT OUTER JOIN cad_pf      ON (prd_pedido.it_cod_pf      = cad_pf.cod_pf)
			LEFT OUTER JOIN cad_pj      ON (prd_pedido.cod_pj		  = cad_pj.cod_pj)
			LEFT OUTER JOIN prd_produto ON (prd_pedido.it_cod_produto = prd_produto.cod_produto)
			WHERE prd_pedido.cod_pedido = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	} 
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Caso PEDIDO ENCONTRADO, ENTÃO FAZ FETCH e TRATAMENTO DOS DADOS ENCAMINHADOS
	if($objResult->rowCount() > 0) {
		// Fetch dos dados do pedido
		$objRS = $objResult->fetch();
		// Passa para Variáveis
		$dblValor   		= (getValue($objRS,"valor") 			== "") ? "" : str_replace(".", ",", str_replace(",", "", number_format((double) getValue($objRS,"valor"), 2)));
		$intQtdeParc        = ((getValue($objRS,"qtde_parc") == "") || (getValue($objRS,"qtde_parc") <= 0)) ? 1 : getValue($objRS,"qtde_parc");
		$strObs   		 	= (getValue($objRS,"obs") 				== "") ? "" : getValue($objRS,"obs");
		$strDesc    		= ((getValue($objRS,"it_tipo") == "card")||(getValue($objRS,"it_tipo") == "homo")) ? getValue($objRS,"prod_descricao")." (".getValue($objRS,"pf_nome").")" : getValue($objRS,"it_descricao");
		$intCodPF 			= (getValue($objRS,"it_cod_pf") 		== "") ? "" : getValue($objRS,"it_cod_pf");
		$intCodPJ 			= (getValue($objRS,"cod_pj") 			== "") ? "" : getValue($objRS,"cod_pj");
		$strPJNomeFantasia 	= (getValue($objRS,"cli_nome_fantasia") == "") ? "" : getValue($objRS,"cli_nome_fantasia");
		$strPJRazaoSocial 	= (getValue($objRS,"cli_razao_social") 	== "") ? "" : getValue($objRS,"cli_razao_social");
		$strPJcnpj 			= (getValue($objRS,"cli_vlr_doc") 		== "") ? "" : getValue($objRS,"cli_vlr_doc");
		$strPFNome 			= (getValue($objRS,"pf_nome") 			== "") ? "" : getValue($objRS,"pf_nome");
		$strPFcpf 			= (getValue($objRS,"pf_cpf") 			== "") ? "" : getValue($objRS,"pf_cpf");
		$strTipo 			= (getValue($objRS,"it_tipo") 			== "") ? "" : getValue($objRS,"it_tipo");
	}
	else {
		$strErro = "Não foi possível processar os dados.";
		mensagem("err_sql_titulo","err_sql_desc",$strErro,"../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	
	// Busca os códigos padrões para conta bancária, plano de conta, 
	// centro de custo, job e num dias corridos para vcto
	$intCodContaPadrao 		 = getVarEntidade($objConn, "pedido_cod_conta_banco_padrao");
	$intCodPlanoContaPadrao  = getVarEntidade($objConn, "pedido_cod_plano_conta_padrao");
	$intCodCentroCustoPadrao = getVarEntidade($objConn, "pedido_cod_centro_custo_padrao");
	$intQtdeDiasVctoPadrao   = getVarEntidade($objConn, "pedido_qtde_dias_vcto_padrao");
	$intCodCFGBoleto 		 = getVarEntidade($objConn, "cod_cfg_boleto_padrao");
	$intCodJobPadrao         = getVarEntidade($objConn, "fin_cod_job");
	
	$intCodPlanoContaCarteirinhas   = getVarEntidade($objConn, "pedido_cod_plano_conta_carteirinhas");
	$intCodPlanoContaTaxaExpediente = getVarEntidade($objConn, "pedido_cod_plano_conta_taxa_expediente");
	
	if ($intCodPlanoContaPadrao != "") {
		if((getValue($objRS,"it_tipo") == "homo")||(getValue($objRS,"it_tipo") == "card")){
			$intCodPlanoContaPadrao = (getValue($objRS,"it_tipo") == "card") ? $intCodPlanoContaCarteirinhas : $intCodPlanoContaTaxaExpediente;
		}
	}
	
	// Calcula a DATA DE VENCIMENTO
	if ($intQtdeDiasVctoPadrao == "") $intQtdeDiasVctoPadrao = "0";
	$intQtdeDiasVctoPadrao = ((getValue($objRS,"it_tipo") == "homo") || (getValue($objRS,"it_tipo") == "card")) ? "2" : $intQtdeDiasVctoPadrao;
	$dtVcto = dateAdd("d", $intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	if(getWeekDay($dtVcto) == "sabado"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 3;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}elseif(getWeekDay($dtVcto) == "domingo"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 2;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}
	$dtVcto = dDate(CFG_LANG, $dtVcto, false);
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
<!--
//****** Funções de ação dos botões - Início ******
var strLocation = null;
function ok() {
	if (validateRequestedFields("formeditor")==true) {			
		if (document.formeditor.var_exibir_boleto.checked)
			document.formeditor.var_exibir_boleto.value = 'T';
		else
			document.formeditor.var_exibir_boleto.value = '';
		
		document.formeditor.submit();
	}
}

function cancelar() {
	location.href="../modulo_PainelAdmin/STindex.php";
}
//****** Funções de ação dos botões - Fim ******

function checkControls(){
	// Esta função faz o tratamento dos checkboxes,
	// alterando o estado de cada um deles conforme
	// a regra para inserção na agenda, envio de email.
	// Ou seja, não é permitido um envio de email caso
	// uma agenda não seja Inserida no sistema.
	if(document.getElementById('var_opcao_gerar_agenda').checked  == true){
		document.getElementById('var_opcao_gerar_email').disabled = false;
		document.getElementById('var_opcao_gerar_email').checked  = true;
	} else{
		document.getElementById('var_opcao_gerar_email').checked  = false;
		document.getElementById('var_opcao_gerar_email').disabled = true;
	}
}			
//-->
</script>
</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","GERAR TÍTULO (de Pedido)",CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="705" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; padding-right: 20px;">
	<form name="formeditor" action="STgeraTituloexec.php" method="post">
	<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>">
	<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>">
	<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>">
	<input type="hidden" name="var_num_documento" value="<?php echo($intCodDado);?>">
	<input type="hidden" name="var_tipo_documento" value="BOLETO">
	<input type="hidden" name="var_tipo" value="<?php echo($strTipo); ?>">
	<input type="hidden" name="var_email_pj" value="<?php echo(getValue($objRS,"email_pj"));?>" />
	<input type="hidden" name="var_email_pf" value="<?php echo(getValue($objRS,"email_pf"));?>" />
	<input type="hidden" name="var_responsavel_agendamento" value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>"/>
	<input type="hidden" name="var_ano_vcto" value="<?php echo(getValue($objRS,"ano_vcto"));?>"/>
	<input type="hidden" name="var_categoria" value="HOMOLOGACAO"/>
	<input type="hidden" name="var_prioridade" value="NORMAL"/>
	<tr><td height="12" style="padding:20px 0px 0px 20px;"><strong><?php echo(getTText("confirmacao_gerar_pedido",C_NONE));?></strong></td></tr>
	<tr>
		<td style="padding:0px 80px 0px 80px;">
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
				<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>DADOS DA EMPRESA E DO PEDIDO</strong></td></tr>
				<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
				<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right" width="28%"><strong><?php echo(getTText("nome_fantasia",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo($strPJNomeFantasia); ?></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo($strPJRazaoSocial); ?></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo($strPJcnpj); ?></td>
				</tr>
				<?php if ($intCodPF != "") { ?>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right"><strong><?php echo(getTText("pf_nome",C_NONE)); ?>:</strong></td>
						<td>&nbsp;<?php echo($strPFNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right"><strong><?php echo(getTText("pf_cpf",C_NONE)); ?>:</strong></td>
						<td>&nbsp;<?php echo($strPFcpf); ?></td>
					</tr>
				<?php } ?>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong><?php echo(getTText("tipo",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo($strTipo); ?></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong><?php echo(getTText("descricao",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo($strDesc); ?></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong><?php echo(getTText("fone_1",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo(getValue($objRS,"fone1_pj"));?></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong><?php echo(getTText("fone_2",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php echo(getValue($objRS,"fone2_pj"));?></td>
				</tr>
				<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
				
				<!-- ----------------------- -->
				<!-- AGENDAMENTO HOMOLOGAÇÃO -->
				<!-- ----------------------- -->
				<?php if(getValue($objRS,"it_tipo") == "homo"){?>
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
					<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>AGENDAMENTO DA HOMOLOGAÇÃO</strong></td></tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("opcoes_agendamento",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="checkbox" name="var_opcao_gerar_agenda" id="var_opcao_gerar_agenda" value="NEW_AGENDA" class="inputclean" checked="checked" onClick="checkControls();" /><?php echo(getTText("ins_evento_sistema",C_NONE))?><br/>
							<input type="checkbox" name="var_opcao_gerar_email"  id="var_opcao_gerar_email"  value="NEW_EMAIL"  class="inputclean" <?php echo(((getValue($objRS,"email_pf") != "") || (getValue($objRS,"email_pj") != "")) ? 'checked="checked"' : "disabled='true'")?> /><?php echo(getTText("enviar_email_colab_empresa",C_NONE))?>
							<span class="comment_peq">
							<?php echo("<br/>".getTText("esta_opcao_so_com_agenda",C_NONE));?>
							</span>
							<span class="comment_peq">
							<?php 
								if((getValue($objRS,"email_pf") != "") || (getValue($objRS,"email_pj") != "")){
									echo("<br/>".getTText("sistema_enviara_emails_para",C_NONE).":");
									echo((getValue($objRS,"email_pf") != "") ? "<br/>&bull;&nbsp;".getValue($objRS,"email_pf") : "");
									echo((getValue($objRS,"email_pj") != "") ? "<br/>&bull;&nbsp;".getValue($objRS,"email_pj") : "");
								} else{
									echo("<br/>".getTText("nenhum_email_cad_para_pj_ou_pf",C_NONE));
								}
							?>
							</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("responsavel_agendamento",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">&nbsp;<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>&nbsp;</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("categoria",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">&nbsp;HOMOLOGAÇÃO&nbsp;
							<!--
							<select name="var_categoria" style="width:120px;">
								<option value="REUNIAO"     >REUNIÃO</option>
								<option value="ENCONTRO"   	>ENCONTRO</option>
								<option value="CONFERENCIA"	>CONFERÊNCIA</option>
								<option value="ALMOCO"     	>ALMOÇO</option>
								<option value="HOMOLOGACAO" selected="selected">HOMOLOGAÇÃO</option>
								<option value="JANTAR"     	>JANTAR</option>
								<option value="VISITA"		>VISITA</option>
								<option value="VIAGEM"		>VIAGEM</option>
								<option value="ANIVERSARIO"	>ANIVERSÁRIO</option>
								<option value="COMEMORACAO"	>COMEMORAÇÃO</option>
								<option value="FERIADO"		>FERIADO</option>
								<option value="OUTROS"		>OUTROS</option>
							</select>
							-->
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("prioridade",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">&nbsp;NORMAL&nbsp;
							<!--
							<select name="var_prioridade" style="width:120px;">
								<option value="BAIXA" >BAIXA</option>
								<option value="NORMAL">NORMAL</option>
								<option value="MEDIA" >MEDIA</option>
								<option value="ALTA"  >ALTA</option>
							</select>
							-->
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("dtt_agendamento_sugestao",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">&nbsp;<?php echo((getValue($objRS,"it_dtt_agendamento") == "") ? "Nenhuma data sugerida  " : dDate(CFG_LANG,getValue($objRS,"it_dtt_agendamento"),true)."&nbsp;<span class='comment_peq'>Data sugerida pela Empresa Afiliada</span>");?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
						<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("previsao_ini",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left" valign="top">
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
								<tr>
									<td width="20%"><input type="text" name="var_dt_prev_iniô" id="var_dt_prev_iniô" size="12" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" value="<?php echo(dDate(CFG_LANG,getValue($objRS,"it_dtt_agendamento"),false));?>" /></td>
									<td width="80%" align="left">
										<input type="text" name="var_hr_prev_iniô" id="var_hr_prev_iniô" size="5" maxlength="5" value="<?php echo(substr(dDate(CFG_LANG,getValue($objRS,"it_dtt_agendamento"),true),11,5));?>" onkeyPress="FormataInputHoraMinuto(this,event);" style="margin-bottom:0px;"/>&nbsp;até <input type="text" name="var_hr_prev_fimô" id="var_hr_prev_fimô" size="5" maxlength="5" value="" onkeyPress="FormataInputHoraMinuto(this,event);" style="margin-bottom:0px;"/>&nbsp;<span class="comment_med">Horário de término</span>
									</td>
								</tr>
								<tr><td colspan="2" valign="top" style="margin-bottom:10px;"><span class="comment_med">Para visualizar a agenda e os horários ocupados, <span onClick="AbreJanelaPAGE('../modulo_Agenda/','900','500');" style="font-weight:bold;cursor:pointer;">clique aqui</span></span></td></tr>
							</table>
						</td>
					</tr>
	
					<tr bgcolor="<?php echo(CL_CORLINHA_1);?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("titulo_agendamento",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_titulo_agendaô" id="var_titulo_agendaô" value="<?php echo(getValue($objRS,"pf_nome"));?> - HOMOLOGAÇÃO" size="50" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("descricao_agendamento",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><textarea name="var_descricao_agenda" rows="5" cols="60"></textarea></td>
					</tr>
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
				<?php }?>
				
				<!-- -------------- -->
				<!-- ENVIO DE EMAIL -->
				<!-- -------------- -->
				<?php if(getValue($objRS,"it_tipo") == "card"){?>
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
					<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>ENVIO DE EMAIL</strong></td></tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
						<td align="right" valign="top"><strong><?php echo(getTText("enviar_email_com_boleto_qm",C_NONE));?></strong></td>
						<td align="left"  valign="top">
							<input type="radio" name="var_opcao_enviar_email" id="var_opcao_enviar_email_1" value="S" class="inputclean" <?php echo((getValue($objRS,"email_pj") == "") ? 'disabled="true"' : 'checked="checked"')?> /><?php echo(getTText("sim",C_NONE))?><br/>
							<input type="radio" name="var_opcao_enviar_email" id="var_opcao_enviar_email_2" value="N" class="inputclean" <?php echo((getValue($objRS,"email_pj") == "") ? 'disabled="true" checked="checked"' : '')?> /><?php echo(getTText("nao",C_NONE))?>
							<span class="comment_peq">
							<?php 
								if(getValue($objRS,"email_pj") != ""){
									echo("<br/>".getTText("sistema_enviara_emails_para",C_NONE).":");
									echo((getValue($objRS,"email_pj") != "") ? "<br/>&bull;&nbsp;".getValue($objRS,"email_pj") : "");
								} else{
									echo("<br/>".getTText("nenhum_email_cad_para_pj",C_NONE));
								}
							?>
							</span>
						</td>
					</tr>
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
				<?php }?>
				
				
				<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>DADOS PARA O TÍTULO</strong></td></tr>
				<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
				<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong><?php echo(getTText("tipo_documento",C_NONE)); ?>:</strong></td>
					<td>&nbsp;Boleto</td>
				</tr>
				<!--
				<tr bgcolor="<?php //echo(CL_CORLINHA_1)?>">
					<td align="right" width="35%"><strong><?php //echo(getTText("num_documento",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<?php //echo($intCodDado); ?></td>
				</tr>
				-->
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong>*<?php echo(getTText("valor",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<input name="var_valorô" id="var_valorô" value="<?php echo($dblValor); ?>" style="width:100px;" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this,event);" /></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong>*<?php echo(getTText("dt_vcto",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<input name="var_dt_vctoô" id="var_dt_vctoô" value="<?php echo($dtVcto); ?>" size="10" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />
					<div style="padding-left:4px;"><span class="comment_peq"><?php echo(getTText("msg_prim_vcto",C_NONE)); ?></span></div>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong>*<?php echo(getTText("qtde_parc",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<select name='var_qtde_parc' id='var_qtde_parc' size='1' style='width=80px;'>
					<?php
					$intQtdeParc = (12 - date("m", now())) + 1;
					if ($intQtdeParc == 1)
						echo "<option value='1' selected='selected'>Uma apenas</option>";
					else for ($i=1;$i<=$intQtdeParc;$i++)
							echo "<option value='".$i."'>".$i."x</option>";
					?>
					</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong>*<?php echo(getTText("conta",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<select name="var_cod_contaô" id="var_cod_contaô" size="1" style="width:180px;">
					<?php echo(montaCombo($objConn, " SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome ", "cod_conta", "nome", $intCodContaPadrao, "")); ?>
					</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong>*<?php echo(getTText("plano_conta",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<select name="var_cod_plano_contaô" id="var_cod_plano_contaô" size="1" style="width:240px;">
					<?php echo(montaCombo($objConn, "SELECT cod_plano_conta, CASE WHEN cod_reduzido IS NULL THEN '000 '||nome ELSE cod_reduzido||' '||nome END AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_plano_conta", "rotulo", $intCodPlanoContaPadrao, "")); ?>
					</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong>*<?php echo(getTText("centro_custo",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<select name="var_cod_centro_custoô" id="var_cod_centro_custoô" size="1" style="width:160px;">
					<?php echo(montaCombo($objConn, " SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome ", "cod_centro_custo", "nome", $intCodCentroCustoPadrao, "")); ?>
					</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong><?php echo(getTText("job",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<select name="var_cod_job" id="var_cod_job" size="1" style="width:160px;">
					<option value=""></option>
					<?php echo(montaCombo($objConn, " SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ", "cod_job", "nome", $intCodJobPadrao, "")); ?>
					</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><strong>*<?php echo(getTText("historico",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<input name="var_historicoô" id="var_historicoô" value="<?php echo($strDesc); ?>" size="60" /></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></td>
					<td>&nbsp;<textarea name="var_obs" id="var_obs" cols="60" rows="5"><?php echo($strObs); ?></textarea></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><label for="var_cod_cfg_boleto"><strong>*<?php echo(getTText("boleto",C_NONE)); ?>:</strong></label></td>
					<td>&nbsp;<select name="var_cod_cfg_boletoô" id="var_cod_cfg_boletoô" size="1" style="width:160px;">
					<?php echo(montaCombo($objConn, " SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao ", "cod_cfg_boleto", "descricao", $intCodCFGBoleto, "")); ?>
					</select>&nbsp;<input type="checkbox" name="var_exibir_boleto" id="var_exibir_boleto" value="T" checked="checked" style="border:none;background:none;">Exibir boleto após gerar o título
					</td>
				</tr>
				<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
				<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding: 10px 0px 10px 10px;" align="right">
			<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
				<tr>
					<td align="right" style="padding: 0px 0px 0px 70px;"><img src="../img/mensagem_info.gif"></td>
					<td align="left" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_gerar_titulo",C_NONE))?></td>
					<td width="1%" align="left" style="padding:10px 70px 10px 10px;" nowrap>
						<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
	$objResult->closeCursor();
	$objConn = NULL;
?>