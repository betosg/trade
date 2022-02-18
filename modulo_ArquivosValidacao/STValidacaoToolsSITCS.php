<?php
// altera nome do arquivo validado ou importado
function alteraNomeArq($arqName,$tipo){
		$path       		  = "../../".getSession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/arqbanco/sicob/sindical/";
		$nomeAntigo 		  = $path.$arqName;
		$data 				  = date('d').date('m')."20".date('y').date('h').date('i').date('s');
		$nomeNovo 			  = $tipo.$data."_".$arqName;
		$_SESSION['novoNome'] = $nomeNovo;
		rename ($nomeAntigo, $path.$nomeNovo);
		$arquivo 			  = $path.$nomeNovo;
		return $arquivo;
}

// Verifica tipo de linha
function verificaLinha($row){
	switch(@$row[7]){ 
		case "0": 
			headerArquivo($row); 
			break; 
		case "1":
			headerLote($row);
			break; 
		case "3":
			segmentos($row);
			break; 
		case "5":
			traileLote($row);
			break; 
		case "9":
			traileArquivo($row);
			break; 
		default: 
	}	
}

// seleciona os segmentos
function segmentos($seg){
	switch($seg[13]){ 
		case "T": 
			segmentoT($seg);
			break; 
		case "U":
			segmentoU($seg);
			break; 
		case "F":
			segmentoF($seg);
			break;
		default: 
			echo "Não foi encontrado Segmento Obrigatório ou Segmento Opcional"; 
	}	
}

