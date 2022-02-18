<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/STathutils.php");
	include_once("STValidacaoToolsSICOBass.php");
	
	// REQUESTS n INITS
	$_SESSION['ValidaErro']= "";		// SETA a variaável de sessão ERRO
	$arqName = $_POST['uploadArquivo'];	// Recebe arquivo 'Uploadeado'
	
	$objConn = abreDBConn(CFG_DB); // Abre conexão com Banco
	
	$intCodRelat = getVarEntidade($objConn,"cod_relat_aslw_apos_importacao");
	if ($intCodRelat == "") $intCodRelat = "104";
	
	// PROCESSAMENTO PHP
	$arquivo = alteraNomeArq($arqName,"I");
	$linhaT  = "";
	$linhaU  = "";
	analisaArquivo($arquivo, $objConn, $intCodRelat);
	
	function convertDate($date){
		$data = substr($date,4,4)."-".substr($date,2,2)."-".substr($date,0,2);
		return $data;
	}
	
	function analisaArquivo($arquivo, $prObjConn, $prCodRelat){
		$strMsgFinal 	   = "";
		$intQtdeLinhasImp  = "";
		
		$intCodConta       = request('var_cod_conta');
		$intCodPlanoConta  = request('var_cod_plano_conta');
		$intCodCentroCusto = request('var_cod_centro_custo');
		$strOpcaoVcto 	   = request('var_opcao_vcto');
		$intQtdeDias       = request('var_dias');
		$strOpcaoBaixa     = request('var_opcao_baixa');
		
		if ($intCodConta == "") $intCodConta = "NULL";
		if ($intCodPlanoConta == "") $intCodPlanoConta = "NULL";
		if ($intCodCentroCusto == "") $intCodCentroCusto = "NULL";
		
		$imp = "";
		$fp = file($arquivo);
		
		// LAÇO DE VOLTAS NAS LINHAS DO ARQUIVO
		for($z=0; $z < count($fp); $z++){
			$cont = 0;
			$row  = str_split($fp[$z]);
			@$first_Number = $row[0].$row[1].$row[2];
			// Verifica se o primeiro número da linha é numérico
			if((!ctype_digit($first_Number)) and (trim ($fp[$z]) <> "")){
				mensagem("err_sql_titulo","err_arq_desc","Arquivo Inválido para Importação.","STArqUploadSICOBass.php","erro",1);
				die();
			} else{
				verificaLinha($row);
			}
			
			$linha = substr($fp[$z], 13, 1);
			$x	   = 1; 
			$y     = 1;
			
			while($y == 1){
				@$proxLinha = substr($fp[$z+$x], 13, 1);
				if(($linha == "T") && ($proxLinha == "U")){
					$linhaT   = $fp[$z];
					$linhaU   = $fp[$z+$x];
					$y        = 0;
					$intQtdeLinhasImp = importaArquivo($prObjConn, $linhaT, $linhaU, $intCodConta, $intCodPlanoConta, $intCodCentroCusto, $strOpcaoVcto, $intQtdeDias, $strOpcaoBaixa);
					$linhaT   = ""; 
					$linhaU   = "";
				} else{
					$y = 0;
				}
			}
		}
		
		// Caso o NÚMERO DE LINHAS IMPORTADAS
		// para a tabela de arquivo ITEM tenha
		// SIDO MAIOR DO QUE Zero, então chama
		// a PROCEDURE que irá fazer os INSERTS
		// de TÍTULOS e Lançamentos
		if($intQtdeLinhasImp <> 0){
			try{
				$strSQL = "
					SELECT 
						  out_qtde_pgto
						, out_vlr_tot_pago
						, out_vlr_tot_desc
						, out_vlr_tot_cred
						, out_qtde_tit_new
						, out_qtde_tit_old
						, out_qtde_pj_new
						, out_qtde_pj_old
					FROM sp_processa_retorno_cobr('".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
				$objResult = $prObjConn->query($strSQL);
				$objRS 	   = $objResult->fetch();
			}catch(PDOException $e){
				mensagem("err_sql_tit","err_sql_tit_desc",$e->getMessage(),"","erro",1);
				die();
			}
							
			// Return da PROCEDURE - MENSAGEM DE RESULTADO
			$strMsgFinal  = "<table cellpadding='0' cellspacing='0' width='100%' border='0'>";
			$strMsgFinal .= "<tr><td style='text-align:right;font-weight:bold;padding-right:15px;'>Geral</td><td></td></tr>";
			$strMsgFinal .= "<tr><td width='200' style='text-align:right;padding-right:15px;'>Lançamentos (Pagamentos Registrados):</td><td style='text-align:left;'>".getValue($objRS,"out_qtde_pgto")."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Pago:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_pago"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Descontos:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_desc"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Creditado:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_cred"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td colspan='2'>&nbsp;</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;font-weight:bold;padding-right:15px;'>Empresas</td><td></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>PJs Adicionadas:</td><td>".getValue($objRS,"out_qtde_pj_new")."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>PJs Encontradas:</td><td>".getValue($objRS,"out_qtde_pj_old")."</td></tr>";
			$strMsgFinal .= "<tr><td colspan='2'>&nbsp;</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;font-weight:bold;padding-right:15px;'>Títulos (Conta a Pagar / Receber)</td><td></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Títulos Gerados:</td><td>".getValue($objRS,"out_qtde_tit_new")." <span class='comment_peq'>(novos)</span></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Títulos Encontrados:</td><td>".getValue($objRS,"out_qtde_tit_old")." <span class='comment_peq'>(pré-registrados)</span></td></tr>";
			$strMsgFinal .= "</table>";
			$strMsgFinal .= "<br/><br/>";
			$strMsgFinal .= "<table cellpadding='0' cellspacing='0' width='100%' border='0'>";
			$strMsgFinal .= "<tr><td style='text-align:left;padding-right:15px;'>Você pode visualizar o resumo geral dos pagamentos realizados no DIA ATUAL, clicando <a href='#' onclick=\"AbreJanelaPAGE('../modulo_ASLWRelatorio/execaslw.php?var_chavereg=".$prCodRelat."','700','600');\" ><strong>NESTE LINK</strong></a>.</td></tr>";
			$strMsgFinal .= "</table>";
			
			$_POST['uploadArquivo'] = "";
			mensagem("info_importacao_realizada","info_importacao_realizada_desc",$strMsgFinal,"../modulo_PainelAdmin/STindex.php","info",1); 
		}
	}
	
	function importaArquivo($prObjConn, $rowT, $rowU, $prConta="", $prPlConta="", $prCentroCusto="", $prOpcaoVcto="", $prDias="", $prOpcaoBaixa=""){
		$strMsgErro  = ""; 
		$auxContador = 0;
		$strMsgErro  = $_SESSION['ValidaErro'];
		
		// Verifica se HÁ ERRO NA SESSÃO			
		if ($strMsgErro == ""){
			$intNumT  	= isNumber($rowT,  9, 13,"Número de SEQUÊNCIA T");
			$intNumU  	= isNumber($rowU,  9, 13,"Número de SEQUÊNCIA U");
			$intCodMovT = isNumber($rowT, 16, 17,"Cod. Movim. T");
			if(($intCodMovT == "02") || ($intCodMovT == "06") || ($intCodMovT == "09") || ($intCodMovT == "44")){
				$rowT       	= str_split($rowT); //Converte uma string em um array
				$intCodArquivo	= $_SESSION['ArqValida_numSeqArqHA'];
				
				// SQL RETORNA CODIGO DO ARQUIVO DE RETORNO
				$strSQL     	  = "SELECT cod_arq_retorno FROM arq_retorno_cobr WHERE id_arq = ".$intCodArquivo;
				$objResult  	  = $prObjConn->query($strSQL);
				$objRS       	  = $objResult->fetch();
				$intCodArqRetorno = getValue($objRS,"cod_arq_retorno");
				
				// DADOS NO SEGMENTO 'T'
				$intNumRegistro		= $intNumT;
				$intTipoCarteira 	= isNumber($rowT, 58, 58,"Tipo Carteira");
				$intCodOcorrencia 	= $intCodMovT;
				$intMotOcorrencia 	= preg_replace("/[^0-9]/", "", isNumber($rowT, 214, 223,"Motivo da Ocorrência"));
				$intCodBancoCobr 	= isNumber($rowT,  97, 99, "Cod. Banco Cobr.");
				$strCodAgenciaCobr 	= isNumber($rowT, 100,104, "Cod. Agencia Cobr.");
				$strNossoNumero		= isNumber($rowT, 47, 57-1, "Nosso Número");
				$strNumDocumento	= trim(isAlfa($rowT, 59, 69));
				$dtVctoBanco 		= isNumber($rowT, 74, 81, "Data de Vencimento do TÍTULO");
				$dtVctoBanco        = (($dtVctoBanco == "00000000") || ($dtVctoBanco == "11111111") || ($dtVctoBanco == "99999999")) ? "NULL" : convertDate($dtVctoBanco);
				$strTipoInscricao   = isNumber($rowT, 133, 133, "Tipo de INSCRIÇÃO");
				$strNumInscricao    = isNumber($rowT, 134, 148, "Número de INSCRIÇÃO");
				$dblVlrTitulo 		= isNumber($rowT, 82, 96, "Valor Nominal do Título");
				$dblVlrTitulo		= substr($dblVlrTitulo,-13,11).".".substr($dblVlrTitulo,-2);
				$dblTarifCustas		= isNumber($rowT, 199, 213, "Valor da Tarifa / Custas");//double
				$dblTarifCustas 	= substr($dblTarifCustas,-13,11).".".substr($dblTarifCustas,-2);
				
				// DADOS NO SEGMENTO 'U'
				$dtOcorrencia       = isNumber($rowU, 138, 145, "Data de Ocorrência");// date
				$dtOcorrencia    	= ($dtOcorrencia == "00000000") ? "NULL" : convertDate($dtOcorrencia);
				$dtCredito			= isNumber($rowU, 146, 153, "Data da Efetivação de Crédito");// date
				$dtCredito			= ($dtCredito == "00000000") ? "NULL" : convertDate($dtCredito);
				$dblAcrescimos		= isNumber($rowU, 18, 32, "Valor Acréscimos");
				$dblAcrescimos		= substr($dblAcrescimos,-13,11).".".substr($dblAcrescimos,-2);
				$dblDescontos		= isNumber($rowU, 33, 47, "Valor Descontos");
				$dblDescontos		= substr($dblDescontos,-13,11).".".substr($dblDescontos,-2);
				$dblAbatimento		= isNumber($rowU, 48, 62, "Valor Abatimento");
				$dblAbatimento		= substr($dblAbatimento,-13,11).".".substr($dblAbatimento,-2);
				$dblIOFRec			= isNumber($rowU, 63, 77, "Valor IOF Recolhido");
				$dblIOFRec			= substr($dblIOFRec,-13,11).".".substr($dblIOFRec,-2);
				$dblPago			= isNumber($rowU, 78, 92, "Valor Pago");
				$dblPago			= substr($dblPago,-13,11).".".substr($dblPago,-2);
				$dblLiquido			= isNumber($rowU, 93, 107, "Valor Líquido");
				$dblLiquido			= substr($dblLiquido,-13,11).".".substr($dblLiquido,-2);
				$dblOutrasDesp		= isNumber($rowU, 108, 122, "Valor de Outras Despesas");
				$dblOutrasDesp		= substr($dblOutrasDesp,-13,11).".".substr($dblOutrasDesp,-2);
				$dblOutrosCred		= isNumber($rowU, 123, 137, "Valor de Outros Créditos");
				$dblOutrosCred		= substr($dblOutrosCred,-13,11).".".substr($dblOutrosCred,-2);
				
				// Tratamento para BAIXA: DT OCORENCIA OU DT CREDITO
				if ($prOpcaoBaixa == "usar_credito") {
					if(($dtCredito != "") && ($dtCredito != "NULL")){
						$dtOcorrencia = $dtCredito;
					}
				}
				
				// Tratamento da DATA DE VCTO e OPCAO
				if(($dtVctoBanco == "") || ($dtVctoBanco == "NULL")){
					$dtVctoBanco = $dtOcorrencia;
				}
				if ($prOpcaoVcto == "dias_antes") { //SUBTRAI INTERVALO DE DATAS
					$dtVctoBanco = dateAdd("d",(-($prDias)),$dtOcorrencia);
				}
				if ($prOpcaoVcto == "datas_iguais") { //DATA DE VENCIMENTO IGUAL A DATA DE PAGAMENTO
					$dtVctoBanco = $dtOcorrencia;
				}
				
				// INSERT EM TABELA DE ARQUIVO-ITEM
				$strSQL = " INSERT INTO arq_retorno_cobr_item( 
											cod_arq_retorno
										  , num_registro
										  , nosso_numero
										  , num_documento
										  , tipo_inscricao
										  , num_inscricao
										  , tipo_carteira
										  , cod_ocorrencia
										  ,	motivo_ocorrencia
										  , cod_banco_cobr
										  , cod_agencia_cobr
										  , dt_vcto_banco
										  ,	dt_ocorrencia
										  ,	vlr_titulo
										  , vlr_tarifas
										  , vlr_acrescimo
										  , vlr_desconto
										  , vlr_abatimento
										  , vlr_iof_rec
										  , vlr_pago
										  , vlr_liquido
										  , vlr_outras_desp
										  , vlr_outros_cred
										  , param_cod_conta_banco
										  , param_cod_plano_conta
										  , param_cod_centro_custo
										  ,	sys_dtt_ins
										  , sys_usr_ins
							) VALUES(	     ".$intCodArqRetorno."
										  , '".$intNumRegistro."'
										  , '".$strNossoNumero."'
										  , '".$strNumDocumento."'
										  , '".$strTipoInscricao."'
										  , '".$strNumInscricao."'
										  , '".$intTipoCarteira."'
										  , '".$intCodOcorrencia."'
										  , '".$intMotOcorrencia."'	
										  , '".$intCodBancoCobr."'
										  ,	'".$strCodAgenciaCobr."'
										  ,	'".$dtVctoBanco."'
										  , '".$dtOcorrencia."'
										  , '".$dblVlrTitulo."'  
										  , '".$dblTarifCustas."'
										  , '".$dblAcrescimos."'
										  , '".$dblDescontos."'
										  , '".$dblAbatimento."'
										  , '".$dblIOFRec."'
										  , '".$dblPago."'
										  , '".$dblLiquido."'
										  , '".$dblOutrasDesp."'
										  , '".$dblOutrosCred."'
										  , ".$prConta."
										  , ".$prPlConta."
										  , ".$prCentroCusto."
										  , CURRENT_TIMESTAMP
										  , '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' )";
				$prObjConn->query($strSQL);
				
				// FAZ UPDATE NA TABELA DE ARQUIVO-PAI SETANDO O ARQUIVO COMO 'IMPORTADO'
				$strArqSituacao	= "imp";	
				$strArqNomeNew  = $_SESSION['novoNome'];
				
				$strSQL = "	UPDATE arq_retorno_cobr 
							SET nome = '".$strArqNomeNew."' 
							  , situacao = '".$strArqSituacao."'
							  , sys_dtt_upd = CURRENT_TIMESTAMP
							  , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
							WHERE cod_arq_retorno = ".$intCodArqRetorno;
				$prObjConn->query($strSQL);
				
				// Incrementa Contador de Linhas
				$auxContador++;
			}
		}else{
			mensagem("err_sql_titulo","err_arq_desc",$strMsgErro."Por favor faça a validação do arquivo para analisar os campos que estão com erros.","STArqUploadSICOBass.php","erro",1);
		}
		return $auxContador;			
	}
	
	// Fecha CONEXÃO com BANCO.
	$objConn = NULL;
?>
