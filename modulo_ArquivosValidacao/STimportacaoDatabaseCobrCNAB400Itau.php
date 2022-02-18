<?php
	// INCLUDES
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);


	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/STathutils.php");
	include_once("STValidacaoToolsCobrCNAB400.php");
	
	// REQUESTS n INITS
	$_SESSION['ValidaErro'];
	$_SESSION['ValidaErro'] = "";  		// Seta a Variável de SESSÃO de ERRO
	$arqName = $_POST['uploadArquivo'];	// Recebe arquivo 'Uploadeado'
	
	$objConn = abreDBConn(CFG_DB); // Abre conexão com Banco
	
	$intCodRelat = getVarEntidade($objConn,"cod_relat_aslw_apos_importacao");
	if ($intCodRelat == "") $intCodRelat = "104";
	
	// PROCESSAMENTO PHP
	$arquivo = alteraNomeArq($arqName,"I");
	$linhaT  = ""; 
	$linhaU  = "";
	analisaArquivo($objConn, $arquivo, $intCodRelat);

	function convertDate($date){
		$data = "20".substr($date,4,2)."-".substr($date,2,2)."-".substr($date,0,2);
		return $data;
	}
	
	function analisaArquivo($prObjConn, $arquivo, $prCodRelat){
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
		 //"arquivo: ".$arquivo;
		$fp = file($arquivo);
		// "<br>count pointer: ".count($fp);
	
		// LAÇO DE VOLTAS NAS LINHAS DO ARQUIVO
		for($z=0; $z < count($fp); $z++){
			$cont = 0;
			$row  = str_split($fp[$z]); 
			@$first_Number = $row[0].$row[1];
			
			// Verifica se o primeiro número da linha é numérico
			if((!ctype_digit($first_Number)) and ($fp[$z] <> "")){
				mensagem("err_sql_titulo","err_arq_desc","Arquivo Inválido para Importação.","STArqUploadCobrCNAB400Itau.php","erro",1);
				die();
			} else{
				verificaLinha($row);
			}
			
			@$first_digit = $row[0];
			if ($first_digit == "1") {
				$intTipoMovimento = isNumber($row,109,110,"Cod. Movim.");
				if(($intTipoMovimento == "06") || ($intTipoMovimento == "07")  || ($intTipoMovimento == "08")/*|| ($intTipoMovimento == "15") || ($intTipoMovimento == "16") || ($intTipoMovimento == "17")*/ ){
					$intQtdeLinhasImp = importaArquivo($prObjConn, $row, $intCodConta, $intCodPlanoConta, $intCodCentroCusto, $strOpcaoVcto, $intQtdeDias, $strOpcaoBaixa);
				}

			}
			
		}
		
		// Caso o NÚMERO DE LINHAS IMPORTADAS
		// para a tabela de arquivo ITEM tenha
		// SIDO MAIOR DO QUE Zero, então chama
		// a PROCEDURE que irá fazer os INSERTS
		// de TÍTULOS e Lançamentos
		//echo "<br><b>linhas imp: </b>". $intQtdeLinhasImp;
		//die();
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
	
	function importaArquivo($prObjConn, $row, $prConta="", $prPlConta="", $prCentroCusto="", $prOpcaoVcto="", $prDias="", $prOpcaoBaixa=""){
		$strMsgErro  = ""; 
		//$auxContador = 0;
		//echo "erro: ". $strMsgErro  = $_SESSION['ValidaErro'];
		//echo("<br><br>".$row);
		// Verifica se HÁ ERRO NA SESSÃO
		$strCNPJCedente = getVarEntidade($prObjConn,"remessa_itau_cnpj");
		
		if ($strCNPJCedente == isNumber($row,4,17,"CNPJ CEDENTE")){
					if ($strMsgErro == ""){
						$intNum	= isNumber($row,395,400,"Número de SEQUÊNCIA Transação Tipo 1");
						$intCodMov = isNumber($row,109,110,"Cod. Movim.");

						if(($intCodMov == "06") || ($intCodMov == "09") || ($intCodMov == "07") || ($intCodMov == "08") ){
							//$row	      	= str_split("",$row); //Converte uma string em um array
							$intIDArquivo	= $_SESSION['ArqValida_DataGravacaoArqHA'];
							if ($intIDArquivo != "") {
								// SQL RETORNA CODIGO DO ARQUIVO DE RETORNO
								$strSQL     	  = "SELECT cod_arq_retorno FROM arq_retorno_cobr WHERE id_arq = ".$intIDArquivo;
								$objResult  	  = $prObjConn->query($strSQL);
								$objRS       	  = $objResult->fetch();
								$intCodArqRetorno = getValue($objRS,"cod_arq_retorno");

								// DADOS NA TRANSAÇÃO TIPO '1'
								$intNumRegistro		= $intNum;
								//$intTipoCarteira 	= isAlfa($row, 108, 108);
								$intTipoCarteira = "null";
								$intCodOcorrencia 	= $intCodMov;
								$intMotOcorrencia 	= preg_replace("/[^0-9]/", "", isAlfa($row, 393, 394));
								$intMotOcorrencia 	            = ($intMotOcorrencia == "") ? "NULL" : $intMotOcorrencia;
								$intCodBancoCobr 	= isNumber($row,166,168,"Cod. Banco Cobr.");
								$strCodAgenciaCobr 	= isNumber($row,169,173,"Cod. Agencia Cobr.");
								
								//Posição 146 tem o Dígito de Controle, não temos o DC no banco, logo não gravamos ele
								$strNossoNumero		= isAlfa($row,63,70);
																		$strNumDocumento	= trim(isAlfa($row,38,62));
								$dtVctoBanco 		= isNumber($row,147,152, "Data de Vencimento do TÍTULO");
								$dtVctoBanco        = (($dtVctoBanco == "000000") || ($dtVctoBanco == "111111") || ($dtVctoBanco == "999999")) ? "NULL" : convertDate($dtVctoBanco);
								$strTipoInscricao   = "NULL"; //Posições 2 a 3 é tipo de inscrição do sindicato e não do cedente
								$strNumInscricao    = "NULL"; //Posições 4 a 17 é número de inscrição do sindicato e não do cedente
								$dblVlrTitulo 		= isNumber($row,153,165, "Valor Nominal do Título");
								$dblVlrTitulo		= substr($dblVlrTitulo,-13,11).".".substr($dblVlrTitulo,-2);
								$dblTarifCustas		= isNumber($row,176,188, "Valor da Tarifa / Custas");
								$dblTarifCustas 	= substr($dblTarifCustas,-13,11).".".substr($dblTarifCustas,-2); //"0000000000000.00"; //
								$dtOcorrencia       = isNumber($row,111,116, "Data de Ocorrência");
								$dtOcorrencia    	= ($dtOcorrencia == "000000") ? "NULL" : convertDate($dtOcorrencia);
								$dtCredito			= isNumber($row, 296, 301, "Data da Efetivação de Crédito");// date
								$dtCredito			= ($dtCredito == "000000") ? "NULL" : convertDate($dtCredito);
								$dblAcrescimos		= isNumber($row, 267, 279, "Valor Acréscimos");
								$dblAcrescimos		= substr($dblAcrescimos,-13,11).".".substr($dblAcrescimos,-2);
								$dblDescontos		= isNumber($row, 241, 253, "Valor Descontos");
								$dblDescontos		= substr($dblDescontos,-13,11).".".substr($dblDescontos,-2);
								$dblAbatimento		= isNumber($row, 228, 240, "Valor Abatimento");
								$dblAbatimento		= substr($dblAbatimento,-13,11).".".substr($dblAbatimento,-2);
								$dblIOFRec			= isNumber($row, 215, 227, "Valor IOF Recolhido");
								$dblIOFRec			= substr($dblIOFRec,-13,11).".".substr($dblIOFRec,-2);
								$dblPago			= isNumber($row, 254, 266, "Valor Pago");
								$dblPago			= substr($dblPago,-13,11).".".substr($dblPago,-2);
								//$dblLiquido			= isNumber($row, 93, 107, "Valor Líquido");
								//$dblLiquido			= "0000000000000.00"; //substr($dblLiquido,-13,11).".".substr($dblLiquido,-2);
								$dblLiquido			= $dblVlrTitulo;
								$dblOutrasDesp		= ""; //isNumber($row, 189, 201, "Valor de Outras Despesas");
								$dblOutrasDesp		= "0000000000000.00";//substr($dblOutrasDesp,-13,11).".".substr($dblOutrasDesp,-2);
								$dblOutrosCred		= ""; //isNumber($row, 280, 292, "Valor de Outros Créditos");
								$dblOutrosCred		= "0000000000000.00"; //substr($dblOutrosCred,-13,11).".".substr($dblOutrosCred,-2);
								
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
									$strSQL 	= "INSERT INTO arq_retorno_cobr_item( 
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
														, ".$strTipoInscricao."
														, ".$strNumInscricao."
														, ".$intTipoCarteira."
														, '".$intCodOcorrencia."'
														, ".$intMotOcorrencia."
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
								
						$strSQL = " UPDATE arq_retorno_cobr 
											SET nome = '".$strArqNomeNew."' 
											, situacao = '".$strArqSituacao."'
											, sys_dtt_upd = CURRENT_TIMESTAMP
											, sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
											WHERE cod_arq_retorno = ".$intCodArqRetorno;
								$prObjConn->query($strSQL);
								
								// Incrementa Contador de Linhas
							//echo"<B> - CONTATOR </B>".	
							$auxContador = $auxContador+1;
							}
						}
					}else{
						mensagem("err_sql_titulo","err_arq_desc",$strMsgErro."<br><br>Por favor faça a validação do arquivo para analisar os campos que estão com erros e/ou verifique se esta no ambiente correto.","STArqUploadCobrCNAB400Itau.php","erro",1);
						die();
					}
					//echo "<br> CONTADOR: ". $auxContador;
				}
		return $auxContador;			
	}
	
	// Fecha CONEXÃO com BANCO.
	$objConn = NULL;
?>
