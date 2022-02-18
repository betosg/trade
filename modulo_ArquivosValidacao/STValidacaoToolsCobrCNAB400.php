<?php
// altera nome do arquivo validado ou importado
function alteraNomeArq($arqName,$tipo){
		$path       		  = "../../".getSession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/arqbanco/cobrcnab400/";
		$nomeAntigo 		  = $path.$arqName;
		$data 				  = date('d').date('m')."20".date('y').date('h').date('i').date('s');
	//	$nomeNovo 			  = $tipo.$data."_".$arqName;
		$nomeNovo 			  = $tipo."_".$arqName;
		$_SESSION['novoNome'] = $nomeNovo;
		rename ($nomeAntigo, $path.$nomeNovo);
		$arquivo 			  = $path.$nomeNovo;
		return $arquivo;
}


//imprime a linha completa
function imprimirLinha($linha){
	$linhaCompl = "";
	$p          = 1;
	for($z=0; $z< count($linha); $z++){
			$linhaCompl.= $linha[$z];
			$p++;
			if($p>75){
				$linhaCompl.="\n";
				$p = 1;
			}
	}
	return $linhaCompl;
}

//imprime valores alfanumerico
function isAlfa($prString, $prPosIni, $prPosFim, $prValorEsperado=""){
	$strResultado  = "";
	$strMSGErro    = "";
	$strLinhasErro = "";
	$intPosIni     = $prPosIni - 1; 
	$intPosFim     = $prPosFim - 1;
	$strString	   = $prString;
	$intTamanho    = sizeof($prString);
	
	//Se tentar acessar posição fora da linha deve dar erro
	//if ($intPosFim > $intTamanho) {
	//	$strMSGErro = "<span style='color:#F00;'>Erro! Tentativa de acessar posição inexistente na linha. Posição: [ <b>".$intPosFim."</b> ], tamanho da linha: [ <b>".$intTamanho."</b> ]</span>";
	//}
	
	if($strMSGErro == ""){
		// Concatena cada caracter da STRING do intervalo de posições encaminhado.
		// Ainda não valida nenhum caracter especial tipo ( @ # $ + - { } ) etc.
		for($auxContador = $intPosIni; ($auxContador <= $intPosFim) && ($auxContador < $intTamanho); $auxContador++){
			$strResultado .= $strString[$auxContador];
		}
		
		// Caso o valor concatenado seja diferente do esperado
		if($prValorEsperado != ""){
			if($strResultado != $prValorEsperado){
				$strMSGErro = "<span style='color:#F00;'>Erro! Valor esperado: [ <b>".$prValorEsperado."</b> ], valor encontrado: [ <b>".$strResultado."</b> ]</span>";
			}
		}
	}
	
	// Verifica se Erro foi encontrado
	if($strMSGErro != ""){
		$_SESSION['ValidaErro'] = "Erro no Arquivo"; //$strMSGErro." / function isAlfa ini ".$prPosIni." fim ".$prPosFim." string ".$prString;
		return($strMSGErro);
	} else{
		return($strResultado);
	}
}

