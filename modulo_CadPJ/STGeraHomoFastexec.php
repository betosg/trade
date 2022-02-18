<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// recebimento de parametros
	$intCodDado  	= request("var_chavereg"); //cod_pj_pf
	$intCodPJ 	 	= request("var_cod_pj");
	$intCodPF 	 	= request("var_cod_pf");
	$strCPF         = request("var_cpf");
	$strNome 	 	= request("var_nome");
	$strDTHomo 	 	= cDate(CFG_LANG,request("var_dt_homo"),false);
	$strGTitulo  	= request("var_opcao_gerar_titulo");
	$dblValor 	 	= str_replace(",",".",request("var_valor"));
	$strDTVcto 	 	= cDate(CFG_LANG,request("var_dt_vcto"),false);
	$dttPGTO		= cDate(CFG_LANG,request("var_tit_dt_pgto"),false);
	$intCodConta 	= request("var_cod_conta");
	$intCPlConta 	= request("var_cod_plano_conta");
	$intCCCusto	 	= request("var_cod_centro_custo");
	$strHist 	 	= request("var_historico");
	$strObs 	 	= request("var_obs");
	$strObsHomo		= request("var_obs_homo");
	$strTipoDoc	 	= request("var_tipo_doc");
	$intCCfgBoelto  = request("var_cod_cfg_boleto");
	$strOPEBoleto	= request("var_exibir_boleto");
	$strRedirect	= request("var_redirect");
	
	//$strTipoLcto    = "receber";
	$strDocumento   = "DINHEIRO";
	$strNumLcto     = request("var_num_lcto");
	$numDocumento   = request("var_num_documento");
	
	// Flag para necessidade de popular o session ou n�o
	$strPopulate = request("var_populate");
	
	//Popula o session para fazer a abertura dos �tens do m�dulo
	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } 
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");
	
	//Inicia objeto para manipula��o do banco
	$objConn = abreDBConn(CFG_DB);
	// echo(cDate(CFG_LANG,date("d/m/Y"),false));
	// testa campos obrigat�rios
	$strErrMSG   = "";
	$strErrMSG 	.= ($intCodDado  == "") ? "Informe c�digo da rela��o PF x PJ <br/>" : "";
	$strErrMSG  .= ($intCodPJ  == "") 	? "Informe c�digo da pessoa jur�dica <br/>" : "";
	$strErrMSG 	.= ($intCodPF  == "") 	? "Informe c�digo da pessoa f�sica <br/>" : "";
	$strErrMSG 	.= ($strNome  == "") 	? "Informe nome <br/>" : "";
	$strErrMSG 	.= ($strDTHomo  == "") 	? "Informe data de homologa��o <br/>" : "";
	$strErrMSG  .= ($strGTitulo  == "") ? "Informe op��o de gera��o de titulo<br/>" : "";
	$strErrMSG 	.= ($dblValor  == "") 	? "Informe valor <br/>" : "";
	$strErrMSG 	.= ($strDTVcto  == "") ? "Informe data de vcto <br/>" : "";
	$strErrMSG 	.= ($intCPlConta  == "")? "Informe c�digo do plano de contas <br/>" : "";
	$strErrMSG 	.= ($intCCCusto  == "") ? "Informe c�digo do centro de custo <br/>" : "";
	$strErrMSG	.= ($strHist  == "") 	? "Informe hist�rico <br/>" : "";
	// $strErrMSG 	.= ($strObs  == "") 	? "Informe observa��o <br/>" : "";
	$strErrMSG 	.= ($intCCfgBoelto  == "") ? "Informe c�digo de boleto padr�o <br/>" : "";
	$strErrMSG  .= ($strTipoDoc    == "")  ? "Informe o tipo de documento <br/>" : "";
	$strErrMSG  .= (($strGTitulo  == "S") && (MoedaToFloat($dblValor) == 0) ? "T�tulo n�o pode ter valor zerado, caso queira emit�-lo j� quitado<br />" : "");

	$strErrMSG  .= (($strGTitulo  == "S") && ($dttPGTO == "") ? "Data de Pagamento n�o Pode ser Vazia, Caso Queria Emitir T�tulo j� Quitado<br />" : "");
	$strErrMSG  .= (($strGTitulo  == "S") && ($dttPGTO > cDate(CFG_LANG,date("d/m/Y"),false)) ? "Data de Pagamento Maior que Data Atual<br />" : "");
	$strErrMSG  .= (($strGTitulo  == "S") && ($dttPGTO > $strDTHomo) ? "Data de Pagamento Maior que Data de Homologa��o<br />" : "");

	$strErrMSG  .= (($strGTitulo  == "S") && ($strNumLcto == "") && ($numDocumento == "")) ? "n�mero LCTO ou n�mero de DOCUMENTO<br/>" : "";
	
	if($strErrMSG != ""){
		mensagem("err_sql_titulo","err_sql_desc",$strErrMSG,"STGeraHomoFast.php?var_chavereg=".$intCodDado,"erro",1);
		die();
	}
	
	// adiciona a observa��o uam string mais especifica
	$strObs = "PEDIDO DE SOLICITA��O DE HOMOLOGA��O GERADO PARA ".$strNome." 
			   <br />(CPF: ".$strCPF.") EM ".dDate(CFG_LANG,now(),false)."<br />".$strObs;
	
	// busca o produto homologa��o ultimo a 
	// ser cadastrado e de maior valor
	try {
		$strSQL = "
				SELECT
					 prd_produto.cod_produto
					,prd_produto.rotulo
					,prd_produto.valor
					,prd_produto.descricao
					,prd_produto.dt_ini_val_produto
					,prd_produto.dt_fim_val_produto
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
	
	// caso nao tenha localizado um produto
	// de homologa��o v�lido, exibe msg erro
	if($objResult->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("produto_homo_validade_off",C_NONE),"","erro",1);
		die();
	}else{
		//fetch dos dados do produto v�lido corrente
		$objRS = $objResult->fetch();
		// coletando dados do produto
		$intCodProduto = getValue($objRS,"cod_produto");
		$strRotuloProd = getValue($objRS,"rotulo");
		$strDescProd   = getValue($objRS,"descricao");
		$intVlrProduto = getValue($objRS,"valor");
		$dtIniValidade = getValue($objRS,"dt_ini_val_produto");
		$dtFimValidade = getValue($objRS,"dt_fim_val_produto");
		
		$objConn->beginTransaction();
		try{
			// insere o pedido de homologa��o para
			// a pf selecionada
			$strSQL = "
					INSERT INTO prd_pedido(
						  cod_pj
						, it_cod_pf
						, it_cod_pj_pf
						, it_cod_produto
						, it_descricao
						, it_dt_ini_val_produto
						, it_dt_fim_val_produto
						, it_tipo
						, it_valor
						, valor
						, obs
						, situacao
						, sys_dtt_ins
						, sys_usr_ins)
					VALUES (
						 ".$intCodPJ."
						,".$intCodPF."
						,".$intCodDado."
						,".$intCodProduto."
						,'".$strRotuloProd."'
						,'".$dtIniValidade."'
						,'".$dtFimValidade."'
						,'homo'
						,".$intVlrProduto."
						,".$intVlrProduto."
						,'".$strObs."'
						,'aberto' 
						,CURRENT_TIMESTAMP
						,'".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
						);";
			$objConn->query($strSQL);
			
			// busca o cod_pedido rec�m inserido para efetuar
			// inser��o na tabela de conta_pagar_receber
			$strSQL    = "SELECT currval('prd_pedido_cod_pedido_seq') as cod_pedido_atual;";
			$objResult = $objConn->query($strSQL);
			$objRS	   = $objResult->fetch();
			$intCodPedido = getValue($objRS,"cod_pedido_atual");
			
			// insere observa��o na rela��o que foi realizado pedido
			// de homologa��o na data atual
			$strSQL = "
					UPDATE relac_pj_pf 
					SET obs = '" . $strObs . "'
					  , sys_dtt_upd = CURRENT_TIMESTAMP
					  , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "'
					WHERE cod_pj_pf = " . $intCodDado;
			$objConn->query($strSQL);
			
			// insere o titulo de homologa��o, que automaticamente
			// passa o pedido rec�m criado de aberto para faturado
			if($strGTitulo == "S"){
				$strSQL = "
					INSERT INTO fin_conta_pagar_receber ( 
						  pagar_receber
						, codigo
						, tipo
						, cod_conta
						, cod_plano_conta
						, cod_centro_custo
						, dt_emissao
						, dt_vcto
						, vlr_conta
						, vlr_saldo
						, vlr_pago
						, historico
						, tipo_documento
						, obs
						, situacao
						, cod_pedido 
						, cod_cfg_boleto
						, sys_dtt_ins
						, sys_usr_ins ) 
					VALUES (
						  FALSE
						,".$intCodPJ."
						,'cad_pj'
						,".$intCodConta."
						,".$intCPlConta."
						,".$intCCCusto."
						, CURRENT_DATE
						,'".$strDTVcto."'
						,".$dblValor."
						,".$dblValor."
						,0
						,'".request("var_historico")."'
						,'".$strTipoDoc."'
						,'".$strObs."'
						,'aberto'
						,".$intCodPedido."
						,".$intCCfgBoelto."
						, CURRENT_TIMESTAMP
						,'".getSession(CFG_SYSTEM_NAME."_id_usuario")."')";
				$objConn->query($strSQL);
				
				$strSQL = "SELECT currval('fin_conta_pagar_receber_cod_conta_pagar_receber_seq') AS cod_conta_pagar_receber_atual";
				$objResult = $objConn->query($strSQL);
				$objRSTit  = $objResult->fetch();
				$intCodTitulo = getValue($objRSTit,"cod_conta_pagar_receber_atual");
						   
				// INSERE UM LCTO CORRESPONDENTE
				$strSQL = "
						INSERT INTO fin_lcto_ordinario (
							  cod_conta_pagar_receber
							, tipo
							, codigo
							, cod_conta
							, cod_plano_conta
							, cod_centro_custo
							, historico
							, obs
							, num_lcto
							, dt_lcto
							, vlr_lcto
							, sys_dtt_ins
							, sys_usr_ins
							, tipo_documento
							, num_documento )
						VALUES (
							  ".$intCodTitulo."
							, 'cad_pj'
							, ".$intCodPJ."
							, ".$intCodConta."
							, ".$intCPlConta."
							, ".$intCCCusto."
							, '".$strHist."'
							, '".$strObs."'
							, '".$strNumLcto."'
							, '".$dttPGTO."'
							, ".$dblValor."
							, CURRENT_TIMESTAMP
							, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
							, '".$strDocumento."'
							, '".$numDocumento."'
							)";
				$objConn->query($strSQL);
			
				$strSQL  = "UPDATE fin_conta_pagar_receber SET sys_dtt_ult_lcto = CURRENT_TIMESTAMP ,sys_usr_ult_lcto = '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."' WHERE cod_conta_pagar_receber = ".$intCodTitulo;
				$objConn->query($strSQL);
			}
			// insere um titulo aberto, caso contrario
			else {
				$strSQL = "
						INSERT INTO fin_conta_pagar_receber ( 
							  pagar_receber
							, codigo
							, tipo
							, cod_conta
							, cod_plano_conta
							, cod_centro_custo
							, dt_emissao
							, dt_vcto
							, vlr_conta
							, vlr_saldo
							, vlr_pago
							, vlr_outros
							, vlr_desc 
							, historico
							, tipo_documento
							, obs
							, situacao
							, cod_pedido 
							, cod_cfg_boleto
							, sys_dtt_ins
							, sys_usr_ins ) 
						VALUES (
							  FALSE
							,".$intCodPJ."
							,'cad_pj'
							,".$intCodConta."
							,".$intCPlConta."
							,".$intCCCusto."
							, CURRENT_DATE
							,'".$strDTVcto."'
							,".$dblValor."
							,".$dblValor."
							,0,0,0
							,'".$strRotuloProd." - ".$strDescProd."'
							,'".$strTipoDoc."'
							,'".$strObs."'
							,'aberto'
							,".$intCodPedido."
							,".$intCCfgBoelto."
							, CURRENT_TIMESTAMP
							,'".getSession(CFG_SYSTEM_NAME."_id_usuario")."')";
				$objConn->query($strSQL);
			}
			// caso a op��o para gerar titulo quitado esteja marcada
				/*{
				// busca o cod_conta_pagar_receber rec�m inserido
				$strSQL = "SELECT 
						   currval('fin_conta_pagar_receber_cod_conta_pagar_receber_seq') 
						   AS cod_conta_pagar_receber_atual";
				$objResult = $objConn->query($strSQL);
				$objRSTit  = $objResult->fetch();
				$intCodTitulo = getValue($objRSTit,"cod_conta_pagar_receber_atual");
						   
				
				// insere lan�amento respectivo, j� 
				// quitando o titulo correspondente
				$strSQL = "
						INSERT INTO fin_lcto_ordinario (
							  cod_conta_pagar_receber
							, tipo
							, codigo
							, cod_conta
							, cod_plano_conta
							, cod_centro_custo
							, historico
							, obs
							, num_lcto
							, dt_lcto
							, vlr_lcto
							, vlr_multa
							, vlr_juros
							, vlr_desc
							, sys_dtt_ins
							, sys_usr_ins
							, tipo_documento
							, num_documento )
						VALUES (
							  ".$intCodTitulo."
							, 'cad_pj'
							, ".$intCodPJ."
							, ".$intCodConta."
							, ".$intCPlConta."
							, ".$intCCCusto."
							, '".$strHist."'
							, '".$strObs."'
							, '".$strNumLcto."'
							, CURRENT_TIMESTAMP
							, ".$dblValor."
							, 0
							, 0
							, 0
							, CURRENT_TIMESTAMP
							, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
							, '".$strDocumento."'
							, '".$numDocumento."'
							)";
				$objConn->query($strSQL);
				
				
				$strSQL  = "UPDATE fin_conta_pagar_receber 
							SET sys_dtt_ult_lcto = CURRENT_TIMESTAMP 
							, sys_usr_ult_lcto = '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."' 
							WHERE cod_conta_pagar_receber = ".$intCodTitulo;
				$objConn->query($strSQL);
				
				
			}*/
			
			// update da homologa��o
			// marcando ela como realizada
			$strSQL = "
					UPDATE sd_homologacao 
					SET dtt_homologacao = '".$strDTHomo."', 
					sys_dtt_upd = CURRENT_TIMESTAMP,
					situacao = 'confirmado',
					usr_homologacao = '".getsession(CFG_SYSTEM_NAME."id_usuario")."' 
					WHERE cod_pedido = ".$intCodPedido;
			$objConn->query($strSQL);
					
			// marca a data de demissao
			$strSQL  = " 
					UPDATE relac_pj_pf 
					SET dt_demissao = '".$strDTHomo."', 
					obs = '".prepStr($strObsHomo)."',
					sys_dtt_upd = CURRENT_TIMESTAMP, 
					sys_usr_upd = '".getSession(CFG_SYSTEM_NAME . "_id_usuario")."' 
					WHERE cod_pj_pf = ".$intCodDado;
			$objConn->query($strSQL);
			
			$objConn->commit();
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConn->rollBack();
			die();
		}
		
		$objConn = NULL;
		redirect($strRedirect);
	}
?>
