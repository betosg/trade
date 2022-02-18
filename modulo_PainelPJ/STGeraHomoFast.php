<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	//echo $intDate = cDate(CFG_LANG,'17/11/2009',false);
	//die;
	
	
	$intCodDado  = request("var_chavereg"); 	// Código da Relação PJ x PF
	$strOperacao = request("var_oper"); 	// Operação a ser realizada
	$strExec 	 = request("var_exec"); 		// Executor externo (fora do kernel)
	$strPopulate = request("var_populate"); // Flag para necessidade de popular o session ou não
	$strAcao 	 = request("var_acao"); 		// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
	$strRedirect = request("var_redirect");
	$intCodPJ	 = request("var_cod_pj");
	
	//Popula o session para fazer a abertura dos ítens do módulo
	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); }
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");
	
	//Inicia objeto para manipulação do banco
	$objConn = abreDBConn(CFG_DB);
	
	// Só é feito a busca e exibição dos dados 
	// seja enviado como parametro para este script.
	if($intCodDado == ""){
		$strErro = "Código de Relação PJxPF inválido.";
		mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"","aviso",1);
		die();
	}
	// codigo do boleto padrao
	$intCodCFGBoleto = getVarEntidade($objConn,"cod_cfg_boleto_padrao");
	
	// busca o produto homologação ultimo a 
	// ser cadastrado e de maior valor
	try {
		$strSQL = "
				SELECT prd_produto.valor FROM prd_produto WHERE 
				CURRENT_DATE BETWEEN prd_produto.dt_ini_val_produto AND prd_produto.dt_fim_val_produto 
				AND prd_produto.tipo = 'homo'
				AND	prd_produto.dtt_inativo IS NULL
				ORDER BY prd_produto.sys_dtt_ins DESC, prd_produto.valor DESC ";
		$objResultPedido = $objConn->query($strSQL);
		$objRSPed = $objResultPedido->fetch();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Verifica os dados da PF a qual quer se
	// gerar o pedido + titulo + lcto_ordinario
	try{
		$strSQL = "
				SELECT	
					cad_pf.cod_pf
				,	cad_pf.nome
				,	cad_pf.apelido
				,	cad_pf.nome_pai
				,	cad_pf.nome_mae
				,	cad_pf.cpf
				,	cad_pf.rg
				,	cad_pf.ctps
				,	cad_pj.cod_pj
				,	cad_pj.razao_social
				,	cad_pj.nome_fantasia
				,	cad_pj.cnpj
				,	cad_pj.endprin_cidade
				,	cad_pj.endprin_estado
				,	relac_pj_pf.tipo
				,	relac_pj_pf.dt_admissao
				,	relac_pj_pf.dt_demissao
				,	relac_pj_pf.funcao
				FROM relac_pj_pf, cad_pf, cad_pj
				WHERE relac_pj_pf.cod_pj_pf = " . $intCodDado . "
				AND relac_pj_pf.cod_pj = cad_pj.cod_pj
				AND relac_pj_pf.cod_pf = cad_pf.cod_pf ";
		
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	//fetch dos dados
	$objRS 			 = $objResult->fetch();
	$intCodPF 		 = getValue($objRS,"cod_pf");
	$strNome 	 	 = getValue($objRS,"nome");
	$strApelido 	 = getValue($objRS,"apelido");
	$strNomePai 	 = getValue($objRS,"nome_pai");
	$strNomeMae 	 = getValue($objRS,"nome_mae");
	$strRG 			 = getValue($objRS,"rg");
	$strCPF 		 = getValue($objRS,"cpf");
	$strCTPS 		 = getValue($objRS,"ctps");
	$intCodPJ 		 = getValue($objRS,"cod_pj");
	$strRazaoSocial  = getValue($objRS,"razao_social");
	$strNomeFantasia = getValue($objRS,"nome_fantasia");
	$strCNPJ 		 = getValue($objRS,"cnpj");
	$strCidade 		 = getValue($objRS,"endprin_cidade");
	$strEstado 		 = getValue($objRS,"endprin_estado");
	$strFuncao 		 = getValue($objRS,"funcao");
	$strTipo 		 = getValue($objRS,"tipo");
	$dtAdmissao 	 = getValue($objRS,"dt_admissao");

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
				document.formstatic.submit();
			}

			function cancelar() {
				//location.href="STpainelPJ.php";
				window.history.back();
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF">
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("gera_homo_fast",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STGeraHomoFastexec.php" method="post">
		<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>">
		<input type="hidden" name="var_cod_pj"   value="<?php echo($intCodPJ);?>">
		<input type="hidden" name="var_cod_pf"   value="<?php echo($intCodPF);?>">
		<input type="hidden" name="var_nome"     value="<?php echo($strNome);?>">
		<input type="hidden" name="var_cpf"      value="<?php echo($strCPF);?>">
		<input type="hidden" name="var_tipo_doc" value="<?php echo("BOLETO");?>">
		<input type="hidden" name="var_redirect" value="<?php echo($strRedirect); ?>">
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("confirmacao_homo",C_NONE));?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("nome_fantasia",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeFantasia); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCNPJ); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cidade",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCidade);?> / <?php echo($strEstado);?></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde">
							<strong>DADOS DO COLABORADOR</strong>
						</td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_pai",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomePai); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_mae",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeMae); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("rg",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRG); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cpf",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCPF);?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("ctps",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCTPS); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("tipo",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strTipo); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strFuncao); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("dt_admissao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(dDate(CFG_LANG,$dtAdmissao,false)); ?></td>
					</tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA SOLICITAÇÃO</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%">
							<label for="var_obs">
								<strong><?php echo(getTText("obs",C_NONE)); ?>:</strong>
							</label>
						</td>
						<td>
							<textarea name="var_obs_homo" id="var_obs_homo" cols="60" rows="5"></textarea><br>
							<span class="comment_med">Informe qualquer comentário sobre esse processo de demiss&atilde;o/rescis&atilde;o. Ele ficará armazenada nos registros da homologação.</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%">
							<label for="var_obs">
								<strong>*<?php echo(getTText("dt_homologacao",C_NONE)); ?>:</strong>
							</label>
						</td>
						<td>
							<input type="text" name="var_dt_homo" id="var_dt_homo" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyUp="FormataInputData(this);">
						</td>
					</tr>
					<!-- ------------------------------------------ -->
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde">
							<strong>DADOS PARA O TÍTULO</strong>
						</td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<!-- ------------------------------------------ -->
					
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%">
							<input type="radio" name="var_opcao_gerar_titulo" value="S" class="inputclean" checked="checked" />
						</td>
						<td>&nbsp;<?php echo(getTText("opcao_gerar_tit_quitado",C_NONE));?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%">
							<input type="radio" name="var_opcao_gerar_titulo" value="N" class="inputclean" />
						</td>
						<td>&nbsp;<?php echo(getTText("opcao_n_gerar_tit_quitado",C_NONE));?></td>
					</tr>
					</tr><tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%">
							<strong><?php echo(getTText("tipo_documento",C_NONE)); ?>:
							</strong>
						</td>
						<td>&nbsp;Boleto</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%">
							<strong>*<?php echo(getTText("valor",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<input name="var_valor" id="var_valor" value="<?php echo((getValue($objRSPed,"valor") == "") ? "0,00" : number_format((double) getValue($objRSPed,"valor"),2,",","."));?>" size="10" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this,event);" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%">
							<strong>*<?php echo(getTText("dt_vcto",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<input type="text" name="var_dt_vcto" id="var_dt_vcto" value="<?php echo(dDate(CFG_LANG,now(),false));?>" size="10" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="javascript:return validateNumKey(event);" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong>*<?php echo(getTText("conta",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<select name="var_cod_conta" id="var_cod_conta" size="1" style="width:180px;">
						<?php echo(montaCombo($objConn, " SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome ", "cod_conta", "nome", $intCodContaPadrao, "")); ?>
									</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong>*<?php echo(getTText("plano_conta",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<select name="var_cod_plano_conta" id="var_cod_plano_conta" size="1" style="width:240px;">
						<?php echo(montaCombo($objConn, " SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_plano_conta", "rotulo", $intCodPlanoContaPadrao, "")); ?>
								</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong>*<?php echo(getTText("centro_custo",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<select name="var_cod_centro_custo" id="var_cod_centro_custo" size="1" style="width:160px;">
						<?php echo(montaCombo($objConn, " SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome ", "cod_centro_custo", "nome", $intCodCentroCustoPadrao, "")); ?>
								</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%">
							<strong>*<?php echo(getTText("historico",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<input name="var_historico" id="var_historico" value="HOMOLOGAÇÃO FAST" size="60" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" valign="top" width="35%">
							<strong>*<?php echo(getTText("num_lcto",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<input name="var_num_lcto" id="var_num_lcto" value="<?php echo($intCodPF.$intCodPJ)?>" size="15" maxlength="30" /><br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" valign="top" width="35%">
							<strong>*<?php echo(getTText("num_documento",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<input name="var_num_documento" id="var_num_documento" 
						           value="<?php echo(str_replace(" ","",(str_replace(":","",(str_replace("-","",now()))))));?>" size="15" maxlength="30" /><br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%">
							<strong><?php echo(getTText("obs",C_NONE));?>:</strong>
						</td>
						<td>&nbsp;<textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right"><label for="var_cod_cfg_boleto">
							<strong>*<?php echo(getTText("boleto",C_NONE));?>:</strong></label>
						</td>
						<td>
						&nbsp;<select name="var_cod_cfg_boleto" id="var_cod_cfg_boleto" size="1" style="width:160px;">
						<?php echo(montaCombo($objConn, " SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao ", "cod_cfg_boleto", "descricao", $intCodCFGBoleto, "")); ?>
							</select>&nbsp;<!--<input type="checkbox" name="var_exibir_boleto" id="var_exibir_boleto" value="T" checked="checked" style="border:none;background:none;">Exibir boleto após gerar o título-->
						</td>
					</tr>
					<tr>
						<td height="10"colspan="2"class="destaque_med" style="padding-top:5px; padding-right:25px">
							<?php echo(getTText("campos_obrig",C_NONE));?>
						</td>
					</tr>
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;">
										<img src="../img/mensagem_aviso.gif">
									</td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;">
										<?php echo(getTText("aviso_solicita_homo_fast",C_NONE));?>
									</td>
									<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
										<button onClick="ok();return false;">
											<?php echo(getTText("ok",C_UCWORDS));?>
										</button>
										<button onClick="cancelar();return false;">
											<?php echo(getTText("cancelar",C_UCWORDS));?>
										</button>
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
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php 
	$objConn = NULL; 
?>