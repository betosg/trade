<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	$intCodDado   = request("var_chavereg");   
	$strOperacao  = request("var_oper");		
	$strExec      = request("var_exec");		
	$strPopulate  = request("var_populate");	
	$strAcao   	  = request("var_acao");
	$strREDIRECT  = request("var_redirect");		
	
	// Popula o session para fazer a abertura dos ítens do módulo
	// if($strPopulate == "yes") { initModuloParams(basename(getcwd())); }
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

	// Só é feito a busca e exibição dos dados 
	// seja enviado como parametro para este script.
	if($intCodDado == ""){
		mensagem("err_sql_titulo","err_sql_desc_card",getTText("cod_ped_invalido",C_NONE),"","aviso",1);
		die();
	}
	
	// ABRE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// Verifica a existencia de pedidos abertos de carteirinhas para 
	// a PJ corrente - Busca usando como parâmetro o cod_pedido 
	// tbm enviado para esta pag. - uma das situações
	try{
		$strSQL = "
			SELECT	
				  sd_homologacao.cod_pj
				, sd_homologacao.cod_pf
				, sd_homologacao.cod_pedido
				, sd_homologacao.pf_nome
				, sd_homologacao.pf_empresa
				, sd_homologacao.pf_rg
				, sd_homologacao.pf_cpf
				, sd_homologacao.pf_funcao
				, sd_homologacao.pf_matricula
				, sd_homologacao.dtt_homologacao
				, cad_pf.nome
				, cad_pf.apelido
				, cad_pf.nome_pai
				, cad_pf.nome_mae
				, cad_pf.cpf
				, cad_pf.rg
				, cad_pf.ctps
				, cad_pf.foto
				, cad_pf.cod_pf as cod_pf_credencial
				, cad_pj.razao_social
				, cad_pj.nome_fantasia
				, cad_pj.cnpj
				, cad_pj.endprin_cidade
				, cad_pj.endprin_estado
				, relac_pj_pf.cod_pj_pf
				, relac_pj_pf.tipo
				, relac_pj_pf.dt_admissao
			FROM 
				  sd_homologacao, cad_pj, cad_pf, relac_pj_pf
			WHERE sd_homologacao.cod_homologacao = ".$intCodDado."
			AND   sd_homologacao.cod_pj = cad_pj.cod_pj 
			AND   sd_homologacao.cod_pf = cad_pf.cod_pf 
			AND   relac_pj_pf.cod_pj = cad_pj.cod_pj
			AND   relac_pj_pf.cod_pf = cad_pf.cod_pf";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","","erro",1);
		die();
	}

	// Fetch dos dados da credencial
	$objRS = $objResult->fetch();
	$intCodPedido 		= (getValue($objRS,"cod_pedido") 		== "") ? "" : getValue($objRS,"cod_pedido");
	$intCodPJ 			= (getValue($objRS,"cod_pj") 			== "") ? "" : getValue($objRS,"cod_pj");
	$strRazaoSocial 	= (getValue($objRS,"razao_social") 		== "") ? "" : getValue($objRS,"razao_social");
	$strNomeFantasia 	= (getValue($objRS,"nome_fantasia") 	== "") ? "" : getValue($objRS,"nome_fantasia");
	$strCNPJ 			= (getValue($objRS,"cnpj") 				== "") ? "" : getValue($objRS,"cnpj");
	$strCidade 			= (getValue($objRS,"endprin_cidade") 	== "") ? "" : getValue($objRS,"endprin_cidade");
	$strEstado 			= (getValue($objRS,"endprin_estado") 	== "") ? "" : getValue($objRS,"endprin_estado");
	$intCodPF 			= (getValue($objRS,"cod_pf") 			== "") ? "" : getValue($objRS,"cod_pf");
	$intCodPFCard 		= (getValue($objRS,"cod_pf_credencial") == "") ? "" : getValue($objRS,"cod_pf_credencial");
	$strNome 			= (getValue($objRS,"nome") 				== "") ? "" : getValue($objRS,"nome");
	$strApelido 		= (getValue($objRS,"apelido") 			== "") ? "" : getValue($objRS,"apelido");
	$strNomePai 		= (getValue($objRS,"nome_pai") 			== "") ? "" : getValue($objRS,"nome_pai");
	$strNomeMae 		= (getValue($objRS,"nome_mae") 			== "") ? "" : getValue($objRS,"nome_mae");
	$strRG 				= (getValue($objRS,"rg") 				== "") ? "" : getValue($objRS,"rg");
	$strCPF 			= (getValue($objRS,"cpf") 				== "") ? "" : getValue($objRS,"cpf");
	$strCTPS 			= (getValue($objRS,"ctps")	 			== "") ? "" : getValue($objRS,"ctps");
	$intCodPJPF 		= (getValue($objRS,"cod_pj_pf") 		== "") ? "" : getValue($objRS,"cod_pj_pf");
	$strFuncao 			= (getValue($objRS,"pf_funcao") 		== "") ? "" : getValue($objRS,"pf_funcao");
	$strNumMatricula 	= (getValue($objRS,"pf_matricula")	 	== "") ? "" : getValue($objRS,"pf_matricula");
	$dtAdmissao 		= (getValue($objRS,"dt_admissao") 		== "") ? "" : dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false);
	$strTipo 			= (getValue($objRS,"tipo") 				== "") ? "" : getValue($objRS,"tipo");
	
	// Monstra mensagem que o COLABORADOR JÁ FOI HOMOLOGADO
	if(getValue($objRS,"dtt_homologacao") != ""){
		mensagem("err_sql_titulo","err_sql_desc_card","Este Colaborador já está HOMOLOGADO para esta empresa.","","aviso",1);
		die();	
	}
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
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
			function ok(){
				var strMSG = "";
				if(document.getElementById("var_dt_homologacao").value == ""){ strMSG += "\n\nDADOS PARA HOMOLOGAÇÃO:"; }
				if(document.getElementById("var_dt_homologacao").value == ""){ strMSG += "\nData da Homologação"; }
				if(strMSG != ""){
					alert("Preencha os campos obrigatórios abaixo: " + strMSG);
				} else{
					document.formstatic.submit();
				}
			}
			
			function cancelar() {
				<?php if($strREDIRECT != ""){?>
					location.href = "<?php echo($strREDIRECT);?>";
				<?php } else{ ?>
					location.href = "../modulo_PainelAdmin/STindex.php";
				<?php }?>
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 10px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("720","none","CONFIRMAR HOMOLOGAÇÃO (de Colaborador)",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="STConfirmaHomoexec.php" method="post">
					<input type="hidden" name="var_chavereg" 	value="<?php echo($intCodDado);?>" />
					<input type="hidden" name="var_cod_pj_pf" 	value="<?php echo($intCodPJPF);?>" />
					<input type="hidden" name="var_cod_pf_card" value="<?php echo($intCodPFCard);?>" />
					<input type="hidden" name="var_redirect"  	value="<?php echo($strREDIRECT)?>"   />
					<tr>
						<td height="12" style="padding:20px 0px 0px 20px;"><strong><?php echo(getTText("confirmacao_homologacao",C_NONE));?></strong></td>
					</tr>
					<tr>
						<td align="center" valign="top" style="padding:20px 80px 10px 80px;" width="1%">
							<table cellpadding="4" cellspacing="0" border="0" width="100%">
								<!-- ------------------- -->
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><strong><?php echo(getTText("razao_social",C_NONE));?>:</strong></td>
									<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><strong><?php echo(getTText("nome_fantasia",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strNomeFantasia); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strCNPJ); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cidade",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strCidade); ?> / <?php echo($strEstado); ?></td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO COLABORADOR</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("nome",C_NONE)); ?>:</strong></td>
									<td>&nbsp;
										<?php echo($strNome);?>
										<?php if(getValue($objRS,"foto")!=""){?>
										<div style="position:absolute;right:10%;vertical-align:top;text-align:right;"><img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/fotospf/<?php echo(getValue($objRS,"foto"));?>" width="150"/></div>
										<?php }?>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("nome_pai",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strNomePai); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("nome_mae",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strNomeMae); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("rg",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strRG); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cpf",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strCPF); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("ctps",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strCTPS); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("tipo",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strTipo); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($strFuncao); ?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("dt_admissao",C_NONE)); ?>:</strong></td>
									<td>&nbsp;<?php echo($dtAdmissao); ?></td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS PARA HOMOLOGAÇÃO</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong>*<?php echo(getTText("dt_homologacao",C_NONE));?>:</strong></label></td>
									<td><input name='var_dt_homologacao' id='var_dt_homologacao' class='edtext' value='' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" >&nbsp;<span class="comment_med">(Formato dd/mm/aaaa)</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong><?php echo(getTText("dtt_demissao",C_NONE));?>:</strong></label></td>
									<td><input name='var_dt_demissao' id='var_dt_demissao' class='edtext' value='' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" >&nbsp;<span class="comment_med">(Formato dd/mm/aaaa)</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_obs"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></label></td>
									<td><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea><br><span class="comment_med">Informe qualquer comentário para a homologação.</span></td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DE DECLARAÇÕES E TERMOS</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong><?php echo(getTText("declaracao",C_NONE));?>:</strong></label></td>
									<td>
										<select name="var_html_declaracao" id="var_html_declaracao" style="width:200px;">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT titulo, texto FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL AND tipo = 'declaracao' ORDER BY titulo","texto","titulo",""));?>
										</select>
										&nbsp;<span class="texto_corpo_peq"></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong><?php echo(getTText("declaracao_op",C_NONE));?>:</strong></label></td>
									<td>
										<select name="var_html_declaracao_op" id="var_html_declaracao_op" style="width:200px;">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT titulo, texto FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL AND tipo = 'declaracao' ORDER BY titulo","texto","titulo",""));?>
										</select>
										&nbsp;<span class="texto_corpo_peq"></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong><?php echo(getTText("ressalva",C_NONE));?>:</strong></label></td>
									<td>
										<select name="var_html_ressalva" id="var_html_ressalva" style="width:200px;">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT titulo, texto FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL AND tipo = 'ressalva' ORDER BY titulo","texto","titulo",""));?>
										</select>
										&nbsp;<span class="texto_corpo_peq"></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="35%"><label for="var_dt_demissao"><strong><?php echo(getTText("termo",C_NONE));?>:</strong></label></td>
									<td>
										<select name="var_html_ressalva" id="var_html_ressalva" style="width:200px;">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT titulo, texto FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL AND tipo = 'termo' ORDER BY titulo","texto","titulo",""));?>
										</select>
										&nbsp;<span class="texto_corpo_peq"></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" valign="top" width="35%"><label for="var_dt_demissao"><strong>*<?php echo(getTText("situacao",C_NONE));?>:</strong></label></td>
									<td>
										<input type="radio" name="var_situacao" id="var_situacao" class="inputclean" value="confirmado" checked="checked">&nbsp;Homologação realizada sem nenhuma observação, com sucesso.<span class="texto_corpo_peq"></span><br/>
										<input type="radio" name="var_situacao" id="var_situacao" class="inputclean" value="cancelado" >&nbsp;Homologação não realizada na data combinada, por observação registrada.<span class="texto_corpo_peq"></span>
									</td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
								<tr>
									<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
										<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
											<tr>
												<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_aviso.gif"></td>
												<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_confirma_homo",C_NONE))?></td>
												<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
													<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
													<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<!-- ------------------- -->
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