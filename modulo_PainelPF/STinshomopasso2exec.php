<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	// ABRE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	$strMsg 	 = "";
	$strEntidade = "";
	
	// RECEBIMENTO DE PARÂMETROS
	$intCodPJ 		= request("var_cod_pj");
	$intCodPF 		= request("var_cod_pf");
	$srtFlagUpdate 	= request("var_str_flag");
	
	// REQUEST - DADOS DA PF
	$strNome  		  = strtoupper(request("var_nome"));
	$strApelido		  = strtoupper(request("var_apelido"));
	$dtDataNasc       = request("var_data_nasc");
	$strSexo  		  = strtoupper(request("var_sexo"));
	$strEmail         = strtoupper(request("var_email"));
	$strEmailExtra    = strtoupper(request("var_email_extra"));
	$strWebsite       = strtoupper(request("var_website"));
	$strFoto          = request("var_foto");
	$strEstadoCivil   = strtoupper(request("var_estado_civil"));
	$strInstrucao     = strtoupper(request("var_instrucao"));
	$strNacionalidade = strtoupper(request("var_nacionalidade"));
	$strNaturalidade  = strtoupper(request("var_naturalidade"));
	$strObs           = strtoupper(request("var_obs"));
	$strNomePai       = strtoupper(request("var_nome_pai"));
	$strNomeMae       = strtoupper(request("var_nome_mae"));
		
	// REQUEST - DOCUMENTOS
	$strCPF		      = request("var_cpf");
	$strRG            = request("var_rg");
	$strCNH           = request("var_cnh");
	$strPIS           = request("var_pis");
	$strCTPS          = request("var_ctps");
	$strTITE 		  = request("var_titulo_eleitoral");
	
	// REQUEST - ENDEREÇO
	$strEndPrinCEP           = request("var_endprin_cep");
	$strEndPrinLogradouro    = strtoupper(request("var_endprin_logradouro"));
	$strEndPrinNumero        = request("var_endprin_numero");
	$strEndPrinCompl         = request("var_endprin_complemento");
	$strEndPrinBairro        = strtoupper(request("var_endprin_bairro"));
	$strEndPrinCidade        = strtoupper(request("var_endprin_cidade"));
	$strEndPrinEstado        = strtoupper(request("var_endprin_estado"));
	$strEndPrinPais          = strtoupper(request("var_endprin_pais"));
	$strEndPrinFone1         = request("var_endprin_fone1");
	$strEndPrinFone2         = request("var_endprin_fone2");
	$strEndPrinFone3         = request("var_endprin_fone3");
	$strEndPrinFone4         = request("var_endprin_fone4");
	$strEndPrinFone5         = request("var_endprin_fone5");
	$strEndPrinFone6         = request("var_endprin_fone6");
	
	// REQUEST - ENDEREÇO COMERCIAL
	$strEndComCEP           = request("var_endcom_cep");
	$strEndComLogradouro    = strtoupper(request("var_endcom_logradouro"));
	$strEndComNumero        = request("var_endcom_numero");
	$strEndComCompl         = request("var_endcom_complemento");
	$strEndComBairro        = strtoupper(request("var_endcom_bairro"));
	$strEndComCidade        = strtoupper(request("var_endcom_cidade"));
	$strEndComEstado        = strtoupper(request("var_endcom_estado"));
	$strEndComPais          = strtoupper(request("var_endcom_pais"));
	$strEndComFone1         = request("var_endcom_fone1");
	$strEndComFone2         = request("var_endcom_fone2");
	$strEndComFone3         = request("var_endcom_fone3");
	$strEndComFone4         = request("var_endcom_fone4");
	$strEndComFone5         = request("var_endcom_fone5");
	$strEndComFone6         = request("var_endcom_fone6");
	
	// REQUEST - DADOS DA FUNÇÃO
	$intTrabCodCargo     = request("var_trab_cod_cargo");
	$strCategoria 		 = (request("var_categoria") == '' && getsession(CFG_SYSTEM_NAME."_grp_user") == 'NORMAL') ? request("var_categoria_hidden") : request("var_categoria");
	$strTrabFuncao       = strtoupper(request("var_trab_funcao"));
	$strTrabDepartamento = strtoupper(request("var_trab_departamento"));
	$strTrabTipo         = strtoupper(request("var_trab_tipo"));
	$strTrabObs          = strtoupper(request("var_trab_obs"));
	$dtTrabDtAdmissao    = request("var_trab_dt_admissao");
	
	// REQUEST DATAS - ESPECIAL
	$auxDtPrevIni	= request("var_dtt_ped_agendamento_homo");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_ped_hr_agendamento_homo"),0,2) >= 24) ? "00".substr(request("var_ped_hr_agendamento_homo"),2,4).":00" : request("var_ped_hr_agendamento_homo"); // HR prev inicio
	
	// Formatação das datas de previsão
	$dtPrevIni	= ($auxDtPrevIni == "") ? "" : $auxDtPrevIni ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	
	//die($dtPrevIni);
	
	// VALIDAÇÃO DE CAMPOS OBRIGATÓRIOS
	if($intCodPJ 			== "") { $strMsg .= "&bull;&nbsp;Informar Empresa<br>";   		}
	if($strNome 			== "") { $strMsg .= "&bull;&nbsp;Informar Nome<br>"; 			}
	if($strSexo 			== "") { $strMsg .= "&bull;&nbsp;Informar Sexo<br>"; 			}
	if($strCPF 				== "") { $strMsg .= "&bull;&nbsp;Informar CPF<br>"; 			}
	if($strRG 				== "") { $strMsg .= "&bull;&nbsp;Informar RG<br>"; 				}
	//if($strFoto 			== "") { $strMsg .= "&bull;&nbsp;Informar Foto<br>"; 			}
	if($strTrabFuncao 		== "") { $strMsg .= "&bull;&nbsp;Informar Função<br>";			}
	if($strEndPrinCEP 		== "") { $strMsg .= "&bull;&nbsp;Informar CEP<br>"; 			}
	if($strEndPrinLogradouro == ""){ $strMsg .= "&bull;&nbsp;Informar Logradouro<br>"; 		}
	if($strEndPrinNumero 	== "") { $strMsg .= "&bull;&nbsp;Informar Número<br>"; 			}	
	if($strEndPrinBairro 	== "") { $strMsg .= "&bull;&nbsp;Informar Bairro<br>"; 			}
	if($strEndPrinCidade 	== "") { $strMsg .= "&bull;&nbsp;Informar Cidade<br>"; 			}
	if($strEndPrinEstado 	== "") { $strMsg .= "&bull;&nbsp;Informar Estado<br>"; 			}
	if($strEndPrinPais 		== "") { $strMsg .= "&bull;&nbsp;Informar País<br>"; 			}
	if($strEndPrinFone1 	== "") { $strMsg .= "&bull;&nbsp;Informar Fone 1<br>"; 			}
	//if($strEndPrinFone2 	== "") { $strMsg .= "&bull;&nbsp;Informar Fone 2<br>"; 			}
	if($dtTrabDtAdmissao 	== "") { $strMsg .= "&bull;&nbsp;Informar Data Admissão<br>"; 	}
	if($dtPrevIni		   	== "") { $strMsg .= "&bull;&nbsp;Informar Data Agendamento<br>";}
	if($auxHrPrevIni        == "") { $strMsg .= "&bull;&nbsp;Informar Hora inicial do Agendamento<br>"; }
	
	if($strMsg != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
		die();
	}
	
	/*** TRATAMENTO DOS CAMPOs ***/
	$dtDataNasc = cDate(CFG_LANG, $dtDataNasc, false);
	$dtTrabDtAdmissao = cDate(CFG_LANG, $dtTrabDtAdmissao, false);
	(($dtDataNasc == "") || (!is_date($dtDataNasc))) ? $dtDataNasc = "NULL" : $dtDataNasc = "'" . $dtDataNasc . "'";
	(($dtTrabDtAdmissao == "") || (!is_date($dtTrabDtAdmissao))) ? $dtTrabDtAdmissao = "NULL" : $dtTrabDtAdmissao = "'" . $dtTrabDtAdmissao . "'";
	if ($intTrabCodCargo == "") $intTrabCodCargo = "NULL";
		
	// De qualquer forma, atualizando ou não, um pedido
	// de HOMOLOGAÇÃO será gerado para esta PJ
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
		mensagem("err_sql_titulo","err_sql_desc","Produto Homologação Vigente não Cadastrado.","","erro",1);
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
	
	
	$objConn->beginTransaction();
	try{
		//PF já está cadastrada, apenas atualiza os dados
		if ($intCodPF != '') {
			//die("update aqui".$intCodPF);
			$strSQL  = " UPDATE cad_pf ";
			$strSQL .= " SET nome = '" . $strNome . "' ";
			$strSQL .= "   , apelido = '" . $strApelido . "' ";
			$strSQL .= "   , data_nasc = " . $dtDataNasc;
			$strSQL .= "   , sexo = '" . $strSexo . "' ";
			$strSQL .= "   , email = '" . $strEmail . "' ";
			$strSQL .= "   , email_extra = '" . $strEmailExtra . "' ";
			$strSQL .= "   , website = '" . $strWebsite . "' ";
			$strSQL .= "   , foto = '" . $strFoto . "' ";
			$strSQL .= "   , estado_civil = '" . $strEstadoCivil . "' ";
			$strSQL .= "   , instrucao = '" . $strInstrucao . "' ";
			$strSQL .= "   , nacionalidade = '" . $strNacionalidade . "' ";
			$strSQL .= "   , naturalidade = '" . $strNaturalidade . "' ";
			$strSQL .= "   , obs = '" . $strObs . "' ";
			$strSQL .= "   , nome_pai = '" . $strNomePai . "' ";
			$strSQL .= "   , nome_mae = '" . $strNomeMae . "' ";
			$strSQL .= "   , rg = '" . $strRG . "' ";
			$strSQL .= "   , cnh = '" . $strCNH . "' ";
			$strSQL .= "   , pis = '" . $strPIS . "' ";
			$strSQL .= "   , ctps = '" . $strCTPS . "' ";
			$strSQL .= "   , titulo_eleitoral = '" . $strTITE . "' ";
			
			$strSQL .= "   , endprin_cep = '" . $strEndPrinCEP . "' ";
			$strSQL .= "   , endprin_logradouro = '" . $strEndPrinLogradouro . "' ";
			$strSQL .= "   , endprin_numero = '" . $strEndPrinNumero . "' ";
			$strSQL .= "   , endprin_complemento = '" . $strEndPrinCompl . "' ";
			$strSQL .= "   , endprin_bairro = '" . $strEndPrinBairro . "' ";
			$strSQL .= "   , endprin_cidade = '" . $strEndPrinCidade . "' ";
			$strSQL .= "   , endprin_estado = '" . $strEndPrinEstado . "' ";
			$strSQL .= "   , endprin_pais = '" . $strEndPrinPais . "' ";
			$strSQL .= "   , endprin_fone1 = '" . $strEndPrinFone1 . "' ";
			$strSQL .= "   , endprin_fone2 = '" . $strEndPrinFone2 . "' ";
			
			$strSQL .= "   , endcom_cep = '" . $strEndComCEP . "' ";
			$strSQL .= "   , endcom_logradouro = '" . $strEndComLogradouro . "' ";
			$strSQL .= "   , endcom_numero = '" . $strEndComNumero . "' ";
			$strSQL .= "   , endcom_complemento = '" . $strEndComCompl . "' ";
			$strSQL .= "   , endcom_bairro = '" . $strEndComBairro . "' ";
			$strSQL .= "   , endcom_cidade = '" . $strEndComCidade . "' ";
			$strSQL .= "   , endcom_estado = '" . $strEndComEstado . "' ";
			$strSQL .= "   , endcom_pais = '" . $strEndComPais . "' ";
			$strSQL .= "   , endcom_fone1 = '" . $strEndComFone1 . "' ";
			$strSQL .= "   , endcom_fone2 = '" . $strEndComFone2 . "' ";
			$strSQL .= " WHERE cod_pf = " . $intCodPF;
			
			$objConn->query($strSQL);
			
			// update na tabela de relacões
			if($srtFlagUpdate != ""){
				$strSQL  = " UPDATE relac_pj_pf ";
				$strSQL .= " SET tipo = '" . $strTrabTipo . "' ";
				$strSQL .= "   , departamento = '" . $strTrabDepartamento . "' ";
				$strSQL .= "   , categoria = '" . $strCategoria . "' ";
				$strSQL .= "   , dt_admissao = " . $dtTrabDtAdmissao;
				$strSQL .= "   , funcao = '" . $strTrabFuncao . "' ";
				$strSQL .= "   , obs = '" . $strTrabObs . "' ";
				$strSQL .= " WHERE cod_pf = " . $intCodPF;
				//die($strSQL);
				$objConn->query($strSQL);
			}
			
			
		}
		//PF não está cadastrada, então insere os dados
		else {
			//die("deu insert".$intCodPF);
			$strSQL  = " INSERT INTO cad_pf ( nome, apelido, data_nasc, sexo, nome_pai, nome_mae, email, email_extra, website, foto, estado_civil ";
			$strSQL .= "                    , instrucao, nacionalidade, naturalidade, obs, cpf, rg, cnh, pis, ctps, titulo_eleitoral ";
			
			$strSQL .= "                    , endprin_cep, endprin_logradouro, endprin_numero, endprin_complemento, endprin_bairro ";
			$strSQL .= "                    , endprin_cidade, endprin_estado, endprin_pais, endprin_fone1, endprin_fone2 ";
			
			$strSQL .= "                    , endcom_cep, endcom_logradouro, endcom_numero, endcom_complemento, endcom_bairro ";
			$strSQL .= "                    , endcom_cidade, endcom_estado, endcom_pais, endcom_fone1, endcom_fone2 ";
			
			$strSQL .= "                    , sys_dtt_ins, sys_usr_ins ) ";
			$strSQL .= " VALUES ( '" . $strNome . "', '" . $strApelido . "', " . $dtDataNasc . ", '" . $strSexo . "', '" . $strNomePai . "', '" . $strNomeMae . "' ";
			$strSQL .= "        , '" . $strEmail . "', '" . $strEmailExtra . "', '" . $strWebsite . "', '" . $strFoto . "', '" . $strEstadoCivil . "' ";
			$strSQL .= "        , '" . $strInstrucao . "', '" . $strNacionalidade . "', '" . $strNaturalidade . "', '" . $strObs . "' ";
			$strSQL .= "        , '" . $strCPF . "', '" . $strRG . "', '" . $strCNH . "', '" . $strPIS . "', '" . $strCTPS . "', '" . $strTITE . "' ";
			
			$strSQL .= "        , '" . $strEndPrinCEP . "', '" . $strEndPrinLogradouro . "', '" . $strEndPrinNumero . "', '" . $strEndPrinCompl . "' ";
			$strSQL .= "        , '" . $strEndPrinBairro . "', '" . $strEndPrinCidade . "', '" . $strEndPrinEstado . "', '" . $strEndPrinPais . "' ";
			$strSQL .= "        , '" . $strEndPrinFone1 . "', '" . $strEndPrinFone2 . "' ";
			
			$strSQL .= "        , '" . $strEndComCEP . "', '" . $strEndComLogradouro . "', '" . $strEndComNumero . "', '" . $strEndComCompl . "' ";
			$strSQL .= "        , '" . $strEndComBairro . "', '" . $strEndComCidade . "', '" . $strEndComEstado . "', '" . $strEndComPais . "' ";
			$strSQL .= "        , '" . $strEndComFone1 . "', '" . $strEndComFone2 . "' ";
			
			$strSQL .= "        , CURRENT_TIMESTAMP, '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
			
			$objConn->query($strSQL);
			
			$strSQL = " SELECT MAX(cod_pf) AS cod_pf FROM cad_pf WHERE sys_usr_ins = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
			$objResult = $objConn->query($strSQL);
			
			if ($objResult->rowCount() > 0) {
				$objRS = $objResult->fetch();
				$intCodPF = getValue($objRS, "cod_pf");
			}
			$objResult->closeCursor();
			
			if ($intCodPF == ''){
				mensagem("err_sql_titulo","err_sql_desc","err_busca_colab_desc","","erro",1);
				$objConn->rollBack();
				die();
			}
		}
		
		if($srtFlagUpdate == ""){
			//Insere relação de PF com PJ, será gerado um pedido 
			//de carteirinha (credencial) por trigger
			$strSQL  = " INSERT INTO relac_pj_pf (cod_pj, cod_pf, categoria, cod_cargo, tipo, funcao, departamento, obs, dt_admissao, sys_dtt_ins, sys_usr_ins) ";
			$strSQL .= " VALUES ( " . $intCodPJ . ", " . $intCodPF . ", '" . $strCategoria . "', " . $intTrabCodCargo . ", '" . $strTrabTipo . "', '" . $strTrabFuncao . "' ";
			$strSQL .= "        , '" . $strTrabDepartamento . "', '" . $strTrabObs . "', " . $dtTrabDtAdmissao . ", CURRENT_TIMESTAMP ";
			$strSQL .= "        , '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "') ";
			$objConn->query($strSQL);
		}
		
		// Localiza RELAÇÃO recém-inserida
		$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq;");
		$objRS	   = $objResult->fetch();
		$intCodREL = getValue($objRS,"max_cod_relac");
		
		// Insere PEDIDO CORRESPONDENTE de Homologação
		$strSQL = "INSERT INTO prd_pedido (cod_pj, situacao, valor, it_cod_pf, it_cod_pj_pf, it_cod_produto, it_descricao, it_valor, it_tipo, it_dt_ini_val_produto, it_dt_fim_val_produto, it_dtt_agendamento, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",'aberto',".$dblVlrProduto.",".$intCodPF.",".$intCodREL.",".$intCodProduto.",'".prepStr($strRotuloProd." - ".$strDescProd)."',".$dblVlrProduto.",'".prepStr($strTipoProd)."','".cDate(CFG_LANG,$dtIniValidade,false)."','".cDate(CFG_LANG,$dtFimValidade,false)."','".$dtPrevIni."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
		$objConn->query($strSQL);
		
		// Commit na TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
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
	
	$objConn = NULL;
	
	redirect("STindex.php");
?>