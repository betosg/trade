<?php
function verificaLinha($row){
	switch($row[7]){ 
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
			echo "Erro no Arquivo de importação"; 
	}	
}
//imprime a linha completa
function imprimirLinha($linha){
	$linhaCompl = "";
	$p=1;
	for($z=0; $z< count($linha); $z++){
			$linhaCompl.= $linha[$z];
			$p++;
			if($p>95){
				$linhaCompl.= "<br>";
				$p=1;
			}
	}
	return $linhaCompl;
}
//imprime valores alfanumerico
function isAlfa($alfa, $start, $end){
		$start = $start -1; $end = $end -1; $valor = "";
		for($y= $start; $y<= $end; $y++){
			$valor.= $alfa[$y];
		}
		return $valor;
}

//função para verifica se é numero no array
function isNumber($var, $ini, $fim, $campo){
		$erro=""; $result = "";
		$ini = $ini -1; $fim = $fim -1;
		for($x= $ini; $x<= $fim; $x++){
				if(!ctype_digit($var[$x])){
					$erro ="<font color='#FF0000'> Esperava-se somente Numeros e veio '<b>".$var[$x]."</b>'"." na posicao <b>".$x."</b></font>";
				}else{
						$result.=	$var[$x];
					}	
		}
		if ($erro <> ""){
			return $erro;		
		}else if($erro == ""){
			return $result;
		}
}

// header de Arquivo
function headerArquivo($headerA){
	$_SESSION['ArqValida_linhaHeaderHA'] = imprimirLinha($headerA);
	$_SESSION['ArqValida_codBancoHA'] = isNumber($headerA, 1, 3,"Cód Banco");
	$_SESSION['ArqValida_loteServicoHA'] = isNumber($headerA, 4, 7, "Lote de Servico");
	$_SESSION['ArqValida_tipoRegistroHA'] = isNumber($headerA, 8, 8, "Tipo de Registro");
	$_SESSION['ArqValida_tipoIncEmpHA'] =	isNumber($headerA, 18, 18, "Tipo de inscrição da Empresa");
	$_SESSION['ArqValida_numIncEmpHA'] = 	isNumber($headerA, 19, 32, "Numero de inscrição da Empresa");
	$_SESSION['ArqValida_usoCAIXA1HA'] = 	isNumber($headerA, 33, 52, "Uso exclusivo CAIXA");
	$_SESSION['ArqValida_ageMantContaHA'] = isNumber($headerA, 53, 57, "Agência mantenedora da Conta");
	$_SESSION['ArqValida_codConvBanco'] =	isNumber($headerA, 59, 64, "Codigo do convencio do Banco");
	$_SESSION['ArqValida_usoCAIXA2HA'] = 	isNumber($headerA, 65, 71, "Uso exclusivo CAIXA");
	$_SESSION['ArqValida_usoCAIXA3HA'] =	isNumber($headerA, 72, 72, "Uso exclusivo CAIXA");
	$_SESSION['ArqValida_codRemRetHA'] = isNumber($headerA, 143, 143, "Código Remessa/Retorno");
	$_SESSION['ArqValida_dtGerArqHA'] = isNumber($headerA, 144, 151, "Data de geração do Arquivo");
	$_SESSION['ArqValida_hsGerArqHA'] =  isNumber($headerA, 152, 157, "Hora de geração do Arquivo");
	$_SESSION['ArqValida_numSeqArqHA'] = isNumber($headerA, 158, 163, "Numero sequencial do arquivo");
	$_SESSION['ArqValida_numVerLayArqHA'] = isNumber($headerA, 164, 166, "Nº da versão do layout do Arquivo");
	$_SESSION['ArqValida_denGerArqHA'] = isNumber($headerA, 167, 171, "Densidade da geração do arquivo");
	
	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAusoExclFeb1HA'] = isAlfa($headerA, 9, 17);
	$_SESSION['ArqValida_AFAdigVerAgeHA'] = isAlfa($headerA, 58, 58);
	$_SESSION['ArqValida_AFAnomEmpHA'] = isAlfa($headerA, 73, 102);
	$_SESSION['ArqValida_AFAnomBanHA'] = isAlfa($headerA, 103, 132);	
	$_SESSION['ArqValida_AFAusoExclFeb2HA'] = isAlfa($headerA, 30, 35);
	$_SESSION['ArqValida_AFAusoResBanHA'] = isAlfa($headerA, 172, 191);
	$_SESSION['ArqValida_AFAusoResEmpHA'] = isAlfa($headerA, 192, 211);
	$_SESSION['ArqValida_AFAverAplCaiHA'] = isAlfa($headerA, 212, 215);
	$_SESSION['ArqValida_AFAusoExclFeb3HA'] = isAlfa($headerA, 216, 240);
	
}

