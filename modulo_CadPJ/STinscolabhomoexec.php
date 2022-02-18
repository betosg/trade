<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	// REQUEST - PJ
	$intCodPJ 	 = request("var_cod_pj");
	
	// REQUEST - DADOS PF
	$flagTipoPF  = request("var_tipo_pf");
	$strCPF 	 = request("var_pf_cpf");
	$strRG 	 	 = request("var_pf_rg");
	$strNOME 	 = request("var_pf_nome");
	$chrSEXO 	 = request("var_pf_sexo");
	$strPFOBS 	 = request("var_pf_obs");
	$intPF   	 = request("var_pf");
	$intPFEMP    = request("var_pf_codigo");
	
	// REQUEST - DADOS DA VAGA
	$strCATEG 	 = request("var_vaga_categoria");
	$strTIPO 	 = request("var_vaga_tipo");
	$strFUNCAO 	 = request("var_vaga_funcao");
	$strDEPART 	 = request("var_vaga_departamento");
	$dttADMISSAO = request("var_vaga_admissao");
	$strVAGAOBS	 = request("var_vaga_obs");
	
	// REQUEST - DADOS DA HOMOLOGAÇÃO
	$strHOMOOBS	 = request("var_homo_obs");
	$dttHOMOLOG  = request("var_homo_data");
	
	// REQUEST - DADOS DO TÍTULO E LCTO
	$flagTITULO  = request("var_tit_opcao_gerar");
	$strTPDOC	 = request("var_tit_tipo_documento");
	$dblVALOR	 = MoedaToFloat(request("var_tit_valor"));
	$dttVENC	 = request("var_tit_dt_vcto");
	$dttPGTO  	 = request("var_tit_dt_pgto");
	$intCCUSTO	 = request("var_tit_centro_custo");
	$intCCONTA	 = request("var_tit_conta");
	$intCPLANO	 = request("var_tit_plano_conta");
	$strHIST	 = request("var_tit_historico");
	$strNUMLCTO	 = request("var_tit_numero_lcto");
	$strNUMDOC	 = request("var_tit_numero_documento");
	$strTITOBS	 = request("var_tit_obs");
	$intCBOLETO	 = request("var_tit_boleto");
	
	
	// IMPORTANTE: Busca o PRODUTO de HOMOLOGAÇÃO
	// CORRENTE PARA inserção de PEDIDO - garantindo
	// a cascata para PEDIDO, TITULO e LANÇAMENTO
	try {
		$strSQL = "
				SELECT
					 prd_produto.cod_produto
					,prd_produto.rotulo
					,prd_produto.valor
					,prd_produto.descricao
					,prd_produto.dt_ini_val_produto
					,prd_produto.dt_fim_val_produto
					,prd_produto.tipo
				FROM prd_produto
				WHERE CURRENT_DATE BETWEEN prd_produto.dt_ini_val_produto AND prd_produto.dt_fim_val_produto 
				AND prd_produto.tipo = 'homo'
				AND	prd_produto.dtt_inativo IS NULL
				ORDER BY prd_produto.sys_dtt_ins DESC, prd_produto.valor DESC ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Caso nenhum Produto encontrado, então 
	// exception avisando que produto não cadastrado
	if($objResult->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("produto_homo_validade_off",C_NONE),"","erro",1);
		die();
	} else{
		// Fetch dos dados do produto válido corrente
		$objRS = $objResult->fetch();
		// Coletando dados do produto
		$intCodProduto = getValue($objRS,"cod_produto");
		$strRotuloProd = getValue($objRS,"rotulo");
		$strDescProd   = getValue($objRS,"descricao");
		$dblVlrProduto = getValue($objRS,"valor");
		$dblVlrProduto = MoedaToFloat($dblVlrProduto);
		$dtIniValidade = getValue($objRS,"dt_ini_val_produto");
		$dtFimValidade = getValue($objRS,"dt_fim_val_produto");
		$strTipoProd   = getValue($objRS,"tipo");
	}
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
		// PARA NOVOS COLABORADORES, CAD_PF + RELAÇÃO
		if($flagTipoPF == "NEW_PF"){
			// Insere PF encaminhada [COLABORADOR]
			$strSQL = "INSERT INTO cad_pf (cpf, rg, nome, sexo, obs, sys_usr_ins, sys_dtt_ins) VALUES ('".prepStr($strCPF)."','".prepStr($strRG)."','".prepStr($strNOME)."','".prepStr($chrSEXO)."','".prepStr($strPFOBS)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza esta PF recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_pf FROM cad_pf_cod_pf_seq;");
			$objRS	   = $objResult->fetch();
			$intCodPF  = getValue($objRS,"max_cod_pf");
			
			// Insere RELAÇÃO da Vaga do COLABORADOR para a PJ
			$strSQL = "INSERT INTO relac_pj_pf (cod_pj, cod_pf, tipo, funcao, departamento, dt_admissao, obs, categoria, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",".$intCodPF.",'".prepStr($strTIPO)."','".prepStr($strFUNCAO)."','".prepStr($strDEPART)."','".cDate(CFG_LANG,$dttADMISSAO,false)."','".prepStr($strVAGAOBS)."','".prepStr($strCATEG)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza RELAÇÃO recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq;");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"max_cod_relac");
		}
		// PARA COLABORADOR QUE JÁ EXISTA MAS EM VAGA NENHUMA
		if($flagTipoPF == "OLD_PF"){
			$intCodPF = $intPFEMP;
			// Insere RELAÇÃO da Vaga do COLABORADOR para a PJ
			$strSQL = "INSERT INTO relac_pj_pf (cod_pj, cod_pf, tipo, funcao, departamento, dt_admissao, obs, categoria, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",".$intCodPF.",'".prepStr($strTIPO)."','".prepStr($strFUNCAO)."','".prepStr($strDEPART)."','".cDate(CFG_LANG,$dttADMISSAO,false)."','".prepStr($strVAGAOBS)."','".prepStr($strCATEG)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza RELAÇÃO recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq;");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"max_cod_relac");
		}
		// PARA HOMOLOGAÇÃO DE COLABORADOR QUE ESTEJA EM UMA PJ
		if($flagTipoPF == "PJ_PF"){
			$intCodPF = $intPF;
			// LOCALIZA RELAÇÃO
			$objResult = $objConn->query("SELECT cod_pj_pf FROM relac_pj_pf WHERE cod_pj = ".$intCodPJ." AND cod_pf = ".$intCodPF.";");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"cod_pj_pf");
		}
			
		// Insere PEDIDO CORRESPONDENTE de Homologação
		$strSQL = "INSERT INTO prd_pedido (cod_pj, situacao, valor, it_cod_pf, it_cod_pj_pf, it_cod_produto, it_descricao, it_valor, it_tipo, it_dt_ini_val_produto, it_dt_fim_val_produto, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",'aberto',".$dblVALOR.",".$intCodPF.",".$intCodREL.",".$intCodProduto.",'".prepStr($strRotuloProd." - ".$strDescProd)."',".$dblVlrProduto.",'".prepStr($strTipoProd)."','".cDate(CFG_LANG,$dtIniValidade,false)."','".cDate(CFG_LANG,$dtFimValidade,false)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
		$objConn->query($strSQL);
		
		// Localiza CÓDIGO DO PEDIDO recém-inserido
		$objResult = $objConn->query("SELECT last_value AS max_cod_pedido FROM prd_pedido_cod_pedido_seq;");
		$objRS	   = $objResult->fetch();
		$intCodPED = getValue($objRS,"max_cod_pedido");
		
		// Faz Inserção de TÍTULO
		$strSQL = "INSERT INTO fin_conta_pagar_receber (codigo, tipo, cod_conta, cod_plano_conta, cod_centro_custo, dt_emissao, dt_vcto, vlr_conta, vlr_pago, vlr_saldo, pagar_receber, historico, tipo_documento, obs, situacao, cod_pedido, cod_cfg_boleto, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",'cad_pj',".$intCCONTA.",".$intCPLANO.",".$intCCUSTO.",CURRENT_DATE,'".cDate(CFG_LANG,$dttVENC,false)."',".$dblVALOR.",0,".$dblVALOR.",FALSE,'".$strHIST."','".$strTPDOC."','".$strTITOBS."','aberto',".$intCodPED.",".$intCBOLETO.",'".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
		$objConn->query($strSQL);
		
		// Verifica opção se deve gerar LANÇAMENTO ou não
		if($flagTITULO == "TIT_NEW"){
			// Localiza CÓDIGO DO PEDIDO recém-inserido
			$objResult = $objConn->query("SELECT last_value AS max_cod_titulo FROM fin_conta_pagar_receber_cod_conta_pagar_receber_seq;");
			$objRS	   = $objResult->fetch();
			$intCodTIT = getValue($objRS,"max_cod_titulo");
			
			// Paga o TÍTULO CORRENTE, INSER EM LANÇAMENTOS
			$strSQL = "INSERT INTO fin_lcto_ordinario (cod_conta_pagar_receber, tipo, codigo, cod_conta, cod_plano_conta, cod_centro_custo, historico, num_lcto, dt_lcto, vlr_multa, vlr_juros, vlr_desc, vlr_lcto, tipo_documento, num_documento, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodTIT.",'cad_pj',".$intCodPJ.",".$intCCONTA.",".$intCPLANO.",".$intCCUSTO.",'".$strHIST."','".$strNUMLCTO."','".cDate(CFG_LANG,$dttPGTO,false)."',0,0,0,".$dblVALOR.",'".$strTPDOC."','".$strNUMDOC."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
		}
		
		// Update na HOMOLOGAÇÃO
		// marcando-a como realizada
		$strSQL = "UPDATE sd_homologacao SET dtt_homologacao = '".cDate(CFG_LANG,$dttHOMOLOG,true)."', obs = '".prepStr($strHOMOOBS)."', sys_dtt_upd = CURRENT_TIMESTAMP, situacao = 'confirmado', usr_homologacao = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."' WHERE cod_pedido = ".$intCodPED;
		$objConn->query($strSQL);
					
		// Marca a DATA DE DEMISSÃO DO
		// COLABORADOR COMO A DATA DE HOMOLOGAÇÃO
		$strSQL = "UPDATE relac_pj_pf SET dt_demissao = '".cDate(CFG_LANG,$dttHOMOLOG,true)."', sys_dtt_upd = CURRENT_TIMESTAMP, sys_usr_upd = '".prepStr(getSession(CFG_SYSTEM_NAME . "_id_usuario"))."' WHERE cod_pj_pf = ".$intCodREL;
		$objConn->query($strSQL);
			
		// Commit na TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Limpa TODO e QUALQUER pedido gerado 
	// automático para a PF recém-inserida
	// $objConn->query("UPDATE prd_pedido SET situacao = 'cancelado' WHERE it_cod_pf = ".$intCodPF.";");
	try{
		$objConn->query("DELETE FROM prd_pedido WHERE it_tipo <> 'homo' AND it_cod_pf = ".$intCodPF.";");	 
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	redirect($strLOCATION);
?>