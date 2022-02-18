<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// ABRE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$intCodDado 			= request("var_chavereg");
	$intCodPJ 				= request("var_cod_pj");
	$intCodPF 				= request("var_cod_pf");
	$dblValor 				= MoedaToFloat(request("var_valorô"));
	$intQtdeParc            = request("var_qtde_parc");
	$dtVcto 				= request("var_dt_vctoô");
	$intCodConta 			= request("var_cod_contaô");
	$intCodPlanoConta 		= request("var_cod_plano_contaô");
	$intCodCentroCusto 		= request("var_cod_centro_custoô");
	$intCodJob              = request("var_cod_job");
	$intCodCFGBoleto		= request("var_cod_cfg_boletoô");
	$strTipoDoc 			= request("var_tipo_documento");
	$strHistorico 			= request("var_historicoô");
	$strObs 				= request("var_obs");
	$strTipo 				= request("var_tipo");
	$strExibirBoleto 		= request("var_exibir_boleto");
	$intAnoVcto  			= request("var_ano_vcto");
	
	// REQUESTS - AGENDAMENTO
	$strIDUsrAgend			= request("var_responsavel_agendamento");
	$strCategoriaAgenda		= request("var_categoria");
	$strPrioridadeAgenda	= request("var_prioridade");
	$strTITULOAGENDA		= request("var_titulo_agendaô");
	$strDESCRAGENDA			= request("var_descricao_agenda");
	$strFLAGAGENDA			= request("var_opcao_gerar_agenda");
	$strFLAGEMAIL			= request("var_opcao_gerar_email");
	$strEmailPJ				= request("var_email_pj");
	$strEmailPF				= request("var_email_pf");
	
	// REQUESTS - ENVIO DE EMAIL BOLETO
	$strFLAGEMAILBOLETO		= request("var_opcao_enviar_email");
	
	// REQUEST DATAS - ESPECIAL
	$auxDtPrevIni	= request("var_dt_prev_iniô");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_hr_prev_iniô"),0,2) >= 24) ? "00".substr(request("var_hr_prev_iniô"),2,4).":00" : request("var_hr_prev_iniô"); // hr prev inicio
	$auxDtPrevFim	= request("var_dt_prev_iniô");												  // data prev fim
	$auxHrPrevFim	= (substr(request("var_hr_prev_fimô"),0,2) >= 24) ? "00".substr(request("var_hr_prev_fimô"),2,4).":00" : request("var_hr_prev_fimô"); // hr prev fim
	
	// Formatação das datas de previsão
	// de início e fim, respectivamente
	$dtPrevIni	= ($auxDtPrevIni == "") ? "" : $auxDtPrevIni ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevFim	= ($auxDtPrevFim == "") ? "" : $auxDtPrevFim ." ". $auxHrPrevFim; // data formatada [PREV_DTT_FIM]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	$dtPrevFim	= cDate(CFG_LANG,$dtPrevFim,true); 	 // data formatada [PREV_DTT_FIM]
	
	// VALIDA CAMPOS OBRIGATÓRIOS
	$strMsg = "";
	if($intCodDado 			== "") { $strMsg .= "&bull;&nbsp;Informar código de pedido<br>"; 		}
	if($intCodPJ 			== "") { $strMsg .= "&bull;&nbsp;Informar código da empresa<br>"; 		}
	//if($intCodPF 			== "") { $strMsg .= "Informar código da pessoa física<br>"; }
	if($dblValor 			== "") { $strMsg .= "&bull;&nbsp;Informar valor<br>"; 					}
	if($intCodConta 		== "") { $strMsg .= "&bull;&nbsp;Informar conta bancária<br>"; 			}
	if($intCodPlanoConta 	== "") { $strMsg .= "&bull;&nbsp;Informar plano de conta<br>"; 			}
	if($intCodCentroCusto 	== "") { $strMsg .= "&bull;&nbsp;Informar centro de custo<br>"; 		}
	if($strHistorico 		== "") { $strMsg .= "&bull;&nbsp;Informar histórico<br>"; 				}
	//if($strObs 			== "") { $strMsg .= "&bull;&nbsp;Informar observação<br>"; 				}
	//if(($dtVcto 			== "") { $strMsg .= "&bull;&nbsp;Informar vencimento<br>"; 				}
	if($dtVcto 				== "") { $strMsg .= "&bull;&nbsp;Informar vencimento<br>"; 				}
	
	// Só testa obrigatoriedade caso 
	// seja para homologação
	if($strTipo == "homo"){
		if($strIDUsrAgend		== "") { $strMsg .= "&bull;&nbsp;Informar responsável pela Agenda<br>"; }
		if($strCategoriaAgenda	== "") { $strMsg .= "&bull;&nbsp;Informar categoria da Agenda<br>";     }
		if($strPrioridadeAgenda	== "") { $strMsg .= "&bull;&nbsp;Informar prioridade da Agenda<br>";    }
		if($dtPrevIni			== "") { $strMsg .= "&bull;&nbsp;Informar previsão de início<br>"; 		}
		if($auxHrPrevIni		== "") { $strMsg .= "&bull;&nbsp;Informar hora Prev. Inicio<br>";		}
		if($auxHrPrevFim		== "") { $strMsg .= "&bull;&nbsp;Informar hora Prev. Fim<br>";			}
		if($dtPrevFim			== "") { $strMsg .= "&bull;&nbsp;Informar previsão de fim<br>"; 		}
		if($strTITULOAGENDA		== "") { $strMsg .= "&bull;&nbsp;Informar título da Agenda <br>";		}
		// if($strDESCRAGENDA		== "") { $strMsg .= "&bull;&nbsp;Informar descrição da Agenda<br>"; 	}
		if(($strFLAGEMAIL != "") && (($strEmailPJ == "") && ($strEmailPF == "")))    { $strMsg .= "&bull;&nbsp;Um email ao menos deve estar cadastrado"; }
		if(($dtPrevFim < $dtPrevIni) && (is_date($dtPrevFim) && is_date($dtPrevIni))){ $strMsg .= "&bull;&nbsp;Data de início maior que FIM<br />"; }
	}
	
	if($strMsg != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "javascript:history.back();", "erro", 1);
		die();
	}
	
	// Tratamentos
	if($intCodCFGBoleto == "") $intCodCFGBoleto = getVarEntidade($objConn, "cod_cfg_boleto_padrao");
	if($intCodCFGBoleto == "") $intCodCFGBoleto = "NULL";
	
	if($intCodJob == "") $intCodJob = "NULL";
	
	$dtVcto = cDate(CFG_LANG,$dtVcto,false);
	
	$intAnoVcto = ($intAnoVcto == "") ? substr($dtVcto,0,4) : $intAnoVcto;
	
	$intQtdeParc = (($intQtdeParc == "") || ($intQtdeParc <= 0)) ? 1 : $intQtdeParc;
	
	//--------------------------------------------------------------------------------
	//Gera título a receber com base no pedido passado, e põe pedido para 
	//faturado. Se pedido for de credencial ou homologação as tabelas "sd_credencial"
	//e "sd_homologacao" serão preenchidas pelo trigger "before insert" de título
	//--------------------------------------------------------------------------------
	$objConn->beginTransaction();
	try{
		$strSQL    = "SELECT razao_social FROM cad_pj WHERE cod_pj = ".$intCodPJ;
		$objResult = $objConn->query($strSQL);
		$objRSPJN  = $objResult->fetch();
		
		if($intCodPF != ""){
			$strSQL    = "SELECT nome FROM cad_pf WHERE cod_pf = ".$intCodPF;
			$objResult = $objConn->query($strSQL);
			$objRSPFN  = $objResult->fetch();
		}
		
		$strSQL = " 
			INSERT INTO fin_conta_pagar_receber ( pagar_receber, codigo, tipo
												, cod_conta, cod_plano_conta, cod_centro_custo, cod_job, cod_cfg_boleto
												, dt_emissao, dt_vcto, vlr_conta, vlr_saldo, vlr_pago, vlr_outros, vlr_desc 
												, historico, tipo_documento, obs, situacao, cod_pedido
												, sys_dtt_ins, sys_usr_ins, ano_vcto) 
			VALUES ( FALSE
					,".$intCodPJ."
					, 'cad_pj'
					,".$intCodConta."
					,".$intCodPlanoConta."
					,".$intCodCentroCusto."
					,".$intCodJob."
					,".$intCodCFGBoleto."
					, CURRENT_DATE
					,'".$dtVcto."'
					,".$dblValor."
					,".$dblValor."
					,0
					,0
					,0
					,'".$strHistorico."'
					,'".$strTipoDoc."'
					,'".$strObs."'
					, 'aberto'
					,".$intCodDado."
					, CURRENT_TIMESTAMP
					,'".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
					,".$intAnoVcto.")";
		$objConn->query($strSQL);
		
		// Localiza o ÚLTIMO COD_TITULO INSERIDO
		$strSQL    = " SELECT MAX(cod_conta_pagar_receber) AS max_cod_titulo 
					   FROM fin_conta_pagar_receber 
					   WHERE pagar_receber = FALSE 
					   AND situacao = 'aberto' 
					   AND codigo = ".$intCodPJ." 
					   AND sys_usr_ins = '".getSession(CFG_SYSTEM_NAME . "_id_usuario")."' ";
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		$intCodContaPagarReceber = getValue($objRS,"max_cod_titulo");
		
		if ($intQtdeParc > 1) {
			$strSQL = " SELECT sp_gera_parcelamento(".$intCodContaPagarReceber.", ".$intQtdeParc.", '".getSession(CFG_SYSTEM_NAME . "_id_usuario")."') ";
			$objConn->query($strSQL);
		}
		
		// Verifica se o TIPO de produto é HOMO,
		// para testar o envio de email ou geração de agenda
		// e por cascata, os CITADOS também
		if($strTipo == "homo"){
			if($strFLAGAGENDA = "NEW_AGENDA"){
				// Localiza o USUARIO DA PJ para o COLOCAR COMO CITADO
				$strSQL     = " SELECT id_usuario FROM sys_usuario WHERE codigo = ".$intCodPJ;
				$objResult  = $objConn->query($strSQL);
				$objRS		= $objResult->fetch();
				$strIDCIT	= getValue($objRS,"id_usuario");
			
				// Localiza o ÚLTIMO SD_HOMOLOGACAO INSERIDO
				$strSQL     = " SELECT MAX(cod_homologacao) AS max_cod_homo FROM sd_homologacao WHERE cod_pj = ".$intCodPJ." AND cod_pf = ".$intCodPF;
				$objResult  = $objConn->query($strSQL);
				$objRS	    = $objResult->fetch();
				$intCODHOMO = getValue($objRS,"max_cod_homo");
			
				$strSQL     = " INSERT INTO ag_agenda (id_responsavel, categoria, prioridade, titulo, descricao, prev_dtt_ini, prev_dtt_fim, sys_usr_ins, sys_dtt_ins, tipo, codigo) 
								VALUES ('".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."','".prepStr($strCategoriaAgenda)."','".prepStr($strPrioridadeAgenda)."','".prepStr($strTITULOAGENDA)."','".prepStr($strDESCRAGENDA)."','".$dtPrevIni."','".$dtPrevFim."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP,'sd_homologacao',".$intCODHOMO."); ";
				$objConn->query($strSQL);
				
				// Localiza o ÚLTIMA AGENDA INSERIDA PARA INSERIR O CITADO
				$strSQL     = " SELECT MAX(cod_agenda) AS max_cod_agenda FROM ag_agenda ";
				$objResult  = $objConn->query($strSQL);
				$objRS	    = $objResult->fetch();
				$intCODAG   = getValue($objRS,"max_cod_agenda");
				
				// INSERE O CITADO REFERENTE A PJ da Homologação
				$strSQL     = " INSERT INTO ag_agenda_citado (cod_agenda, id_usuario, sys_usr_ins, sys_dtt_ins) 
								VALUES (".$intCODAG.",'".prepStr($strIDCIT)."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP) ";
				$objConn->query($strSQL);
				
				if($strFLAGEMAIL == "NEW_EMAIL"){
					// POR ÚLTIMO, SE OPÇÃO DE ENVIAR EMAIL ESTÁ MARCADA
					// ENTÃO LOCALIZA TODOS OS DADOS DA AGENDA INSERIDA
					$strSQL = "
						SELECT
							  ag_agenda.id_responsavel
							, ag_agenda.categoria
							, ag_agenda.titulo
							, ag_agenda.descricao
							, ag_agenda.prev_dtt_ini
							, ag_agenda.prev_dtt_fim
						FROM ag_agenda
						WHERE ag_agenda.cod_agenda = ".$intCODAG;
					$objResult = $objConn->query($strSQL);
					$objRS     = $objResult->fetch();
					
					// LOCALIZA OS CITADOS PARA COLOCAÇÃO NO CAMPO 'CITADOS'
					$strSQL 	= " SELECT id_usuario FROM ag_agenda_citado WHERE cod_agenda = ".$intCODAG;
					$objCITADOS = $objConn->query($strSQL);
					$strCITADOS = "";
					foreach($objCITADOS as $objCIT){ 
						$strCITADOS .= getValue($objCIT,"id_usuario").";"; 
					}
					$strCITADOS = rtrim($strCITADOS,";");
					
					// MONTA O CORPO DO EMAIL
					$strBodyEmail  = '';
					$strBodyEmail .= '
						<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
							<tr>
								<td colspan="2">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
										<tr>
											<td class="td_label">&nbsp;</td>
											<td><strong>'.getTText("inclusao_agenda",C_NONE).'</strong></td>
										</tr>
										<tr><td colspan="2">&nbsp;</td></tr>
										<tr>
											<td class="td_label">'.getTText("titulo_agenda",C_NONE).':</td>
											<td>'.getValue($objRS,"titulo").'</td>
										</tr>
										<tr>
											<td class="td_label">'.getTText("categoria_agenda",C_NONE).':</td>
											<td>'.strtoupper(getValue($objRS,"categoria")).'</td>
										</tr>
										<tr>
											<td class="td_label">'.getTText("responsavel_agenda",C_NONE).':</td>
											<td>'.getValue($objRS,"id_responsavel").'</td>
										</tr>
										<tr>
											<td class="td_label">'.getTText("citados_agenda",C_NONE).':</td>
											<td>'.$strCITADOS.'</td>
										</tr>
										<tr>
											<td class="td_label">'.getTText("prev_ini_agenda",C_NONE).':</td>
											<td>'.dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true).'  até  '.dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true).'</td>
										</tr>
										<tr>
											<td class="td_label">'.getTText("descricao_agenda",C_NONE).':</td>
											<td>'.getValue($objRS,"descricao").'</td>
										</tr>
										<tr><td colspan="2">&nbsp;</td></tr>
									</table>
								</td>
							</tr>
						</table>';
					
					// CONFIGURA LINHA DE DESTINATÁRIOS
					$strEmailLINE  = "";
					$strEmailLINE .= ($strEmailPJ == "") ? "" : $strEmailPJ.",";
					$strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
					$strEmailLINE  = trim($strEmailLINE,",");
					
					// CONFIGURA TÍTULO DO EMAIL / SUBJECT
					$strSUBJECT = ucwords(CFG_SYSTEM_NAME)." - ".getTText("insercao_de_agenda",C_NONE);
					
					// Encaminha o email somente se estiver ONLINE
					if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
						emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
					}
				}
			}
		}
		
		
		// CASO ALGUM EMAIL ESTEJA CADASTRADO E O TIPO SEJA CREDENCIAL, 
		// ENTÃO ENCAMINHA UM EMAIL CONTENDO O BOLETO DO TÍTULO GERADO
		if($strTipo == "card"){
			if($strFLAGEMAILBOLETO == "S"){
				// LOCALIZA OS DADOS DO TÍTULO RECÉM INSERIDO, PARA MONTAR A LINHA DO BOLETO
				$strSQL    = "SELECT MAX(cod_conta_pagar_receber) AS cod_titulo FROM fin_conta_pagar_receber";
				$objResult = $objConn->query($strSQL);
				$objRSTIT  = $objResult->fetch();
				
				// MONTA O CORPO DO EMAIL
				$strBodyEmail = getVarEntidade($objConn, "msg_associado_card_gerada");
				$strBodyEmail = str_replace("<TAG_RAZAO_SOCIAL/>", getValue($objRSPJN,"razao_social")    , $strBodyEmail);
				$strBodyEmail = str_replace("<TAG_COLAB_NOME/>"  , strtoupper(getValue($objRSPFN,"nome")), $strBodyEmail);
				$strBodyEmail = str_replace("<TAG_COD_TITULO/>"  , getValue($objRSTIT,"cod_titulo")      , $strBodyEmail);
				
				// CONFIGURA LINHA DE DESTINATÁRIOS
				$strEmailLINE  = "";
				$strEmailLINE .= ($strEmailPJ == "") ? "" : $strEmailPJ.",";
				$strEmailLINE  = trim($strEmailLINE,",");
				// $strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
				
				// CONFIGURA TÍTULO DO EMAIL / SUBJECT
				$strSUBJECT = ucwords(CFG_SYSTEM_NAME)." - ".getTText("pedido_aprovado_credencial_emitida",C_NONE);
				
				// Encaminha o email somente se estiver ONLINE
				if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
					emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
				}
			}
		}
		
		if($strTipo == "certificado"){
			// Localiza o ÚLTIMO SD_HOMOLOGACAO INSERIDO
			$strSQL     = " SELECT MAX(cod_certificado) AS max_cod_certificado FROM sd_certificado WHERE cod_pj = ".$intCodPJ;
			$objResult  = $objConn->query($strSQL);
			$objRS	    = $objResult->fetch();
			$intCodCertificado = getValue($objRS,"max_cod_certificado");
			
			if ($intCodCertificado != "") {
				//Caminho logico básico para buscar o arquivo de modelo do certificado
				$strCaminhoLogico = findLogicalPath();
				
				try {
				$strSQL = "SELECT 
								sd_certificado.cod_pedido
							  , sd_certificado.cod_pj
							  , cad_pj.razao_social  
							  , sd_certificado.dtt_pedido
							  , cad_pj.cnpj 				    AS cnpj
							  , to_char(dtt_pedido,'DD') 		AS dia_compra
							  , to_char(dtt_pedido,'YYYY') 		AS ano_compra
							  , to_char(dt_validade,'DD')	 	AS dia_validade
							  , to_char(dt_validade,'YYYY') 	AS ano_validade
							  , CASE WHEN to_char(dtt_pedido,'MM') = '01' THEN 'janeiro'
									 WHEN to_char(dtt_pedido,'MM') = '02' THEN 'fevareiro'
									 WHEN to_char(dtt_pedido,'MM') = '03' THEN 'marco'
									 WHEN to_char(dtt_pedido,'MM') = '04' THEN 'abril'
									 WHEN to_char(dtt_pedido,'MM') = '05' THEN 'maio'
									 WHEN to_char(dtt_pedido,'MM') = '06' THEN 'junho'
									 WHEN to_char(dtt_pedido,'MM') = '07' THEN 'julho'
									 WHEN to_char(dtt_pedido,'MM') = '08' THEN 'agosto'
									 WHEN to_char(dtt_pedido,'MM') = '09' THEN 'setembro'
									 WHEN to_char(dtt_pedido,'MM') = '10' THEN 'outubro'
									 WHEN to_char(dtt_pedido,'MM') = '11' THEN 'novembro'
									 WHEN to_char(dtt_pedido,'MM') = '12' THEN 'dezembro'
								END AS mes_compra
							  , CASE WHEN to_char(dt_validade,'MM') = '01' THEN 'janeiro'
									 WHEN to_char(dt_validade,'MM') = '02' THEN 'fevareiro'
									 WHEN to_char(dt_validade,'MM') = '03' THEN 'marco'
									 WHEN to_char(dt_validade,'MM') = '04' THEN 'abril'
									 WHEN to_char(dt_validade,'MM') = '05' THEN 'maio'
									 WHEN to_char(dt_validade,'MM') = '06' THEN 'junho'
									 WHEN to_char(dt_validade,'MM') = '07' THEN 'julho'
									 WHEN to_char(dt_validade,'MM') = '08' THEN 'agosto'
									 WHEN to_char(dt_validade,'MM') = '09' THEN 'setembro'
									 WHEN to_char(dt_validade,'MM') = '10' THEN 'outubro'
									 WHEN to_char(dt_validade,'MM') = '11' THEN 'novembro'
									 WHEN to_char(dt_validade,'MM') = '12' THEN 'dezembro'
								END AS mes_validade
							  , sd_certificado.dt_validade
							  , sd_certificado.sys_dtt_ins
							  , prd_pedido.it_arq_modelo
							FROM sd_certificado
							INNER JOIN cad_pj ON (sd_certificado.cod_pj = cad_pj.cod_pj)
							INNER JOIN prd_pedido ON (sd_certificado.cod_pedido = prd_pedido.cod_pedido)
							WHERE sd_certificado.cod_certificado = " .$intCodCertificado;
				$objResult = $objConn->query($strSQL);
				}
				catch(PDOException $e) {
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				}
				//Cria array associativo para o sql gerado acima
				$objRS = $objResult->fetch();
				
				//Leitura do HTML do modelo de certificado
				$strStreamHTML = file_get_contents("../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/modelos/certificado/".getValue($objRS,"it_arq_modelo"));
				$strStreamHTML = preg_replace("/\<TAG_NOME_CLIENTE\>/",$strCaminhoLogico,$strStreamHTML);
			
				//Troca a string '<TAG_' de todo stream do modelo (html) por um código php que faz a busca dos dados no $objRS, em fetch
				preg_match_all("/\<TAG_[A-Za-z0-9_]+\>/",$strStreamHTML,$arrMatches);
				foreach($arrMatches[0] as $strMatch){
					$strParse 		= preg_replace("/\<TAG_|\>/","",$strMatch);
					$strStreamHTML	= str_replace($strMatch,getValue($objRS,strtolower($strParse)),$strStreamHTML);
				}
				
				// Prefixo FILE
				$strPrefixFile 	= date("Y").date("m").date("d").date("H").date("i").date("s");
				$strFileName	= "certificado_".$strPrefixFile."_".$intCodCertificado.".html";
				$strStreamFile 	= $strStreamHTML;
				
				// Feito o Stream do Arquivo, guarda-o em um html
				$strFileNew = "../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/certificado/".$strFileName;
				file_put_contents($strFileNew,$strStreamFile);
				
				if(file_exists($strFileNew)){
					try {
						$strSQL = " UPDATE sd_certificado 
									SET arquivo = '".$strFileName."' 
									  , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
									  , sys_dtt_upd = CURRENT_TIMESTAMP
									WHERE cod_certificado = ".$intCodCertificado;
						$objResult = $objConn->query($strSQL);
					}
					catch(PDOException $e) {
						mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
						die();
					}
				}
			}
		}
		
		if($strTipo != "card") {
			//Se for diferente de CARD manda aviso, CARD tem flag específico para enviar e também mensagem específica
			
			// LOCALIZA OS DADOS DO TÍTULO RECÉM INSERIDO, PARA MONTAR A LINHA DO BOLETO
			$strSQL    = "SELECT MAX(cod_conta_pagar_receber) AS cod_titulo FROM fin_conta_pagar_receber";
			$objResult = $objConn->query($strSQL);
			$objRSTIT  = $objResult->fetch();
			
			// MONTA O CORPO DO EMAIL
			$strBodyEmail = getVarEntidade($objConn, "msg_associado_pedido_faturado");
			$strBodyEmail = str_replace("<TAG_RAZAO_SOCIAL/>", getValue($objRSPJN,"razao_social"), $strBodyEmail);
			$strBodyEmail = str_replace("<TAG_COD_TITULO/>"  , getValue($objRSTIT,"cod_titulo")  , $strBodyEmail);
			
			// CONFIGURA LINHA DE DESTINATÁRIOS
			$strEmailLINE  = "";
			$strEmailLINE .= ($strEmailPJ == "") ? "" : $strEmailPJ.",";
			$strEmailLINE  = trim($strEmailLINE,",");
			// $strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
			
			// CONFIGURA TÍTULO DO EMAIL / SUBJECT
			$strSUBJECT = ucwords(CFG_SYSTEM_NAME)." - ".getTText("pedido_faturado",C_NONE);
			
			// Encaminha o email somente se estiver ONLINE
			if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
				emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
			}
		}
		
		// Commit TRANSAÇÃO
		$objConn->commit();
	}
	catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Fecha o cursor
	$objResult->closeCursor();
	
	// Tratamento para abertura da janela que exibirá o BOLETO
	if($strExibirBoleto == "T"){
		if($intCodContaPagarReceber != ""){?>
			<script type="text/javascript" language="javascript">AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo($intCodContaPagarReceber); ?>','750','580');</script>
		<?php
		} else{
			mensagem("err_sql_titulo","err_sql_desc","Código da conta não foi encontrado","","erro",1);
			die();
		}
	}
	
	$objConn = NULL;
	
// Não podemos usar a "redirect" por causa dos códigos de script acima
// redirect("STindex.php");
?>
<script language="javascript" type="text/javascript">
	location.href = "STindex.php";
</script>