//header de Lote
function headerLote($headerL){
	$_SESSION['ArqValida_linhaHeaderL'] = imprimirLinha($headerL);
	$_SESSION['ArqValida_codBanHL'] = isNumber($headerL, 1, 3, "Cód do Banco na compensação");
	$_SESSION['ArqValida_lotSerHL'] = isNumber($headerL, 4, 7, "Lote de Serviço");
	$_SESSION['ArqValida_tipRegHL'] = isNumber($headerL, 8, 8, "Tipo de Registro");
	$_SESSION['ArqValida_tipServHL'] = isNumber($headerL, 10, 11, "Tipo de Serviço");
	$_SESSION['ArqValida_usoExcFebHL'] = isNumber($headerL, 12, 13, "Uso Exclusivo FEBRABAN/CNAB");
	$_SESSION['ArqValida_numVerLayLotHL'] = isNumber($headerL, 14, 16, "Nº da versão do layout do lote");
	$_SESSION['ArqValida_tipInsEmpHL'] = isNumber($headerL, 18, 18, "Tipo de inscrição da Empresa");
	$_SESSION['ArqValida_insEmpHL'] = isNumber($headerL, 19, 33, "Nº de inscrição da Empresa");
	$_SESSION['ArqValida_codConBanHL'] = isNumber($headerL, 34, 39, "Cód do Convênio no Banco");
	$_SESSION['ArqValida_usoExcCaiHL'] = isNumber($headerL, 40, 53, "Uso Exclusivo da CAIXA");
	$_SESSION['ArqValida_ageManConHL'] = isNumber($headerL, 54, 58, "Agência Mantenedora da Conta");
	$_SESSION['ArqValida_codConBanHL'] = isNumber($headerL, 60, 65, "Cód do Convênio no Banco");
	$_SESSION['ArqValida_codModPerHL'] = isNumber($headerL, 66, 72, "Cód Modelo Personalizado");
	$_SESSION['ArqValida_usoExcCaiHL'] = isNumber($headerL, 73, 73, "Uso Exclusivo da CAIXA");
	$_SESSION['ArqValida_numRemRetHL'] = isNumber($headerL, 184, 191, "Número Remessa/Retorno");
	$_SESSION['ArqValida_dtRemRetHL'] = isNumber($headerL, 192, 199, "Data gravação Remessa/Retorno");
	$_SESSION['ArqValida_dtCreHL'] = isNumber($headerL, 200, 207, "Data do Crédito");
	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAtipOperHL'] = isAlfa($headerL, 9, 9);
	$_SESSION['ArqValida_AFAusoExcFebHL'] = isAlfa($headerL, 17, 17);
	$_SESSION['ArqValida_AFAdigVerConHL'] = isAlfa($headerL, 59, 59);
	$_SESSION['ArqValida_AFAnomEmpHL'] = isAlfa($headerL, 74, 103);
	$_SESSION['ArqValida_AFAmens1HL'] = isAlfa($headerL, 104, 143);
	$_SESSION['ArqValida_AFAmens2pHL'] = isAlfa($headerL, 144, 183);
	$_SESSION['ArqValida_AFAusoExcFeb2pHL'] = isAlfa($headerL, 208, 240);	
}

// seleciona os segmentos
function segmentos($seg){
	switch($seg[13]){ 
		case "T": 
			segmentoT($seg);
			verificaSegT($seg);
			break; 
		case "U":
			segmentoU($seg);
			verificaSegU($seg);
			break; 
		case "P":
			//segmentoP($seg);
			break;
		case "Q":
			//segmentoQ($seg);
			break;		
		default: 
			echo "Não foi encontrado Segmento Obrigatório ou Segmento Opcional"; 
	}	
}

