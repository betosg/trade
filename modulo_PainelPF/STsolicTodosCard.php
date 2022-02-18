<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	$chave 			= request("chave");
	$intCodDado 	= request("var_chavereg"); 	// COD PF
	$intCodPJ 		= request("var_cod_pj");	// COD PJ
	$strOperacao 	= request("var_oper"); 		// Operação a ser realizada
	$strExec 		= request("var_exec"); 		// Executor externo (fora do kernel)
	$strPopulate 	= request("var_populate"); 	// Flag para necessidade de popular o session ou não
	$strAcao 		= request("var_acao"); 		// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
	$auxCounter 	= 0;

	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");

	// ABRE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	$objConn->beginTransaction();	
	try{
		// LOCALIZA UMA CONTA A PAGAR QUE ESTEJA 
		// QUITADA E QUE SEJA SINDICCAL. CONTROLES
		// IMPOSTOS APÓS ANÁLISE NO SINDIEVENTOS.
		// PARA ALGUNS CASOS, É PRECISO QUE CONTRIBUIÇÕES
		// SINDICAL ESTEJA PAGA PARA LIBERAR ALGUMA AÇÃO
		$strSQL = "SELECT cod_conta_pagar_receber FROM fin_conta_pagar_receber WHERE situacao = 'lcto_total' AND ano_vcto = '".date("Y")."' AND (historico ILIKE '%sindical%' OR historico ILIKE '%GRCS%' OR historico ILIKE '%sind%') AND codigo = ".$intCodPJ;
		$objResultSIND = $objConn->query($strSQL);
		// echo($objResultSIND->rowCount());
		
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e) {
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	if((getsession(CFG_SYSTEM_NAME."_grp_user") != "ADMIN") && (getsession(CFG_SYSTEM_NAME."_grp_user") != "SU")){
		if($objResultSIND->rowCount() <= 0){
			mensagem("err_sql_titulo","err_sql_desc_card",getTText("contrib_sindical_nao_paga_em_lote",C_NONE),"STpainelPJ.php","aviso",1);
			die();
		}
	}
	
	$objConn->beginTransaction();
	try{
		// LOCALIZA O PRODUTO DO ANO CORRENTE PARA
		// EXIBIÇÃO DE DADOS, ETC, ANTES DA CONFIRMAÇÃO
		$strSQL = "
			SELECT 
				  cod_produto
				, rotulo
				, descricao 
				, valor
			FROM prd_produto 
			WHERE CURRENT_DATE BETWEEN dt_ini_val_produto AND dt_fim_val_produto
			AND tipo = 'card' AND visualizacao = 'publico'
			AND dtt_inativo IS NULL ORDER BY dt_fim_val_produto DESC";
		// die($strSQL);
		$objResultCARDATUAL = $objConn->query($strSQL);
		$objRSCARDATUAL	    = $objResultCARDATUAL->fetch();
		
		// LOCALIZA PFS DE DETERMINADA PJ, TODAS ELAS
		// DEPOIS O TRATAMENTO NO LAÇO SEPARARÁ QUEM SERÁ
		// RENOVADO OS PEDIDOS DE CREDENCIAL
		$strSQL = "
			SELECT 	
				  t2.cod_pf
				, t2.nome
				, t2.cpf
				, t1.cod_pj
				, t1.cnpj,t1.razao_social
				, count(t5.cod_credencial) AS qtde_credencial
				, count(t6.cod_pedido)     AS qtde_ped_card
				, (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora
				, (t5.dt_validade - CURRENT_DATE ) AS vencida
			FROM cad_pj t1 
			INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
			INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) 
			LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) 
			LEFT OUTER JOIN sd_credencial t5 ON (t5.dtt_inativo is NULL AND t5.cod_pf = t2.cod_pf AND CURRENT_DATE <= dt_validade) 
			LEFT OUTER JOIN prd_pedido t6 ON (t6.situacao <> 'cancelado' AND t6.it_tipo = 'card' AND t6.it_cod_pf = t2.cod_pf AND t6.cod_pj = t3.cod_pj AND CURRENT_DATE <= t6.it_dt_fim_val_produto ) 
			WHERE t1.cod_pj = ".$intCodPJ." 
			GROUP BY 
				  t1.cod_pj
				, t2.cod_pf
				, t2.nome
				, t2.cpf
				, t1.cod_pj
				, t1.cnpj
				, t1.razao_social
				, (CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour'
				, vencida 
			ORDER BY t2.cod_pf";
		// die($strSQL);
		$objResult = $objConn->query($strSQL);
		
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	
	// INICIALIZA VAR PARA PINTAR LINHA 
	$strColor = CL_CORLINHA_2;
	// FUNÇÃO PARA TROCA DE CORES
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
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
				location.href="../modulo_PainelPJ/STindex.php";
				//window.history.back(-1);
			}
			
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("solic_card",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STsolicTodosCardexec.php" method="post">
		<input type="hidden" name="var_valor"       value="<?php echo(getValue($objRSCARDATUAL,"valor"));?>" />
		<input type="hidden" name="var_cod_produto" value="<?php echo(getValue($objRSCARDATUAL,"cod_produto"));?>" />
		<input type="hidden" name="var_cod_pj"      value="<?php echo($intCodPJ);?>" />
		<tr><td height="12" style="padding:20px 0px 0px 20px;"><strong><?php echo(getTText("solicitacao_card",C_NONE)); ?></strong></td></tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%"> 
					<tr><td colspan="2" height="15">&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong><?php echo(getTText("dados_dos_colaboradores",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr>
						<td colspan="2">
						<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
							<thead>
								<tr>
									<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_pf",C_TOUPPER));?></th>
									<th width="10%" class="sortable" nowrap><?php echo(getTText("cpf",C_TOUPPER));?></th>
									<th width="85%" class="sortable" nowrap><?php echo(getTText("nome",C_TOUPPER));?></th>
								</tr>
							</thead>
							<tbody>
							<?php foreach($objResult as $objRS){?>
							<?php if((getValue($objRS,"qtde_credencial") < 1) && (getValue($objRS,"qtde_ped_card") < 1)){ $auxCounter++;?>
								<tr bgcolor="<?php echo(getLineColor($strColor));?>">
									<td align="center" style="vertical-align:top;"><?php echo(getValue($objRS,"cod_pf"));?></td>
									<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cpf"));?></td>
									<td align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"nome"));?></td>
								</tr>
							<?php }?>
							<?php }?>
							</tbody>
						</table>
						</td>
					</tr>
					<tr><td colspan="2" height="10"></td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong><?php echo(getTText("dados_da_pessoa_juridica",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("cod_pj",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,"cod_pj"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("razao_social",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,"razao_social"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("cnpj",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRS,"cnpj"));?></td>
					</tr>
					<tr><td colspan="2" height="15">&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong><?php echo(getTText("observacoes_da_solicitacao",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("rotulo",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRSCARDATUAL,"rotulo"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("descricao",C_NONE));?>:</strong></td>
						<td><?php echo(getValue($objRSCARDATUAL,"descricao"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("valor_unit",C_NONE)); ?>:</strong></td>
						<td><?php echo(number_format((double) getValue($objRSCARDATUAL,"valor"),2,",","."));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("valor_tot",C_NONE)); ?>:</strong></td>
						<td><?php echo(number_format((double) ($auxCounter * getValue($objRSCARDATUAL,"valor")),2,",","."));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><input type="radio" name="var_opcao" id="var_opcao" value="1" checked="checked" style="border:none;background:none"></td>	
						<td><?php echo(getTText("gerar_um_unico_titulo_para_todas_credenciais",C_NONE));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><input type="radio" name="var_opcao" id="var_opcao" value="0" style="border:none;background:none"></td>	
						<td><?php echo(getTText("gerar_um_titulo_para_cada_credencial",C_NONE));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right"><strong><?php echo(getTText("obs",C_NONE));?>:</strong></td>
						<td><textarea id="var_obs" name="var_obs" rows="5" cols="55"></textarea></td>
					</tr>				
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
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
<?php $objConn = NULL; ?>