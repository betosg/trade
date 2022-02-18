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
	
	// PEGA PREFIX DO SESSION
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

	// TRATAMENTO
	if($intCodDado == ""){
		mensagem("err_sql_titulo","err_sql_desc_card",getTText("cod_ped_invalido",C_NONE),"","aviso",1);
		die();
	}
	
	// ABRE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// LOCALIZA DADOS DO COLABORADOR, LIGADOS COM
	// OS DADOS DO TÍTULO, EMPRESA E FUNÇÃO 
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
				, sd_homologacao.situacao AS situacao_homo
				, cad_pf.nome
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
				, cad_pj.endprin_fone1
				, cad_pj.endprin_fone2
				, relac_pj_pf.cod_pj_pf
				, relac_pj_pf.tipo
				, relac_pj_pf.funcao
				, relac_pj_pf.departamento
				, relac_pj_pf.dt_admissao
				, fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.cod_pedido
				, fin_conta_pagar_receber.nosso_numero
				, UPPER(fin_conta_pagar_receber.situacao) AS situacao
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.historico
			FROM 
				  sd_homologacao
			INNER JOIN cad_pj ON (cad_pj.cod_pj = sd_homologacao.cod_pj)
			INNER JOIN cad_pf ON (cad_pf.cod_pf = sd_homologacao.cod_pf)
			INNER JOIN relac_pj_pf ON (relac_pj_pf.cod_pj = cad_pj.cod_pj AND relac_pj_pf.cod_pf = cad_pf.cod_pf)
			LEFT  JOIN fin_conta_pagar_receber ON (sd_homologacao.cod_pedido = fin_conta_pagar_receber.cod_pedido)
			WHERE sd_homologacao.cod_homologacao = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	// MSG QUE COLABORADOR JÁ FOI HOMOLOGADO
	if(getValue($objRS,"situacao_homo") != "aberto"){
		mensagem("err_sql_titulo","err_sql_desc_card","Esta HOMOLOGAÇÃO não está em situação para ser CONFIRMADA.","","aviso",1);
		die();	
	}
	
	// INICIALIZA LINHA PARA BOX
	$strColor = CL_CORLINHA_1;
	
	// FUNÇÃO DE LINHAS PARA BOX
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
				if(
				   (document.getElementById("var_dt_homologacao").value == "")||
				   (document.getElementById("var_dt_demissao").value == "")||
				   (document.getElementById("var_hr_homologacao").value == "")
				  ){ strMSG += "\n\nDADOS PARA HOMOLOGAÇÃO:"; }
				if(document.getElementById("var_dt_homologacao").value == ""){ strMSG += "\nData da Homologação"; }
				if(document.getElementById("var_dt_demissao").value == ""){ strMSG += "\nData da Demissão"; }
				if(document.getElementById("var_hr_homologacao").value == ""){ strMSG += "\nHora da Homologação"; }
				if(strMSG != ""){
					alert("Preencha os campos obrigatórios abaixo: " + strMSG);
				} else{
					document.formstatic.DEFAULT_LOCATION.value = "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>";
					document.formstatic.submit();
				}
			}
			
			function cancelar() {
				<?php if($strREDIRECT != ""){?>
					location.href = "<?php echo($strREDIRECT);?>";
				<?php } else{ ?>
					location.href = "<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>";
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
			<?php athBeginFloatingBox("720","none","HOMOLOGAÇÕES - (Confirmar Homologação)",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="../modulo_SdHomologacao/STconfirmahomologacaoexec.php" method="post">
					<input type="hidden" name="var_chavereg"     value="<?php echo($intCodDado);?>"  />
					<input type="hidden" name="var_cod_pj_pf"    value="<?php echo(getValue($objRS,"cod_pj_pf"));?>" /> 
					<input type="hidden" name="var_cod_pf"       value="<?php echo(getValue($objRS,"cod_pf"));?>" />
					<input type="hidden" name="DEFAULT_LOCATION" value="" />
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
									<td align="right" width="20%"><strong><?php echo(getTText("razao_social",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"razao_social"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="25%"><strong><?php echo(getTText("nome_fantasia",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"nome_fantasia"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cnpj",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"cnpj"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cidade",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"endprin_cidade"));?> | <?php echo(getValue($objRS,"endprin_estado"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("endprin_fone1",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"endprin_fone1"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("endprin_fone2",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"endprin_fone2"));?></td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO COLABORADOR</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("nome",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"nome"));?>
										<?php if(getValue($objRS,"foto")!=""){?>
										<div style="position:absolute;right:10%;vertical-align:top;text-align:right;"><img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/fotospf/<?php echo(getValue($objRS,"foto"));?>" width="150"/></div>
										<?php }?>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("rg",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"rg"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cpf",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"cpf"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("ctps",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"ctps"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("tipo",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"tipo"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("funcao",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"funcao"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("dt_admissao",C_NONE));?>:</strong></td>
									<td><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false));?></td>
								</tr>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO TÍTULO VINCULADO</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<?php if(getValue($objRS,"cod_conta_pagar_receber") != ""){?>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("cod_titulo",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"cod_conta_pagar_receber"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("pedido",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"cod_pedido"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("nosso_numero",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"nosso_numero"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("situacao",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"situacao"));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("vlr_orig",C_NONE));?>:</strong></td>
									<td><?php echo(FloatToMoeda(getValue($objRS,"vlr_conta")));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("vlr_saldo",C_NONE));?>:</strong></td>
									<td><?php echo(FloatToMoeda(getValue($objRS,"vlr_saldo")));?></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><strong><?php echo(getTText("historico",C_NONE));?>:</strong></td>
									<td><?php echo(getValue($objRS,"historico"));?></td>
								</tr>
								<?php } else{ ?>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="center" valign="middle" colspan="2" style="border:1px dashed #CCC;font-size:09px;color:#CCC;"><strong><?php echo(getTText("nenhum_tit_vinculado",C_NONE));?></strong></td>
									<td><?php echo(getValue($objRS,"cod_conta_pagar_receber"));?></td>
								</tr>
								<?php }?>
								<tr><td colspan="2" height="5">&nbsp;</td></tr>
								
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS PARA HOMOLOGAÇÃO</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="25%" valign="top"><strong>*<?php echo(getTText("dt_homologacao",C_NONE));?>:</strong></td>
									<td>
										<input name="var_dt_homologacao" id="var_dt_homologacao" class="edtext" value="<?php echo(dDate(CFG_LANG,now(),false));?>" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" >
										&nbsp;&nbsp;<input type="text" name="var_hr_homologacao" id="var_hr_homologacao" class="edtext" value="" maxlength="5" size="5" onkeyPress="FormataInputHoraMinuto(this,event);" />
										&nbsp;&nbsp;<span class="comment_med">(Formato dd/mm/aaaa HH:MM - somente 4 dígitos)</span><br />
										<span class="comment_med">A data informada neste campo finalizará a agenda vinculada. A agenda poderá ser visualizada posteriormente.</span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="25%"><strong>*<?php echo(getTText("dtt_demissao",C_NONE));?>:</strong></td>
									<td><input name="var_dt_demissao" id="var_dt_demissao" class="edtext" value="" type="text" maxlength="10" style="width:70px;" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" >&nbsp;<span class="comment_med">(Formato dd/mm/aaaa)</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right" width="25%" valign="top"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></td>
									<td><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea><br><span class="comment_med">Informe qualquer comentário para a homologação.</span></td>
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