//traile Lote
function traileLote($traileL){
	$_SESSION['ArqValida_linhaTrailerL'] = imprimirLinha($traileL);
	// verificando se os campos são numeros
	$_SESSION['ArqValida_codBanTL'] = isNumber($traileL, 1, 3, "Cód do Banco na Compensação");
	$_SESSION['ArqValida_lotSerTL'] = isNumber($traileL, 4, 7, "Lote de Serviço");
	$_SESSION['ArqValida_tipSerTL'] = isNumber($traileL, 8, 8, "Tipo de registro");
	$_SESSION['ArqValida_quanRegLotTL'] =isNumber($traileL, 18, 23, "Quantidade de Registros no Lote");
	$_SESSION['ArqValida_quanTCobr1TL'] = isNumber($traileL, 24, 29, "Quantidade de Titulos em Cobrança");
	$_SESSION['ArqValida_valTotTitCarTL'] = isNumber($traileL, 30, 46, "Valor Total dos Títulos em Carteiras");
	$_SESSION['ArqValida_quanTitCobr2TL'] = isNumber($traileL, 47, 52, "Quantidade de Titulos em Cobrança");
	$_SESSION['ArqValida_valTotTitCar2TL'] = isNumber($traileL, 53, 69, "Valor Total dos Títulos em Carteiras");
	$_SESSION['ArqValida_quanTitCobr3TL'] = isNumber($traileL, 70, 75, "Quantidade de Titulos em Cobrança");
	$_SESSION['ArqValida_valTotTitCar3TL'] = isNumber($traileL, 76, 92, "Quantidade de Titulos em Carteiras");
	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAusoExclFeb1TL'] = isAlfa($traileL, 9, 17);
	$_SESSION['ArqValida_AFAusoExclFeb2TL'] = isAlfa($traileL, 93, 123);
	$_SESSION['ArqValida_AFAusoExclFeb3TL'] = isAlfa($traileL, 124, 240);
}

//traile arquivo
function traileArquivo($traileA){
	$_SESSION['ArqValida_linhaTrailerA'] = imprimirLinha($traileA);
	// verificando se os campos são numeros e grava na sessão para ser impresso
	$_SESSION['ArqValida_codBanCompTA'] = isNumber($traileA, 1, 3, "Código do Banco na Compensação","Traile de Arquivo");
	$_SESSION['ArqValida_lotServTA'] = isNumber($traileA, 4, 7, "Lote de Serviço","Traile de Arquivo");
	$_SESSION['ArqValida_tipRegTA'] = isNumber($traileA, 8, 8, "Tipo de Registro","Traile de Arquivo");
	$_SESSION['ArqValida_quanLotArqTA'] = isNumber($traileA, 18, 23, "Quantidade de Lotes de Arquivo","Traile de Arquivo");
	$_SESSION['ArqValida_quanRegArqTA']=isNumber($traileA, 24, 29, "Quantidade de registros de Arquivo","Traile de Arquivo");
	
	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAusoExclFeb1TA'] = isAlfa($traileA, 9, 17);
	$_SESSION['ArqValida_AFAusoExclFeb2TA'] = isAlfa($traileA, 30, 35);
	$_SESSION['ArqValida_AFAusoExclFeb3TA'] = isAlfa($traileA, 36, 240);
}