//função para verifica se é numero no array
function isNumber($prString, $prPosIni, $prPosFim, $campo, $prValorEsperado=""){
	$strResultado  = "";
	$strMSGErro    = "";
	$strLinhasErro = "";
	$strDado       = "";
	$intPosIni     = $prPosIni - 1; 
	$intPosFim     = $prPosFim - 1;
	$strString	   = $prString;
	$intTamanho    = sizeof($prString);
	
	//Se tentar acessar posição fora da linha deve dar erro
	//if ($intPosFim > $intTamanho) {
	//	$strMSGErro = "<span style='color:#F00;'>Erro! Tentativa de acessar posição inexistente na linha. Posição: [ <b>".$intPosFim."</b> ], tamanho da linha: [ <b>".$intTamanho."</b> ], linha: [".$strString."]</span>";
	//}
	
	if($strMSGErro == ""){
		// Faz um laço para cada caracter da STRING ENCAMINHADA para verificar se a posição 
		// é UM NÚMERO REALMENTE	
		for($auxContador = $intPosIni; ($auxContador <= $intPosFim) && ($auxContador < $intTamanho); $auxContador++){
			$strDado = $strString[$auxContador];
			if(!ctype_digit($strDado)){ //verifica se os caracteres são numericos
				$strLinhasErro .= ($auxContador + 1).",";
				$strMSGErro     = "<span style='color:#F00;'>Erro! Valor esperado: numérico, valor encontrado: [ <b>".$auxContador[$auxContador]."</b> ]"." na(s) posição(ões) [ <b>".$strLinhasErro."</b> ]</span>";
			}else{
				$strResultado  .= $strDado;
			}	
		}
		
		// Caso o valor concatenado seja diferente do esperado
		if($prValorEsperado != ""){
			if($strResultado != $prValorEsperado){
				$strMSGErro   = "<span style='color:#F00;'>Erro! Valor esperado: [ <b>".$prValorEsperado."</b> ], valor encontrado: [ <b>".$strResultado."</b> ]</span>";
			}
		}
	}
	
	// Verifica se Erro foi encontrado
	if($strMSGErro != ""){
		$_SESSION['ValidaErro'] = "Erro no Arquivo"; //$strMSGErro." / function isNumber ini ".$prPosIni." fim ".$prPosFim." string ".$prString;
		return($strMSGErro);
	} else{
		return($strResultado);
	}
}

// Verifica tipo de linha
function verificaLinha($row){
	switch(@$row[0]){ 
		case "0": 
			headerArquivo($row); 
			break; 
		case "1":
				transacoes($row);
				break;
		case "3":
			transacoes($row);
			break; 
		case "9":
			traileArquivo($row);
			break; 
		default: 
	}	
}

// seleciona os segmentos
function transacoes($trans){
	switch($trans[0]){ 
		case "1": 
			transacaoTipo1($trans);
			break; 
		case "3":
			transacaoTipo3($trans);
			break;
		default: 
			echo "Não foi encontrado Transação Obrigatória ou Transação Opcional"; 
	}	
}


