<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	$intCodDado  = request("var_chavereg"); // COD_PJ_PF
	$intCodPJ	 = request("var_cod_pj");   // COD_PJ
	
	// CARREGA PREFIX DOS SESSION
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");

	// ABRE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);

	// LOCALIZA PRODUTO CARTEIRINHA DE MAIOR VALOR
	// E COM VALIDADE VIGENTE, ATÉ OS 'PRIVADOS'
	try{
		$strSQL = "
				SELECT
					  prd_produto.rotulo
					, prd_produto.descricao
					, prd_produto.cod_produto
					, prd_produto.valor
				FROM  prd_produto
				WHERE prd_produto.tipo = 'card'
				AND prd_produto.dtt_inativo IS NULL
				AND CURRENT_DATE < dt_fim_val_produto
				ORDER BY dt_fim_val_produto DESC";
		$objResultP = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// FETCH EM DADOS
	// $objRS = $objResult->fetch();
	// $intCodProduto   = getValue($objRS,"cod_produto");
	// $strRotuloProd 	 = getValue($objRS,"rotulo");
	// $strDescProd     = getValue($objRS,"descricao");
	// $dblValorProduto = getValue($objRS,"valor");


	// LOCALIZA PF CORRENTE COM BASE NO COD_RELACAO
	// TUDO ISSO PARA CONFIRMAÇÃO DOS DADOS EM TELA
	try{
		$strSQL = "
				SELECT 
					  cad_pf.cod_pf
					, cad_pj.razao_social
					, cad_pj.cod_pj
					, cad_pj.cnpj
					, cad_pf.nome
					, cad_pf.cpf
					, cad_pf.rg
					, cad_pf.email AS email_pf
					, cad_pj.email AS email_pj
					, cad_pf.matricula
					, relac_pj_pf.funcao
				FROM
					relac_pj_pf 
				INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf)
				INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj)
				WHERE relac_pj_pf.cod_pj_pf = ".$intCodDado;
		$objResultC = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// FETCH EM DADOS
	$objRS = $objResultC->fetch();
	// $intCodPF 		= getValue($objRS,"cod_pf");
	// $intRG			= getValue($objRS,"rg");
	// $intCPF			= getValue($objRS,"cpf");
	// $strNome			= getValue($objRS,"nome");
	// $strEmail		= getValue($objRS,"email");
	// $intMatricula	= getValue($objRS,"matricula");
	// $strFuncao		= getValue($objRS,"funcao");
	// $strRazaoSocial	= getValue($objRS,"razao_social");
	// $intCNPJ			= getValue($objRS,"cnpj");
	// $intCodPJ		= getValue($objRS,"cod_pj");
	
	// INICIALIZA VARIÁVEL PARA PINTAR LINHA
	$strColor = CL_CORLINHA_1;
	
	// FUNÇÃO PARA CORES DE LINHAS
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// Calcula a DATA DE VENCIMENTO
	$strTIPO = "card";
	$intQtdeDiasVctoPadrao = getVarEntidade($objConn,"pedido_qtde_dias_vcto_padrao");
	$intQtdeDiasVctoPadrao = ($intQtdeDiasVctoPadrao == "") ? 0 : $intQtdeDiasVctoPadrao;
	$intQtdeDiasVctoPadrao = (($strTIPO == "homo") || ($strTIPO == "card")) ? "2" : $intQtdeDiasVctoPadrao;
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
	<title><?php echo(CFG_SYSTEM_TITLE);?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		.table_master{
			background-color:#FFFFFF;
			border:1px solid #BBB;
			padding-bottom: 5px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	<!--
		//****** Funções de ação dos botões - Início ******
		var strLocation = null;
		var dbCCheck = false;

		function ok() {	
			var strMSG = "";
			if (!dbCCheck) {
				dbCCheck = true;

				if(document.getElementById("var_produto").value 		== ""){ strMSG += "\n\nDADOS PRODUTO/PEDIDO:"; }
				if(document.getElementById("var_produto").value 		== ""){ strMSG += "\nProduto"; }
				
				// CASO A OPÇÃO PARA GERAÇÃO DE TÍTULO ESTEJA MARCADA
				// ENTÃO DEVE TRATAR OS CAMPOS OBRIGATÓRIOS C/PAGAR RECEBER
				if(document.getElementById("var_opcao_gerar_titulo").checked == true){
					if(
						(document.getElementById("var_valor").value 			== "")||
						(document.getElementById("var_dt_vcto").value 			== "")||
						(document.getElementById("var_cod_conta").value 		== "")||
						(document.getElementById("var_cod_plano_conta").value 	== "")||
						(document.getElementById("var_cod_centro_custo").value	== "")||
						(document.getElementById("var_historico").value 		== "")||
						(document.getElementById("var_cod_cfg_boleto").value 	== "")
					  ){ strMSG += "\n\nDADOS TÍTULO:"; }
					if(document.getElementById("var_valor").value 			 == ""){ strMSG += "\nValor do Título"; }
					if(document.getElementById("var_dt_vcto").value 		 == ""){ strMSG += "\nData de Vencimento"; }
					if(document.getElementById("var_cod_conta").value 		 == ""){ strMSG += "\nConta"; }
					if(document.getElementById("var_cod_plano_conta").value  == ""){ strMSG += "\nPlano de Contas"; }
					if(document.getElementById("var_cod_centro_custo").value == ""){ strMSG += "\nCentro de Custo"; }
					if(document.getElementById("var_historico").value 		 == ""){ strMSG += "\nHistórico"; }
					if(document.getElementById("var_cod_cfg_boleto").value 	 == ""){ strMSG += "\nTipo de Boleto"; }
				}	
				if(strMSG != ""){ alert("Preencha os Campos Obrigatórios: "+strMSG); }
				else{
					document.getElementById('DEFAULT_LOCATION').value = "../modulo_CadPJ/STviewpfs.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>";
					document.formstatic.submit(); 
				}
			} else {
				alert("DuploClik detectado! ATENÇÃO - não utilizar clique duplo.\n(O sistema tentará enviar o formulário apenas uma vez...)");
				return false; 
			}
		}
		
		function cancelar() { 
			document.location.href = "../modulo_CadPJ/STviewpfs.php?var_chavereg=<?php echo(getValue($objRS,"cod_pj"));?>";
			// window.history.back(); 
		}
		
		// FUNÇÃO QUE GERENCIA OS COLLAPSE/DISPLAY DA TABELA DE TITULOS
		function showTable(){
			if(document.getElementById('table_titulo').style.display == 'block'){ 
				document.getElementById('table_titulo').style.display = 'none'; 
			} else{
				document.getElementById('table_titulo').style.display = 'block';
			}
			resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
		}
		
		// FUNÇÃO QUE LOCALIZA VIA AJAX OS DADOS DO PRODUTO
		function changePROD(){
			if(document.getElementById("var_produto") 		== null){ return(null); }
			if(document.getElementById("var_produto").value == null){ return(null); }
			if(document.getElementById("var_produto").value == ""  ){ return(null); }
			var objAjax;
			var strReturnValue;
			var strSQL = "SELECT prd_produto.valor, prd_produto.descricao FROM prd_produto WHERE cod_produto = "+document.getElementById("var_produto").value;
			document.getElementById("ajax_loader").innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' />";
			objAjax = createAjax();
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						arrReturn = strReturnValue.split("|");
						if((arrReturn[0] != "")||(arrReturn[0] != null)){ document.getElementById("prod_valor").innerHTML = FloatToMoeda(arrReturn[0]); document.getElementById("var_valor").value = FloatToMoeda(arrReturn[0]); }
						if((arrReturn[1] != "")||(arrReturn[1] != null)){ document.getElementById("prod_descr").innerHTML = arrReturn[1]; }
						document.getElementById("var_historico").value = arrReturn[1] + ' ('+ document.getElementById('var_nome_colaborador').value + ')' ;
						document.getElementById("ajax_loader").innerHTML = "";
						// FAÇA ALGO!
					} else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/returndados.php?var_sql=" + strSQL,  true); 
			objAjax.send(null); 
		}
		
		//****** Funções de ação dos botões - Fim ******
	//-->
	</script>
