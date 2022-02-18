<?php
// altera nome do arquivo validado ou importado
function alteraNomeArq($arqName,$tipo){
		$path = "../../".getSession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/arqbanco/sicob/assisten/";
		$nomeAntigo = $path.$arqName;
		$data = date('d').date('m')."20".date('y').date('h').date('i').date('s');
		$nomeNovo = $tipo.$data."_".$arqName;
		$_SESSION['novoNome'] = $nomeNovo;
		rename ($nomeAntigo, $path.$nomeNovo);
		$arquivo = $path.$nomeNovo;
		return $arquivo;
}

//verifica tipo de linha
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
		case "P":
			//segmentoP($seg);
			break;
		case "Q":
			//segmentoQ($seg);
			break;		
		default: 
			echo "N�o foi encontrado Segmento Obrigat�rio ou Segmento Opcional"; 
	}	
}

//imprime a linha completa
function imprimirLinha($linha){
	$linhaCompl = "";
	$p=1;
	for($z=0; $z< count($linha); $z++){
			$linhaCompl.= $linha[$z];
			$p++;
			if($p>75){
				$linhaCompl.= "\n";
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

//fun��o para verifica se � numero no array
function isNumber($var, $ini, $fim, $campo){
		$erro=""; $result = "";
		$valor = "";
		$ini = $ini -1; $fim = $fim -1;
		for($x= $ini; $x<= $fim; $x++){
					/*if(trim($var[$x])==""){
							$var[$x] = "0";
					}		*/
					trim($var[$x]);
					if(!ctype_digit($var[$x])){//verifica se os caracteres s�o numericos
					$valor .=($x+1).",";
					$erro ="<font color='#FF0000'> Esperava-se somente Numeros e veio '<b>".$var[$x]."</b>'"."
							 na(s) posi��o(�es) <b>".$valor."</b></font>";
				}else{
						$result.= $var[$x];
					 }	
		}
		if ($erro <> ""){
			$_SESSION['ValidaErro'] = "Erro no Arquivo";
			return $erro;
		}else if($erro == ""){
			return $result;
		}
}

// header de Arquivo
function headerArquivo($headerA){
	$_SESSION['ArqValida_linhaHeaderHA'] = imprimirLinha($headerA);
	$_SESSION['ArqValida_codBancoHA'] = isNumber($headerA, 1, 3,"C�d Banco");
	$_SESSION['ArqValida_loteServicoHA'] = isNumber($headerA, 4, 7, "Lote de Servico");
	$_SESSION['ArqValida_tipoRegistroHA'] = isNumber($headerA, 8, 8, "Tipo de Registro");
	$_SESSION['ArqValida_tipoIncEmpHA'] = isNumber($headerA, 18, 18, "Tipo de inscri��o da Empresa");
	$_SESSION['ArqValida_numIncEmpHA'] = isNumber($headerA, 19, 32, "Numero de inscri��o da Empresa");
	$_SESSION['ArqValida_codConvBancoHA'] =	isNumber($headerA, 33, 48, "C�digo Conv�nio no Banco");
	$_SESSION['ArqValida_ageMantContaHA'] = isNumber($headerA, 53, 57, "Ag�ncia mantenedora da Conta");
	$_SESSION['ArqValida_digVerAgeHA'] = isNumber($headerA, 58, 58,"Dig Verificador Agencia");
	$_SESSION['ArqValida_codCedenteHA'] = isNumber($headerA, 59, 70, "Codigo do cedente");
	$_SESSION['ArqValida_digVerCedHA'] = isNumber($headerA, 71, 71, "D�g. Verif. Cedente");
	$_SESSION['ArqValida_digVerAgCedHA'] = isNumber($headerA, 72, 72, "D�g. Verif. Ag/Ced");
	$_SESSION['ArqValida_codRemRetHA'] = isNumber($headerA, 143, 143, "C�digo Remessa/Retorno");
	$_SESSION['ArqValida_dtGerArqHA'] = isNumber($headerA, 144, 151, "Data de gera��o do Arquivo");
	$_SESSION['ArqValida_hsGerArqHA'] = isNumber($headerA, 152, 157, "Hora de gera��o do Arquivo");
	$_SESSION['ArqValida_numSeqArqHA'] = isNumber($headerA, 158, 163, "Numero sequencial do arquivo");
	$_SESSION['ArqValida_numVerLayArqHA'] = isNumber($headerA, 164, 166, "N� da vers�o do layout do Arquivo");
	$_SESSION['ArqValida_denGerArqHA'] = isNumber($headerA, 167, 171, "Densidade da gera��o do arquivo");
	
	//get dos campos ALFANUMERICOS	
	$_SESSION['ArqValida_AFAusoCAIXA1HA'] = isAlfa($headerA, 49, 52);	
	$_SESSION['ArqValida_AFAusoExclFeb1HA'] = isAlfa($headerA, 9, 17);
	$_SESSION['ArqValida_AFAnomEmpHA'] = isAlfa($headerA, 73, 102);
	$_SESSION['ArqValida_AFAnomBanHA'] = isAlfa($headerA, 103, 132);	
	$_SESSION['ArqValida_AFAusoExclFeb2HA'] = isAlfa($headerA, 30, 35);
	$_SESSION['ArqValida_AFAusoResBanHA'] = isAlfa($headerA, 172, 191);
	$_SESSION['ArqValida_AFAusoResEmpHA'] = isAlfa($headerA, 192, 211);
	$_SESSION['ArqValida_AFAusoExclFeb3HA'] = isAlfa($headerA, 212, 240);
	
}

//header de Lote
function headerLote($headerL){
	$_SESSION['ArqValida_linhaHeaderL'] = imprimirLinha($headerL);
/*1*/	$_SESSION['ArqValida_codBanHL'] = isNumber($headerL, 1, 3, "C�d do Banco na compensa��o");
/*2*/	$_SESSION['ArqValida_lotSerHL'] = isNumber($headerL, 4, 7, "Lote de Servi�o");
/*3*/	$_SESSION['ArqValida_tipRegHL'] = isNumber($headerL, 8, 8, "Tipo de Registro");
/*5*/	$_SESSION['ArqValida_tipServHL'] = isNumber($headerL, 10, 11, "Tipo de Servi�o");
/*7*/	$_SESSION['ArqValida_numVerLayLotHL'] = isNumber($headerL, 14, 16, "N� da vers�o do layout do lote");
/*9*/	$_SESSION['ArqValida_tipInsEmpHL'] = isNumber($headerL, 18, 18, "Tipo de inscri��o da Empresa");
/*10*/	$_SESSION['ArqValida_insEmpHL'] = isNumber($headerL, 19, 33, "N� de inscri��o da Empresa");
/*13*/	$_SESSION['ArqValida_ageManConHL'] = isNumber($headerL, 54, 58, "Ag�ncia Mantenedora da Conta");
/*14*/	$_SESSION['ArqValida_digVerAgHL'] = isNumber($headerL, 59, 59,"Dig. Verificador da Ag");	
/*15*/	$_SESSION['ArqValida_numConCorrHL'] = isNumber($headerL, 60, 71, "Num Conta Corrente");	
/*16*/	$_SESSION['ArqValida_digVerConHL'] = isNumber($headerL, 72, 72, "D�g Verificador Conta");	
/*17*/	$_SESSION['ArqValida_digVerAgCedHL'] = isNumber($headerL, 73, 73, "dig Verif. Ag./Ced(sem op)");
/*21*/	$_SESSION['ArqValida_numRemRetHL'] = isNumber($headerL, 184, 191, "N�mero Remessa/Retorno");
/*22*/	$_SESSION['ArqValida_dtRemRetHL'] = isNumber($headerL, 192, 199, "Data grava��o Remessa/Retorno");
/*23*/	$_SESSION['ArqValida_dtCreHL'] = isNumber($headerL, 200, 207, "Data do Cr�dito");
	
	//get dos campos ALFANUMERICOS	
/*4*/	$_SESSION['ArqValida_AFAtipOperHL'] = isAlfa($headerL, 9, 9);
/*6*/	$_SESSION['ArqValida_AFAusoExcFeb1HL'] = isAlfa($headerL, 12, 13);
/*8*/	$_SESSION['ArqValida_AFAusoExcFebHL'] = isAlfa($headerL, 17, 17);
/*11*/	$_SESSION['ArqValida_AFAcodConBanHL'] = isAlfa($headerL, 34, 49);
/*12*/$_SESSION['ArqValida_AFAusoExcCaiHL'] = isAlfa($headerL, 50, 53);
/*18*/	$_SESSION['ArqValida_AFAnomEmpHL'] = isAlfa($headerL, 74, 103);
/*19*/	$_SESSION['ArqValida_AFAmens1HL'] = isAlfa($headerL, 104, 143);
/*20*/	$_SESSION['ArqValida_AFAmens2pHL'] = isAlfa($headerL, 144, 183);
/*24*/	$_SESSION['ArqValida_AFAusoExcFeb2pHL'] = isAlfa($headerL, 208, 240);	
}

//traile Lote
function traileLote($traileL){
	
	$_SESSION['ArqValida_linhaTrailerL'] = imprimirLinha($traileL);
	// verificando se os campos s�o numeros
/*1*/	$_SESSION['ArqValida_codBanTL'] = isNumber($traileL, 1, 3, "C�d do Banco na Compensa��o");
/*2*/	$_SESSION['ArqValida_lotSerTL'] = isNumber($traileL, 4, 7, "Lote de Servi�o");
/*3*/	$_SESSION['ArqValida_tipSerTL'] = isNumber($traileL, 8, 8, "Tipo de registro");
/*5*/	$_SESSION['ArqValida_quanRegLotTL'] =isNumber($traileL, 18, 23, "Quantidade de Registros no Lote");
/*6*/	$_SESSION['ArqValida_quanTCobr1TL'] = isNumber($traileL, 24, 29, "Quantidade de Titulos em Cobran�a");
/*7*/	$_SESSION['ArqValida_valTotTitCarTL'] = isNumber($traileL, 30, 46, "Valor Total dos T�tulos em Carteiras");

/*10*/	$_SESSION['ArqValida_quanTitCobr2TL'] = isNumber($traileL, 70, 75, "Quantidade de Titulos em Cobran�a");
/*11*/	$_SESSION['ArqValida_valTotTitCar3TL'] = isNumber($traileL, 76, 92, "Quantidade de Titulos em Carteiras");
/*12*/	$_SESSION['ArqValida_valTotTitCobr3TL'] = isNumber($traileL, 93, 98, "Quantidade de Titulos em Cobran�a");
/*13*/	$_SESSION['ArqValida_valTotTitCar2TL'] = isNumber($traileL,99,115,"Valor Total dos T�tulos em Carteiras");
		//get dos campos ALFANUMERICOS	
/*4*/	$_SESSION['ArqValida_AFAusoExclFeb1TL'] = isAlfa($traileL, 9, 17);
/*8*/	$_SESSION['ArqValida_usoExclFeb1TL'] = isAlfa($traileL, 47, 52);
/*9*/	$_SESSION['ArqValida_usoExclFeb2TL'] = isAlfa($traileL, 53, 69);
/*14*/	$_SESSION['ArqValida_AFAusoExclFeb2TL'] = isAlfa($traileL, 116, 123);
/*15*/	$_SESSION['ArqValida_AFAusoExclFeb3TL'] = isAlfa($traileL, 124, 240);
}

//traile arquivo
function traileArquivo($traileA){
	
	$_SESSION['ArqValida_linhaTrailerA'] = imprimirLinha($traileA);
	// verificando se os campos s�o numeros e grava na sess�o para ser impresso
	$_SESSION['ArqValida_codBanCompTA'] = isNumber($traileA, 1, 3, "C�digo do Banco na Compensa��o","Traile de Arquivo");
	$_SESSION['ArqValida_lotServTA'] = isNumber($traileA, 4, 7, "Lote de Servi�o","Traile de Arquivo");
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
/*1*/ $_SESSION['ArqValida_codBanComST'.$r] = isNumber($segT, 1, 3, "C�d do Banco na Compensa��o");
/*2*/ $_SESSION['ArqValida_lotSerST'.$r] = isNumber($segT, 4, 7, "Lote de Servi�o");
/*3*/ $_SESSION['ArqValida_tipRegST'.$r] = isNumber($segT, 8, 8, "Tipo de Registro");
/*4*/ $_SESSION['ArqValida_numSecRegLotST'.$r] = isNumber($segT, 9, 13, "N�mero Sequencial Registro de Lote");
/*7*/ $_SESSION['ArqValida_codMovRetST'.$r] = isNumber($segT, 16, 17, "C�digo de Movimento Retorno");
/*8*/ $_SESSION['ArqValida_agMantCon'.$r] = isNumber($segT, 18, 22, "Ag�ncia Mantenedora da Conta");
/*9*/ $_SESSION['ArqValida_digVeriAge'.$r] = isNumber($segT, 23, 23, "D�g. Verifi. da Ag");
/*10*/$_SESSION['ArqValida_codCede'.$r] = isNumber($segT, 24, 35, "C�d do cedente");
/*11*/$_SESSION['ArqValida_digVerCedeST'.$r] = isNumber($segT, 36, 36, "Dig. ver. Cedente");
/*12*/$_SESSION['ArqValida_digVerAgeCed'.$r] = isNumber($segT, 37, 37, "dig. ver. ag./ced");
/*14*/ $_SESSION['ArqValida_ideTitST'.$r] = isNumber($segT, 47, 57, "Identifica��o do Titulo");
/*17*/ $_SESSION['ArqValida_codCartST'.$r] = isNumber($segT, 58, 58, "C�d da Carteira");
/*20*/ $_SESSION['ArqValida_dtVenTituST'.$r] = isNumber($segT, 74, 81, "Data de Vencimento do Titulo");
/*21*/ $_SESSION['ArqValida_valNomTitST'.$r] = isNumber($segT, 82, 96, "Valor Nominal do Titulo");
/*22*/ $_SESSION['ArqValida_numBanST'.$r] = isNumber($segT, 97, 99, "Num do Banco");
/*23*/ $_SESSION['ArqValida_AgeCobRebST'.$r] = isNumber($segT, 100, 104, "Agencia Cobr/Receb");
/*24*/ $_SESSION['ArqValida_digVerAgeST'.$r] = isNumber($segT, 105, 105, "Digito Verificador da Ag�ncia Cobr/Rec");
/*26*/ $_SESSION['ArqValida_codMoeST'.$r] = isNumber($segT, 131, 132, "C�d da Moeda");
/*27*/ $_SESSION['ArqValida_tipInsST'.$r] = isNumber($segT, 133, 133, "Tipo de Inscri��o");
/*28*/ $_SESSION['ArqValida_numInsST'.$r] = isNumber($segT, 134, 148, "Numero de Inscri��o");
/*31*/ $_SESSION['ArqValida_valTarCusST'.$r] = isNumber($segT, 199, 213, "Valor da Tarifa/Custas");
/*32*/ $_SESSION['ArqValida_ideRejTafCusLiqBaiST'.$r] = isNumber($segT, 214, 223,"Ident. para Rejei...");

	//get dos campos ALFANUMERICOS	
/*5*/ $_SESSION['ArqValida_AFAcodSegRegDetST'.$r] = isAlfa($segT, 14, 14);
/*6*/ $_SESSION['ArqValida_AFAusoExcFeb1ST'.$r] = isAlfa($segT, 15, 15);
/*13*/$_SESSION['ArqValida_AFAusoExcCai2ST'.$r] = isAlfa($segT, 38, 46);
/*18*/ $_SESSION['ArqValida_AFAnumDocCobST'.$r] = isAlfa($segT, 59, 69);
/*19*/ $_SESSION['ArqValida_AFAusoExclCaiST'.$r] = isAlfa($segT, 70, 73);
/*25*/ $_SESSION['ArqValida_AFAidenTituEmpST'.$r] = isAlfa($segT, 106, 130);
/*29*/ $_SESSION['ArqValida_AFAnomeSacST'.$r] = isAlfa($segT, 149, 188);
/*30*/ $_SESSION['ArqValida_AFAusoExcFeb2ST'.$r] = isAlfa($segT, 189, 198);
/*33*/ $_SESSION['ArqValida_AFAusoExcFeb3ST'.$r] = isAlfa($segT, 224, 240);

}

//segmento U
function segmentoU($segU){
		@$_SESSION['ArqValida_chave'] = $_SESSION['ArqValida_chave'] + 1;
		$y = $_SESSION['ArqValida_chave'];
		$_SESSION['ArqValida_linhaSegmU'.$y] = imprimirLinha($segU);
		$_SESSION['ArqValida_codBanComSU'.$y] = isNumber($segU, 1, 3, "C�digo do Banco na Compensa��o");
		$_SESSION['ArqValida_lotSerSU'.$y] = isNumber($segU, 4, 7, "Lote de Servi�o");
		$_SESSION['ArqValida_tipRegSU'.$y] = isNumber($segU, 8, 8, "Tipo de Registro");
		$_SESSION['ArqValida_numSeqRegDetSU'.$y] = isNumber($segU, 9, 13, "N� Sequencial do registro no Lote");
		$_SESSION['ArqValida_codMovRetSU'.$y] = isNumber($segU, 16, 17, "C�d de Movimento Retorno");
		$_SESSION['ArqValida_jurMulEncSU'.$y] = isNumber($segU, 18, 32, "Juros / Multa / Encargos");
		$_SESSION['ArqValida_valDesConSU'.$y] = isNumber($segU, 33, 47, "Valor do desconto Concedido");
		$_SESSION['ArqValida_valAbtConCanSU'.$y] = isNumber($segU, 48, 62, "Valor do Abat. Concedido/Cancel.");
		$_SESSION['ArqValida_valiofRecSU'.$y] = isNumber($segU, 63, 77, "Valor do IOF Recolhido");
		$_SESSION['ArqValida_valpagSacSU'.$y] = isNumber($segU, 78, 92, "Valor pago pelo Sacado");
		$_SESSION['ArqValida_valliqCreSU'.$y] = isNumber($segU, 93, 107, "Valor liquido a ser Creditado");
		$_SESSION['ArqValida_valOutDesSU'.$y] = isNumber($segU, 108, 122, "Valor de Outras Despesas");
		$_SESSION['ArqValida_valOutCreSU'.$y] = isNumber($segU, 123, 137, "Valor de Outros Cr�ditos");
		$_SESSION['ArqValida_dtOcoSU'.$y] = isNumber($segU, 138, 145, "Data da Ocorr�ncia");
		$_SESSION['ArqValida_dtEfeCreSU'.$y] = isNumber($segU, 146, 153, "Data da Efetiva��o do Cr�dito");
		$_SESSION['ArqValida_dtDebTarSU'.$y] = isNumber($segU, 154, 161, "Data do D�bito da Tarifa");
		
		//no novo docuemto diz que � num no antigo �ra alfa.. deixado alfa devido a espa�os no arquivo
		$_SESSION['ArqValida_usoExcFebSU'.$y] = isAlfa($segU, 162, 240);//, "Uso Exclusivo FEBRABAN/CNAB"
		
		// verifica��o de alfanumericos
		$_SESSION['ArqValida_ALFAcodSegRegDetSU'.$y] = isAlfa($segU,14,14);
		$_SESSION['ArqValida_ALFAusoExcFebSU'.$y] = isAlfa($segU,15,15);
}

//segmento P
function segmentoP($segP){
	echo "<br>Segmento P<br>";
	imprimirLinha($segP);
	isNumber($segP, 1, 3, "C�d do Banco na Compensa��o");
	isNumber($segP, 4, 7, "Lote de Servi�o");
	isNumber($segP, 8, 8, "Tipo de Registro");
	isNumber($segP, 9, 13, "N� Sequencial do Registro no Lote");
	isNumber($segP, 16, 17, "C�d de Movimento Remessa");
	isNumber($segP, 18, 22, "Ag�ncia Mantenedora da Conta");
	isNumber($segP, 24, 29, "C�d do Conv�nio no Banco");
	isNumber($segP, 30, 40, "Uso Exclusivo da CAIXA");
	isNumber($segP, 41, 42, "Modalidade da Carteira");
	isNumber($segP, 43, 57, "Identifica��o do Titulo no Banco");
	isNumber($segP, 58, 58, "C�digo da acarteira");
	isNumber($segP, 59, 59, "Forma de Cadastr. do T�tulo no Banco");
	isNumber($segP, 61, 61, "Identifica��o da Entrega do bloueto");
	isNumber($segP, 78, 85, "Data de Vencimento do T�tulo");
	isNumber($segP, 86, 100, "Valor Nominal do T�tulo");
	isNumber($segP, 101, 105, "Ag�ncia Encarregada da Cobran�a");
	isNumber($segP, 107, 108, "Esp�cie do T�tulo");
	isNumber($segP, 110, 117, "Data da Emiss�o do T�tulo");
	isNumber($segP, 118, 118, "C�d do Juros de Mora");
	isNumber($segP, 119, 126, "Data do Juros de Mora");
	isNumber($segP, 127, 141, "Juros de Mora por Dia/Taxa");
	isNumber($segP, 142, 142, "C�d do Desconto 1");
	isNumber($segP, 143, 150, "Data do Desconto");
	isNumber($segP, 151, 165, "Valor/Percentual a ser Concedido");
	isNumber($segP, 166, 180, "Valor do IOF a ser Recolhido");
	isNumber($segP, 181, 195, "Valor do Abatimento");
	isNumber($segP, 221, 221, "C�d para Protesto");
	isNumber($segP, 222, 223, "N�mero de dias de para Protesto");
	isNumber($segP, 224, 224, "C�d para Baixa/Devolu��o");
	isNumber($segP, 228, 229, "C�d da Moeda");
	isNumber($segP, 230, 239, "Uso Exclusivo CAIXA");
}

//segmento Q
function segmentoQ($segQ){
	echo "<br>Segmento Q<br>";
	imprimirLinha($segQ);
	$valor = $segQ[15].$segQ[16];
	if(($valor == 36) or ($valor == 37) or ($valor == 38)){
			//segmentu Q  para c�d de movimento 36, 37, 38 (banco de sacados)
			isNumber($segQ, 1, 3,"C�d do Banco na Compensa��o","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 4, 7,"Lote de Servi�o","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 8, 8,"Tipo de Registro","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 9, 13,"N� Sequencial do Registro no Lote","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 14, 14,"C�d segmento do registro detalhe","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 16, 17,"C�d de Movimento Remessa","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 18, 18,"Tipo de Inscri��o","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 19, 33,"Numero de Inscri��o","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 129, 133,"CEP","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 134, 136,"Sufixo do CEP","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 154, 156,"Numero do Banco de Sacados","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 192, 192,"Identifica��o Manuten��o","Segmento Q com C�d Movimento 36,37,38");
			isNumber($segQ, 193, 207,"Valor do Titulo","Segmento Q com C�d Movimento 35,36,37");
	}else{	
			//Segmento Q obrigat�rio
			isNumber($segQ, 1, 3, "C�digo do Banco na Compensa��o", "Segmento Q");
			isNumber($segQ, 4, 7, "Lote de Servi�o","Segmento Q");
			isNumber($segQ, 8, 8, "Tipo de Registro","Segmento Q");
			isNumber($segQ, 9, 13, "N� Sequencial do registro no Lote","Segmento Q");
			isNumber($segQ, 16, 17, "C�d de Movimento Remessa","Segmento Q");
			isNumber($segQ, 18, 18, "Tipo de Inscri��o","Segmento Q");
			isNumber($segQ, 19, 33, "N�mero de Inscri��o","Segmento Q");
			isNumber($segQ, 129, 133, "CEP","Segmento Q");
			isNumber($segQ, 134, 136, "Sufuxo do CEP","Segmento Q");
			isNumber($segQ, 154, 154, "Tipo de Inscri��o","Segmento Q");
			isNumber($segQ, 155, 169, "N�mero de Inscri��o","Segmento Q");
			isNumber($segQ, 210, 212, "C�d Bco. Correspe. na Compensa��o","Segmento Q");
	}		
}

?>