//segmento T
function segmentoT($segT){
	
	@$_SESSION['ArqValida_key'] = $_SESSION['ArqValida_key'] + 1;
	$r = $_SESSION['ArqValida_key'];
	$_SESSION['ArqValida_linhaSegmT'.$r] =  imprimirLinha($segT);
	$_SESSION['ArqValida_codBanComST'.$r] = isNumber($segT, 1, 3, "Cód do Banco na Compensação");
	$_SESSION['ArqValida_lotSerST'.$r] = isNumber($segT, 4, 7, "Lote de Serviço");
	$_SESSION['ArqValida_tipRegST'.$r] = isNumber($segT, 8, 8, "Tipo de Registro");
	$_SESSION['ArqValida_numSecRegLotST'.$r] = isNumber($segT, 9, 13, "Número Sequencial Registro de Lote");
	$_SESSION['ArqValida_codMovRetST'.$r] = isNumber($segT, 16, 17, "Código de Movimento Retorno");
	$_SESSION['ArqValida_usoExcCai1ST'.$r] = isNumber($segT, 18, 22, "Uso Exclusivo CAIXA");
	$_SESSION['ArqValida_usoExcCai2ST'.$r] = isNumber($segT, 23, 23, "Uso Exclusivo CAIXA");
	$_SESSION['ArqValida_codConvBanST'.$r] = isNumber($segT, 24, 29, "Cód do Convênio no Banco");
	$_SESSION['ArqValida_usoExcCai3ST'.$r] = isNumber($segT, 30, 32, "Uso Exclusivo CAIXA");
	$_SESSION['ArqValida_numBanSacST'.$r] = isNumber($segT, 33, 35, "Número do Banco de Sacados");
	$_SESSION['ArqValida_usoExcCai4ST'.$r] = isNumber($segT, 36, 39, "Uso Exclusivo CAIXA");
	$_SESSION['ArqValida_modNosNumST'.$r] = isNumber($segT, 40, 41, "Modalidade Nosso Número");
	$_SESSION['ArqValida_ideTitBanST'.$r] = isNumber($segT, 42, 56, "Identificação do Titulo no Banco");
	$_SESSION['ArqValida_usoExcCai5ST'.$r] = isNumber($segT, 57, 57, "Uso Exclusivo CAIXA");
	$_SESSION['ArqValida_codCartST'.$r] = isNumber($segT, 58, 58, "Cód da Carteira");
	$_SESSION['ArqValida_dtVenTituST'.$r] = isNumber($segT, 74, 81, "Data de Vencimento do Titulo");
	$_SESSION['ArqValida_valNomTitST'.$r] = isNumber($segT, 82, 96, "Valor Nominal do Titulo");
	$_SESSION['ArqValida_codBanST'.$r] = isNumber($segT, 97, 99, "Cód do Banco");
	$_SESSION['ArqValida_codAgeCobRebST'.$r] = isNumber($segT, 100, 104, "Cód da Agencia Cobr/Receb");
	$_SESSION['ArqValida_digVerAgeCobRecST'.$r] = isNumber($segT, 105, 105, "Digito Verificador da Agência Cobr/Rec");
	$_SESSION['ArqValida_codMoeST'.$r] = isNumber($segT, 131, 132, "Cód da Moeda");
	$_SESSION['ArqValida_tipInsST'.$r] = isNumber($segT, 133, 133, "Tipo de Inscrição");
	$_SESSION['ArqValida_numInsST'.$r] = isNumber($segT, 134, 148, "Numero de Inscrição");
	$_SESSION['ArqValida_valTarCusST'.$r] = isNumber($segT, 199, 213, "Valor da Tarifa/Custas");

	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAcodSegRegDetST'.$r] = isAlfa($segT, 14, 14);
	$_SESSION['ArqValida_AFAusoExcFeb1ST'.$r] = isAlfa($segT, 15, 15);
	$_SESSION['ArqValida_AFAnumDocCobST'.$r] = isAlfa($segT, 59, 69);
	$_SESSION['ArqValida_AFAusoExclCaiST'.$r] = isAlfa($segT, 70, 73);
	$_SESSION['ArqValida_AFAidenTituEmpST'.$r] = isAlfa($segT, 106, 130);
	$_SESSION['ArqValida_AFAnomeST'.$r] = isAlfa($segT, 149, 188);
	$_SESSION['ArqValida_AFAusoExcFeb2ST'.$r] = isAlfa($segT, 189, 198);
	$_SESSION['ArqValida_AFAideRejTafCusLiqBaiST'.$r] = isAlfa($segT, 214, 223);
	$_SESSION['ArqValida_AFAusoExcFeb3ST'.$r] = isAlfa($segT, 224, 240);
}