/*
function LimpaVarSessions() {
	$_SESSION['ValidaErro'] = "";
	$_SESSION['ValidaArquivo_Arquivo'] = "";
	
	$_SESSION['ArqValida_LinhaHeaderHA']				= "";
	$_SESSION['ArqValida_IdentificacaoRegistroHA'] 		= "";
	$_SESSION['ArqValida_IdentificacaoArqRetornoHA']	= "";
	$_SESSION['ArqValida_LiteralRetornoHA'] 			= "";
	$_SESSION['ArqValida_CodigoServicoHA'] 				= "";
	$_SESSION['ArqValida_LiteralServicoHA'] 			= "";
	$_SESSION['ArqValida_CodigoEmpresaHA'] 				= "";
	$_SESSION['ArqValida_NomeEmpresaHA'] 				= "";
	$_SESSION['ArqValida_NumBancoHA'] 					= "";
	$_SESSION['ArqValida_NomeBancoHA'] 					= "";
	$_SESSION['ArqValida_DataGravacaoArqHA'] 			= "";
	$_SESSION['ArqValida_DensidadeGravacaoHA']			= "";
	$_SESSION['ArqValida_NumAvisoBancarioHA']			= "";
	$_SESSION['ArqValida_BRANCOS1HA'] 					= "";
	$_SESSION['ArqValida_DataCreditoHA']				= "";
	$_SESSION['ArqValida_BRANCOS2HA'] 					= "";
	$_SESSION['ArqValida_NumSeqRegistroHA'] 			= "";
	
	if ($_SESSION['ArqValida_key'] != "") {
		$total = $_SESSION['ArqValida_key'];
		
		for($key=1; $key<=$total; $key++){
			$_SESSION['ArqValida_LinhaTransT1'.$key] 						= "";
			$_SESSION['ArqValida_IdentificacaoRegistroT1'.$key]				= "";
			$_SESSION['ArqValida_TipoInscrEmpresaT1'.$key] 					= "";
			$_SESSION['ArqValida_NumInscrEmpresaT1'.$key] 					= "";
			$_SESSION['ArqValida_ZEROS1T1'.$key] 							= "";
			$_SESSION['ArqValida_IdentificacaoEmpresaCedenteT1'.$key] 		= "";
			$_SESSION['ArqValida_NumControleParticipanteT1'.$key] 			= "";
			$_SESSION['ArqValida_ZEROS2T1'.$key] 							= "";
			$_SESSION['ArqValida_IdentificacaoTituloT1'.$key] 				= "";
			$_SESSION['ArqValida_UsoBanco1T1'.$key] 						= "";
			$_SESSION['ArqValida_UsoBanco2T1'.$key] 						= "";
			$_SESSION['ArqValida_IndicadorRateioCreditoT1'.$key] 			= "";
			$_SESSION['ArqValida_ZEROS3T1'.$key] 							= "";
			$_SESSION['ArqValida_CarteiraT1'.$key] 							= "";
			$_SESSION['ArqValida_IdentificacaoOcorrenciaT1'.$key] 			= "";
			$_SESSION['ArqValida_DataOcorrenciaT1'.$key] 					= "";
			$_SESSION['ArqValida_NumDocumentoT1'.$key] 						= "";
			$_SESSION['ArqValida_IdentificacaoTituloT1'.$key] 				= "";
			$_SESSION['ArqValida_DataVctoTituloT1'.$key] 					= "";
			$_SESSION['ArqValida_ValorTituloT1'.$key] 						= "";
			$_SESSION['ArqValida_BancoT1'.$key] 							= "";
			$_SESSION['ArqValida_AgenciaT1'.$key] 							= "";
			$_SESSION['ArqValida_EspecieTituloT1'.$key] 					= "";
			$_SESSION['ArqValida_DespesasCobrCodigosOcorr02e28T1'.$key]		= "";
			$_SESSION['ArqValida_OutrasDespesasCustasProtestoT1'.$key] 		= "";
			$_SESSION['ArqValida_JurosOperacaoAtrasoT1'.$key] 				= "";
			$_SESSION['ArqValida_IOFDevidoT1'.$key] 						= "";
			$_SESSION['ArqValida_AbatimentoConcedidoT1'.$key] 				= "";
			$_SESSION['ArqValida_DescontoConcedidoT1'.$key] 				= "";
			$_SESSION['ArqValida_ValorPagoT1'.$key] 						= "";
			$_SESSION['ArqValida_JurosMoraT1'.$key] 						= "";
			$_SESSION['ArqValida_OutrosCreditosT1'.$key] 					= "";
			$_SESSION['ArqValida_BRANCOS1T1'.$key] 							= "";
			$_SESSION['ArqValida_MotivoCodigoOcorrencia25T1'.$key] 			= "";
			$_SESSION['ArqValida_DataCreditoT1'.$key] 						= "";
			$_SESSION['ArqValida_OrigemPagamentoT1'.$key] 					= "";
			$_SESSION['ArqValida_BRANCOS2T1'.$key] 							= "";
			$_SESSION['ArqValida_CodigoBancoChequeBradescoT1'.$key] 		= "";
			$_SESSION['ArqValida_MotivosRejeicoesCodOcorrPos109a110T1'.$key]= "";
			$_SESSION['ArqValida_BRANCOS3T1'.$key] 							= "";
			$_SESSION['ArqValida_NumCartorioT1'.$key] 						= "";
			$_SESSION['ArqValida_NumeroProtocoloT1'.$key] 					= "";
			$_SESSION['ArqValida_BRANCOS4T1'.$key] 							= "";
			$_SESSION['ArqValida_NumSeqRegistroT1'.$key] 					= "";
			
			$_SESSION['ArqValida_LinhaTransT3'.$key] 			= "";
			$_SESSION['ArqValida_IdentificacaoRegistroT3'.$key] = "";
			$_SESSION['ArqValida_TipoInscrEmpresaT3'.$key] 		= "";
			$_SESSION['ArqValida_NumInscrEmpresaT3'.$key]	 	= "";
		}
	}
	
	$_SESSION['ArqValida_LinhaTrailerTA'] 				= "";
	$_SESSION['ArqValida_IdentificacaoRegistroTA'] 		= "";
	$_SESSION['ArqValida_IdentificacaoRetornoTA']		= "";
	$_SESSION['ArqValida_IdentificacaoTipoRegistroTA']	= "";
	$_SESSION['ArqValida_NumBancoTA'] 					= "";
	$_SESSION['ArqValida_BRANCOS1TA'] 					= "";
	$_SESSION['ArqValida_QtdeTitulosCobrancaTA'] 		= "";
	$_SESSION['ArqValida_VlrTotalCobrancaTA']			= "";
	$_SESSION['ArqValida_NumAvisoBancarioTA']			= "";
	$_SESSION['ArqValida_BRANCOS2TA'] 					= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia02TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia02TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia06aTA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia06TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia06bTA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia09e10TA'] 	= "";
	$_SESSION['ArqValida_VlrRegOcorrencia09e10TA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia13TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia13TA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia14TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia14TA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia12TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia12TA'] 		= "";
	$_SESSION['ArqValida_QtdeRegOcorrencia19TA'] 		= "";
	$_SESSION['ArqValida_VlrRegOcorrencia19TA'] 		= "";
	$_SESSION['ArqValida_BRANCOS3TA'] 					= "";
	$_SESSION['ArqValida_VlrTotalRateiosTA'] 			= "";
	$_SESSION['ArqValida_QtdeTotalRateiosTA'] 			= "";
	$_SESSION['ArqValida_BRANCOS4TA'] 					= "";
	$_SESSION['ArqValida_NumSeqRegistroTA'] 			= "";
}
*/

