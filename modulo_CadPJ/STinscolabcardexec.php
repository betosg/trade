<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athkernelfunc.php");
	
	// ABERTURA DE CONEX�O COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$flagTipoPF  	= request("var_tipo_pf");
	$strCPF			= request("var_pf_cpf");
	$strRG 	 	 	= request("var_pf_rg");
	$strNOME 	 	= request("var_pf_nome");
	$strAPELIDO		= request("var_pf_apelido");
	$chrSEXO 	 	= request("var_pf_sexo");
	$strPFOBS 	 	= request("var_pf_obs");
	$strPFFOTO		= request("var_foto");
	$intCodPJ 	 	= request("var_cod_pj");
	$intCodPF 	 	= request("var_cod_pf");
	$intPRODUTO   	= request("var_produto");
	$strOBSPEDIDO 	= request("var_obs_pedido");
	$strEmailPJ		= request("var_email_pj");
	$strEmailPF		= request("var_email_pf");
	$intPFEMP       = request("var_cod_pf");
	
	// REQUEST - DADOS DA VAGA
	$strCATEG 	 = request("var_vaga_categoria");
	$strTIPO 	 = request("var_vaga_tipo");
	$strFUNCAO 	 = request("var_vaga_funcao");
	$strDEPART 	 = request("var_vaga_departamento");
	$dttADMISSAO = request("var_vaga_admissao");
	$strVAGAOBS	 = request("var_vaga_obs");
	
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
	

	// TESTA OS CAMPOS OBRIGAT�RIOS
	/*$strErrMSG   = "";
	$strErrMSG  .= ($intCODPJ  		== "") ? "Informe c�digo da pessoa jur�dica <br/>" 	: "";
	$strErrMSG 	.= ($intCODPF 	 	== "") ? "Informe c�digo da pessoa f�sica <br/>" 	: "";
	$strErrMSG 	.= ($dblVALORTIT  	== "") ? "Informe valor <br/>" 						: "";
	$strErrMSG 	.= ($dttVCTOTIT	  	== "") ? "Informe data de vcto <br/>" 				: "";
	$strErrMSG 	.= ($intPLANOCONTA  == "") ? "Informe c�digo do plano de contas <br/>" 	: "";
	$strErrMSG 	.= ($intCENTROCUSTO == "") ? "Informe c�digo do centro de custo <br/>" 	: "";
	$strErrMSG	.= ($strHISTORICO  	== "") ? "Informe hist�rico <br/>" 					: "";
	$strErrMSG 	.= ($intCODBOLETO	== "") ? "Informe c�digo de boleto padr�o <br/>" 	: "";
	$strErrMSG  .= ($strTIPODOC    	== "") ? "Informe o tipo de documento <br/>" 		: "";
	// $strErrMSG  .= (($flagGERARTIT == "S") && ($strNumLcto == "") && ($numDocumento == "")) ? "n�mero LCTO ou n�mero de DOCUMENTO<br/>" : "";
	if($strErrMSG != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strErrMSG, "", "erro", 1);
		die();
	}*/
	
	$intCodREL = -1; //inicializa vari�vel usada para guardar o c�digo da relacao PF_PJ - by Vini 12-11-2012
	// OLD: CHAMA PROCEDURE QUE FAZ INSER��O DE PEDIDO
	// NEW: LOCALIZA DADOS DO PRODUTO PARA GERA��O DE PEDIDO
	// INICIALIZA TRANSA��O
	$objConn->beginTransaction();
	try{
		// PARA NOVOS COLABORADORES, CAD_PF + RELA��O
		if($flagTipoPF == "NEW_PF"){
			// INI: altera��o: -----------------------------------------------------------------------------
			// Mesmo que receba a indica��o "NEW_PF', o sistema passa a procurar pelo CPF indicado, 
			// caso exista n�o insere a PF, apenas a utiliza esta PF, criando uma nova rela��o.
			// --------------------------------------------------------------------------- [TAREFA 26255] --
			// Localiza PF pelo CPF (poderia buscar tbm pelo RG)
			$objResult = $objConn->query("SELECT cod_pf FROM cad_pf WHERE cpf LIKE '" . prepStr($strCPF) . "'");
			$objRS	   = $objResult->fetch();
			$intCodPF  = getValue($objRS,"cod_pf");
			//echo($strSQL."<br /><br />");
			if ($intCodPF != "") {
				// Se encontrou a PF, ent�o faz update b�sico de dados desta PF encaminhada [COLABORADOR]
				$strSQL  = "UPDATE cad_pf ";
				$strSQL .= "   SET nome ='".prepStr($strNOME)."', apelido='".prepStr($strAPELIDO)."' ";
				$strSQL .= "     , sexo='".prepStr($chrSEXO)."', obs='".prepStr($strPFOBS)."', foto='".prepStr($strPFFOTO)."' ";
				$strSQL .= "     , sys_usr_upd='".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."', sys_dtt_upd=CURRENT_TIMESTAMP), dtt_inativo = NULL ";
				$strSQL .= "WHERE cod_pf = " . $intCodPF;
				$objConn->query($strSQL);
				//echo($strSQL."<br /><br />");
			} else { 
				// Se n�o encontrou a PF, ent�o � realmente NEW_PF
				// Insere PF encaminhada [COLABORADOR]
				$strSQL = "INSERT INTO cad_pf (cpf, rg, nome, apelido, sexo, obs, foto, sys_usr_ins, sys_dtt_ins) VALUES ('".prepStr($strCPF)."','".prepStr($strRG)."','".prepStr($strNOME)."','".prepStr($strAPELIDO)."','".prepStr($chrSEXO)."','".prepStr($strPFOBS)."','".prepStr($strPFFOTO)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
				$objConn->query($strSQL);
				//echo($strSQL."<br /><br />");
				
				// Localiza esta PF rec�m-inserida
				$objResult = $objConn->query("SELECT last_value AS max_cod_pf FROM cad_pf_cod_pf_seq;");
				$objRS	   = $objResult->fetch();
				$intCodPF  = getValue($objRS,"max_cod_pf");
				//echo($strSQL."<br /><br />");
			}
			// FIM: ---------------------------------------------------------------------- [TAREFA 26255] --


			// Insere RELA��O da Vaga do COLABORADOR para a PJ
			$strSQL = "INSERT INTO relac_pj_pf (cod_pj, cod_pf, tipo, funcao, departamento, dt_admissao, obs, categoria, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",".$intCodPF.",'".prepStr($strTIPO)."','".prepStr($strFUNCAO)."','".prepStr($strDEPART)."','".cDate(CFG_LANG,$dttADMISSAO,false)."','".prepStr($strVAGAOBS)."','".prepStr($strCATEG)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			//echo($strSQL."<br /><br />");
			
			// Localiza RELA��O rec�m-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq;");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"max_cod_relac");
			//echo($strSQL."<br /><br />");
		}
		// PARA COLABORADOR QUE J� EXISTA MAS EM VAGA NENHUMA
		if($flagTipoPF == "OLD_PF"){
			$intCodPF = $intPFEMP;
			// Insere RELA��O da Vaga do COLABORADOR para a PJ
			$strSQL = "INSERT INTO relac_pj_pf (cod_pj, cod_pf, tipo, funcao, departamento, dt_admissao, obs, categoria, sys_usr_ins, sys_dtt_ins) VALUES (".$intCodPJ.",".$intCodPF.",'".prepStr($strTIPO)."','".prepStr($strFUNCAO)."','".prepStr($strDEPART)."','".cDate(CFG_LANG,$dttADMISSAO,false)."','".prepStr($strVAGAOBS)."','".prepStr($strCATEG)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza RELA��O rec�m-inserida
			$objResult = $objConn->query("SELECT last_value AS max_cod_relac FROM relac_pj_pf_cod_pj_pf_seq;");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"max_cod_relac");
		}
		// PARA HOMOLOGA��O DE COLABORADOR QUE ESTEJA EM UMA PJ
		if($flagTipoPF == "PJ_PF"){
			$intCodPF = $intPF;
			// LOCALIZA RELA��O
			$objResult = $objConn->query("SELECT cod_pj_pf FROM relac_pj_pf WHERE cod_pj = ".$intCodPJ." AND cod_pf = ".$intCodPF.";");
			$objRS	   = $objResult->fetch();
			$intCodREL = getValue($objRS,"cod_pj_pf");
		}
		$strSQL    = "SELECT razao_social FROM cad_pj WHERE cod_pj = ".$intCodPJ;
		$objResult = $objConn->query($strSQL);
		$objRSPJN  = $objResult->fetch();
		
		$strSQL    = "SELECT nome FROM cad_pf WHERE cod_pf = ".$intCodPF;
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
			
		if($intCodREL < 0){ //se for menor que zero � porque n�o entrou nos testes acima "PJ_PF", "OLD_PF", "NEW_PF"
		  $strSQL = "SELECT cod_pj_pf FROM relac_pj_pf WHERE cod_pf = ".$intCodPF." AND cod_pj = ".$intCodPJ;
		  $objResult = $objConn->query($strSQL);
		  $objRSP	= $objResult->fetch();	
		  $intCodREL = getValue($objRSP,"cod_pj_pf");
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
				  ".$intCodPJ."
				, 'aberto'
				, '".prepStr($strOBSPEDIDO)."'
				, ".MoedaToFloat(getValue($objRS,"valor"))."
				, ".$intCodPF."
				, ".$intCodREL."
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
		//echo($strSQL);
		$objConn->query($strSQL);
				
		// VERIFICA SE DEVEMOS INSERIR UM TITULO OU NAO
		// OBSERVA��O: TITULO GERADO � ABERTO!
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
					, ".$intCodPJ."
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
			// ENT�O ENCAMINHA UM EMAIL CONTENCO O BOLETO DO T�TULO GERADO
			if($strFLAGEMAILBOLETO == "S"){
				// LOCALIZA OS DADOS DO T�TULO REC�M INSERIDO, PARA
				// MONTAR A LINHA DO BOLETO
				$strSQL    = "SELECT MAX(cod_conta_pagar_receber) AS cod_titulo FROM fin_conta_pagar_receber";
				$objResult = $objConn->query($strSQL);
				$objRSTIT  = $objResult->fetch();
							
				// echo($strCITADOS);
				//<span style='color:green'><a href='https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=".$arrTITSC[$auxCounter]."' target='_blank' />Boleto Referente ao T�TULO de c�digo ".$arrTITSC[$auxCounter]."</a></span>
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
								Gostar�amos de informar que uma nova credencial de associado foi emitida para o colaborador '.strtoupper(getValue($objRSPFN,"nome")).'.
								Seu pedido de credencial para associado foi aprovado pelo Sindicato. Abaixo encontra-se o link para impress�o do boleto banc�rio, 
								que deve ser pago e apresentado com recibo no ato da retirada da credencial.
							</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td><strong>Boleto para Impress�o</strong></td></tr>
							<tr><td><span style="color:green"><a href="https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg='.getValue($objRSTIT,"cod_titulo").'" target="_blank" />Boleto Referente ao T�TULO de c�digo '.getValue($objRSTIT,"cod_titulo").'</a></span></td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>Atenciosamente,</td></tr>
							<tr><td>SINDIEVENTOS</td></tr>
						</table>
						</td>
					</tr>
					</table>';
				
				// CONFIGURA LINHA DE DESTINAT�RIOS
				$strEmailLINE  = "";
				$strEmailLINE .= ($strEmailPJ == "") ? "" : $strEmailPJ.",";
				$strEmailLINE  = trim($strEmailLINE,",");
				// $strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
				// echo($strEmailLINE);
				
				// CONFIGURA T�TULO DO EMAIL / SUBJECT
				$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("pedido_aprovado_credencial_emitida",C_NONE);
					
				// Encaminha o email somente se estiver ONLINE
				if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
					emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
				}
			}
		}
		
		// COMMITA A TRANSA��O
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