//segmento U
function segmentoU($segU){
		//insereBancoSegU($segU);
		$valor = $segU[15].$segU[16];
		if(($valor == 35) or ($valor == 36) or ($valor == 37)){
				//segmentu U  para cód de movimento 35, 36, 37 (banco de sacados)
				@$_SESSION['ArqValida_segU_B'] = $_SESSION['ArqValida_segU_B'] + 1;
				$w = $_SESSION['ArqValida_segU_B'];
				$_SESSION['ArqValida_linhaSegmU_B'.$w] = imprimirLinha($segU);	
				$_SESSION['ArqValida_codBanComSU_B'.$w] = isNumber($segU, 1, 3,"Cód do Banco na Compensação");
				$_SESSION['ArqValida_lotSerSU_B'.$w] = isNumber($segU, 4, 7,"Lote de Serviço");
				$_SESSION['ArqValida_tipRegSU_B'.$w] = isNumber($segU, 8, 8,"Tipo de Registro");
				$_SESSION['ArqValida_numSeqRegLotSU_B'.$w] = isNumber($segU, 9, 13,"Nº Sequencial do Registro no Lote");
				$_SESSION['ArqValida_codMovRetSU_B'.$w] = isNumber($segU, 16, 17,"Cód de Movimento Retorno");
				$_SESSION['ArqValida_jurmulEncSU_B'.$w] = isNumber($segU, 18, 32,"Juros / Multa / Encargos");
				$_SESSION['ArqValida_valDesConSU_B'.$w] = isNumber($segU, 33, 47,"Valor do Desconto Concedido");
				$_SESSION['ArqValida_valAbaConCanSU_B'.$w] = isNumber($segU, 48, 62,"Valor do Abat. Concedido/Cancel.");
				$_SESSION['ArqValida_valIofRecSU_B'.$w] = isNumber($segU, 63, 77,"Valor do IOF Recolhido");
				$_SESSION['ArqValida_valPagSacSU_B'.$w] = isNumber($segU, 78, 92,"Valor Pago pelo Sacado");
				$_SESSION['ArqValida_valLiqCreSU_B'.$w] = isNumber($segU, 93, 107,"Valor Líquido a ser creditado");
				$_SESSION['ArqValida_valOutDesSU_B'.$w] = isNumber($segU, 108, 122,"Valor de Outras Despesas");
				$_SESSION['ArqValida_valOutCreSU_B'.$w] = isNumber($segU, 123, 137,"Valor de Outros Créditos");
				$_SESSION['ArqValida_dtOcoreSU_B'.$w] = isNumber($segU, 138, 145,"Data da Ocorrência");
				$_SESSION['ArqValida_dtEfeCreSU_B'.$w] = isNumber($segU, 146, 153,"Data da efetivação do Crédito");
				$_SESSION['ArqValida_numBanSacSU_B'.$w] = isNumber($segU, 154, 156,"Numero do Banco de Sacados");
				$_SESSION['ArqValida_idModBloqSU_B'.$w] = isNumber($segU, 179, 180,"ID Modelo Bloqueto - Banco Sacados");
				$_SESSION['ArqValida_idViaEntDistSU_B'.$w] = isNumber($segU, 181, 181,"ID Via Entrega / Distribuição");
				$_SESSION['ArqValida_idEspTitSU_B'.$w] = isNumber($segU, 182, 183,"ID Espécie Título");
				
				//imprime campos alfanumericos
				$_SESSION['ArqValida_ALFAcodSegRegDetSU_B'.$w] = isAlfa($segU, 14, 14);
				$_SESSION['ArqValida_ALFAusoExcFeb1SU_B'.$w] = isAlfa($segU, 15, 15);
				$_SESSION['ArqValida_ALFAnomBanSacSU_B'.$w] = isAlfa($segU, 157, 176);
				$_SESSION['ArqValida_ALFAidAjuVenSU_B'.$w] = isAlfa($segU, 177, 177);
				$_SESSION['ArqValida_ALFAidAjuEmiSU_B'.$w] = isAlfa($segU, 178, 178);
				$_SESSION['ArqValida_ALFAidAceiSU_B'.$w] = isAlfa($segU, 184, 184);
				$_SESSION['ArqValida_ALFAcodSacbancoSU_B'.$w] = isAlfa($segU, 184, 184);
				$_SESSION['ArqValida_ALFAusoExcCaiSU_B'.$w] = isAlfa($segU, 200, 210);
				$_SESSION['ArqValida_ALFAusoExcFeb2SU_B'.$w] = isAlfa($segU, 211, 240);
		}else{	
				//Segmento U obrigatório
				@$_SESSION['ArqValida_chave'] = $_SESSION['ArqValida_chave'] + 1;
				$y = $_SESSION['ArqValida_chave'];
				$_SESSION['ArqValida_linhaSegmU'.$y] = imprimirLinha($segU);
				$_SESSION['ArqValida_codBanComSU'.$y] = isNumber($segU, 1, 3, "Código do Banco na Compensação");
				$_SESSION['ArqValida_lotSerSU'.$y] = isNumber($segU, 4, 7, "Lote de Serviço");
				$_SESSION['ArqValida_tipRegSU'.$y] = isNumber($segU, 8, 8, "Tipo de Registro");
				$_SESSION['ArqValida_numSeqRegDetSU'.$y] = isNumber($segU, 9, 13, "Nº Sequencial do registro no Lote");
				$_SESSION['ArqValida_codMovRetSU'.$y] = isNumber($segU, 16, 17, "Cód de Movimento Retorno");
				$_SESSION['ArqValida_jurMulEncSU'.$y] = isNumber($segU, 18, 32, "Juros / Multa / Encargos");
				$_SESSION['ArqValida_valDesConSU'.$y] = isNumber($segU, 33, 47, "Valor do desconto Concedido");
				$_SESSION['ArqValida_valAbtConCanSU'.$y] = isNumber($segU, 48, 62, "Valor do Abat. Concedido/Cancel.");
				$_SESSION['ArqValida_valiofRecSU'.$y] = isNumber($segU, 63, 77, "Valor do IOF Recolhido");
				$_SESSION['ArqValida_valpagSacSU'.$y] = isNumber($segU, 78, 92, "Valor pago pelo Sacado");
				$_SESSION['ArqValida_valliqCreSU'.$y] = isNumber($segU, 93, 107, "Valor liquido a ser Creditado");
				$_SESSION['ArqValida_valOutDesSU'.$y] = isNumber($segU, 108, 122, "Valor de Outras Despesas");
				$_SESSION['ArqValida_valOutCreSU'.$y] = isNumber($segU, 123, 137, "Valor de Outros Créditos");
				$_SESSION['ArqValida_dtOcoSU'.$y] = isNumber($segU, 138, 145, "Data da Ocorrência");
				$_SESSION['ArqValida_dtEfeCreSU'.$y] = isNumber($segU, 146, 153, "Data da Efetivação do Crédito");
				$_SESSION['ArqValida_usoExcCai1SU'.$y] = isNumber($segU, 154, 157, "Uso Exclusivo CAIXA");
				$_SESSION['ArqValida_dtDebTarSU'.$y] = isNumber($segU, 158, 165, "Data do Débito da Tarifa");
				$_SESSION['ArqValida_codSacBanSU'.$y] = isNumber($segU, 166, 180, "Código do Sacado no Banco");
				$_SESSION['ArqValida_usoExcCai2SU'.$y] = isNumber($segU, 181, 210, "Uso Exclusivo CAIXA");
				$_SESSION['ArqValida_codBanCorSU'.$y] = isNumber($segU, 211, 213, "Cód Banco Correspondente Compens.");
				$_SESSION['ArqValida_nosNumBanCorSU'.$y] = isNumber($segU, 214, 233, "Nosso Nº Banco Correspondente");
				
				
				//imprime campos alfanumericos
				$_SESSION['ArqValida_ALFAcodSegRegDetSU'.$y] = isAlfa($segU, 14, 14);
				$_SESSION['ArqValida_ALFAusoExcFebSU'.$y] = isAlfa($segU, 15, 15);
				$_SESSION['ArqValida_ALFAusoExcFeb2SU'.$y] = isAlfa($segU, 234, 240);
		}		
	}