//imprime a linha completa
function imprimirLinha($linha){
	$linhaCompl = "";
	$p          = 1;
	//print_r($linha);
	//die();
	for($z=0; $z< count($linha); $z++){
			$linhaCompl.= $linha[$z];
			$p++;
			if($p>75){
				$linhaCompl.="\n";
				$p = 1;
			}
	}
	//echo $linhaCompl;
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
	
	// Concatena cada caracter da STRING do 
	// intervalo de posições encaminhado.
	// Ainda não valida nenhum caracter espe-
	// cial tipo ( @ # $ + - { } ) etc.
	for($auxContador = $intPosIni; $auxContador <= $intPosFim; $auxContador++){
		$strResultado .= $strString[$auxContador];
	}
	
	// Caso o valor concatenado seja diferente do esperado
	if($prValorEsperado != ""){
		if($strResultado != $prValorEsperado){
			$strMSGErro = "<span style='color:#F00;'>Erro! Valor esperado: [ <b>".$prValorEsperado."</b> ], valor encontrado: [ <b>".$strResultado."</b> ]</span>";
		}
	}
	$strMSGErro = "";
	// Verifica se Erro foi encontrado
	if($strMSGErro != ""){
		$_SESSION['ValidaErro'] = "Erro no Arquivo";
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
	$intPosIni     = $prPosIni - 1; 
	$intPosFim     = $prPosFim - 1;
	$strString	   = $prString;
	$strDado = "";
	$intTamanho = count($prString);
	
	//NAO FUNCIONOU Se tentar acessar posição fora da linha deve dar erro
	//if ($intPosFim > $intTamanho) {
	//	$strMSGErro = "<span style='color:#F00;'>Erro! Tentativa de acessar posição inexistente na linha. Posição: [ <b>".$intPosFim."</b> ], tamanho da linha: [ <b>".$intTamanho."</b> ]</span>";
	//}
	
	if($strMSGErro == ""){
		// Faz um laço para cada caracter da STRING ENCAMINHADA para verificar se a posição 
		// é UM NÚMERO REALMENTE	
		for($auxContador = $intPosIni; $auxContador <= $intPosFim; $auxContador++){
			$strDado = trim($strString[$auxContador]);
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
	$strMSGErro = "";
	// Verifica se Erro foi encontrado
	if($strMSGErro != ""){
		$_SESSION['ValidaErro'] = "Erro no Arquivo";
		return($strMSGErro);
	} else{
		return($strResultado);
	}
}


// Header de ARQUIVO
function headerArquivo($headerA){
	// $strCNPJSindi = getVarEntidade($objConn,"cnpj");
	// $strCNPJSindi = str_replace("-","",str_replace(".","",str_replace("/","",$strCNPJSindi)));
	
	$_SESSION['ArqValida_linhaHeaderHA'] 	= imprimirLinha($headerA);
	$_SESSION['ArqValida_codBancoHA'] 		= isNumber($headerA ,1   ,3   ,"Cód Banco","104");//
	$_SESSION['ArqValida_loteServicoHA'] 	= isNumber($headerA ,4   ,7   ,"Lote de Servico","0000");//
	$_SESSION['ArqValida_tipoRegistroHA'] 	= isNumber($headerA ,8   ,8   ,"Tipo de Registro","0");//
	$_SESSION['ArqValida_AFAusoExclFeb1HA'] =   isAlfa($headerA ,9   ,17 );    //                         					// CNAB - Uso Exclusivo Febraban / CNAB
	$_SESSION['ArqValida_tipoIncEmpHA'] 	= isNumber($headerA ,18  ,18  ,"Tipo de inscrição da Empresa","2");//
	$_SESSION['ArqValida_numIncEmpHA'] 		= isNumber($headerA ,19  ,32  ,"Numero de inscrição da Empresa");//				// CNPJ
//	$_SESSION['ArqValida_codConvBancoHA'] 	=   isAlfa($headerA	,33	 ,52 ); //uso exclusivocaixa    											// Código Convênio no Banco
	$_SESSION['ArqValida_AFusoExclCaixa0'] 	= isNumber($headerA	,33	 ,52 ); //uso exclusivocaixa
	$_SESSION['ArqValida_ageMantContaHA'] 	= isNumber($headerA	,53	 ,57  ,"Agência mantenedora da Conta");//
	$_SESSION['ArqValida_digVerAgeHA'] 		=   isAlfa($headerA	,58	 ,58 );												// Dig Verificador Agencia
	$_SESSION['ArqValida_codConvBancoHA'] 	= isNumber($headerA	,59	 ,65  ,"Num. CC/Codigo do Cedente");//
	$_SESSION['ArqValida_codCedenteHA'] 		=   isAlfa($headerA	,66	 ,72 );					     						// Díg. Verif. Num. CC/Cedente
	//$_SESSION['ArqValida_digVerCedHA'] 		=   isAlfa($headerA	,71	 ,71 );					     						// Díg. Verif. Num. CC/Cedente
	$_SESSION['ArqValida_digVerAgCedHA'] 	=   isAlfa($headerA	,73	 ,73 );     										// Díg. Verif. Ag/ CC/Ced
	$_SESSION['ArqValida_AFAnomEmpHA'] 		=   isAlfa($headerA	,74  ,103 );												// Nome da Empresa
	$_SESSION['ArqValida_AFAnomBanHA'] 		=   isAlfa($headerA	,104 ,133 );												// Nome do Banco
	//$_SESSION['ArqValida_AFAnomBanHA'] 	= isAlfa($headerA, 133, 142);												// USO Febraban - EM BRANCO
//	$_SESSION['ArqValida_brancos1'] 		= isAlfa($headerA	,134 ,143);
	$_SESSION['ArqValida_codRemRetHA'] 		= isNumber($headerA	,144 ,144 ,"Código Remessa/Retorno","2");
	$_SESSION['ArqValida_dtGerArqHA'] 		= isNumber($headerA	,145 ,152 ,"Data de geração do Arquivo");
	$_SESSION['ArqValida_hsGerArqHA'] 		= isNumber($headerA	,153 ,158 ,"Hora de geração do Arquivo");
	$_SESSION['ArqValida_numSeqArqHA'] 		= isNumber($headerA	,159 ,164 ,"Numero sequencial do arquivo");
	$_SESSION['ArqValida_numVerLayArqHA'] 	= isNumber($headerA	,165 ,167 ,"Nº da versão do layout do Arquivo","060");
	$_SESSION['ArqValida_denGerArqHA'] 		= isNumber($headerA	,168 ,172 ,"Densidade da geração do arquivo","0");
	$_SESSION['ArqValida_AFAusoResBanHA'] 	=   isAlfa($headerA	,173 ,192 );   											// Reservado ao BANCO
	$_SESSION['ArqValida_AFAusoResEmpHA'] 	=   isAlfa($headerA	,193 ,212 );   											// Reservado a EMPRESA
	$_SESSION['ArqValida_VersaoAppCaixa'] 	=   isAlfa($headerA	,213 ,216 );   											// Reservado a EMPRESA
	$_SESSION['ArqValida_AFAusoExclFeb3HA'] =   isAlfa($headerA	,217 ,240 );   											// Uso Exclusivo FEBRABAN
	
	
	
	
	
}

// Header de LOTE
function headerLote($headerL){
	$_SESSION['ArqValida_linhaHeaderL'] 	= imprimirLinha($headerL);
	$_SESSION['ArqValida_codBanHL'] 		= isNumber($headerL, 1, 3, "Cód do Banco na compensação","104");
	$_SESSION['ArqValida_lotSerHL'] 		= isNumber($headerL, 4, 7, "Lote de Serviço","1");
	$_SESSION['ArqValida_tipRegHL'] 		= isNumber($headerL, 8, 8, "Tipo de Registro","1");
	$_SESSION['ArqValida_AFAtipOperHL'] 	= isAlfa($headerL, 9, 9,"T");
	$_SESSION['ArqValida_tipServHL'] 		= isNumber($headerL, 10, 11, "Tipo de Serviço","01");
	$_SESSION['ArqValida_AFAusoExcFeb1HL'] 	= isAlfa($headerL, 12, 13);
	$_SESSION['ArqValida_numVerLayLotHL'] 	= isNumber($headerL, 14, 16, "Nº da versão do layout do lote","30");
	$_SESSION['ArqValida_AFAusoExcFebHL'] 	= isAlfa($headerL, 17, 17);
	$_SESSION['ArqValida_tipInsEmpHL'] 		= isNumber($headerL, 18, 18, "Tipo de inscrição da Empresa","2");
	$_SESSION['ArqValida_insEmpHL'] 		= isNumber($headerL, 19, 32, "Nº de inscrição da Empresa");
	$_SESSION['ArqValida_AFAcodConBanHL'] 	= isAlfa($headerL, 33, 39); 	// Cod Convenio no BANCO
	$_SESSION['ArqValida_AFAusoExcCxHL'] 	= isAlfa($headerL, 40, 54);
	$_SESSION['ArqValida_ageManConHL'] 		= isNumber($headerL, 55, 59, "Agência Mantenedora da Conta");
	$_SESSION['ArqValida_digVerAgHL'] 		= isAlfa($headerL, 60, 60); 	// Dig. Verificador da Ag	
	$_SESSION['ArqValida_numConvenioHL'] 	= isNumber($headerL, 61, 67, "Num Convenio");	
	
	$_SESSION['ArqValida_numCodModPersoHL']	= isNumber($headerL, 68, 74, "Código modelo personalizado","0000000");
	$_SESSION['ArqValida_AFAusoExcCx2HL'] 	= isAlfa($headerL, 75, 75);
	$_SESSION['ArqValida_AFAnomEmpHL'] 		= isAlfa($headerL, 76, 105);
	$_SESSION['ArqValida_AFAmens1HL'] 		= isAlfa($headerL, 106, 145);
	$_SESSION['ArqValida_AFAmens2pHL']	 	= isAlfa($headerL, 146, 185);
	$_SESSION['ArqValida_numRemRetHL'] 		= isNumber($headerL, 186, 197, "Número Remessa/Retorno");
	$_SESSION['ArqValida_dtRemRetHL'] 		= isNumber($headerL, 198, 205, "Data gravação Remessa/Retorno");
	$_SESSION['ArqValida_dtCreHL'] 			= isNumber($headerL, 206, 213, "Data do Crédito","0");
	$_SESSION['ArqValida_AFAusoExcFeb2pHL'] = isAlfa($headerL, 214, 240);	


	
//	$_SESSION['ArqValida_numConCorrHL'] 	= isNumber($headerL, 60, 71, "Num Conta Corrente");	
//	$_SESSION['ArqValida_digVerConHL'] 		= isAlfa($headerL, 72, 72); 	// Díg Verificador Conta	
//	$_SESSION['ArqValida_digVerAgCedHL'] 	= isAlfa($headerL, 73, 73,"0");  	// dig Verif. Ag./Ced(sem op)
	

}

// Trailer de LOTE
function traileLote($traileL){
	$_SESSION['ArqValida_linhaTrailerL'] 	  = imprimirLinha($traileL);
	// Verificando se os campos são numeros
	$_SESSION['ArqValida_codBanTL'] 		  = isNumber($traileL, 1, 3, "Cód do Banco na Compensação","104");
	$_SESSION['ArqValida_lotSerTL'] 		  = isNumber($traileL, 4, 7, "Lote de Serviço","1");
	$_SESSION['ArqValida_tipSerTL'] 		  = isNumber($traileL, 8, 8, "Tipo de registro","5");
	$_SESSION['ArqValida_AFAusoExclFeb1TL']   = isAlfa($traileL, 9, 17);
	$_SESSION['ArqValida_quanRegLotTL'] 	  = isNumber($traileL, 18, 23, "Quantidade de Registros no Lote");
	$_SESSION['ArqValida_quanTCobr1TL'] 	  = isNumber($traileL, 24, 29, "Quantidade de Titulos em Cobrança Simples","0");
	$_SESSION['ArqValida_valTotTitCarTL'] 	  = isNumber($traileL, 30, 46, "Valor Total dos Títulos em Carteiras Simples","0");
	$_SESSION['ArqValida_quanTitCobr2TL'] 	  = isNumber($traileL, 47, 52, "Quantidade de Titulos em Cobrança Caucionada","0");
	$_SESSION['ArqValida_valTotTitCar2TL'] 	  = isNumber($traileL, 53, 69, "Valor Total dos Títulos em Carteiras Caucionada");
	$_SESSION['ArqValida_valTotTitCar3TL'] 	  = isNumber($traileL, 70, 75, "Quantidade de Titulos em Cobrança Descontada","0");
	$_SESSION['ArqValida_valTotTitCobr3TL']   = isNumber($traileL, 76, 92, "Quantidade de Titulos em Carteiras");
	//$_SESSION['ArqValida_valTotTitCar4TL'] 	  = isNumber($traileL,99,115, "Valor Total dos Títulos em Carteiras");
	$_SESSION['ArqValida_numAvisoLctoTL']     = isAlfa($traileL, 93, 240); // Num AVISO LCTO
	//$_SESSION['ArqValida_AFAusoExclFeb3TL']   = isAlfa($traileL, 124, 240);
}

// Trailer de ARQUIVO
function traileArquivo($traileA){
	$_SESSION['ArqValida_linhaTrailerA'] 		= imprimirLinha($traileA);
	// verificando se os campos são numeros e grava na sessão para ser impresso
	$_SESSION['ArqValida_codBanCompTA'] 		= isNumber($traileA, 1, 3, "Código do Banco na Compensação","104");
	$_SESSION['ArqValida_lotServTA']	 		= isNumber($traileA, 4, 7, "Lote de Serviço","9999");
	$_SESSION['ArqValida_tipRegTA'] 			= isNumber($traileA, 8, 8, "Tipo de Registro","9");
	$_SESSION['ArqValida_AFAusoExclFeb1TA'] 	= isAlfa($traileA, 9, 17);
	$_SESSION['ArqValida_quanLotArqTA'] 		= isNumber($traileA, 18, 23, "Quantidade de Lotes de Arquivo","1");
	$_SESSION['ArqValida_quanRegArqTA']			= isNumber($traileA, 24, 29, "Quantidade de registros de Arquivo");
	$_SESSION['ArqValida_AFAusoExclFeb2TA'] 	= isAlfa($traileA, 30, 35,"0");
	$_SESSION['ArqValida_AFAusoExclFeb3TA'] 	= isAlfa($traileA, 36, 240);
}

// Segmento T
// ARRECADAÇÃO DIRETA 
function segmentoT($segT){
	@$_SESSION['ArqValida_key'] 						= $_SESSION['ArqValida_key'] + 1;
	$r = $_SESSION['ArqValida_key'];
	$_SESSION['ArqValida_linhaSegmT'.$r] 			= imprimirLinha($segT);
	$_SESSION['ArqValida_codBanComST'.$r] 			= isNumber($segT, 1, 3, "Cód do Banco na Compensação","104");
	$_SESSION['ArqValida_lotSerST'.$r] 				= isNumber($segT, 4, 7, "Lote de Serviço","1");
	$_SESSION['ArqValida_tipRegST'.$r] 				= isNumber($segT, 8, 8, "Tipo de Registro","3");
	$_SESSION['ArqValida_numSecRegLotST'.$r] 		= isNumber($segT, 9, 13, "Número Sequencial Registro de Lote");
	$_SESSION['ArqValida_AFAcodSegRegDetST'.$r] 	= isAlfa($segT, 14, 14,"T");
	$_SESSION['ArqValida_AFAusoExcFeb1ST'.$r] 		= isAlfa($segT, 15, 15);
	
	$_SESSION['ArqValida_codMovRetST'.$r] 			= isNumber($segT, 16, 17, "Código de Movimento Retorno");
	$_SESSION['ArqValida_agMantCon'.$r] 			= isNumber($segT, 18, 22, "Agência Mantenedora da Conta");
	$_SESSION['ArqValida_digVeriAge'.$r] 			= isNumber($segT, 23, 23, "Díg. Verifi. da Ag");
	$_SESSION['ArqValida_codCede'.$r] 				= isNumber($segT, 24, 30, "Cód do cedente / CC");
	$_SESSION['ArqValida_AFAusoExcCXST'.$r]			= isNumber($segT, 31, 33);
	$_SESSION['ArqValida_numBanPagadorST'.$r] 		= isNumber($segT, 34, 36, "Num do Banco");
	$_SESSION['ArqValida_AFAusoExcCX2ST'.$r]		= isAlfa($segT, 37, 40);
	$_SESSION['ArqValida_ideTitST'.$r] 				= isAlfa($segT, 41, 57);	// Identificação do Titulo - NOSSO NUMERO
	$_SESSION['ArqValida_AFAusoExcCX3ST'.$r]		= isNumber($segT, 58, 58);
	$_SESSION['ArqValida_codCartST'.$r] 			= isNumber($segT, 59, 59, "Cód da Carteira","1");
	$_SESSION['ArqValida_AFAnumDocCobST'.$r] 		= isAlfa($segT, 60, 70); // NUMERO DO DOCUMRNTO DE COBRANÇA
	$_SESSION['ArqValida_AFAusoExcCX4ST'.$r]		= isNumber($segT, 71, 74);
	$_SESSION['ArqValida_dtVenTituST'.$r] 			= isNumber($segT, 75, 82, "Data de Vencimento do Titulo");
	$_SESSION['ArqValida_valNomTitST'.$r] 			= isNumber($segT, 83, 97, "Valor Nominal do Titulo");
	
	$_SESSION['ArqValida_numBanST'.$r]		 		= isNumber($segT, 98, 100, "Num do Banco");
	$_SESSION['ArqValida_AgeCobRebST'.$r] 			= isNumber($segT, 101, 105, "Agencia Cobr/Receb");
	$_SESSION['ArqValida_digVerAgeST'.$r] 			= isNumber($segT, 106, 106, "Digito Verificador da Agência Cobr/Rec");
	$_SESSION['ArqValida_AFAidenTituEmpST'.$r]		= isAlfa($segT, 107, 131);
	$_SESSION['ArqValida_codMoeST'.$r] 				= isNumber($segT, 132, 133, "Cód da Moeda","09");
	$_SESSION['ArqValida_tipInsST'.$r] 				= isNumber($segT, 134, 134, "Tipo de Inscrição","0");
	$_SESSION['ArqValida_numInsST'.$r] 				= isNumber($segT, 135, 149, "Numero de Inscrição");
	$_SESSION['ArqValida_AFAnomeSacST'.$r] 			= isAlfa($segT, 150, 189);
	$_SESSION['ArqValida_AFAusoExcFeb3ST'.$r] 		= isAlfa($segT, 190, 199);
	$_SESSION['ArqValida_valTarCusST'.$r] 			= isNumber($segT, 200, 214, "Valor da Tarifa/Custas");
	$_SESSION['ArqValida_ideRejTafCusLiqBaiST'.$r] 	= isNumber($segT, 215, 224,"Ident. para Rejei...");
	$_SESSION['ArqValida_AFAusoExcFeb3ST'.$r] 		= isAlfa($segT, 225, 240);
	
//	$_SESSION['ArqValida_digVerCedeST'.$r] 			= isNumber($segT, 36, 36, "Dig. ver. Cedente / CC");
//	$_SESSION['ArqValida_digVerAgeCed'.$r] 			= isNumber($segT, 37, 37, "dig. ver. ag./ced / CC","0");
//	$_SESSION['ArqValida_numContrCredST'.$r] 		= isNumber($segT, 189, 198, "Número do contrato de Cobrança");
	

}

// Segmento U
// ARRECADAÇÃO DIRETA 
function segmentoU($segU){
	@$_SESSION['ArqValida_chave'] 			 	 = $_SESSION['ArqValida_chave'] + 1;
	$y = $_SESSION['ArqValida_chave'];
	$_SESSION['ArqValida_linhaSegmU'.$y] 	 	 = imprimirLinha($segU);
	$_SESSION['ArqValida_codBanComSU'.$y] 	 	 = isNumber($segU, 1, 3, "Código do Banco na Compensação","104");
	$_SESSION['ArqValida_lotSerSU'.$y] 		 	 = isNumber($segU, 4, 7, "Lote de Serviço","1");
	$_SESSION['ArqValida_tipRegSU'.$y] 		 	 = isNumber($segU, 8, 8, "Tipo de Registro","3");
	$_SESSION['ArqValida_numSeqRegDetSU'.$y] 	 = isNumber($segU, 9, 13, "Nº Sequencial do registro no Lote");
	$_SESSION['ArqValida_ALFAcodSegRegDetSU'.$y] = isAlfa($segU,14,14,"U");
	$_SESSION['ArqValida_ALFAusoExcFebSU'.$y] 	 = isAlfa($segU,15,15);
	$_SESSION['ArqValida_codMovRetSU'.$y] 	 	 = isNumber($segU, 16, 17, "Cód de Movimento Retorno");
	
	$_SESSION['ArqValida_CapitalSocialEmpSU'.$y] 	 	 = isNumber($segU, 18, 30, "Capital social empresa");
	$_SESSION['ArqValida_CapitalSocialEstSU'.$y] 	 	 = isNumber($segU, 31, 43, "Capital social estabelecimento");
	$_SESSION['ArqValida_NumEmpregadosContribSU'.$y] 	 = isNumber($segU, 44, 52, "Número de empregados contribuintes");
	$_SESSION['ArqValida_RemuneracaoContribSU'.$y] 	 	 = isNumber($segU, 53, 65, "Total da remuneração contribuintes");	
	$_SESSION['ArqValida_NumEmpregadosSU'.$y]		 	 = isNumber($segU, 66, 74, "Número de empregados ");
	$_SESSION['ArqValida_cnaeSU'.$y]	 	 	 		 = isNumber($segU, 75, 79, "CNAE");
	
	$_SESSION['ArqTipoEntidadeSU'.$y]	 	 	 		 = isNumber($segU, 80, 80, "Tipo Entidade Sindical");
	$_SESSION['ArqCodigoEntidadeSU'.$y]	 	 	 		 = isNumber($segU, 81, 85, "Codigo sindical da entidade sindical");
	
	$_SESSION['ArqTipoArecadacaoSU'.$y]	 	 	 		 = isAlfa($segU, 86, 86);
	$_SESSION['ArqValida_valpagSacSU'.$y]	 	 		 = isNumber($segU, 87, 101, "Valor pago pelo Sacado");
	$_SESSION['ArqValida_valliqCreSU'.$y] 	 	 		 = isNumber($segU, 102, 116, "Valor liquido a ser Creditado");	
	$_SESSION['ArqValida_jurMulEncSU'.$y] 	 	 = isNumber($segU, 117, 131, "Juros / Multa / Encargos");
	$_SESSION['ArqValida_valOutCreSU'.$y] 	 	 = isNumber($segU, 132, 146, "Valor de Outros Créditos","0");
	$_SESSION['ArqValida_dtOcoSU'.$y] 		 	 = isNumber($segU, 147, 154, "Data da Ocorrência");
	$_SESSION['ArqValida_dtEfeCreSU'.$y] 	 	 = isNumber($segU, 155, 162, "Data da Efetivação do Crédito");
	$_SESSION['ArqValida_usoExcCax1SU'.$y] 		 = isNumber($segU, 163, 166);//, "Uso Exclusivo CAIXA"
	$_SESSION['ArqValida_dtTarifaSU'.$y] 		 	 = isNumber($segU, 167, 174, "Data da Debito Tarifa");
	$_SESSION['ArqValida_CodSacadoBancoSU'.$y] 		 	 = isNumber($segU, 175, 189, "Código do pagador no banco");
	$_SESSION['ArqValida_usoExcCax2SU'.$y] 		 = isNumber($segU, 190, 219);//, "Uso Exclusivo CAIXA"
	$_SESSION['ArqValida_usoExcFebSU'.$y] 		 = isAlfa($segU, 220, 240);//, "Uso Exclusivo FEBRABAN/CNAB"
	//$_SESSION['ArqValida_valDesConSU'.$y] 	 	 = isNumber($segU, 33, 47, "Valor do desconto Concedido");
	//$_SESSION['ArqValida_valAbtConCanSU'.$y] 	 = isNumber($segU, 48, 62, "Valor do Abat. Concedido/Cancel.");
	//$_SESSION['ArqValida_valiofRecSU'.$y] 	 	 = isNumber($segU, 63, 77, "Valor do IOF Recolhido");	
	//$_SESSION['ArqValida_valOutDesSU'.$y] 	 	 = isNumber($segU, 108, 122, "Valor de Outras Despesas");
	//$_SESSION['ArqValida_sacadoCodOcorrSU'.$y] 	 = isAlfa($segU, 154, 157);		// Cod. Ocorrencia	
	//$_SESSION['ArqValida_complOcorrSU'.$y] 	 	 = isAlfa($segU, 181, 210); 	// Compl Ocorrencia	
	
	// $_SESSION['ArqValida_codBanCorrSU'.$y] 	 	 = isNumber($segU, 214, 233, "Nosso Numero Banco Corresp.");
	// $_SESSION['ArqValida_sacadoDtOcorrSU'.$y] 	= isAlfa($segU, 154, 165);	// dt. Ocorrencia
	// $_SESSION['ArqValida_sacadoVlrOcorrSU'.$y] 	= isNumber($segU, 166, 180, "Vlr. Ocorrencia");
}

// Segmento F
// TARIFAS DE SERVIÇO
function segmentoF($segF){
	@$_SESSION['ArqValida_chaveSF'] 			 = $_SESSION['ArqValida_chaveSF'] + 1;
	$z = $_SESSION['ArqValida_chaveSF'];
	$_SESSION['ArqValida_linhaSegmF'.$z] 		 = imprimirLinha($segF);
	$_SESSION['ArqValida_codBanComSF'.$z] 		 = isNumber($segF, 1, 3, "Código do Banco na Compensação","104");
	$_SESSION['ArqValida_lotSerSF'.$z] 			 = isNumber($segF, 4, 7, "Lote de Serviço");
	$_SESSION['ArqValida_tipRegSF'.$z] 		 	 = isNumber($segF, 8, 8, "Tipo de Registro","3");
	$_SESSION['ArqValida_numSeqRegDetSF'.$z] 	 = isNumber($segF, 9, 13, "Nº Sequencial do registro no Lote");
	$_SESSION['ArqValida_ALFAcodSegRegDetSF'.$z] = isAlfa($segF,14,14,"F");		// Código do Segmento do REG. Detalhe
	$_SESSION['ArqValida_ALFAusoExcFebSF'.$z] 	 = isAlfa($segF,15,102);    // Uso Exclusivo FEBRABAN/CNAB
	$_SESSION['ArqValida_hrTransSF'.$z] 		 = isNumber($segF, 103, 108, "Horário da Transação","0");		
	$_SESSION['ArqValida_natLctoSF'.$z] 		 = isAlfa($segF, 109, 111); // Natureza do LANÇAMENTO
	$_SESSION['ArqValida_tpCompLctoSF'.$z] 		 = isNumber($segF, 112, 113, "Tipo do Complemento do LCTO","0");
	$_SESSION['ArqValida_compLctoSF'.$z] 		 = isAlfa($segF, 114, 133); // Complemento do LANÇAMENTO
	$_SESSION['ArqValida_isenCpmfSF'.$z] 		 = isAlfa($segF, 134, 134); // Identificação de Isenção de CPMF
	$_SESSION['ArqValida_dtContabilSF'.$z] 		 = isNumber($segF, 135, 142, "Data Contábil");
	
	$_SESSION['ArqValida_dtLctoSF'.$z]  		 = isNumber($segF, 143, 150, "Data LCTO");
	$_SESSION['ArqValida_vlrLctoSF'.$z] 		 = isNumber($segF, 151, 168, "Vlr. Lançamento");
	$_SESSION['ArqValida_tpLctoSF'.$z] 		     = isAlfa($segF, 169, 169); // TIPO LANÇAMENTO
	$_SESSION['ArqValida_catLctoSF'.$z] 		 = isNumber($segF, 170, 172, "Categoria do Lançamento");
	$_SESSION['ArqValida_codHistLctoSF'.$z]	 	 = isAlfa($segF, 173, 177); // Código Hist lcto Banco
	$_SESSION['ArqValida_descHistLctoSF'.$z]	 = isAlfa($segF, 178, 202); // Descr. do Serviço origem na tarifa
	$_SESSION['ArqValida_numDocComplSF'.$z] 	 = isAlfa($segF, 203, 240); // Númerdo do Documento / COmplemento
}

?>