<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// VERIFICAÇÃO DE DIREITOS DO USUARIO
	// CORRENTE. PARA UTILIZAR, DESCOMENTE 
	// AS LINHAS ABAIXO
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
	
	// Abertura de Conexão com o BD
	$objConn = abreDBConn(CFG_DB);
	
	$intCodTitulo 			 = request("var_chavereg");
	$strNomeCampoChaveDetail = request("var_field_detail");
	$strLocation 			 = request("var_location");     // SETA o Location para lugar diferente do PADRÃO
	
	$intCodJob = getVarEntidade($objConn, "fin_cod_job");
	
	try{
		// Busca todos os dados da
		// conta_pagar_receber
		$strSQL = "  
			SELECT
			      fin_conta_pagar_receber.cod_conta_pagar_receber
				, fin_conta_pagar_receber.tipo
				, fin_conta_pagar_receber.codigo
				, fin_conta_pagar_receber.dt_emissao
				, fin_conta_pagar_receber.historico
				, fin_conta_pagar_receber.tipo_documento
				, fin_conta_pagar_receber.num_documento
				, fin_conta_pagar_receber.nosso_numero
				, fin_conta_pagar_receber.pagar_receber
				, fin_conta_pagar_receber.dt_vcto
				, fin_conta_pagar_receber.vlr_conta
				, fin_conta_pagar_receber.vlr_saldo
				, fin_conta_pagar_receber.vlr_pago
				, fin_conta_pagar_receber.vlr_desc
				, fin_conta_pagar_receber.vlr_outros
				, fin_conta_pagar_receber.cod_job
				, fin_conta.cod_conta
				, fin_conta.nome AS conta
				, fin_conta_pagar_receber.situacao
				, fin_conta_pagar_receber.obs
				, fin_plano_conta.nome AS plano_conta
				, fin_plano_conta.cod_plano_conta
				, fin_plano_conta.cod_reduzido AS plano_conta_cod_reduzido
				, fin_centro_custo.cod_centro_custo
				, fin_centro_custo.nome AS centro_custo
				, fin_centro_custo.cod_reduzido AS centro_custo_cod_reduzido 
			FROM fin_conta_pagar_receber
			LEFT OUTER JOIN fin_conta ON (fin_conta_pagar_receber.cod_conta = fin_conta.cod_conta)
			LEFT OUTER JOIN fin_plano_conta ON (fin_conta_pagar_receber.cod_plano_conta = fin_plano_conta.cod_plano_conta)
			LEFT OUTER JOIN fin_centro_custo ON (fin_conta_pagar_receber.cod_centro_custo = fin_centro_custo.cod_centro_custo)
			WHERE fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodTitulo;
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($objRS = $objResult->fetch()) {
		// monta o label se é uma conta a pagar ou receber
		$intCodigoTipo = getValue($objRS,"codigo"); 		
		$strTitle = getTText("dados",C_UCWORDS). " " . getTText("conta",C_UCWORDS);
		$strLabelEnt = getTText("receber_de",C_NONE);
		$strLabelCor = "#027C02"; 								//verde		
		$strTITULO   = getTText("conta_receber",C_TOUPPER);
		if(getValue($objRS,"pagar_receber")){
			$strLabelEnt = getTText("pagar_para",C_NONE);
			$strLabelCor = "#FF0000"; 							//vermelho
			$strTITULO   = getTText("conta_pagar",C_TOUPPER);
		}
		$dblVlrOrig = 0;
		$dblVlrTotalDebito = 0;
		$dblVlrTotalPago = 0;
		$dblVlrTotalDesc = 0;
		$dblVlrTotalOutros = 0;
		if(getValue($objRS,"vlr_conta" ) != "") $dblVlrOrig 	   = number_format((double) getValue($objRS,"vlr_conta"),2,",","");
		if(getValue($objRS,"vlr_saldo" ) != "") $dblVlrTotalDebito = number_format((double) getValue($objRS,"vlr_saldo"),2,",","");
		if(getValue($objRS,"vlr_pago"  ) != "") $dblVlrTotalPago   = number_format((double) getValue($objRS,"vlr_pago"),2,",","");
		if(getValue($objRS,"vlr_desc"  ) != "") $dblVlrTotalDesc   = number_format((double) getValue($objRS,"vlr_desc"),2,",","");
		if(getValue($objRS,"vlr_outros") != "") $dblVlrTotalOutros = number_format((double) getValue($objRS,"vlr_outros"),2,",","");
		$strSQL = "";
		
		if((getValue($objRS,"tipo") == "cad_pf" || getValue($objRS,"tipo") == "cad_pf") && is_numeric($intCodigoTipo))
		if(getValue($objRS,"tipo") == "cad_pf" and is_numeric($intCodigoTipo)) { 
			$strSQL = "SELECT nome FROM cad_pf WHERE cod_pf = " . $intCodigoTipo; 
		}
		if(getValue($objRS,"tipo") == "cad_pj" and is_numeric($intCodigoTipo)) { 
			$strSQL = "SELECT nome_fantasia AS nome FROM cad_pj WHERE cod_pj =" . $intCodigoTipo; 
		}
		
		$strEntidade = "";
		if($strSQL != "") {
			try{ $objResultTipo = $objConn->query($strSQL); }
			catch(PDOException $e){ mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);	die();
		}
			if($objRSTipo = $objResultTipo->fetch()) { $strEntidade = getValue($objRSTipo, "nome"); } 
			$objResultTipo->closeCursor();
		}
		
		// -------------------------------
		// Verifica situação da conta
		// -------------------------------
		$strMsg = "";
			
		if(getValue($objRS,"situacao") == "cancelado" ) { $strMsg = "Conta encontra-se CANCELADA!"; }
		if(getValue($objRS,"situacao") == "lcto_total") { $strMsg = "Os lançamentos para a conta já foram FEITOS!"; }
		
		if($strMsg != "") {
			mensagem("err_sql_desc_card","erro_ins_tit_pago",$strMsg,"","erro",1,"not_html");
			echo("<script type='text/javascript'>
  			// Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  				resizeIframeParent('".CFG_SYSTEM_NAME."_detailiframe_".request("var_chavereg")."',0);
			// ----------------------------------------------------------------------------------------------------------
			</script>");
			die();
		}
	?> 
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="JavaScript" type="text/javascript">
		<!--
			function replaceAll(str, de, para){
    			var pos = str.indexOf(de);
			    while (pos > -1){
					str = str.replace(de, para);
					pos = str.indexOf(de);
				}
			    return (str);
			}
			
			function calculaTotaisLcto(prLcto,prJuros,prDesc,prIDCampoTitulo,prIDCampoConta){
				var dblLcto, dblJuros, dblDesc, strIDCampoTitulo, strIDCampoConta;
				
				strIDCampoTitulo = prIDCampoTitulo;
				strIDCampoConta  = prIDCampoConta;
				
				dblLcto  = (document.getElementById(prLcto).value  == "") ? 0 : document.getElementById(prLcto).value;
				dblJuros = (document.getElementById(prJuros).value == "") ? 0 : document.getElementById(prJuros).value;
				dblDesc  = (document.getElementById(prDesc).value  == "") ? 0 : document.getElementById(prDesc).value;
				
				dblLcto = replaceAll(dblLcto,'.','');
				dblLcto = replaceAll(dblLcto,',','.');
				
				dblJuros = replaceAll(dblJuros,'.','');
				dblJuros = replaceAll(dblJuros,',','.');
				
				dblDesc = replaceAll(dblDesc,'.','');
				dblDesc = replaceAll(dblDesc,',','.');
				
				dblTotalTitulo = parseFloat(dblLcto);
				dblTotalContaB = parseFloat(dblLcto) + parseFloat(dblJuros) - parseFloat(dblDesc);
				
				if(strIDCampoTitulo != null){ document.getElementById(strIDCampoTitulo).innerHTML = FloatToMoeda(RoundNumber(dblTotalTitulo,2)); }
				if(strIDCampoConta  != null){ document.getElementById(strIDCampoConta).innerHTML  = FloatToMoeda(RoundNumber(dblTotalContaB,2)); }
			}
			
			var strLocation = null;
			var dbCCheck = false;

			function ok() {
				strLocation = "../modulo_FinContaPagarReceber/STifrlancamento.php?var_chavereg=<?php echo($intCodTitulo)?>";
				submeterForm();
			}

			function cancelar() {
				document.location.href = "../modulo_FinContaPagarReceber/STifrlancamento.php?var_chavereg=<?php echo($intCodTitulo)?>";
			}

			function aplicar() {
				strLocation = "../modulo_FinContaPagarReceber/STinslctoordinario.php?var_chavereg=<?php echo($intCodTitulo)?>";
				submeterForm();
			}

			function submeterForm() {
				if (!dbCCheck) {
					dbCCheck = true;
					document.formconf.var_location.value = strLocation;
					document.formconf.submit();
				} else {
					alert("DuploClik detectado! ATENÇÃO - não utilizar clique duplo.\n(O sistema tentará enviar o formulário apenas uma vez...)");
					return false; 
				}
			}
		
			function searchModulo(prType){
				if(prType == "pessoa"){
					combo         = document.forms[0].var_tipo;
					strModulo     = "CadPJ";
					strComponente = "var_codigo";
				}
				else if(prType == "centrocusto"){
					strModulo     = "FinCentroCusto";
					strComponente = "var_cod_centro_custo";
				}
				else if(prType == "planoconta"){
					strModulo     = "FinPlanoConta";
					strComponente = "var_cod_plano_conta";
				}
				
				AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + strComponente + "&var_formname=formconf","800","600");
			}
		//-->
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
	 <tr>
	   <td align="center" valign="top">
		<?php athBeginFloatingBox("600","none",$strTITULO . " - " . getTText("lcto_ord",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
			<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
			  <form name="formconf" action="STinslctoordinarioexec.php" method="post">
				<input type="hidden" name="var_chavereg" 	value="<?php echo(getValue($objRS,"cod_conta_pagar_receber")); ?>">
				<input type="hidden" name="var_tipo_conta" 	value="<?php echo((getValue($objRS,"pagar_receber") != false) ? "pagar" : "receber"); ?>">
				<input type="hidden" name="var_location" 	value="">
				<input type="hidden" name="var_codigo" 		value="<?php echo(getValue($objRS,'codigo')); ?>">
				<input type="hidden" name="var_tipo" 		value="<?php echo(getValue($objRS,'tipo')); ?>">
				<tr>
					<td align="center" valign="top">
						<table width="500" border="0" cellspacing="0" cellpadding="4">
							<tr><td colspan="2" height="10"></td></tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><b><?php echo($strTitle); ?></b></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td align="right"><b><?php echo(getTText("cod_conta_pagar_receber",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td><?php echo($intCodTitulo); ?></td>
							</tr>			 		
							<tr bgcolor="#FAFAFA"> 
								<td align="right" style="color:<?php echo($strLabelCor); ?>;"><b><?php echo($strLabelEnt); ?>:&nbsp;</b></td>
								<td valign="middle"><?php echo($strEntidade); ?></td>
							</tr>
							<tr>	
								<td align="right"><b><?php echo(getTText("cod_conta",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td><?php echo(getValue($objRS,"conta")) ?></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td align="right"><b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td>
								<?php 
									echo(getValue($objRS,"plano_conta"));
									if(getValue($objRS,"plano_conta_cod_reduzido") != "") { echo("&nbsp;&nbsp;&nbsp;" . getValue($objRS,"plano_conta_cod_reduzido")); }
								?>
								</td>
							</tr>
							<tr> 
								<td align="right"><b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td>
								<?php 
									echo(getValue($objRS,"centro_custo"));
									if(getValue($objRS,"centro_custo_cod_reduzido") != "") { echo("&nbsp;&nbsp;&nbsp;" . getValue($objRS,"centro_custo_cod_reduzido")); }
								?>		
								</td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right"><b><?php echo(getTText("vlr_conta",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td><?php echo($dblVlrOrig); ?></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td align="right"><b><?php echo(getTText("nosso_numero",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td><?php echo(getValue($objRS,"nosso_numero")); ?></td>
							</tr>
							<tr>
								<td align="right"><b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td>
									<table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
										<tr>
											<td width="90px">
												<?php echo(getTText(strtolower(getValue($objRS,"tipo_documento")),C_UCWORDS)); ?>
											</td>
											<td width="110px" align="right"><b><?php echo(getTText("num_documento",C_UCWORDS)); ?>:&nbsp;</b></td>
											<td><?php echo(getValue($objRS,"num_documento")); ?></td>
										</tr>
									</table>
								</td>	
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right"><b><?php echo(getTText("dt_emissao",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td>
									<table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
										<tr>
											<td width="90px"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false)); ?></td>
											<td width="110px" align="right"><b><?php echo(getTText("dt_vcto",C_UCWORDS)); ?>:&nbsp;</b></td>
											<td><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false)); ?></td>
										</tr>
									</table>
								</td>		
							</tr>
							<tr bgcolor="#FFFFFF"> 
								<td align="right"><b><?php echo(getTText("historico",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td style="padding-left:2px;"><?php echo(getValue($objRS,"historico")); ?></td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td><?php echo(getValue($objRS,"obs")); ?></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("dados_lancamento",C_UCWORDS)); ?></b></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr> 
								<td align="right" valign="middle">*<b><?php echo(getTText("conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle"> 
									<select name="var_cod_conta" class="edtext" style="width:230px;">
										<?php echo(montaCombo($objConn,"SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS  NULL ORDER BY 2","cod_conta","nome",getValue($objRS,"cod_conta"))); ?>
									</select>
								</td>
							</tr> 
							<tr> 
								<td align="right" valign="middle">*<b><?php echo(getTText("centro_custo",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle">
									<table border="0px" cellpadding="0px" cellspacing="0px">
										<tr>
											<td style="padding-right:3px;" valign="middle">
												<select name="var_cod_centro_custo" class="edtext" style="width:230px;">
													<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY 2","cod_centro_custo","nome",'')); ?>
												</select>
											</td>
											<td valign="middle">
												<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('centrocusto');" class="inputclean">
											</td> 			
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td align="right" valign="middle">*<b><?php echo(getTText("plano_conta",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle">
									<table border="0px" cellpadding="0px" cellspacing="0px">
										<tr valign="middle">
											<td style="padding-right:3px;">
												<select name="var_cod_plano_conta" class="edtext" style="width:307px;">
													<?php 
													   //echo(montaCombo($objConn," SELECT cod_plano_conta, COALESCE(cod_reduzido,NULL,'') || ' ' || COALESCE(nome,NULL,'') AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ","cod_plano_conta","rotulo",getValue($objRS,'cod_plano_conta'))); 
													   //CHAMADO que originou a TAREFA 22116 - pedia par aque o código reduzido não aparece no COMBO 25/11/2013
													   echo(montaCombo($objConn,"SELECT cod_plano_conta, (COALESCE(nome,NULL,'') || ' (' || COALESCE(cod_reduzido,NULL,'') || ')') AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY 2 ","cod_plano_conta","rotulo",getValue($objRS,'cod_plano_conta'))); 
													?>
												</select>	
											</td>
											<td>
												<input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('planoconta');" class="inputclean">
											</td>
										</tr>
									</table>
								</td>
							</tr> 
							<tr>
								<td align="right" valign="middle"><b><?php echo(getTText("job",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle">
									<table border="0px" cellpadding="0px" cellspacing="0px">
										<tr valign="middle">
											<td style="padding-right:3px;">
												<select name="var_cod_job" class="edtext" style="width:230px;">
													<option value=""></option>
													<?php echo(montaCombo($objConn," SELECT cod_job, nome FROM fin_job WHERE dtt_inativo IS NULL ORDER BY nivel, ordem ","cod_job","nome",getValue($objRS,'cod_job'))); ?>
												</select>	
											</td>
										</tr>
									</table>
								</td>
							</tr> 
							<tr>		
								<td align="right" valign="top"><b><?php echo(getTText("valores",C_UCWORDS)); ?>:&nbsp;</b></td>
								<td>
									<table width="330px;" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td>
												<table cellpadding="0" cellspacing="0" width="100%" border="0">
													<tr>
														<td align="left" width="80px" height="18"><?php echo(getTText("original",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><?php echo($dblVlrOrig); ?><input type="hidden" name="var_vlr_orig" value="<?php echo($dblVlrOrig); ?>"></td>
													</tr>
													<tr>
														<td align="left" height="18"><?php echo(getTText("ja_pago",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><?php echo($dblVlrTotalPago); ?></td>
													</tr>
													<tr>
														<td align="left" height="18"><?php echo(getTText("desconto",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><?php echo($dblVlrTotalDesc); ?></td>
													</tr>
													<tr>
														<td align="left" height="18"><?php echo(getTText("outros",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><?php echo($dblVlrTotalOutros); ?></td>
													</tr>
													<tr>
														<td align="left" height="18"><?php echo(getTText("debito",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><strong><?php echo($dblVlrTotalDebito); ?></strong></td>
													</tr>
													<!--tr>
														<td align="left" height="18"><?php echo(getTText("multa",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><input name="var_vlr_multa" class="edtext" dir="rtl" maxlength="12" type="text" style="width:80px;" value="0" onKeyPress="return(validateFloatKeyNew(this,event));"></td>
													</tr-->
													<tr>
														<td align="left" height="16"><?php echo(getTText("desconto",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><input name="var_vlr_desc" id="var_vlr_desc"class="edtext" dir="rtl" maxlength="12" type="text" style="width:80px;" value="0" onKeyPress="return(validateFloatKeyNew(this,event));"></td>
													</tr>
													<tr>
														<td align="left" height="16"><?php echo(getTText("juros",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><input name="var_vlr_juros" id="var_vlr_juros" class="edtext" dir="rtl" maxlength="12" type="text" style="width:80px;" value="0" onKeyPress="return(validateFloatKeyNew(this,event));"></td>
													</tr>
													<tr>
														<td align="left"><?php echo(getTText("lancamento",C_UCWORDS)); ?>:&nbsp;</td>
														<td align="right"><input name="var_vlr_lcto" id="var_vlr_lcto" class="edtext" dir="rtl" maxlength="12" type="text" style="width:80px;" onKeyPress="return(validateFloatKeyNew(this,event));" value="<?php echo($dblVlrTotalDebito); ?>"></td>
													</tr>
													<tr><td colspan="2" style="border-bottom:1px dashed #CCC;">&nbsp;</td></tr>
													<tr>
														<td align="left" height="16"><strong><?php echo(getTText("total_a_ser_abatido_titulo",C_NONE));?>:</strong>&nbsp;</td>
														<td align="right">
															<span id="valor_abatido_titulo" style="text-align:right">0,00</span>
														</td>
													</tr>
													<tr>
														<td align="left" height="16"><strong><?php echo(getTText("total_entrada_conta_banco",C_NONE));?>:</strong>&nbsp;</td>
														<td align="right">
															<span id="valor_entrada_banco"  style="text-align:right">0,00</span>
														</td>
													</tr>
												</table>
											</td>
											<td style="padding-left:10px;padding-bottom:8px;vertical-align:bottom;">
												<input type="button" value="<?php echo(getTText("calcular",C_UCWORDS)); ?>" onClick="calculaTotaisLcto('var_vlr_lcto','var_vlr_juros','var_vlr_desc','valor_abatido_titulo','valor_entrada_banco');" class="inputclean">
											</td>
										</tr>
									</table>
								</td>	
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right" valign="middle">*<b><?php echo(getTText("numero",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle"><input name="var_num_lcto" type="text" class="edtext" style="width:125px;" maxlength="50" value="<?php if(getValue($objRS,"cod_conta_pagar_receber") != ""){echo("TITULO: ".getValue($objRS,"cod_conta_pagar_receber"));}?>"></td>
							</tr>
							<tr> 
								<td align="right" valign="middle">*<b><?php echo(getTText("data",C_UCWORDS)); ?> Pagamento:</b>&nbsp;</td>
								<td valign="middle"><input name='var_dt_lcto' id='var_dt_lcto' class='edtext' value='<?php echo(date('d/m/Y')); ?>' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" ></td>
							</tr>
							<tr> 
								<td align="right" valign="middle">*<b><?php echo(getTText("data",C_UCWORDS)); ?> Crédito:</b>&nbsp;</td>
								<td valign="middle"><input name='var_dt_cred' id='var_dt_cred' class='edtext' value='<?php echo(date('d/m/Y')); ?>' type='text' maxlength='10' style='width:70px;' onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);" ></td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right" valign="middle">*<b><?php echo(getTText("historico",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle"><input name="var_historico" type="text" class="edtext" maxlength="50" style="width:357px;" value="<?php echo(getValue($objRS,"historico"));?>"></td>
							</tr>
							<tr> 
								<td align="right" valign="top"><b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>&nbsp;</td>
								<td valign="middle"><textarea name="var_obs" class="edtext" rows="7" style="width:357px;"></textarea></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><b><?php echo(getTText("dados_documento",C_UCWORDS)); ?></b></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td align="right">*<b><?php echo(getTText("tipo_documento",C_UCWORDS)); ?>:</b></td>
								<td>
									<select name="var_documento">
										<option value="BOLETO"><?php echo(getTText("boleto",C_TOUPPER)); ?></option>
										<option value="CHEQUE"><?php echo(getTText("cheque",C_UCWORDS)); ?></option>
										<option value="DINHEIRO"><?php echo(getTText("dinheiro",C_UCWORDS)); ?></option>
										<option value="EXTRATO"><?php echo(getTText("extrato",C_TOUPPER)); ?></option>
										<option value="HOLERITE"><?php echo(getTText("holerite",C_TOUPPER)); ?></option>
										<option value="TARIFA"><?php echo(getTText("tarifa",C_TOUPPER)); ?></option>
										<option value="TED"><?php echo(getTText("ted",C_UCWORDS)); ?></option>
										<option value="CARTAO_VISA_DEB"><?php echo(getTText("visa_deb",C_UCWORDS)); ?></option>
										<option value="CARTAO_VISA_CRED"><?php echo(getTText("visa_cred",C_UCWORDS)); ?></option>
										<option value="CARTAO_MASTER_DEB"><?php echo(getTText("master_deb",C_UCWORDS)); ?></option>
										<option value="CARTAO_MASTER_CRED"><?php echo(getTText("master_cred",C_UCWORDS)); ?></option>
										<option value="CARTAO_AMEX_CRED"><?php echo(getTText("amex_cred",C_UCWORDS)); ?></option>
									</select>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right">
								</td>
								<td><b><?php echo(getTText("dados_cheque",C_UCWORDS)); ?></b></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td></td>
								<td><span style="text-align:right; width:55px;"><?php echo(getTText("numero_cheque",C_UCWORDS)); ?>:&nbsp;</span><input name="var_cheque_numero" class="edtext" type="text" maxlength="50" style="width:105px;"></td>
							</tr>
							<tr> 
								<td align="right">
								</td>
								<td><b><?php echo(getTText("dados_cartao",C_UCWORDS)); ?></b></td>
							</tr>	
							<tr>
								<td>
								</td>
								<td><span style="text-align:right; width:55px;"><?php echo(getTText("numero",C_UCWORDS)); ?>:&nbsp;</span><input name="var_cartao_numero" id="var_cartao_numero" class="edtext" type="text" maxlength="50" style="width:105px;"><span class="comment_med">&nbsp;Últimos 4 números</span></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<span style="text-align:right; width:55px;"><?php echo(getTText("validade",C_UCWORDS)); ?>:&nbsp;</span><input name='var_cartao_validade' id='var_cartao_validade' class='edtext' value='' type='text' maxlength='7' style='width:70px;'><span class="comment_med">&nbsp;mm/aaaa</span>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<span style="text-align:right; width:55px;"><?php echo(getTText("portador",C_UCWORDS)); ?>:&nbsp;</span><input name="var_cartao_portador" class="edtext" type="text" maxlength="50" style="width:225px;">
								</td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td align="right">
									<b><?php echo(getTText("obs",C_UCWORDS)); ?>:</b>
								</td>
								<td><input type="text" class="edtext" style="width:225px;" name="var_extra_documento" /> </td>
							</tr>
							<tr>
								<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
							</tr>
							<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
							<tr>
								<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
									<button onClick="ok();">
										<?php echo(getTText("ok",C_UCWORDS));?>
									</button>
									<button onClick="cancelar();return false;">
										<?php echo(getTText("cancelar",C_UCWORDS));?>
									</button>
									<button onClick="aplicar();">
										<?php echo(getTText("aplicar",C_UCWORDS));?>
									</button>
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
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php
}
?>