//segmento P
function segmentoP($segP){
echo "<br>Segmento P<br>";
	imprimirLinha($segP);
	isNumber($segP, 1, 3, "Cód do Banco na Compensação");
	isNumber($segP, 4, 7, "Lote de Serviço");
	isNumber($segP, 8, 8, "Tipo de Registro");
	isNumber($segP, 9, 13, "Nº Sequencial do Registro no Lote");
	isNumber($segP, 16, 17, "Cód de Movimento Remessa");
	isNumber($segP, 18, 22, "Agência Mantenedora da Conta");
	isNumber($segP, 24, 29, "Cód do Convênio no Banco");
	isNumber($segP, 30, 40, "Uso Exclusivo da CAIXA");
	isNumber($segP, 41, 42, "Modalidade da Carteira");
	isNumber($segP, 43, 57, "Identificação do Titulo no Banco");
	isNumber($segP, 58, 58, "Código da acarteira");
	isNumber($segP, 59, 59, "Forma de Cadastr. do Título no Banco");
	isNumber($segP, 61, 61, "Identificação da Entrega do bloueto");
	isNumber($segP, 78, 85, "Data de Vencimento do Título");
	isNumber($segP, 86, 100, "Valor Nominal do Título");
	isNumber($segP, 101, 105, "Agência Encarregada da Cobrança");
	isNumber($segP, 107, 108, "Espécie do Título");
	isNumber($segP, 110, 117, "Data da Emissão do Título");
	isNumber($segP, 118, 118, "Cód do Juros de Mora");
	isNumber($segP, 119, 126, "Data do Juros de Mora");
	isNumber($segP, 127, 141, "Juros de Mora por Dia/Taxa");
	isNumber($segP, 142, 142, "Cód do Desconto 1");
	isNumber($segP, 143, 150, "Data do Desconto");
	isNumber($segP, 151, 165, "Valor/Percentual a ser Concedido");
	isNumber($segP, 166, 180, "Valor do IOF a ser Recolhido");
	isNumber($segP, 181, 195, "Valor do Abatimento");
	isNumber($segP, 221, 221, "Cód para Protesto");
	isNumber($segP, 222, 223, "Número de dias de para Protesto");
	isNumber($segP, 224, 224, "Cód para Baixa/Devolução");
	isNumber($segP, 228, 229, "Cód da Moeda");
	isNumber($segP, 230, 239, "Uso Exclusivo CAIXA");
}

