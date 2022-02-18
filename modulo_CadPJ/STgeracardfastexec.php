<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athkernelfunc.php");
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$intCODPJPF     = request("var_chavereg"); //pega o código da relacao PJ_PF - by Vini - 12.11.2012
	$intCODPJ 	 	= request("var_cod_pj");
	$intCODPF 	 	= request("var_cod_pf");
	$intPRODUTO   	= request("var_produto");
	$strOBSPEDIDO 	= request("var_obs_pedido");
	$strEmailPJ		= request("var_email_pj");
	$strEmailPF		= request("var_email_pf");
	
	$flagGERARTIT	= request("var_opcao_gerar_titulo");
	$dblVALORTIT	= request("var_valor");
	$dttVCTOTIT	    = cDate(CFG_LANG,request("var_dt_vcto"),false);
	$intCODBOLETO 	= request("var_cod_cfg_boleto");
	$intCODCONTA 	= request("var_cod_conta");
	$intPLANOCONTA 	= request("var_cod_plano_conta");
	$intCENTROCUSTO	= request("var_cod_centro_custo");
	$strHISTORICO 	= request("var_historico");
	$strOBSTITULO 	= request("var_obs");
	$strTIPODOC 	= request("var_tipo_doc");
	$strFLAGEMAILBOLETO	= request("var_opcao_enviar_email");
	
	$strREDIRECT	 = request("DEFAULT_LOCATION");
	
	// $strDocumento = "DINHEIRO";
	// $strNumLcto   = request("var_num_lcto");
	// $numDocumento = request("var_num_documento");
	

	// TESTA OS CAMPOS OBRIGATÓRIOS
	$strErrMSG   = "";
	$strErrMSG  .= ($intCODPJ  		== "") ? "Informe código da pessoa jurídica <br/>" 	: "";
	$strErrMSG 	.= ($intCODPF 	 	== "") ? "Informe código da pessoa física <br/>" 	: "";
	$strErrMSG 	.= ($dblVALORTIT  	== "") ? "Informe valor <br/>" 						: "";
	$strErrMSG 	.= ($dttVCTOTIT	  	== "") ? "Informe data de vcto <br/>" 				: "";
	$strErrMSG 	.= ($intPLANOCONTA  == "") ? "Informe código do plano de contas <br/>" 	: "";
	$strErrMSG 	.= ($intCENTROCUSTO == "") ? "Informe código do centro de custo <br/>" 	: "";
	$strErrMSG	.= ($strHISTORICO  	== "") ? "Informe histórico <br/>" 					: "";
	$strErrMSG 	.= ($intCODBOLETO	== "") ? "Informe código de boleto padrão <br/>" 	: "";
	$strErrMSG  .= ($strTIPODOC    	== "") ? "Informe o tipo de documento <br/>" 		: "";
	// $strErrMSG  .= (($flagGERARTIT == "S") && ($strNumLcto == "") && ($numDocumento == "")) ? "número LCTO ou número de DOCUMENTO<br/>" : "";
	if($strErrMSG != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strErrMSG, "", "erro", 1);
		die();
	}
	
	// OLD: CHAMA PROCEDURE QUE FAZ INSERÇÃO DE PEDIDO
	// NEW: LOCALIZA DADOS DO PRODUTO PARA GERAÇÃO DE PEDIDO
	// INICIALIZA TRANSAÇÃO
	$objConn->beginTransaction();
	try{
		$strSQL    = "SELECT razao_social FROM cad_pj WHERE cod_pj = ".$intCODPJ;
		$objResult = $objConn->query($strSQL);
		$objRSPJN  = $objResult->fetch();
		
		$strSQL    = "SELECT nome FROM cad_pf WHERE cod_pf = ".$intCODPF;
		$objResult = $objConn->query($strSQL);
		$objRSPFN  = $objResult->fetch();
		
		$strSQL    = "
			SELECT 
				  prd_produto.rotulo
				, prd_produto.valor
				, prd_produto.tipo
				, prd_produto.descricao 
				, prd_produto.dt_ini_val_produto
				, prd_produto.dt_fim_val_produto
			FROM  prd_produto 
			WHERE prd_produto.cod_produto = ".$intPRODUTO;
		$objResult = $objConn->query($strSQL);
		$objRS 	= $objResult->fetch();
		
		if(($intCODPJPF == "") or ($intCODPJPF < 0)){ //se o parametro de entrada COD_PJ_PF estiver vazio consulta como estava antes - By Vini - 12.11.12
		  $strSQL = "SELECT cod_pj_pf FROM relac_pj_pf WHERE cod_pf = ".$intCODPF." AND cod_pj = ".$intCODPJ;
		  $objResult = $objConn->query($strSQL);
		  $objRSP	= $objResult->fetch();
		  $intCODPJPF = getValue($objRSP,"cod_pj_pf");
		}
		
		$strSQL = "
			INSERT INTO prd_pedido(
				  cod_pj
				, situacao
				, obs
				, valor
				, it_cod_pf
				, it_cod_pj_pf
				, it_cod_produto
				, it_descricao
				, it_valor
				, it_obs
				, it_tipo
				, it_dt_ini_val_produto
				, it_dt_fim_val_produto
				, sys_usr_ins
				, sys_dtt_ins
			) VALUES (
				  ".$intCODPJ."
				, 'aberto'
				, '".prepStr($strOBSPEDIDO)."'
				, ".MoedaToFloat(getValue($objRS,"valor"))."
				, ".$intCODPF."
				, ".$intCODPJPF."
				, ".$intPRODUTO."
				, '".prepStr(getValue($objRS,"descricao"))."'
				, ".MoedaToFloat(getValue($objRS,"valor"))."
				, '".prepStr($strOBSPEDIDO)."'
				, '".getValue($objRS,"tipo")."'
				, '".getValue($objRS,"dt_ini_val_produto")."'
				, '".getValue($objRS,"dt_fim_val_produto")."'
				, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
				, CURRENT_TIMESTAMP
			);";
		// echo($strSQL);
		$objConn->query($strSQL);
				
		// VERIFICA SE DEVEMOS INSERIR UM TITULO OU NAO
		// OBSERVAÇÃO: TITULO GERADO É ABERTO!
		if($flagGERARTIT != ""){
			// LOCALIZA ULTIMO PEDIDO INSERIDO
			$strSQL = "SELECT MAX(cod_pedido) AS cod_pedido FROM prd_pedido";
			$objResult = $objConn->query($strSQL);
			$objRS = $objResult->fetch();
			
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
					, sys_usr_ins
				) VALUES (
					  FALSE
					, ".$intCODPJ."
					, 'cad_pj'
					, ".$intCODCONTA."
					, ".$intPLANOCONTA."
					, ".$intCENTROCUSTO."
					,  CURRENT_DATE
					, '".$dttVCTOTIT."'
					, ".MoedaToFloat($dblVALORTIT)."
					, ".MoedaToFloat($dblVALORTIT)."
					, 0
					, 0
					, 0
					, '".prepStr($strHISTORICO)."'
					, '".$strTIPODOC."'
					, '".prepStr($strOBSTITULO)."'
					, 'aberto'
					, ".getValue($objRS,"cod_pedido")."
					, ".$intCODBOLETO."
					, CURRENT_TIMESTAMP
					, '".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
				)";
			// die($strSQL);
			$objConn->query($strSQL);
			
			// CASO ALGUM EMAIL ESTEJA CADASTRADO E O TIPO SEJA CREDENCIAL, 
			// ENTÃO ENCAMINHA UM EMAIL CONTENCO O BOLETO DO TÍTULO GERADO
			if($strFLAGEMAILBOLETO == "S"){
				// LOCALIZA OS DADOS DO TÍTULO RECÉM INSERIDO, PARA
				// MONTAR A LINHA DO BOLETO
				$strSQL    = "SELECT MAX(cod_conta_pagar_receber) AS cod_titulo FROM fin_conta_pagar_receber";
				$objResult = $objConn->query($strSQL);
				$objRSTIT  = $objResult->fetch();
							
				// echo($strCITADOS);
				//<span style='color:green'><a href='https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=".$arrTITSC[$auxCounter]."' target='_blank' />Boleto Referente ao TÍTULO de código ".$arrTITSC[$auxCounter]."</a></span>
				// MONTA O CORPO DO EMAIL
				$strBodyEmail  = '';
				$strBodyEmail .= '
					<table width="100%" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" valign="top"> 
						<table width="100%" cellpadding="2" cellspacing="2">
							<tr><td width="100%"></td></tr>
							<tr><td>EMPRESA <strong>'.getValue($objRSPJN,"razao_social").'</strong>,</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td><strong>Nova Credencial Emitida</strong></td></tr>
							<tr><td>
								Gostaríamos de informar que uma nova credencial de associado foi emitida para o colaborador '.strtoupper(getValue($objRSPFN,"nome")).'.
								Seu pedido de credencial para associado foi aprovado pelo Sindicato. Abaixo encontra-se o link para impressão do boleto bancário, 
								que deve ser pago e apresentado com recibo no ato da retirada da credencial.
							</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td><strong>Boleto para Impressão</strong></td></tr>
							<tr><td><span style="color:green"><a href="https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg='.getValue($objRSTIT,"cod_titulo").'" target="_blank" />Boleto Referente ao TÍTULO de código '.getValue($objRSTIT,"cod_titulo").'</a></span></td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>Atenciosamente,</td></tr>
							<tr><td>SINDIEVENTOS</td></tr>
						</table>
						</td>
					</tr>
					</table>';
				
				// CONFIGURA LINHA DE DESTINATÁRIOS
				$strEmailLINE  = "";
				$strEmailLINE .= ($strEmailPJ == "") ? "" : $strEmailPJ.",";
				$strEmailLINE  = trim($strEmailLINE,",");
				// $strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
				// echo($strEmailLINE);
				
				// CONFIGURA TÍTULO DO EMAIL / SUBJECT
				$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("pedido_aprovado_credencial_emitida",C_NONE);
					
				// Encaminha o email somente se estiver ONLINE
				if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
					emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
				}
			}
		}
		
		// COMMITA A TRANSAÇÃO
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	$objConn = NULL;
	redirect($strREDIRECT);
?>