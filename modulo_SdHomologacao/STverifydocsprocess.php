<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");	
	
	// REQUESTS GERAIS
	$intCodPJ 	 = request("var_cod_pj");			// COD_PJ
	$intCodPF  	 = request("var_cod_pf");			// COD_PF
	$intCodRE	 = request("var_cod_pj_pf");		// COD_PJ_PF
	$flagPJ		 = request("var_flag_insert_pj");	// FLAG PARA INSERÇÃO OU NÃO DE PJ
	$flagPF		 = request("var_flag_insert_pf");	// FLAG PARA INSERÇÃO OU NÃO DE PF
	$flagRE		 = request("var_flag_insert_re");	// FLAG PARA INSERÇÃO OU NÃO DE RELAÇÃO
	$intCODPROD	 = request("var_cod_produto");		// CODIGO DO PRODUTO
	$strLOCATION = request("DEFAULT_LOCATION");
	
	// REQUESTS PJ
	$strRAZAO	 = request("var_pj_razao_social");
	$strFANTASIA = request("var_pj_nome_fantasia");
	$intCNPJ	 = request("var_pj_cnpj");
	$intCNAE	 = request("var_pj_cnae_grupo");
	$intCEP		 = request("var_pj_endprin_cep");
	$strLOGRAD   = request("var_pj_endprin_logradouro");
	$intNUMERO   = request("var_pj_endprin_numero");
	$strCOMPLE   = request("var_pj_endprin_complemento");
	$strBAIRRO	 = request("var_pj_endprin_bairro");
	$strCIDADE	 = request("var_pj_endprin_cidade");
	$strESTADO	 = request("var_pj_endprin_estado");
	$strPAIS     = request("var_pj_endprin_pais");
	
	// REQUESTS PF
	$intCPF		 = request("var_pf_cpf");
	$intRG		 = request("var_pf_rg");
	$strNOME	 = request("var_pf_nome");
	$chrSEXO	 = request("var_pf_sexo");
	$strOBSPF	 = request("var_pf_obs");
	
	// REQUESTS PJxPF
	$strTIPO	 = request("var_vaga_tipo");
	$strCATEG	 = request("var_vaga_categoria");
	$strFUNCAO	 = request("var_vaga_funcao");
	$strDEPART	 = request("var_vaga_departamento");
	$dtADMISSAO	 = request("var_vaga_admissao");
	$strOBSVAGA  = request("var_vaga_obs");
	
	// REQUESTS HOMOLOGAÇÃO
	$strOBSHOMO	 = request("var_homo_obs");
	$dtHOMOLOG	 = request("var_homo_data");
	
	// REQUEST - DADOS DO TÍTULO E LCTO
	$flagTITULO  = request("var_tit_opcao_gerar");
	$strTPDOC	 = request("var_tit_tipo_documento");
	$dblVALOR	 = MoedaToFloat(request("var_tit_valor"));
	$dttVENC	 = request("var_tit_dt_vcto");
	$dttPGTO     = request("var_tit_dt_pgto");
	$intCCUSTO	 = request("var_tit_centro_custo");
	$intCCONTA	 = request("var_tit_conta");
	$intCPLANO	 = request("var_tit_plano_conta");
	$strHIST	 = request("var_tit_historico");
	$strNUMLCTO	 = request("var_tit_numero_lcto");
	$strNUMDOC	 = request("var_tit_numero_documento");
	$strTITOBS	 = request("var_tit_obs");
	$intCBOLETO	 = request("var_tit_boleto");
	
	// TRATAMENTO DAS FLAGS
	$flagPJ = ($flagPJ == "TRUE") ? TRUE : FALSE;
	$flagPF = ($flagPF == "TRUE") ? TRUE : FALSE;
	$flagRE = ($flagRE == "TRUE") ? TRUE : FALSE;
	
	$strSesPfx 	= strtolower(str_replace("modulo_","",basename(getcwd())));
	
	// ABERTURA DE CONEXÃO NO BANCO
	$objConn   	= abreDBConn(CFG_DB);
		
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
		// Verifica a FLag de INSERÇÃO DE PJ
		if(!$flagPJ){
			// FALSE = INSERE NOVA PJ
			$strSQL = " INSERT INTO cad_pj (categoria, cod_cnae_n3, cnpj, razao_social, nome_fantasia, endprin_cep, endprin_logradouro, endprin_numero, endprin_complemento, endprin_bairro, endprin_cidade, endprin_estado, endprin_pais, sys_usr_ins, sys_dtt_ins) 
						VALUES ('CONTRIBUINTE',".$intCNAE.",'".prepStr($intCNPJ)."','".prepStr($strRAZAO)."','".prepStr($strFANTASIA)."','".prepStr($intCEP)."','".prepStr($strLOGRAD)."','".prepStr($intNUMERO)."','".prepStr($strCOMPLE)."','".prepStr($strBAIRRO)."','".prepStr($strCIDADE)."','".prepStr($strESTADO)."','".prepStr($strPAIS)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP) ";
			$objConn->query($strSQL);
			// Localiza esta PJ recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_pj FROM cad_pj_cod_pj_seq");
			$objRS	   = $objResult->fetch();
			$intCodPJ  = getValue($objRS,"max_cod_pj");
		}
		// PARA NOVAS PFs
		if(!$flagPF){
			// Insere PF encaminhada [COLABORADOR]
			$strSQL = " INSERT INTO cad_pf (cpf, rg, nome, sexo, obs, sys_usr_ins, sys_dtt_ins) 
						VALUES ('".prepStr($intCPF)."','".prepStr($intRG)."','".prepStr($strNOME)."','".prepStr($chrSEXO)."','".prepStr($strOBSPF)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP) ";
			$objConn->query($strSQL);
			
			// Localiza esta PF recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_pf FROM cad_pf_cod_pf_seq");
			$objRS	   = $objResult->fetch();
			$intCodPF  = getValue($objRS,"max_cod_pf");
		}
		// PARA NOVAS RELAÇÕES
		if(!$flagRE){
			// Insere RELAÇÃO da Vaga do COLABORADOR para a PJ
			$strSQL = " INSERT INTO relac_pj_pf (cod_pj, cod_pf, tipo, funcao, departamento, dt_admissao, obs, categoria, sys_usr_ins, sys_dtt_ins) 
						VALUES (".$intCodPJ.",".$intCodPF.",'".prepStr($strTIPO)."','".prepStr($strFUNCAO)."','".prepStr($strDEPART)."','".cDate(CFG_LANG,$dtADMISSAO,false)."','".prepStr($strOBSVAGA)."','".prepStr($strCATEG)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza RELAÇÃO recém-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq");
			$objRS	   = $objResult->fetch();
			$intCodRE  = getValue($objRS,"max_cod_relac");
			
			//Procura pedido recém-inserido para ser deletado porque quando se insere 
			//uma relação PJxPF também se cria pedido para credencial, por exemplo
			//Como se trata de uma homologação rápida deve-se ignorar esse pedido
			$strSQL = " SELECT cod_pedido FROM prd_pedido 
						WHERE cod_pj = ".$intCodPJ."
						AND it_cod_pf = ".$intCodPF."
						AND it_cod_pj_pf = ".$intCodRE."
						ORDER BY cod_pedido DESC LIMIT 1 ";
			$objResult = $objConn->query($strSQL);
			$objRS	   = $objResult->fetch();
			$intCodPedido = getValue($objRS,"cod_pedido");
			
			if ($intCodPedido != "") {
				$strSQL = " DELETE FROM prd_pedido WHERE cod_pedido = ".$intCodPedido;
				$objConn->query($strSQL);
			}
		}
		
		// Insere PEDIDO CORRESPONDENTE de Homologação
		$strSQL = "	INSERT INTO prd_pedido (cod_pj, situacao, valor, it_cod_pf, it_cod_pj_pf, it_cod_produto, it_descricao, it_valor, it_tipo, it_dt_ini_val_produto, it_dt_fim_val_produto, sys_usr_ins, sys_dtt_ins) 
					VALUES (".$intCodPJ.",'aberto',".$dblVALOR.",".$intCodPF.",".$intCodRE.",".$intCodProduto.",'".prepStr($strRotuloProd." - ".$strDescProd)."',".$dblVlrProduto.",'".prepStr($strTipoProd)."','".cDate(CFG_LANG,$dtIniValidade,false)."','".cDate(CFG_LANG,$dtFimValidade,false)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP) ";
		$objConn->query($strSQL);
		
		// Localiza CÓDIGO DO PEDIDO recém-inserido
		$objResult = $objConn->query("SELECT last_value AS max_cod_pedido FROM prd_pedido_cod_pedido_seq");
		$objRS	   = $objResult->fetch();
		$intCodPED = getValue($objRS,"max_cod_pedido");
		
		// Faz Inserção de TÍTULO
		$strSQL = "	INSERT INTO fin_conta_pagar_receber (codigo, tipo, cod_conta, cod_plano_conta, cod_centro_custo, dt_emissao, dt_vcto, vlr_conta, vlr_pago, vlr_saldo, pagar_receber, historico, tipo_documento, obs, situacao, cod_pedido, cod_cfg_boleto, sys_usr_ins, sys_dtt_ins) 
					VALUES (".$intCodPJ.",'cad_pj',".$intCCONTA.",".$intCPLANO.",".$intCCUSTO.",CURRENT_DATE,'".cDate(CFG_LANG,$dttVENC,false)."',".$dblVALOR.",0,".$dblVALOR.",FALSE,'".$strHIST."','".$strTPDOC."','".$strTITOBS."','aberto',".$intCodPED.",".$intCBOLETO.",'".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP) ";
		$objConn->query($strSQL);
		
		// Verifica opção se deve gerar LANÇAMENTO ou não
		if($flagTITULO == "TIT_NEW"){
			// Localiza CÓDIGO DO PEDIDO recém-inserido
			$objResult = $objConn->query("SELECT last_value AS max_cod_titulo FROM fin_conta_pagar_receber_cod_conta_pagar_receber_seq ");
			$objRS	   = $objResult->fetch();
			$intCodTIT = getValue($objRS,"max_cod_titulo");
			
			// Paga o TÍTULO CORRENTE, INSER EM LANÇAMENTOS
			$strSQL = "INSERT INTO fin_lcto_ordinario (cod_conta_pagar_receber, tipo, codigo, cod_conta, cod_plano_conta, cod_centro_custo, historico, num_lcto, dt_lcto, vlr_multa, vlr_juros, vlr_desc, vlr_lcto, tipo_documento, num_documento, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodTIT.",'cad_pj',".$intCodPJ.",".$intCCONTA.",".$intCPLANO.",".$intCCUSTO.",'".$strHIST."','".$strNUMLCTO."','".cDate(CFG_LANG,$dttPGTO,false)."',0,0,0,".$dblVALOR.",'".$strTPDOC."','".$strNUMDOC."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
		}
		
		// Update na HOMOLOGAÇÃO marcando-a como realizada
		$strSQL = " UPDATE sd_homologacao 
					SET dtt_homologacao = '".cDate(CFG_LANG,$dtHOMOLOG,true)."'
					  , obs = '".prepStr($strOBSHOMO)."'
					  , situacao = 'confirmado'
					  , sys_dtt_upd = CURRENT_TIMESTAMP
					  , usr_homologacao = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."' 
					WHERE cod_pedido = ".$intCodPED;
		$objConn->query($strSQL);
		
		// Marca a DATA DE DEMISSÃO DO COLABORADOR COMO A DATA DE HOMOLOGAÇÃO
		$strSQL = "	UPDATE relac_pj_pf 
					SET dt_demissao = '".cDate(CFG_LANG,$dtHOMOLOG,true)."'
					  , sys_dtt_upd = CURRENT_TIMESTAMP
					  , sys_usr_upd = '".prepStr(getSession(CFG_SYSTEM_NAME . "_id_usuario"))."' 
					WHERE cod_pj_pf = ".$intCodRE;
		$objConn->query($strSQL);
		
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	redirect($strLOCATION);
?>