// Header de ARQUIVO
function headerArquivo($linha){
	$_SESSION['ArqValida_LinhaHeaderHA']				= imprimirLinha($linha);
	$_SESSION['ArqValida_IdentificacaoRegistroHA'] 		= isNumber($linha,1   ,1   ,"IdentificacaoRegistro","0");
	$_SESSION['ArqValida_IdentificacaoArqRetornoHA']	= isNumber($linha,2   ,2   ,"IdentificacaoArqRetorno","2");
	$_SESSION['ArqValida_LiteralRetornoHA'] 			=   isAlfa($linha,3   ,9); //"RETORNO"
	$_SESSION['ArqValida_CodigoServicoHA'] 				= isNumber($linha,10  ,11   ,"CodigoServico","01");
	$_SESSION['ArqValida_LiteralServicoHA'] 			=   isAlfa($linha,12  ,26); //"COBRANCA"
	$_SESSION['ArqValida_CodigoEmpresaHA'] 				= isNumber($linha,27  ,46   ,"CodigoEmpresa","");
	$_SESSION['ArqValida_NomeEmpresaHA'] 				=   isAlfa($linha,47  ,76);
	$_SESSION['ArqValida_NumBancoHA'] 					= isNumber($linha,77  ,79   ,"NumBanco","");
	$_SESSION['ArqValida_NomeBancoHA'] 					=   isAlfa($linha,80  ,94);
	$_SESSION['ArqValida_DataGravacaoArqHA'] 			= isNumber($linha,95  ,100  ,"DataGravacaoArq","");
	$_SESSION['ArqValida_DensidadeGravacaoHA']			= isNumber($linha,101 ,108  ,"DensidadeGravacao","01600000");
	$_SESSION['ArqValida_NumAvisoBancarioHA']			= isNumber($linha,109 ,113  ,"NumAvisoBancario","");
	$_SESSION['ArqValida_BRANCOS1HA'] 					=   isAlfa($linha,114 ,379);
	$_SESSION['ArqValida_DataCreditoHA']				= isNumber($linha,380 ,385  ,"DataCredito","");
	$_SESSION['ArqValida_BRANCOS2HA'] 					=   isAlfa($linha,386 ,394);
	$_SESSION['ArqValida_NumSeqRegistroHA'] 			= isNumber($linha,395 ,400  ,"NumSeqRegistro","000001");
}