//segmento Q
function segmentoQ($segQ){
echo "<br>Segmento Q<br>";
	imprimirLinha($segQ);
	$valor = $segQ[15].$segQ[16];
	if(($valor == 36) or ($valor == 37) or ($valor == 38)){
			//segmentu Q  para cód de movimento 36, 37, 38 (banco de sacados)
			isNumber($segQ, 1, 3,"Cód do Banco na Compensação","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 4, 7,"Lote de Serviço","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 8, 8,"Tipo de Registro","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 9, 13,"Nº Sequencial do Registro no Lote","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 14, 14,"Cód segmento do registro detalhe","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 16, 17,"Cód de Movimento Remessa","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 18, 18,"Tipo de Inscrição","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 19, 33,"Numero de Inscrição","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 129, 133,"CEP","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 134, 136,"Sufixo do CEP","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 154, 156,"Numero do Banco de Sacados","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 192, 192,"Identificação Manutenção","Segmento Q com Cód Movimento 36,37,38");
			isNumber($segQ, 193, 207,"Valor do Titulo","Segmento Q com Cód Movimento 35,36,37");
	}else{	
			//Segmento Q obrigatório
			isNumber($segQ, 1, 3, "Código do Banco na Compensação", "Segmento Q");
			isNumber($segQ, 4, 7, "Lote de Serviço","Segmento Q");
			isNumber($segQ, 8, 8, "Tipo de Registro","Segmento Q");
			isNumber($segQ, 9, 13, "Nº Sequencial do registro no Lote","Segmento Q");
			isNumber($segQ, 16, 17, "Cód de Movimento Remessa","Segmento Q");
			isNumber($segQ, 18, 18, "Tipo de Inscrição","Segmento Q");
			isNumber($segQ, 19, 33, "Número de Inscrição","Segmento Q");
			isNumber($segQ, 129, 133, "CEP","Segmento Q");
			isNumber($segQ, 134, 136, "Sufuxo do CEP","Segmento Q");
			isNumber($segQ, 154, 154, "Tipo de Inscrição","Segmento Q");
			isNumber($segQ, 155, 169, "Número de Inscrição","Segmento Q");
			isNumber($segQ, 210, 212, "Cód Bco. Correspe. na Compensação","Segmento Q");
	}		
}

?>