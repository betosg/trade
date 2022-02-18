<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	//$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
	


	
	// REQUESTS
	$intCodDado = request("var_chavereg"); // cod_titulo
	$strDB = request("var_db");
	$strCriptoBoleto = request("var_boleto");

	if ($strCriptoBoleto!=""){
		$arrBoleto = explode("|",base64_decode($strCriptoBoleto));
		$intCodDado = $arrBoleto[0];
		$strDB      = $arrBoleto[1];
	}
	
	// Abre conexão com DB	

if ($strDB == "")
	{ $strDB = CFG_DB; }
$objConn = abreDBConn($strDB);
	
	try{
		$strSQL = " SELECT
					  fin_conta_pagar_receber.vlr_conta
					, fin_conta_pagar_receber.vlr_mora_multa
					, fin_conta_pagar_receber.vlr_outros_acresc
					, fin_conta_pagar_receber.num_documento
					, fin_conta_pagar_receber.nosso_numero
					, fin_conta_pagar_receber.dt_emissao
					, fin_conta_pagar_receber.dt_vcto
					, fin_conta_pagar_receber.obs
					, fin_conta_pagar_receber.instrucoes_boleto
					, fin_conta_pagar_receber.situacao
					, fin_conta_pagar_receber.ano_vcto
					, fin_conta_pagar_receber.historico
					
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT cod_pf FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT cod_pj FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT cod_pj_fornec FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_codigo
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT razao_social FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_nome
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT cpf FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT cnpj FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT cnpj FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_cnpj
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_cep FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_cep FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_cep FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_cep
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endcobr_rotulo FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_rotulo
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_logradouro FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_logradouro FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_logradouro FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_logradouro
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_numero FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_numero FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_numero FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_numero
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_complemento FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_complemento FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_complemento FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_complemento
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_bairro FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_bairro FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_bairro FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_bairro
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_cidade FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_cidade FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_cidade FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_cidade
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_estado FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_estado FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_estado FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_estado
					
					, cad_cnae_secao.cod_digi_secao_cnae
					, cad_cnae_divisao.cod_digi_divisao
					, cad_cnae_grupo.cod_digi_grupo
					, cad_cnae_classe.cod_digi_classe
					, cad_cnae_subclasse.cod_digi_subclasse
					
					, prd_pedido.cli_cep         AS ped_endcobr_cep
					, prd_pedido.cli_rotulo      AS ped_endcobr_rotulo
					, prd_pedido.cli_logradouro  AS ped_endcobr_logradouro
					, prd_pedido.cli_num         AS ped_endcobr_numero
					, prd_pedido.cli_complemento AS ped_endcobr_complemento
					, prd_pedido.cli_bairro      AS ped_endcobr_bairro
					, prd_pedido.cli_cidade      AS ped_endcobr_cidade
					, prd_pedido.cli_estado      AS ped_endcobr_estado
					
					, cfg_boleto.modelo_html
					, cfg_boleto.aceite
					, cfg_boleto.especie
					, cfg_boleto.especie_doc
					, cfg_boleto.agencia					
					, cfg_boleto.conta
					, cfg_boleto.conta_dv
					, cfg_boleto.cedente
					, cfg_boleto.cedente_dv
					, cfg_boleto.carteira
					, cfg_boleto.inic_nosso_numero
					, cfg_boleto.local_pgto
					, cfg_boleto.rotulo
					, cfg_boleto.cedente_nome_simples
					, cfg_boleto.cedente_nome_completo
					, cfg_boleto.cedente_cnpj
					, cfg_boleto.cedente_logradouro
					, cfg_boleto.cedente_num
					, cfg_boleto.cedente_comp
					, cfg_boleto.cedente_bairro
					, cfg_boleto.cedente_cidade
					, cfg_boleto.cedente_estado
					, cfg_boleto.cedente_cep
					, cfg_boleto.instrucoes_1
					, cfg_boleto.instrucoes_2
					, cfg_boleto.instrucoes_3
					, cfg_boleto.instrucoes_4
					, cfg_boleto.instrucoes_5
					, cfg_boleto.msg_extra_1
					, cfg_boleto.msg_extra_2
					, cfg_boleto.msg_extra_3
					, cfg_boleto.msg_extra_4
					
					FROM fin_conta_pagar_receber 
					INNER JOIN cad_pj 		       ON (fin_conta_pagar_receber.codigo = cad_pj.cod_pj)
					INNER JOIN cfg_boleto 	   	   ON (fin_conta_pagar_receber.cod_cfg_boleto = cfg_boleto.cod_cfg_boleto)
					LEFT JOIN cad_cnae_secao   	   ON (cad_pj.cod_cnae_n1 = cad_cnae_secao.cod_cnae_secao)
					LEFT JOIN cad_cnae_divisao     ON (cad_pj.cod_cnae_n2 = cad_cnae_divisao.cod_cnae_divisao)
					LEFT JOIN cad_cnae_grupo   	   ON (cad_pj.cod_cnae_n3 = cad_cnae_grupo.cod_cnae_grupo)
					LEFT JOIN cad_cnae_classe      ON (cad_pj.cod_cnae_n4 = cad_cnae_classe.cod_cnae_classe)
					LEFT JOIN cad_cnae_subclasse   ON (cad_pj.cod_cnae_n5 = cad_cnae_subclasse.cod_cnae_subclasse)
					LEFT OUTER JOIN prd_pedido 	   ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido)
                    WHERE fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodDado ." AND cad_pj.cod_pj = ". getsession(CFG_SYSTEM_NAME."_entidade_codigo");
                    
			$objResult = $objConn->query($strSQL);
			$objRS 	   = $objResult->fetch();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Testa se o RESULTSET não é um ARRAY [?]
	if($objRS !== array()){ 
		$strMODELO_HTML = getValue($objRS,"modelo_html");
		$strSituacao 	= getValue($objRS,"situacao");
		// Caso modelo HTML Vazio
		if ($strMODELO_HTML == "") {
			mensagem("err_modelo_boleto_nao_encontrado_titulo","err_modelo_boleto_nao_encontrado_desc","","","erro",1);
			die();
		}
		// Caso o TITULO esteja CANCELADO
		if ($strSituacao == "cancelado") {
			mensagem("alert_titulo_cancelado_titulo","alert_titulo_cancelado_desc","","","aviso",1);
			die();
		}
		// Caso o TITULO esteja PAGO
		if ($strSituacao == "lcto_total") {
			mensagem("alert_titulo_pago_titulo","alert_titulo_pago_desc","","","aviso",1);
			die();
		}
		// CASO O TITULO esteja AGRUPADO
		if ($strSituacao == "agrupado") {
			mensagem("alert_titulo_agrupado_titulo","alert_titulo_agrupado_desc","","","aviso",1);
			die();
		}
		
		// para exibição das parcelas
		if ($strSituacao == "parcelado") {
			try{
				$strSQL = "
					SELECT 
						  cod_conta_pagar_receber
						, nosso_numero
						, vlr_conta
						, vlr_pago
						, dt_emissao
						, dt_vcto
						, situacao
						, historico
					FROM
						fin_conta_pagar_receber
					WHERE cod_agrupador = ".$intCodDado;
				$objResultParc = $objConn->query($strSQL);
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
			
			// caso resultado torne mais de um registro,
			// então monta tablesort com a listagem de
			// parcelas
			if($objResultParc->rowCount() > 0){
				echo("
				<html>
				<head>
				<title></title>
				<meta http-equiv='Content-Type' content='text/html;charset=iso-8859-1'>
				<link href='_css/default.css' rel='stylesheet' type='text/css'>
				<link rel='stylesheet' type='text/css' href='../_css/tablesort.css'>
				<link href='../_css/".CFG_SYSTEM_NAME.".css' rel='stylesheet' type='text/css>
				<script type='text/javascript' src='../_scripts/tablesort.js'></script>
				<style>
					ul{ margin-top: 0px; margin-bottom: 0px; }
					li{ margin-left: 0px; }
				</style>
				</head>
				<body style='margin:20px 20px;' bgcolor='".CL_CORLINHA_1."'>
				<table bgcolor='#FFFFFF' style='width:100%;margin-bottom:0px;' class='tablesort'>
				<thead>
				<tr>
				<th width='1%'></th>
				<th width='10%' class='sortable' nowrap>COD</th>
				<th width='10%' class='sortable' nowrap>NOSSO NUM</th>
				<th width='11%' class='sortable-numeric' nowrap>VLR ORIG</th>
				<th width='11%' class='sortable-numeric' nowrap>VLR PAGO</th>
				<th width='11%' class='sortable-date-dmy' nowrap>EMISSAO</th>
				<th width='11%' class='sortable-date-dmy' nowrap>VCTO</th>
				<th width='15%' class='sortable' nowrap>SITUACAO</th>
				<th width='20%' class='sortable' nowrap>HISTORICO</th>
				</tr>
				</thead>
				<tbody>");
				foreach($objResultParc as $objRSParc){
					echo("
					<tr>
						<td align='center' style='vertical-align:middle;'>");
						if(getValue($objRSParc,"situacao") == "aberto"){
							echo("<a href='../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=".getValue($objRSParc,"cod_conta_pagar_receber")."' target='_blank'>");
							echo("<img src='../img/icon_boleto.gif' alt='Exibir Boleto' title='Exibir Boleto' border='0'></a>");
						}
						echo("
						</td>
						<td align='center' style='vertical-align:middle;'>".getValue($objRSParc,"cod_conta_pagar_receber")."</td>
						<td align='center' style='vertical-align:middle;'>".getValue($objRSParc,"nosso_numero")."</td>
						<td align='right' style='vertical-align:middle;'>".number_format((double) getValue($objRSParc,"vlr_conta"),2,',','.')."</td>
						<td align='right' style='vertical-align:middle;'>".number_format((double) getValue($objRSParc,"vlr_pago"),2,',','.')."</td>
						<td align='center' style='vertical-align:middle;'>".dDate(CFG_LANG, getValue($objRSParc,"dt_emissao"), false)."</td>
						<td align='center' style='vertical-align:middle;'>".dDate(CFG_LANG, getValue($objRSParc,"dt_vcto"), false)."</td>
						<td align='center' style='vertical-align:middle;'>".strtoupper(getValue($objRSParc,"situacao"))."</td>
						<td align='left' style='vertical-align:middle;'>".getValue($objRSParc,"historico")."</td>
					</tr>		
					");
				}
				echo("
				</tbody>
				</table>");
			}
		}
		else {
			// Title da SCREEN
			$DadosBoleto_IDENTIFICACAO = CFG_SYSTEM_TITLE;
			
			// Caso a conta tenha valor ZERO, não printa NADA
			if(getValue($objRS,"vlr_conta") > 0) {
				$DadosBoleto_VLR_TITULOforma1 = number_format((double) getValue($objRS,"vlr_conta"), 2);
				$DadosBoleto_VLR_TITULOforma1 = str_replace(",", "", $DadosBoleto_VLR_TITULOforma1);
				$DadosBoleto_VLR_TITULOforma1 = str_replace(".", ",", $DadosBoleto_VLR_TITULOforma1);
				$DadosBoleto_VLR_TITULOforma2 = number_format((double) getValue($objRS,"vlr_conta"), 2);
			} else{
				$DadosBoleto_VLR_TITULOforma1 = "";
				$DadosBoleto_VLR_TITULOforma2 = "";
			}
			$DadosBoleto_VLR_DESC_ABATIM     = "";
			$DadosBoleto_VLR_OUTRAS_DEDUCOES = "";
			$DadosBoleto_VLR_COBRADO         = "";
			
			$DadosBoleto_VLR_MORA_MULTA = "";
			if(getValue($objRS,"vlr_mora_multa") > 0) {
				$DadosBoleto_VLR_MORA_MULTA = number_format((double) getValue($objRS,"vlr_mora_multa"), 2);
				$DadosBoleto_VLR_MORA_MULTA = str_replace(",", "", $DadosBoleto_VLR_MORA_MULTA);
				$DadosBoleto_VLR_MORA_MULTA = str_replace(".", ",", $DadosBoleto_VLR_MORA_MULTA);
			}
			$DadosBoleto_VLR_OUTROS_ACRESC = "";
			if(getValue($objRS,"vlr_outros_acresc") > 0) {
				$DadosBoleto_VLR_OUTROS_ACRESC = number_format((double) getValue($objRS,"vlr_outros_acresc"), 2);
				$DadosBoleto_VLR_OUTROS_ACRESC = str_replace(",", "", $DadosBoleto_VLR_OUTROS_ACRESC);
				$DadosBoleto_VLR_OUTROS_ACRESC = str_replace(".", ",", $DadosBoleto_VLR_OUTROS_ACRESC);
			}
			
			$DadosBoleto_CATEGORIA = "OUTRAS";
			$DadosBoleto_HISTORICO = getValue($objRS,"historico");
			if((getValue($objRS,"vlr_mora_multa") > 0) || (getValue($objRS,"vlr_outros_acresc") > 0)) $DadosBoleto_CATEGORIA = "LABORAL";
			
			// LOCALIZA O COD_ENTIDADE SINDICAL
			// UPDATE: CODIGO DO CEDENTE DEIXA DE SER O FORMATO ATUAL DA ENTSINDICAL
			// E PASSA PARA S-99999
			$DadosBoleto_COD_ENT_SINDICAL		= getVarEntidade($objConn,"cod_entsindical_guia");
			
			$DadosBoleto_TAXA_BOLETO 			= number_format((double) getValue($objRS,"vlr_taxa_boleto"), 2);
			$DadosBoleto_DT_VCTOforma1 			= dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false);
			$DadosBoleto_DT_VCTOforma2 			= dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false);
			$DadosBoleto_DT_ANO_VCTO			= getValue($objRS,"ano_vcto");
			$DadosBoleto_DT_EMISSAO 			= dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false);
			$DadosBoleto_DT_PROC 				= dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false);
	
			$DadosBoleto_NOSSO_NUMERO 			= getValue($objRS,"nosso_numero");
			$DadosBoleto_NUM_DOCUMENTO 			= getValue($objRS,"num_documento");
			$DadosBoleto_SACADO_NOME 			= getValue($objRS,"sacado_nome");
			$DadosBoleto_SACADO_CODIGO 			= getValue($objRS,"sacado_codigo");
			$DadosBoleto_SACADO_CNPJ 			= getValue($objRS,"sacado_cnpj");
			$DadosBoleto_SACADO_CNAE_N1			= getValue($objRS,"cod_digi_secao_cnae");
			$DadosBoleto_SACADO_CNAE_N2			= getValue($objRS,"cod_digi_divisao");
			$DadosBoleto_SACADO_CNAE_N3			= getValue($objRS,"cod_digi_grupo");
			$DadosBoleto_SACADO_CNAE_N4			= getValue($objRS,"cod_digi_classe");
			$DadosBoleto_SACADO_CNAE_N5			= getValue($objRS,"cod_digi_subclasse");
			
			/*if (getValue($objRS,"ped_endcobr_logradouro") != "") {
				$DadosBoleto_SACADO_LOGRADOURO 	= getValue($objRS,"ped_endcobr_logradouro");
				$DadosBoleto_SACADO_NUMERO 		= getValue($objRS,"ped_endcobr_numero");
				$DadosBoleto_SACADO_COMPLEMENTO = getValue($objRS,"ped_endcobr_complemento");
				$DadosBoleto_SACADO_BAIRRO		= getValue($objRS,"ped_endcobr_bairro");
				$DadosBoleto_SACADO_CIDADE 		= getValue($objRS,"ped_endcobr_cidade");
				$DadosBoleto_SACADO_ESTADO 		= getValue($objRS,"ped_endcobr_estado");
				$DadosBoleto_SACADO_CEP 		= getValue($objRS,"ped_endcobr_cep");
			}
			else {*/
				$DadosBoleto_SACADO_LOGRADOURO 	= getValue($objRS,"cli_endcobr_logradouro");
				$DadosBoleto_SACADO_NUMERO 		= getValue($objRS,"cli_endcobr_numero");
				$DadosBoleto_SACADO_COMPLEMENTO = getValue($objRS,"cli_endcobr_complemento");
				$DadosBoleto_SACADO_BAIRRO		= getValue($objRS,"cli_endcobr_bairro");
				$DadosBoleto_SACADO_CIDADE 		= getValue($objRS,"cli_endcobr_cidade");
				$DadosBoleto_SACADO_ESTADO 		= getValue($objRS,"cli_endcobr_estado");
				$DadosBoleto_SACADO_CEP 		= getValue($objRS,"cli_endcobr_cep");
			//}
					
			// Coleta informações extras
			$DadosBoleto_MSG_EXTRA_1 = getValue($objRS,"msg_extra_1");
			$DadosBoleto_MSG_EXTRA_2 = getValue($objRS,"msg_extra_2");
			$DadosBoleto_MSG_EXTRA_3 = getValue($objRS,"msg_extra_3");
			$DadosBoleto_MSG_EXTRA_4 = getValue($objRS,"msg_extra_4");
			//echo "obs:".getValue($objRS,"obs");
			// Explode de Informações
			$strObs 			= getValue($objRS,"obs");
			$arrObs 			= explode("<br>", $strObs);
			$DadosBoleto_INFO1 	= "";
			$DadosBoleto_INFO2 	= "";
			$DadosBoleto_INFO3 	= "";
			if (sizeof($arrObs) >= 1) $DadosBoleto_INFO1 = $arrObs[0];
			if (sizeof($arrObs) >= 2) $DadosBoleto_INFO2 = $arrObs[1];
			if (sizeof($arrObs) >= 3) $DadosBoleto_INFO3 = $arrObs[2];
			if ($DadosBoleto_INFO1 == ""){$DadosBoleto_INFO1 = getValue($objRS,"obs");}
			
			// Popula as instruções do BOLETO com preferencial-
			// mente as instruções setadas no PRÓPRIO TÍTULO.
			// Caso não existam informações setadas no título,
			// então preenche com as linhas de informações do
			// BOLETO MESMO.
			if (getValue($objRS,"instrucoes_boleto") == ""){
				// Linhas do Boleto
				$DadosBoleto_INSTRUCOES1 = getValue($objRS,"instrucoes_1");
				$DadosBoleto_INSTRUCOES2 = getValue($objRS,"instrucoes_2");
				$DadosBoleto_INSTRUCOES3 = getValue($objRS,"instrucoes_3");
				$DadosBoleto_INSTRUCOES4 = getValue($objRS,"instrucoes_4");
				$DadosBoleto_INSTRUCOES5 = getValue($objRS,"instrucoes_5");
			} else{ 
				// Explode de Linhas do TÍTULO para cada TAG <BR>
				$strInstrucoes = getValue($objRS,"instrucoes_boleto"); 
				$arrInstrucoes = explode("<br>", $strInstrucoes);
				$DadosBoleto_INSTRUCOES1 = "";
				$DadosBoleto_INSTRUCOES2 = "";
				$DadosBoleto_INSTRUCOES3 = "";
				$DadosBoleto_INSTRUCOES4 = "";
				$DadosBoleto_INSTRUCOES5 = "";
				if (sizeof($arrInstrucoes) >= 1) $DadosBoleto_INSTRUCOES1 = $arrInstrucoes[0];
				if (sizeof($arrInstrucoes) >= 2) $DadosBoleto_INSTRUCOES2 = $arrInstrucoes[1];
				if (sizeof($arrInstrucoes) >= 3) $DadosBoleto_INSTRUCOES3 = $arrInstrucoes[2];
				if (sizeof($arrInstrucoes) >= 4) $DadosBoleto_INSTRUCOES4 = $arrInstrucoes[3];	
				if (sizeof($arrInstrucoes) >= 5) $DadosBoleto_INSTRUCOES5 = $arrInstrucoes[4];	
			}
			
			$DadosBoleto_ACEITE 			   = getValue($objRS,"aceite");
			$DadosBoleto_ESPECIE 			   = getValue($objRS,"especie");
			$DadosBoleto_ESPECIE_DOC 		   = getValue($objRS,"especie_doc");
			$DadosBoleto_AGENCIA 			   = getValue($objRS,"agencia");
			//$DadosBoleto_AGENCIA_DV			   = getValue($objRS,"agencia_dv");
			$DadosBoleto_CONTA 				   = getValue($objRS,"conta");
			$DadosBoleto_CONTA_DV 			   = getValue($objRS,"conta_dv");
			$DadosBoleto_CEDENTE 			   = getValue($objRS,"cedente");
			$DadosBoleto_CEDENTE_DV 		   = getValue($objRS,"cedente_dv");
			$DadosBoleto_CARTEIRA 			   = getValue($objRS,"carteira");
			$DadosBoleto_INIC_NOSSO_NUMERO 	   = getValue($objRS,"inic_nosso_numero");
			$DadosBoleto_LOCAL_PGTO 		   = getValue($objRS,"local_pgto");
			$DadosBoleto_ROTULO 			   = getValue($objRS,"rotulo");
			
			$DadosBoleto_CEDENTE_NOME_SIMPLES  = getValue($objRS,"cedente_nome_simples");
			$DadosBoleto_CEDENTE_NOME_COMPLETO = getValue($objRS,"cedente_nome_completo");
			$DadosBoleto_CEDENTE_CNPJ 		   = getValue($objRS,"cedente_cnpj");
			$DadosBoleto_CEDENTE_LOGRADOURO    = getValue($objRS,"cedente_logradouro");
			$DadosBoleto_CEDENTE_NUMERO 	   = getValue($objRS,"cedente_num");
			$DadosBoleto_CEDENTE_COMPLEMENTO   = getValue($objRS,"cedente_comp");
			$DadosBoleto_CEDENTE_BAIRRO 	   = getValue($objRS,"cedente_bairro");
			$DadosBoleto_CEDENTE_CIDADE 	   = getValue($objRS,"cedente_cidade");
			$DadosBoleto_CEDENTE_ESTADO 	   = getValue($objRS,"cedente_estado");
			$DadosBoleto_CEDENTE_CEP 		   = getValue($objRS,"cedente_cep");

			
			// Coleta Imagem LOGO
			$DadosBoleto_LOGOTIPO = getVarEntidade($objConn,"logotipo_empresa");

			// Fecha conexão com banco /resultset
			$objResult->closeCursor();
			
			// Path base para incluir imagens e outros arquivos dentro das funções de boleto de banco
			$DadosBoleto_PATH_BASE = "..";
			
			// Faz include do layout CEF correto
			if ($strMODELO_HTML == "boleto_cef_padrao")  include_once("../_boletos/boleto_cef_padrao.php");
			if ($strMODELO_HTML == "boleto_cef_grcs"  )  include_once("../_boletos/boleto_cef_grcsu.php" );
			if ($strMODELO_HTML == "boleto_cef_grca"  )  include_once("../_boletos/boleto_cef_grca.php"  );
			if ($strMODELO_HTML == "boleto_bra_padrao")  include_once("../_boletos/boleto_bradesco_padrao.php");
			if ($strMODELO_HTML == "boleto_itau_padrao")  include_once("../_boletos/boleto_itau_padrao.php");
			if ($strMODELO_HTML == "boleto_bra_shopfacil"){
		?>
        		<form name="bradesco_shopfacil" id="bradesco_shopfacil" method="get" action="../_boletos/STshowboletoShopFacil.php">
                	<input type="hidden" name="var_chavereg" id="var_chavereg" value="<?php echo($intCodDado);?>" />                                   
                </form>
                <!--span onclick="javascript:document.getElementById('bradesco_shopfacil').submit();" style="cursor:pointer">[ clique aqui ]</span//-->
                <script language="javascript">
					document.getElementById("bradesco_shopfacil").submit();
				</script>
        <?php	
			}
		
			// if ($strMODELO_HTML == "boleto_cef_grcsu" ) include_once("../_boletos/boleto_cef_grcsu.php" );
		}
	}
	else {
		mensagem("err_sql_titulo","err_titulo_nao_encontrado","","","erro",1);
		die();
	}
	
	$objConn = NULL;
?>