// Trailer de ARQUIVO
function traileArquivo($linha){
	$_SESSION['ArqValida_LinhaTrailerTA'] 				= imprimirLinha($linha);
	$_SESSION['ArqValida_IdentificacaoRegistroTA'] 		= isNumber($linha,1   ,1   ,"IdentificacaoRegistro","9");
	$_SESSION['ArqValida_IdentificacaoRetornoTA']		= isNumber($linha,2   ,2   ,"IdentificacaoRetorno","2");
	$_SESSION['ArqValida_IdentificacaoTipoRegistroTA']	= isNumber($linha,3   ,4   ,"IdentificacaoTipoRegistro","01");
	$_SESSION['ArqValida_NumBancoTA'] 					= isNumber($linha,5   ,7   ,"NumBanco","");
	$_SESSION['ArqValida_BRANCOS1TA'] 					=   isAlfa($linha,8   ,17);
	$_SESSION['ArqValida_QtdeTitulosCobrancaTA'] 		= isNumber($linha,18  ,25  ,"QtdeTitulosCobranca","");
	$_SESSION['ArqValida_VlrTotalCobrancaTA']			= isNumber($linha,26  ,39  ,"VlrTotalCobranca","");
	$_SESSION['ArqValida_NumAvisoBancarioTA']			= isNumber($linha,40  ,47  ,"NumAvisoBancario","");
	$_SESSION['ArqValida_BRANCOS2TA'] 					=   isAlfa($linha,48  ,57);
	$_SESSION['ArqValida_QtdeRegOcorrencia02TA'] 		= isNumber($linha,58  ,62  ,"QtdeRegOcorrencia02","");
	$_SESSION['ArqValida_VlrRegOcorrencia02TA'] 		= isNumber($linha,63  ,74  ,"VlrRegOcorrencia02","");
	$_SESSION['ArqValida_VlrRegOcorrencia06aTA'] 		= isNumber($linha,75  ,86  ,"VlrRegOcorrencia06a","");
	$_SESSION['ArqValida_QtdeRegOcorrencia06TA'] 		= isNumber($linha,87  ,91  ,"QtdeRegOcorrencia06","");
	$_SESSION['ArqValida_VlrRegOcorrencia06bTA'] 		= isNumber($linha,92  ,103 ,"VlrRegOcorrencia06b","");
	$_SESSION['ArqValida_QtdeRegOcorrencia09e10TA'] 	= isNumber($linha,104 ,108 ,"QtdeRegOcorrencia09e10","");
	$_SESSION['ArqValida_VlrRegOcorrencia09e10TA'] 		= isNumber($linha,109 ,120 ,"VlrRegOcorrencia09e10","");
	$_SESSION['ArqValida_QtdeRegOcorrencia13TA'] 		= isNumber($linha,121 ,125 ,"QtdeRegOcorrencia13","");
	$_SESSION['ArqValida_VlrRegOcorrencia13TA'] 		= isNumber($linha,126 ,137 ,"VlrRegOcorrencia13","");
	$_SESSION['ArqValida_QtdeRegOcorrencia14TA'] 		= isNumber($linha,138 ,142 ,"QtdeRegOcorrencia14","");
	$_SESSION['ArqValida_VlrRegOcorrencia14TA'] 		= isNumber($linha,143 ,154 ,"VlrRegOcorrencia14","");
	$_SESSION['ArqValida_QtdeRegOcorrencia12TA'] 		= isNumber($linha,155 ,159 ,"QtdeRegOcorrencia12","");
	$_SESSION['ArqValida_VlrRegOcorrencia12TA'] 		= isNumber($linha,160 ,171 ,"VlrRegOcorrencia12","");
	$_SESSION['ArqValida_QtdeRegOcorrencia19TA'] 		= isNumber($linha,172 ,176 ,"QtdeRegOcorrencia19","");
	$_SESSION['ArqValida_VlrRegOcorrencia19TA'] 		= isNumber($linha,177 ,188 ,"VlrRegOcorrencia19","");
	$_SESSION['ArqValida_BRANCOS3TA'] 					=   isAlfa($linha,189 ,362);
	$_SESSION['ArqValida_VlrTotalRateiosTA'] 			= isNumber($linha,363 ,377 ,"VlrTotalRateios","");
	$_SESSION['ArqValida_QtdeTotalRateiosTA'] 			= isNumber($linha,378 ,385 ,"QtdeTotalRateios","");
	$_SESSION['ArqValida_BRANCOS4TA'] 					=   isAlfa($linha,386 ,394);
	$_SESSION['ArqValida_NumSeqRegistroTA'] 			= isNumber($linha,395 ,400 ,"NumSeqRegistro","");
}

