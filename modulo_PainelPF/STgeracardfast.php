<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodDado  = request("var_chavereg"); // cod_pj_pf - relação
$intCodPJ	 = request("var_cod_pj");   // cod_pj
$strOperacao = request("var_oper"); // Operação a ser realizada
$strExec 	 = request("var_exec"); // Executor externo (fora do kernel)
$strAcao 	 = request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

// busca o produto do tipo card de maior valor
// e validade corrente para exibicao dos dados
// em tela - confirmação para a PJ logada
try{
	$strSQL = "
			SELECT
				rotulo,
				descricao, 
				cod_produto, 
				valor
    		FROM 
				prd_produto
		    WHERE tipo = 'card'
		    AND dtt_inativo IS NULL
    		AND CURRENT_DATE < dt_fim_val_produto
		    ORDER BY dt_fim_val_produto DESC";
	$objResult = $objConn->query($strSQL);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
$intCodProduto   = getValue($objRS,"cod_produto");
$strRotuloProd 	 = getValue($objRS,"rotulo");
$strDescProd     = getValue($objRS,"descricao");
$dblValorProduto = getValue($objRS,"valor");


// busca a PF corrente com base no cod_pf
// enviado e cod_pj tbm. Para exibição dos
// dados em tela - confirmação da PJ
try{
	$strSQL = "
			SELECT 
				cad_pf.cod_pf,
				cad_pj.razao_social,
				cad_pj.cod_pj,
				cad_pj.cnpj,
				cad_pf.nome,
				cad_pf.cpf,
				cad_pf.rg,
				cad_pf.email,
				cad_pf.matricula,
				relac_pj_pf.funcao
			FROM
				relac_pj_pf 
			INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf)
			INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj)
			WHERE relac_pj_pf.cod_pj_pf = '".$intCodDado."'";
	$objResult = $objConn->query($strSQL);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
$intCodPF 		= getValue($objRS,"cod_pf");
$intRG			= getValue($objRS,"rg");
$intCPF			= getValue($objRS,"cpf");
$strNome		= getValue($objRS,"nome");
$strEmail		= getValue($objRS,"email");
$intMatricula	= getValue($objRS,"matricula");
$strFuncao		= getValue($objRS,"funcao");
$strRazaoSocial	= getValue($objRS,"razao_social");
$intCNPJ		= getValue($objRS,"cnpj");
$intCodPJ		= getValue($objRS,"cod_pj");



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
			
			function showTable(){
				if(document.getElementById('table_titulo').style.display == 'block'){
					document.getElementById('table_titulo').style.display = 'none';
				}else{
					document.getElementById('table_titulo').style.display = 'block';
				}
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("solic_card",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STgeracardfastexec.php" method="post">
		<input type="hidden" name="var_cod_produto" value="<?php echo($intCodProduto);?>" />
		<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>" />
		<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>" />
		<input type="hidden" name="var_opcao" value="uma_empresa" />
		<input type="hidden" name="var_tipo_doc" value="<?php echo("BOLETO");?>">
		<input type="hidden" name="var_valor" value="<?php echo($dblValorProduto);?>" />
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("solicitacao_card",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS PESSOA FÍSICA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("cod_pf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCodPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("int_matricula",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intMatricula); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("nome",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("rg",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intRG); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("cpf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("email",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strEmail); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strFuncao); ?></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS PESSOA JURÍDICA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cod_pj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCodPJ); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCNPJ); ?></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>OBSERVAÇÕES DA SOLICITAÇÃO</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("rotulo",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRotuloProd); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("descricao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strDescProd); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("valor",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(number_format((double) $dblValorProduto,2,",",".")); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("obs",C_NONE)); ?>:</strong>
						</td>
						<td><textarea id="var_obs_card" name="var_obs_card" rows="5" cols="55"></textarea></td>
					</tr>
					<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
					
					
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%">
							<input type="checkbox" name="var_opcao_gerar_titulo" value="S" class="inputclean" onclick="showTable();"/>
						</td>
						<td>&nbsp;<?php echo(getTText("opcao_pagar_ato",C_NONE));?></td>
					</tr>
					
					
					<tr>
						<td colspan="2">
							<table cellpadding="4" cellspacing="0" border="0" width="100%" id="table_titulo" style="display:none;">
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde">
										<strong>DADOS PARA O TÍTULO</strong>
									</td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<!-- ------------------------------------------ -->
								
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
									<td>&nbsp;<input name="var_valor" id="var_valor" value="<?php echo(($dblValorProduto == "") ? "0,00" : number_format((double) $dblValorProduto,2,",","."));?>" size="10" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this,event);" />
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
									<td>&nbsp;<input name="var_historico" id="var_historico" value="CARD FAST" size="60" />
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
							</table>
						</td>
					</tr>
					
					
										
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_info.gif"></td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_solic_card",C_NONE))?></td>
									<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
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