<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/STathutils.php");
	include_once("STValidacaoToolsSITCS.php");
	
	// REQUESTS n INITS
	$_SESSION['ValidaErro'] = "";  		// Seta a Vari�vel de SESS�O de ERRO
	$arqName = $_POST['uploadArquivo'];	// Recebe arquivo 'Uploadeado'
	
	$objConn = abreDBConn(CFG_DB); // Abre conex�o com Banco
	
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
		$auxContador = 0;
		$strMsgFinal 	   = "";
		$intQtdeLinhasImp  = "";
		$prossegue = true;
		$intCodConta       = request('var_cod_conta');
		$intCodPlanoConta  = request('var_cod_plano_conta');
		$intCodCentroCusto = request('var_cod_centro_custo');
		$intCodJob		   = request('var_cod_job');
		$strOpcaoVcto 	   = request('var_opcao_vcto');
		$intQtdeDias       = request('var_dias');
		$strOpcaoBaixa     = request('var_opcao_baixa');
		
		if ($intCodConta == "") $intCodConta = "NULL";
		if ($intCodPlanoConta == "") $intCodPlanoConta = "NULL";
		if ($intCodCentroCusto == "") $intCodCentroCusto = "NULL";
		if ($intCodJob == "") $intCodJob = "NULL";
		
		$imp = "";
		//if (file_exists($arquivo)){echo "existe";}else{echo "n�o existe";}
		$fp = file($arquivo);
		//$arquivo;
		//count($fp);
		//echo file_get_contents($arquivo);

		// LA�O DE VOLTAS NAS LINHAS DO ARQUIVO
		for($z=0; $z < count($fp); $z++){
			$cont = 0;
			$row  = str_split($fp[$z]);
			@$first_Number = $row[0].$row[1].$row[2];
			// Verifica se o primeiro n�mero da linha � num�rico
			if((!ctype_digit($first_Number)) and (trim ($fp[$z]) <> "")){
				mensagem("err_sql_titulo","err_arq_desc","Arquivo Inv�lido para Importa��o.","STArqUploadSITCS.php","erro",1);
				die();
			} else{
				verificaLinha($row);
			}
			
			$linha = substr($fp[$z], 13, 1);
			$x	   = 1; 
			$y     = 1;
			
			while($y == 1){
				//echo"<br>entrei aqui / ";
				@$proxLinha = substr($fp[$z+$x], 13, 1);			
				//echo substr($fp[$z+$x], 13, 1);
				//echo "<br>linha: ".$linha  . "proxLinha = ".$proxLinha ;
				if(($linha == "T") && ($proxLinha == "U")){
					$linhaT   = $fp[$z];
					$linhaU   = $fp[$z+$x];
					$y        = 0;
					$intQtdeLinhasImp = importaArquivo($prObjConn, $linhaT, $linhaU, $intCodConta, $intCodPlanoConta, $intCodCentroCusto, $intCodJob, $strOpcaoVcto, $intQtdeDias, $strOpcaoBaixa);
					$linhaT   = ""; 
					$linhaU   = "";
					if ($intQtdeLinhasImp !=0){
						$prossegue = true;
					}else{$prossegue = false;}
				} else{
					$y = 0;
				}
			}
			//echo "<br>".$intQtdeLinhasImp;
		}
		//echo $prossegue;
		// Caso o N�MERO DE LINHAS IMPORTADAS
		// para a tabela de arquivo ITEM tenha
		// SIDO MAIOR DO QUE Zero, ent�o chama
		// a PROCEDURE que ir� fazer os INSERTS
		// de T�TULOS e Lan�amentos
		//echo "<strong>linas imp: ".$intQtdeLinhasImp."</strong><br>";
		if($prossegue){
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
			$strMsgFinal .= "<tr><td width='200' style='text-align:right;padding-right:15px;'>Lan�amentos (Pagamentos Registrados):</td><td style='text-align:left;'>".getValue($objRS,"out_qtde_pgto")."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Pago:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_pago"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Descontos:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_desc"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>Valor Total Creditado:</td><td>".number_format((double) getValue($objRS,"out_vlr_tot_cred"),2,',','.')."</td></tr>";
			$strMsgFinal .= "<tr><td colspan='2'>&nbsp;</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;font-weight:bold;padding-right:15px;'>Empresas</td><td></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>PJs Adicionadas:</td><td>".getValue($objRS,"out_qtde_pj_new")."</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>PJs Encontradas:</td><td>".getValue($objRS,"out_qtde_pj_old")."</td></tr>";
			$strMsgFinal .= "<tr><td colspan='2'>&nbsp;</td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;font-weight:bold;padding-right:15px;'>T�tulos (Conta a Pagar / Receber)</td><td></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>T�tulos Gerados:</td><td>".getValue($objRS,"out_qtde_tit_new")." <span class='comment_peq'>(novos)</span></td></tr>";
			$strMsgFinal .= "<tr><td style='text-align:right;padding-right:15px;'>T�tulos Encontrados:</td><td>".getValue($objRS,"out_qtde_tit_old")." <span class='comment_peq'>(pr�-registrados)</span></td></tr>";
			$strMsgFinal .= "</table>";
			$strMsgFinal .= "<br/><br/>";
			$strMsgFinal .= "<table cellpadding='0' cellspacing='0' width='100%' border='0'>";
			$strMsgFinal .= "<tr><td style='text-align:left;padding-right:15px;'>Voc� pode visualizar o resumo geral dos pagamentos realizados no DIA ATUAL, clicando <a href='#' onclick=\"AbreJanelaPAGE('../modulo_ASLWRelatorio/execaslw.php?var_chavereg=".$prCodRelat."','700','600');\" ><strong>NESTE LINK</strong></a>.</td></tr>";
			$strMsgFinal .= "</table>";
			
			$_POST['uploadArquivo'] = "";
			mensagem("info_importacao_realizada","info_importacao_realizada_desc",$strMsgFinal,"../modulo_PainelAdmin/STindex.php","info",1); 
		}else{echo "<br>deu merda! ".$intQtdeLinhasImp;}
	}
	
	function importaArquivo($prObjConn, $rowT, $rowU, $prConta="", $prPlConta="", $prCentroCusto="", $intCodJob="", $prOpcaoVcto="", $prDias="", $prOpcaoBaixa=""){
		$strMsgErro  = ""; 
		//$auxContador = 0;
		$strMsgErro  = $_SESSION['ValidaErro'];
		
		// Verifica se H� ERRO NA SESS�O			
		if ($strMsgErro == ""){
			$intNumT  	= isNumber($rowT,  9, 13,"N�mero de SEQU�NCIA T");
			$intNumU  	= isNumber($rowU,  9, 13,"N�mero de SEQU�NCIA U");
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
				$intTipoCarteira 	= isNumber($rowT, 59, 59,"Tipo Carteira");
				$intCodOcorrencia 	= $intCodMovT;
				$intMotOcorrencia 	= preg_replace("/[^0-9]/", "", isAlfa($rowT, 215, 224));
				if ($intMotOcorrencia == ""){$intMotOcorrencia = "05";}
				$intCodBancoCobr 	= isNumber($rowT,  98, 100,"Cod. Banco Cobr.");
				$strCodAgenciaCobr 	= isNumber($rowT, 101,105,"Cod. Agencia Cobr.");
				$strNossoNumero		= isAlfa($rowT, 41, 57);
				$strNumDocumento	= trim(isAlfa($rowT, 60, 70));
				$dtVctoBanco 		= isNumber($rowT, 75, 85, "Data de Vencimento do T�TULO");
				$dtVctoBanco        = (($dtVctoBanco == "00000000") || ($dtVctoBanco == "11111111") || ($dtVctoBanco == "99999999")) ? "NULL" : convertDate($dtVctoBanco);
				$strTipoInscricao   = isNumber($rowT, 134, 134, "Tipo de INSCRI��O");
				$strNumInscricao    = isNumber($rowT, 135, 149, "N�mero de INSCRI��O");
				$dblVlrTitulo 		= isNumber($rowT, 83, 97, "Valor Nominal do T�tulo");
				$dblVlrTitulo		= substr($dblVlrTitulo,-13,11).".".substr($dblVlrTitulo,-2);
				$dblTarifCustas		= isNumber($rowT, 200, 214, "Valor da Tarifa / Custas");//double
				$dblTarifCustas 	= substr($dblTarifCustas,-13,11).".".substr($dblTarifCustas,-2);
				
				// DADOS NO SEGMENTO 'U'
				$dtOcorrencia       = isNumber($rowU, 147, 154, "Data de Ocorrencia");// date
				$dtOcorrencia    	= ($dtOcorrencia == "00000000") ? "NULL" : convertDate($dtOcorrencia);
				$dtCredito			= isNumber($rowU, 155, 162, "Data da Efetiva��o de Cr�dito");// date
				$dtCredito			= ($dtCredito == "00000000") ? "NULL" : convertDate($dtCredito);
				$dblAcrescimos		= isNumber($rowU, 117, 131, "Valor Acr�scimos");
				$dblAcrescimos		= substr($dblAcrescimos,-13,11).".".substr($dblAcrescimos,-2);
				//$dblDescontos		= isNumber($rowU, 33, 47, "Valor Descontos");
				//$dblDescontos		= substr($dblDescontos,-13,11).".".substr($dblDescontos,-2);
				//$dblAbatimento		= isNumber($rowU, 48, 62, "Valor Abatimento");
				//$dblAbatimento		= substr($dblAbatimento,-13,11).".".substr($dblAbatimento,-2);
				//$dblIOFRec			= isNumber($rowU, 63, 77, "Valor IOF Recolhido");
				//$dblIOFRec			= substr($dblIOFRec,-13,11).".".substr($dblIOFRec,-2);
				$dblPago			= isNumber($rowU, 87, 101, "Valor Pago");
				$dblPago			= substr($dblPago,-13,11).".".substr($dblPago,-2);
				$dblLiquido			= isNumber($rowU, 102, 116, "Valor L�quido");
				$dblLiquido			= substr($dblLiquido,-13,11).".".substr($dblLiquido,-2);
				//$dblOutrasDesp		= isNumber($rowU, 108, 122, "Valor de Outras Despesas");
				//$dblOutrasDesp		= substr($dblOutrasDesp,-13,11).".".substr($dblOutrasDesp,-2);
				$dblOutrosCred		= isNumber($rowU, 132, 146, "Valor de Outros Cr�ditos");
				$dblOutrosCred		= substr($dblOutrosCred,-13,11).".".substr($dblOutrosCred,-2);
				
				//esses 4 dados nao fazem mais parte da nova versao do cnab da caixa
				$dblDescontos		= "0.00";
				$dblAbatimento		= "0.00";
				$dblIOFRec			= "0.00";
				$dblOutrasDesp		= "0.00";
				
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
			try{	
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
										  , param_cod_job
										  ,	sys_dtt_ins
										  , sys_usr_ins
							) VALUES(	    ".$intCodArqRetorno."
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
										  , ".$intCodJob."
										  , CURRENT_TIMESTAMP
										  , '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' )";
				$prObjConn->query($strSQL);
			}catch(PDOException $e){
				mensagem("err_sql_tit","err_sql_tit_desc",$e->getMessage(),"","erro",1);
				die();
			}
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
				
				//echo $strSQL ."<br>";
				$auxContador = $auxContador+1;
				//echo "<br>cont: ".$auxContador;
			}
		}else{
			mensagem("err_sql_titulo","err_arq_desc",$strMsgErro."Por favor fa�a a valida��o do arquivo para analisar os campos que est�o com erros.","STArqUploadSITCS.php","erro",1);
		}
		return $auxContador;			
	}
	
	// Fecha CONEX�O com BANCO.
	$objConn = NULL;
?>