</head>
<body style="margin:10px;" bgcolor="#FFFFFF">
<center>
<?php athBeginFloatingBox("680","","CREDENCIAL - (Solicitação Rápida)",CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="660" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
			<strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 80px 10px 80px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
			<form name="formstatic" id="formstatic" action="STgeracardfastexec.php" method="post">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>" />
				<input type="hidden" name="DEFAULT_LOCATION" id="DEFAULT_LOCATION" value="" />
				<input type="hidden" name="var_cod_pf" value="<?php echo(getValue($objRS,"cod_pf"));?>" />
				<input type="hidden" name="var_cod_pj" value="<?php echo(getValue($objRS,"cod_pj"));?>" />
				<input type="hidden" name="var_email_pj" value="<?php echo(getValue($objRS,"email_pj"));?>" />
				<input type="hidden" name="var_email_pf" value="<?php echo(getValue($objRS,"email_pf"));?>" />
				<input type="hidden" name="var_nome_colaborador" id="var_nome_colaborador" value="<?php echo(getValue($objRS,"nome"));?>" />
				<input type="hidden" name="var_tipo_doc" value="BOLETO">
				<!-- DADOS PESSOA FÍSICA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pessoa_fisica",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_pf",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_pf"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("matricula",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"matricula"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"nome"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("rg",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"rg"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cpf"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("funcao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"funcao"));?></td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				<!-- DADOS PESSOA JURÍDICA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pessoa_juridica",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_pj",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cod_pj"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("razao_social",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"razao_social"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cnpj",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><?php echo(getValue($objRS,"cnpj"));?></td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				<!-- DADOS PEDIDO/PRODUTO -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pedido_produto",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("produto",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_produto" id="var_produto" style="width:180px;" onChange="changePROD();">
							<option value="" selected="selected"></option>
							<?php echo(montaCombo($objConn,"SELECT UPPER(prd_produto.rotulo) AS rotulo, prd_produto.cod_produto FROM prd_produto WHERE prd_produto.tipo = 'card' AND prd_produto.dtt_inativo IS NULL AND CURRENT_DATE < dt_fim_val_produto ORDER BY valor, dt_fim_val_produto DESC","cod_produto","rotulo",""));?>
						</select>
						&nbsp;<span id="ajax_loader"></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><span id="prod_descr">-</span></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("valor",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><span id="prod_valor">-</span></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("obs_pedido",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><textarea name="var_obs_pedido" id="var_obs_pedido" rows="6" cols="60"></textarea></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><input type="checkbox" name="var_opcao_gerar_titulo" id="var_opcao_gerar_titulo" value="TRUE" class="inputclean" onClick="showTable();"/></td>
					<td width="77%" align="left">&nbsp;<?php echo(getTText("opcao_pagar_ato",C_NONE));?></td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				
				<tr>
					<td colspan="2">
					<table cellpadding="4" cellspacing="0" border="0" width="100%" id="table_titulo" style="display:none;">
						<tr bgcolor="#FFFFFF">
							<td width="23%" align="right">&nbsp;</td>
							<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_titulo",C_TOUPPER));?></strong></td>
						</tr>
						<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo_documento",C_NONE));?>:</strong></td>
							<td width="77%" align="left">BOLETO</td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("valor",C_NONE));?>:</strong></td>
							<td width="77%" align="left"><input name="var_valor" id="var_valor" value="" size="10" maxlength="10" onKeyPress="return validateFloatKeyNew(this,event);" /></td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("dt_vcto",C_NONE));?>:</strong></td>
							<td width="77%" align="left"><input type="text" name="var_dt_vcto" id="var_dt_vcto" value="<?php echo($dtVcto);?>" size="12" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="return validateNumKey(event);" /></td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("conta",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<select name="var_cod_conta" id="var_cod_conta" size="1" style="width:180px;">
									<?php echo(montaCombo($objConn,"SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_conta","nome","",""));?>
								</select>
							</td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("plano_conta",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<select name="var_cod_plano_conta" id="var_cod_plano_conta" size="1" style="width:240px;">
									<?php echo(montaCombo($objConn,"SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome","cod_plano_conta","rotulo",259,""));?>
								</select>
							</td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("centro_custo",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<select name="var_cod_centro_custo" id="var_cod_centro_custo" size="1" style="width:160px;">
									<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_centro_custo","nome","",""));?>
								</select>
							</td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("historico",C_NONE));?>:</strong></td>
							<td width="77%" align="left"><input name="var_historico" id="var_historico" value="" size="60" maxlength="200" /></td>
						</tr>
						<!--tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("num_lcto",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<input name="var_num_lcto" id="var_num_lcto" value="<?php echo(getValue($objRS,"cod_pf").getValue($objRS,"cod_pj"))?>" size="15" maxlength="30" />
								<br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>	
							</td>
						</tr>
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("num_documento",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<input name="var_num_documento" id="var_num_documento" value="<?php echo(str_replace(" ","",(str_replace(":","",(str_replace("-","",now()))))));?>" size="15" maxlength="30" />
								<br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>	
							</td>
						</tr-->
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong><?php echo(getTText("obs",C_NONE));?>:</strong></td>
							<td width="77%" align="left"><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea></td>
						</tr>				
						<tr bgcolor="<?php echo(getLineColor($strColor));?>">
							<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("boleto",C_NONE));?>:</strong></td>
							<td width="77%" align="left">
								<select name="var_cod_cfg_boleto" id="var_cod_cfg_boleto" size="1" style="width:160px;">
									<?php echo(montaCombo($objConn,"SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao","cod_cfg_boleto","descricao","",""));?>
								</select>
							</td>
						</tr>
						
						<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
						<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>ENVIO DE EMAIL</strong></td></tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<!-- ------------------------------------------ -->
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
						<!-- ------------------------------------------ -->
						<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
						<tr><td colspan="2" height="10"></tr>
					</table>
					</td>
				</tr>
				<tr><td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				<tr>
					<td colspan="2">
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td><img src="../img/mensagem_info.gif"></td>
						<td><?php echo(getTText("aviso_solic_card",C_NONE))?></td>
						<td align="right">
							<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>
							<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						</td>
					</tr>
					</table>
					</td>	
				</tr>
			</form>			
			</table>			
		</td>
	</tr>
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>





<!--table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("solic_card",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STgeracardfastexec.php" method="post">
		
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
						<td align="right"><strong><?php echo(getTText("rotulo",C_NONE)); ?>:</strong></td>
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
								<-- ------------------------------------------ ->
								
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
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script-->
</html>
<?php $objConn = NULL; ?>