// Transação Tipo 1
function transacaoTipo1($linha){
	$intCodMov = isNumber($linha,109,110,"IdentificacaoOcorrencia","");
	
	if(($intCodMov == "06") || ($intCodMov == "09") || ($intCodMov == "15") || ($intCodMov == "16") || ($intCodMov == "17")){
		@$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key'] + 1;
		$key = $_SESSION['ArqValida_key'];
		
		$_SESSION['ArqValida_LinhaTransT1'.$key] 						= imprimirLinha($linha);
		$_SESSION['ArqValida_IdentificacaoRegistroT1'.$key]				= isNumber($linha,1  ,1  ,"IdentificacaoRegistro","1");
		$_SESSION['ArqValida_TipoInscrEmpresaT1'.$key] 					= isNumber($linha,2  ,3  ,"TipoInscrEmpresa","");
		$_SESSION['ArqValida_NumInscrEmpresaT1'.$key] 					= isNumber($linha,4  ,17 ,"NumInscrEmpresa","");
		$_SESSION['ArqValida_ZEROS1T1'.$key] 							= isAlfa($linha,18 ,20);
		$_SESSION['ArqValida_IdentificacaoEmpresaCedenteT1'.$key] 		= isAlfa($linha,21 ,37);
		$_SESSION['ArqValida_NumControleParticipanteT1'.$key] 			= isAlfa($linha,38 ,62);
		$_SESSION['ArqValida_ZEROS2T1'.$key] 							= isNumber($linha,63 ,70 ,"ZEROS","00000000");
		$_SESSION['ArqValida_IdentificacaoTituloT1'.$key] 				= isAlfa($linha,71 ,82);
		$_SESSION['ArqValida_UsoBanco1T1'.$key] 						= isNumber($linha,83 ,92 ,"UsoBanco","0000000000");
		$_SESSION['ArqValida_UsoBanco2T1'.$key] 						= isAlfa($linha,93 ,104);
		$_SESSION['ArqValida_IndicadorRateioCreditoT1'.$key] 			= isAlfa($linha,105,105);
		$_SESSION['ArqValida_ZEROS3T1'.$key] 							= isNumber($linha,106,107,"ZEROS","00");
		$_SESSION['ArqValida_CarteiraT1'.$key] 							= isNumber($linha,108,108,"Carteira","");
		$_SESSION['ArqValida_IdentificacaoOcorrenciaT1'.$key] 			= isNumber($linha,109,110,"IdentificacaoOcorrencia","");
		$_SESSION['ArqValida_DataOcorrenciaT1'.$key] 					= isNumber($linha,111,116,"DataOcorrencia","");
		$_SESSION['ArqValida_NumDocumentoT1'.$key] 						= isAlfa($linha,117,126);
		$_SESSION['ArqValida_IdentificacaoTituloT1'.$key] 				= isAlfa($linha,127,146);
		$_SESSION['ArqValida_DataVctoTituloT1'.$key] 					= isNumber($linha,147,152,"DataVctoTitulo","");
		$_SESSION['ArqValida_ValorTituloT1'.$key] 						= isNumber($linha,153,165,"ValorTitulo","");
		$_SESSION['ArqValida_BancoT1'.$key] 							= isNumber($linha,166,168,"Banco","");
		$_SESSION['ArqValida_AgenciaT1'.$key] 							= isNumber($linha,169,173,"Agencia","");
		$_SESSION['ArqValida_EspecieTituloT1'.$key] 					= isAlfa($linha,174,175);
		$_SESSION['ArqValida_DespesasCobrCodigosOcorr02e28T1'.$key]		= isNumber($linha,176,188,"DespesasCobrCodigosOcorr02e28","");
		$_SESSION['ArqValida_OutrasDespesasCustasProtestoT1'.$key] 		= isNumber($linha,189,201,"OutrasDespesasCustasProtesto","");
		$_SESSION['ArqValida_JurosOperacaoAtrasoT1'.$key] 				= isNumber($linha,202,214,"JurosOperacaoAtraso","");
		$_SESSION['ArqValida_IOFDevidoT1'.$key] 						= isNumber($linha,215,227,"IOFDevido","");
		$_SESSION['ArqValida_AbatimentoConcedidoT1'.$key] 				= isNumber($linha,228,240,"AbatimentoConcedido","");
		$_SESSION['ArqValida_DescontoConcedidoT1'.$key] 				= isNumber($linha,241,253,"DescontoConcedido","");
		$_SESSION['ArqValida_ValorPagoT1'.$key] 						= isNumber($linha,254,266,"ValorPago","");
		$_SESSION['ArqValida_JurosMoraT1'.$key] 						= isNumber($linha,267,279,"JurosMora","");
		$_SESSION['ArqValida_OutrosCreditosT1'.$key] 					= isNumber($linha,280,292,"OutrosCreditos","");
		$_SESSION['ArqValida_BRANCOS1T1'.$key] 							= isAlfa($linha,293,294);
		$_SESSION['ArqValida_MotivoCodigoOcorrencia25T1'.$key] 			= isAlfa($linha,295,295);
		$_SESSION['ArqValida_DataCreditoT1'.$key] 						= isNumber($linha,296,301,"DataCredito","");
		$_SESSION['ArqValida_OrigemPagamentoT1'.$key] 					= isAlfa($linha,302,304); //Virou alfa porque no arquivo pode vir vazio quando informação é irrelevante
		$_SESSION['ArqValida_BRANCOS2T1'.$key] 							= isAlfa($linha,305,314);
		$_SESSION['ArqValida_CodigoBancoChequeBradescoT1'.$key] 		= isAlfa($linha,315,318);
		$_SESSION['ArqValida_MotivosRejeicoesCodOcorrPos109a110T1'.$key]= isNumber($linha,319,328,"MotivosRejeicoesCodOcorrPos109a110","");
		$_SESSION['ArqValida_BRANCOS3T1'.$key] 							= isAlfa($linha,329,368);
		$_SESSION['ArqValida_NumCartorioT1'.$key] 						= isAlfa($linha,369,370); //Virou alfa porque no arquivo pode vir vazio quando informação é irrelevante
		$_SESSION['ArqValida_NumeroProtocoloT1'.$key] 					= isAlfa($linha,371,380);
		$_SESSION['ArqValida_BRANCOS4T1'.$key] 							= isAlfa($linha,381,394);
		$_SESSION['ArqValida_NumSeqRegistroT1'.$key] 					= isNumber($linha,395,400,"NumSeqRegistro","");
	}
}

// Transação Tipo 3
function transacaoTipo3($linha){
	@$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key'] + 1;
	$key = $_SESSION['ArqValida_key'];
	
	$_SESSION['ArqValida_LinhaTransT3'.$key] 			= imprimirLinha($linha);
	$_SESSION['ArqValida_IdentificacaoRegistroT3'.$key] = isNumber($linha,1  ,1  ,"IdentificacaoRegistro","3");
	$_SESSION['ArqValida_TipoInscrEmpresaT3'.$key] 		= isNumber($linha,2  ,3  ,"TipoInscrEmpresa","");
	$_SESSION['ArqValida_NumInscrEmpresaT3'.$key]	 	= isNumber($linha,4  ,17 ,"NumInscrEmpresa","");
}

?>