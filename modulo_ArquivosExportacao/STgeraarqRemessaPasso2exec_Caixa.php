<?php 
/***           		   INCLUDES                   ***/
/****************************************************/
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$dateDtEmissaoIni = request("var_dt_emissao_ini");
$dateDtEmissaoFim = request("var_dt_emissao_fim");
$dateDtVctoIni = request("var_dt_vcto_ini");
$dateDtVctoFim = request("var_dt_vcto_fim");
$strHistorico = request("var_historico");
$strTipoDocumento = request("var_tipo_documento");


function dias_ano(){
    $data_inicio = new DateTime(date("y")."-01-01");
    $data_fim = new DateTime(date("y-m-d"));

    // Resgata diferença entre as datas
    $dateInterval = $data_inicio->diff($data_fim);
    return $dateInterval->days;
}

function modulo_11($num, $base=9, $r=0)  {
    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */                                        

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1){
        $resto = $soma % 11;
        return $resto;
    }
}

function digitoVerificador_nossonumero($numero) {
	$resto2 = modulo_11($numero, 7, 1);
    $digito = 11 - $resto2;
    if ($digito == 10) {
        $dv = "P";
    } elseif($digito == 11) {
     	$dv = 0;
	} else {
        $dv = $digito;
     	}
	return $dv;
}

function GeraLinhaHeaderArquivo($prNomeEmpresa, $prNomeBanco, $prDia, $prMes, $prAno, $prNumSeqRemessa, $prCNPJ, $prAgencia, $strAgenciaDV, $prConvenio, $prCodigoSindical) {
	$strLinha = "";
	
	$strLinha .= "104";                                          				// [1 - 3] Controle - Banco     - Código do Banco na Compensação
	$strLinha .= "0000";                                        	 			// [4 - 7] Controle - Lote  	- Lote de Serviço
	$strLinha .= "0";	                                    	     			// [8 - 8] Controle - Registro  - Tipo de Registro
	$strLinha .= str_pad("", 9);	                    	         			// [9 - 17] CNAB     - Uso Exclusivo Febraban CNAB
	$strLinha .= "2";	                            	             			// Empresa  - Inscrição - Tipo Inscrição da Empresa (1 - CPF / 2 - CNPJ)
	$strLinha .= str_pad($prCNPJ, 14, "0", STR_PAD_LEFT);   					// Empresa  - Inscrição - Número Inscrição da Empresa (CNPJ)
	$strLinha .= str_pad("0", 20 , "0");                                    	// Empresa  - Uso Exclusivo CAIXA
	$strLinha .= str_pad($prAgencia, 5, "0", STR_PAD_LEFT);  					// Empresa  - Agência da Conta
	$strLinha .= str_pad($strAgenciaDV,1);   									// Empresa  - DV Agência da Conta (pág 75 - G009)	
	$strLinha .= str_pad($prConvenio,7, "0", STR_PAD_LEFT);						// Empresa  - Código do Convênio Beneficiário
	$strLinha .= "0000000";			                           	  				// Empresa  - Uso Exclusivo CAIXA
	$strLinha .= "0";				                           		  			// Empresa  - Uso Exclusivo CAIXA	
	$strLinha .= str_pad(substr(removeAcento($prNomeEmpresa), 0, 30), 30);		// Empresa  - Nome da Empresa
	$strLinha .= str_pad($prNomeBanco,30);			    	        			// Banco	- Nome do banco
	$strLinha .= str_pad("",10);	                            	 			// CNAB     - Uso Exclusivo Febraban CNAB
	$strLinha .= "1";								 							// Arquivo  - Código da Remessa - (remessa cliente -> banco = 1)
	$strLinha .= str_pad($prDia.$prMes.$prAno,8);							    // Arquivo  - Data Geração do Arquivo - DDMMAAAA
	$strLinha .= str_pad(date('His', time()),6);								// Arquivo  - Hora Geração do Arquivo - HHMMSS
	$strLinha .= str_pad($prNumSeqRemessa,6,"0",STR_PAD_LEFT);							    	// Arquivo  - Número Sequencial do Arquivo (pág 78 - G018)	
	$strLinha .= "101";															// Arquivo  - Número Versão Layout do Arquivo			
	$strLinha .= "00000";												// Arquivo  - Densidade Gravação do Arquivo (pág 78 - G020)
	$strLinha .= "1";															// Entidade - Tipo Entidade Sindical (1 - Sindicado / 2 = Feder. / 3 = Confed. / 5 = Central Sind)
	$strLinha .= str_pad($prCodigoSindical, 5, "0", STR_PAD_LEFT);   			// Empresa  - Código Sindical (pág 73 - C106)
	$strLinha .= str_pad("REMESSA-PRODUCAO", 20);									// Reservado- Situação Remessa (Alterar para "REMESSA-PRODUÇÃO" quando finalizado testes mas não pode voltar para TESTES depois disso)
	$strLinha .= str_pad("",4);         					   	        	    // Versão Aplicativo instalado no cliente (pág 67 - C077)	
	$strLinha .= str_pad("",38);	                            	 			// CNAB     - Uso Exclusivo Febraban CNAB	
	
	
	return $strLinha;
}

function             GeraLinhaHeaderLote( $prNomeEmpresa , $prDia, $prMes, $prAno, $prCnpj , $prCodigoSindical , $prAgencia , $strAgenciaDV, $prConvenio , $prIdenficadorSequencial , $prNroRemessa  , $prMensagem1 , $prMensagem2 ){
//GeraLinhaHeaderLote( $prNomeEmpresa, $prDia, $prMes, $prAno, $prCnpj, $prCodigoSindical, $prAgencia, $strAgenciaDV, $prConvenio, $prNroRemessa, $prMensagem1, $prMensagem2) {

/*
echo "<br>Nome: ".$prNomeEmpresa    ;
echo "<br>dia: ".$prDia            ;
echo "<br>mes: ".$prMes            ;
echo "<br>ano: ".$prAno            ;
echo "<br>cnpj: ".$prCnpj           ;
echo "<br>cod sindical: ".$prCodigoSindical ;
echo "<br>agencia; ".$prAgencia        ;
echo "<br>DV: ".$strAgenciaDV     ;
echo "<br>Convenio: ".$prConvenio       ;
echo "<br>NroRemessa: ".$prNroRemessa     ;
echo "<br>Msg1: ".$prMensagem1	  ;
echo "<br>Msg2: ".$prMensagem2      ;
*/
	$strLinha = "";
	
	$strLinha .= "104";                                          				// [1-3]     Controle - Banco     - Código do Banco na Compensação
	$strLinha .= "0001";				                                        // [4-7]     Controle - Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "1";	                                    	     			// [8-8]     Controle - Registro  - Tipo de Registro
	$strLinha .= "R";	                                    	     			// [9-9]     Serviço  - Operação  - Tipo de Operação "R" - Remessa, "T" - Retorno
	$strLinha .= "01";	                                    	     			// [10-11]   Serviço  - Serviço   - Tipo de Serviço: 01 - Cobr. Registrada, 03 - Desconto Tít., 04 - Caução Tít.
	$strLinha .= "00";				                    	         			// [12-13]   CNAB     - Uso Exclusivo Febraban CNAB
	$strLinha .= "060";															// [14-16]   Arquivo  - Número Versão Layout do Lote
	$strLinha .= str_pad("", 1);	                    	         			// [17-17]   CNAB     - Uso Exclusivo Febraban CNAB	
	$strLinha .= "2";	                            	             			// [18-18]   Empresa  - Inscrição - Tipo Inscrição da Empresa (1 - CPF / 2 - CNPJ)
	$strLinha .= str_pad($prCnpj, 14, "0", STR_PAD_LEFT);					   	// [19-32]   Empresa  - Inscrição - Número Inscrição da Empresa (CNPJ)
	$strLinha .= str_pad($prConvenio,7, "0", STR_PAD_LEFT);						// [33-39]   Empresa  - Código do Convênio Beneficiário*
	$strLinha .= str_pad("0", 15 , "0");                                    	// [40-54]   Empresa  - Uso Exclusivo CAIXA	
	$strLinha .= str_pad($prAgencia, 5, "0", STR_PAD_LEFT);  	  	            // [55-59]   Empresa  - Agência da Conta
	$strLinha .= str_pad($strAgenciaDV,1);   						            // [60-60]   Empresa  - DV Agência da Conta	(pág 75 - G011)	
	$strLinha .= str_pad($prConvenio,7, "0", STR_PAD_LEFT);				        // [61-67]   Empresa  - Código do Convênio Beneficiário *2x
	$strLinha .= "1";															// [68-68]   Entidade - Tipo Entidade Sindical (1 - Sindicado / 2 = Feder. / 3 = Confed. / 5 = Central Sind)
	$strLinha .= str_pad($prCodigoSindical, 5, "0", STR_PAD_LEFT);   	        // [69-73]   Empresa  - Código Sindical (pág 103 - C106)
	$strLinha .= "00";				                    	         			// [74-75]   Empresa  - Uso Exclusivo Febraban CNAB	
    $strLinha .= str_pad(substr(removeAcento($prNomeEmpresa), 0, 30), 30);		// [76-105]  Empresa  - Nome da Empresa
	$strLinha .= str_pad($prMensagem1,40);										// [106-145] Info 1 	- Mensagem 1 - Instruções da Ficha de Compensação
	$strLinha .= str_pad($prMensagem2,40);										// [146-185] Info 1 	- Mensagem 2 - Instruções da Ficha de Compensação	
	$strLinha .= str_pad($prIdenficadorSequencial,12); 									// [186-197] Controle - Número Remessa (pág 84 - G079)
	$strLinha .= str_pad($prDia.$prMes.$prAno,8);							    // [198-205] Controle - Data Gravação Remessa - DDMMAAAA	
	$strLinha .= "00000000";							    					// [206-216] Controle - Data do Crédito - DDMMAAAA			
	$strLinha .= str_pad("",27);	                            	 			// [214-240] CNAB     - Uso Exclusivo Febraban CNAB		
			
	return $strLinha;
}
function GeraLinhaTipo3SegmentoP($prContLinhas,$prCont, $prAgencia, $prAgenciaDV, $prConvenio,  $prNossoNumero, $prMesAnoCompetencia, $prDiaVcto, $prVlrTitulo, $prDiaEmissao, $prDiasBaixa)
 {
	$strLinha = "";

	$strLinha .= "104";                                          				// Controle 		- Banco     - Código do Banco na Compensação
	$strLinha .= str_pad($prCont, 4, "0", STR_PAD_LEFT);							// Controle 		- Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "3";	                                    	     			// Controle 		- Registro  - Tipo de Registro	
	$strLinha .= str_pad($prContLinhas,5,"0",STR_PAD_LEFT);											// Serviço  		- Número Sequencial do Registro no Lote (pág 80 - G038)		
	$strLinha .= "P";	                                    	     			// Serviço  		- Segmento  - Cód Segmento
	$strLinha .= str_pad("",1);		                            	 			// CNAB     		- Uso Exclusivo Febraban CNAB			
	$strLinha .= "01";				                            	 			// Cód Mov  		- Cód Movimento de Remessa (pág 44 - C004) (01 - Incluir titulo)
	$strLinha .= str_pad($prAgencia, 5, "0", STR_PAD_LEFT);  					// Benefic. 		 - Agência da Conta
	$strLinha .= str_pad($prAgenciaDV,1);   									// Benefic  		- DV Agência da Conta (pág 75 - G009)	
	$strLinha .= str_pad($prConvenio,7, "0", STR_PAD_LEFT);						// Benefic  		- Código do Convênio Beneficiário
	$strLinha .= "0000000";			                           	  				// Benefic  		- Uso Exclusivo CAIXA
	$strLinha .= "000";			                     	      	  				// CAIXA			- Uso Exclusivo CAIXA
//	$strLinha .= "01";															// Carteira 		- Modalidade Carteira (pág 82 - G069)
//	$strLinha .= str_pad($prNossoNumero,15, "0", STR_PAD_LEFT);					// Carteira 		- Identificação do titulo no banco  (pág 82 - G069)
	$strLinha .= "00";															// Carteira 		- Modalidade Carteira (pág 82 - G069)
	$strLinha .= str_pad("0",15, "0", STR_PAD_LEFT);					// Carteira 		- Identificação do titulo no banco  (pág 82 - G069)
	$strLinha .= "1";					  										// Caracteristica 	- Código da Carteira 
	$strLinha .= "1";					  										// Caracteristica 	- Forma cadastramento do titulo no banco
	$strLinha .= "2";					  										// Cobrança			- Tipo de documento
	$strLinha .= "2";					  										// Cobrança			- Identificação Emissão (1 - pela caixa / 2 - pelo beneficiario)
	$strLinha .= "0";					  										// Cobrança			- Identificação entrega do boleto (0 - beneficiario / 1 - pela caixa)
	$strLinha .= str_pad($prMesAnoCompetencia,11,"0", STR_PAD_LEFT);								// No Documento		- No documento de cobranca (pág 45 - C011)
	$strLinha .= str_pad("",4);		                            	 			// Exclusivo	    - Uso Exclusivo CAIXA
	$strLinha .= str_pad($prDiaVcto,8);											// Vencimento		- Data vecto titulo - DDMMAAAA	
	$prVlrTitulo = str_replace(".","",str_replace(",","",$prVlrTitulo));
	$strLinha .= str_pad($prVlrTitulo,15, "0", STR_PAD_LEFT);					// Valor do titulo 	- valor nominal do tit com 2 casas decimais
	$strLinha .= "00000";													// Ag. Cobradora	- Agência encarregada pela Cobrança
	$strLinha .= "0"	;														// Ag. Cobradora	- Digito verificador da Agência encarregada pela Cobrança
	$strLinha .= "99"	;														// Espécie Titulo   
	$strLinha .= "A"	;														// Aceite			- Ident. Titulo Aceito / Nao aceito
	$strLinha .= str_pad($prDiaEmissao,8);										// Data emissao titulo - DDMMAAAAA
	$strLinha .= "3";															// Juros			- código juros de mora
	$strLinha .= str_pad("0", 8, "0", STR_PAD_LEFT);							// Juros			- Data juros de mora (pág 46-47 - preencher com 0s - C019)
	$strLinha .= str_pad("0", 15, "0", STR_PAD_LEFT);							// Juros			- juros de mora por dia/ taxa (pág 47 preencher com 0s - C020)
	$strLinha .= "0"	;														// Desconto			- código do desconto 1
	$strLinha .= str_pad("0", 8, "0", STR_PAD_LEFT);							// Desconto			- data desconto 1
	$strLinha .= str_pad("0", 15, "0", STR_PAD_LEFT);							// Desconto			- valor desconto 1
	$strLinha .= str_pad("0", 15, "0", STR_PAD_LEFT);							// IOF				- valor IOF
	$strLinha .= str_pad("0", 15, "0", STR_PAD_LEFT);							// Abatimento		- valor Abatimento
	$strLinha .= str_pad($prNossoNumero,25);									// Benefic			- Nosso No (pág 83 - G072)
	$strLinha .= "3"	;														// Protesto			- código para protesto
	$strLinha .= "00"	;														// Protesto			- numero dias para protesto
	$strLinha .= "1"	;														// Baixa			- código baixa / devolução
	//$strLinha .= str_pad($prDiasBaixa,3);										// Baixa			- Número de dias p/ baixa (tipo pgto 1 ou 2 = 01 / se 3 = 90) (pág 48 - C029)
	$strLinha .= "001";								// Baixa			- Número de dias p/ baixa (tipo pgto 1 ou 2 = 01 / se 3 = 90) (pág 48 - C029)

	$strLinha .= "09"	;														// Moeda			- Código da moeda
	$strLinha .= "0000000000";		                            	 			// Exclusivo	    - Uso Exclusivo CAIXA
	$strLinha .= "2";															// Uso livre		- 1 - não autoriza pgto parcial / 2 - autoriza pgto parcial

	return $strLinha;
}

function GeraLinhaTipo3SegmentoQ($prContLinhas,$prCont, $prTipoPagador, $prPagadorCNPJ, $prPagadorNome, $prPagadorEndereco, $prPagadorBairro, $prPagadorCEP, $prPagadorSulfixoCEP, $prPagadorCidade, $prPagadorUF, $prPagadorCapitalSocial, $prPagadorCapitalSocialEstab, $prPagadorNumEmpregContrib, $prPagadorVlrTotalContrib, $prPagadorTotalEmpregados, $prPagadorCNAE, $prCodigoSindical){

	$strLinha = "";
	
	$strLinha .= "104";                                          				// Controle 	- Banco     - Código do Banco na Compensação
	$strLinha .= str_pad($prCont, 4, "0", STR_PAD_LEFT);							// Controle 	- Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "3";	                                    	     			// Controle 	- Registro  - Tipo de Registro	
	$strLinha .= str_pad($prContLinhas,5,"0",STR_PAD_LEFT);											// Serviço  	- Número Sequencial do Registro no Lote (pág 80 - G038)		
	$strLinha .= "Q";	                                    	     			// Serviço  	- Segmento  - Cód Segmento
	$strLinha .= str_pad("",1);		                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB			
	$strLinha .= "01";				                            	 			// Cód Mov  	- Cód Movimento de Remessa (pág 44 - C004) (01 - Incluir titulo)
	$strLinha .= str_pad($prTipoPagador,1);  	    		         			// Pagador		- Inscrição - Tipo Inscrição da Empresa (1 - CPF / 2 - CNPJ)
	$strLinha .= str_pad($prPagadorCNPJ, 15, "0", STR_PAD_LEFT);				// Pagador		- Inscrição - Número Inscrição do pagador (CNPJ ou CPF)
	$strLinha .= str_pad(substr($prPagadorNome,0,39),40);  	             					// Pagador		- Nome
	$strLinha .= str_pad($prPagadorEndereco,40);  	             				// Pagador		- Endereco
	$strLinha .= str_pad(substr($prPagadorBairro,0,14),15); 	           /*ATE AQUI OK*/  					// Pagador		- Bairro
	$strLinha .= str_pad($prPagadorCEP,5);		  	             				// Pagador		- CEP
	$strLinha .= str_pad($prPagadorSulfixoCEP,3); 	             				// Pagador		- Sulfixo CEP
	$strLinha .= str_pad($prPagadorCidade,15);		  	           				// Pagador		- Cidade
	$strLinha .= str_pad($prPagadorUF,2);			  	      					// Pagador		- Estado (UF)
	//$strLinha .= str_pad("0",13, "0", STR_PAD_LEFT); // Pagador		- Valor Capital social da empresa(2 casas decimais) (pág 72 - C098)******               ---
	$strLinha .= "0000000000000";
	//$strLinha .= str_pad("0",13, "0", STR_PAD_LEFT);// Pagador		- Valor Capital social do estabelecimento(2 casas decimais)(pág 72 - C099)***           ---
	$strLinha .= "0000000000000";
	//$strLinha .= str_pad("0",9);           				// Pagador		- Número empregados contribuintes                                                   ---
	$strLinha .= str_pad("000000000",9);
	//$strLinha .= str_pad("0",13, "0", STR_PAD_LEFT);// Pagador		- Valor Total remuneração contribuintes (2 casas decimais)***                           ---
	$strLinha .= "0000000000000";
	$strLinha .= str_pad("000000000",9);       	    				// Pagador		- Número total de empregados                                            ---
	$strLinha .= "00".$prPagadorCNAE;		       	    					// Pagador		- Código CNAE GRUPO                                               invalido?????
	$strLinha .= "1";															// Entidade 	- Tipo Entidade Sindical (1 - Sindicado / 2 = Feder. / 3 = Confed. / 5 = Central Sind)
	$strLinha .= str_pad($prCodigoSindical, 5, "0", STR_PAD_LEFT);   			// Empresa  	- Código Sindical (pág 103 - C106)
	$strLinha .= str_pad("",19);	                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB	
	
	
	return $strLinha;
}

function GeraLinhaTipo3SegmentoY53($prContLinhas,$prCont, $prNumSeqRegistroLote, $prVlrMaximo, $prVlrMinimo){
	$strLinha = "";
	
	$strLinha .= "104";                                          				// Controle 	- Banco     - Código do Banco na Compensação
	$strLinha .= str_pad($prCont+1, 4, "0", STR_PAD_LEFT);	 				    // Controle 	- Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "3";	                                    	     			// Controle 	- Registro  - Tipo de Registro	
	$strLinha .= str_pad($prContLinhas,5,"0",STR_PAD_LEFT);						// Serviço  	- Número Sequencial do Registro no Lote (pág 80 - G038)		
	$strLinha .= "Y";	                                    	     			// Serviço  	- Segmento  - Cód Segmento
	$strLinha .= str_pad("",1);		                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB			
	$strLinha .= "01";				                            	 			// Cód Mov  	- Cód Movimento de Remessa (pág 44 - C004)	 (01 - Incluir titulo)
	$strLinha .= "53";				                            	 			// Cód Reg Opc	- Cód Registro Opcional
	$strLinha .= "01";				                            	 			// Tipo Pgto	- 01 - aceita pgto qualquer valor / 02 - aceita entre intervalo valor / 03 - nao aceita valor divergente
	$strLinha .= "01";				                            	 			// Qtde Pgtos 
	$strLinha .= "0";				                            	 			// Alter Nominal - tipo de valor informado
	$strLinha .= str_pad(str_replace(".","",str_replace(",","",$prVlrMaximo)),15, "0", STR_PAD_LEFT); 					// Alter Nominal - valor máximo (2 casas decimais)
	$strLinha .= "0";			                            	 				// Alter Nominal - tipo de valor informado	
	$strLinha .= str_pad(str_replace(".","",str_replace(",","",$prVlrMinimo)),15, "0", STR_PAD_LEFT); 					// Alter Nominal - valor mínimo(2 casas decimais)
	$strLinha .= str_pad("",185);	                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB		
	
	
	return $strLinha;
}

function GeraRodapeLoteTipo5($prContLinhas,$prContLote,$prValorTotalLote) {
	$strLinha = "";
	
	$strLinha .= "104";                                          				// Controle 	- Banco     - Código do Banco na Compensação
	$strLinha .= str_pad("0001", 4, "0", STR_PAD_LEFT);							// Controle 	- Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "5";	                                    	     			// Controle 	- Registro  - Tipo de Registro	
	$strLinha .= str_pad("",9);		                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB			
	$strLinha .= str_pad($prContLinhas,6,"0", STR_PAD_LEFT);										// Qtde Registros no Lote    --------------,,,,,,
	$strLinha .= str_pad($prContLote,6,"0", STR_PAD_LEFT);										// Cobr. Simples - Qtde de títulos em cobrança,,,,,,	
	$strLinha .= str_pad(str_replace(".","", str_replace(",","",$prValorTotalLote)),17, "0", STR_PAD_LEFT);				// Cobr. Simples - Valor total de títulos em cobrança
	$strLinha .= str_pad("0",6,"0",STR_PAD_LEFT);								// Cobr. Caucionada - Qtde de títulos em cobrança 
	$strLinha .= str_pad("0",17,"0",STR_PAD_LEFT);								// Cobr. Caucionada - Valor total de títulos em carteiras
	$strLinha .= str_pad("0",6,"0",STR_PAD_LEFT);								// Cobr. Descontada - Qtde de títulos em cobrança 
	$strLinha .= str_pad("0",17,"0",STR_PAD_LEFT);								// Cobr. Descontada - Valor total de títulos em carteiras
	$strLinha .= str_pad("",31);	                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB		
	$strLinha .= str_pad("",117);	                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB		
	
	return $strLinha;
}

function GeraRodapeArquivoTipo9($prContRegistros) {
	$strLinha = "";
	
	$strLinha .= "104";                                          				// Controle 	- Banco     - Código do Banco na Compensação
	$strLinha .= "9999"	;														// Controle 	- Lote  	- Lote de Serviço (pág 74 - G002)
	$strLinha .= "9";	                                    	     			// Controle 	- Registro  - Tipo de Registro	
	$strLinha .= str_pad("",9);		                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB			
	$strLinha .= str_pad("000001",6);								// Totais		- Qtde de Lotes do arquivo
	$strLinha .= str_pad($prContRegistros,6,"0", STR_PAD_LEFT);							// Totais		- Qtde de Registros do arquivo	
	$strLinha .= str_pad("",6);			                           	 			// CNAB     	- Uso Exclusivo Febraban CNAB		
	$strLinha .= str_pad("",205);	                            	 			// CNAB     	- Uso Exclusivo Febraban CNAB		
		
	return $strLinha;
}


$strPopulate = request("var_populate");   // Flag para necessidade de popular o session ou não

if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");

// abre conexão com o banco de dados
$objConn = abreDBConn(CFG_DB);

$strNomeArquivo = "remessa_cnab240_".date("Y").date("m").date("d")."_".date("H").date("i").date("s").".txt";
$fArquivo = fopen("../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/arqbanco/cobrcnab240/".$strNomeArquivo, "w");

$iCont = 1; //Contador geral

// -------------------------------------
// gera linha de header
// -------------------------------------
$iCodEmpresa = getVarEntidade($objConn, "remessa_caixa_240pos_cod_empresa");
$strNomeEmpresa = getVarEntidade($objConn, "nome_comercial");
$strNomeBanco = "CAIXA ECONOMICA FEDERAL";
$iDia = date("d");
$iMes = date("m");
$iAno = date("Y");
$intDiasAno = dias_ano();
$iNumSeqRemessa = getVarEntidade($objConn, "remessa_caixa_240pos_num_seq_arquivo").gregoriantojd($iMes, $iDia, $iAno);
$strCarteira = getVarEntidade($objConn, "remessa_caixa_240pos_carteira"); //"009";  //TEMPORARIO
$iNumLote = getVarEntidade($objConn, "remessa_caixa_240pos_identificador_lote");
$iIdentificador7Dig = getVarEntidade($objConn, "remessa_caixa_240pos_identificador_lote_sete_posicoes"); 


$strIdenficadorSequencial = substr(date("y"), -2).str_pad($intDiasAno, 3, "0", STR_PAD_LEFT).str_pad(substr($iIdentificador7Dig,0,7), 7, "0", STR_PAD_LEFT);

$strCnpj     		= str_replace("-","",str_replace("/","",str_replace(".","",getVarEntidade($objConn, "cnpj"))));
$strAgencia  		= getVarEntidade($objConn, "remessa_caixa_240pos_agencia"); 
$strAgenciaDV 		= getVarEntidade($objConn, "remessa_caixa_240pos_agencia_dv"); 
$strConvenio 		= getVarEntidade($objConn, "remessa_caixa_240pos_convenio"); 
$strMensagem1 		= getVarEntidade($objConn, "remessa_caixa_240_msg1"); 
$strMensagem2 		= getVarEntidade($objConn, "remessa_caixa_240_msg2");
$strDiasBaixa 		= getVarEntidade($objConn, "remessa_caixa_240_dias_baixa");
$strCodigoSindical 	= getVarEntidade($objConn, "remessa_caixa_240_codigo_sindical");
$strVlrMinimo		= getVarEntidade($objConn, "remessa_caixa_240_vlr_min");
$strVlrMaximo		= getVarEntidade($objConn, "remessa_caixa_240_vlr_max");

$strLinha = GeraLinhaHeaderArquivo($strNomeEmpresa, $strNomeBanco, $iDia, $iMes, $iAno, $strCodigoSindical, $strCnpj, $strAgencia, $strAgenciaDV, $strConvenio, $strCodigoSindical);
fwrite($fArquivo, $strLinha . chr(13) . chr(10));
$iCont++;

$strLinha = GeraLinhaHeaderLote( $strNomeEmpresa, $iDia , $iMes , $iAno , $strCnpj, $strCodigoSindical, $strAgencia, $strAgenciaDV, $strConvenio, $strIdenficadorSequencial, $iNumSeqRemessa, $strMensagem1, $strMensagem2);

fwrite($fArquivo, $strLinha . chr(13) . chr(10));
$iCont++;

$iNumSeqRemessa     = (int)$iNumSeqRemessa+1;
$iNumLote           = (int)$iNumLote+1;
$iIdentificador7Dig = (int)$iIdentificador7Dig+1;
try{
	$strSQL = " UPDATE sys_var_entidade
				SET valor = '".$iNumSeqRemessa."'
				WHERE id_var ILIKE 'remessa_caixa_240pos_identificador_lote_sete_posicoes' ";
	$objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}
try{
	$strSQL = " UPDATE sys_var_entidade
				SET valor = '".$iNumSeqRemessa."'
				WHERE id_var ILIKE 'remessa_caixa240_pos_num_seq_arquivo' ";
	$objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}


// -------------------------------------
// gera linhas de dados
// -------------------------------------
try{
	$strSQL = " SELECT t1.nosso_numero, t1.num_documento, t1.dt_vcto, t1.dt_emissao, t1.vlr_conta
	                 , t2.cnpj AS sacado_cnpj, t2.razao_social AS sacado_nome, t2.endprin_logradouro
				     , t2.endprin_numero, t2.endprin_complemento, t2.endprin_bairro, t2.endprin_cidade
				     , t2.endprin_estado, t2.endprin_cep AS sacado_cep, t2.capital as capital_social, t1.ano_vcto, REPLACE(substr(cod_digi_subclasse,0,7),'-','') as cnae
					 , t6.cod_digi_grupo as cnae_grupo
				FROM fin_conta_pagar_receber t1, cad_pj t2, fin_conta t3, fin_banco t4 , cad_cnae_subclasse t5, cad_cnae_grupo t6
				WHERE t1.pagar_receber = FALSE
				AND t1.situacao ILIKE 'aberto'
				AND t1.codigo = t2.cod_pj
				AND t1.tipo = 'cad_pj' 
				AND t1.cod_conta = t3.cod_conta
				AND t3.cod_banco = t4.cod_banco
				AND t4.num_banco = '104'  /*FIXO POR ENQUANTO*/
				AND t5.cod_cnae_subclasse = t2.cod_cnae_n5 
				AND t6.cod_cnae_grupo = t2.cod_cnae_n3 ";
	if (($dateDtEmissaoIni != "") && ($dateDtEmissaoFim != "")) $strSQL .= " AND t1.dt_emissao BETWEEN TO_TIMESTAMP('".$dateDtEmissaoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtEmissaoFim."', 'DD/MM/YYYY') ";
	if (($dateDtVctoIni != "") && ($dateDtVctoFim != "")) $strSQL .= " AND t1.dt_vcto BETWEEN TO_TIMESTAMP('".$dateDtVctoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtVctoFim."', 'DD/MM/YYYY') ";
	if ($strHistorico != "") $strSQL .= " AND t1.historico ILIKE '".$strHistorico."%' ";
	if ($strTipoDocumento != "") $strSQL .= " AND t1.tipo_documento ILIKE '".$strTipoDocumento."' ";
	$strSQL .= " ORDER BY t2.razao_social, t1.dt_emissao, t1.num_documento limit 1";



	
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

$strNumBanco = "237";
$strOcorrencia = "01"; //remessa
$strIdentEmpresaCedente = getVarEntidade($objConn, "remessa_caixa_240pos_id_empresa_cedente");
//$strLoteIdentidicador   = getVarEntidade($objConn, "remessa_caixa_240pos_identificador_lote");

foreach($objResult as $objRS){ //inicio do loop
	
	$strNumDocumento = getValue($objRS,"num_documento");

	$iDiaVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),0,2);
	$iMesVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),3,2);
	$iAnoVcto = substr(dDate(CFG_LANG, getValue($objRS,"dt_vcto"), false),-4);
	//$iDiaEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),0,2);
	//$iMesEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),3,2);
	//$iAnoEmissao = substr(dDate(CFG_LANG, getValue($objRS,"dt_emissao"), false),-4);

	$iDiaEmissao = $iDia;
	$iMesEmissao = $iMes;
	$iAnoEmissao = $iAno;


	$strNossoNumero			= getValue($objRS,"nosso_numero");
	$strMesAnoCompetencia 	= "01/".getValue($objRS,"ano_vcto");
	$strDiaVcto 			= $iDiaVcto.$iMesVcto.$iAnoVcto;
	$strVlrTitulo			= getValue($objRS,"vlr_conta");
	$strDiaEmissao 			= $iDiaEmissao.$iMesEmissao.$iAnoEmissao;
	
	$dblTotalTitulo			= $dblTotalTitulo + getValue($objRS,"vlr_conta");
	$iContRegistros	++;
	$iContLinhas++;
	$strLinha = GeraLinhaTipo3SegmentoP($iContLinhas,$iContRegistros, $strAgencia, $strAgenciaDV, $strConvenio,  $strNossoNumero, $strMesAnoCompetencia, $strDiaVcto, $strVlrTitulo, $strDiaEmissao, $strDiasBaixa);
	fwrite($fArquivo, $strLinha . chr(13) . chr(10));
	$iCont++;	

	$strTipoPagador					= "2"; //Tipo Inscrição da Empresa (1 - CPF / 2 - CNPJ)
	$strSacadoCNPJ 					= str_replace("-","",str_replace("/","",str_replace(".","",getValue($objRS,"sacado_cnpj"))));
	$strPagadorNome					= getValue($objRS,"sacado_nome");
	$strPagadorEndereco				= getValue($objRS,"endprin_logradouro");
	if (getValue($objRS,"endprin_numero") != "")      $strPagadorEndereco .= ",".getValue($objRS,"endprin_numero");
	if (getValue($objRS,"endprin_complemento") != "") $strPagadorEndereco .= " ".getValue($objRS,"endprin_complemento");	
	$strPagadorBairro				= getValue($objRS,"endprin_bairro");
	$strPagadorCEP					= substr(getValue($objRS,"sacado_cep"),0,5);
	$strPagadorSulfixoCEP			= substr(getValue($objRS,"sacado_cep"),-3);
	$strPagadorCidade				= getValue($objRS,"endprin_cidade");
	$strPagadorUF					= getValue($objRS,"endprin_estado");
	$strPagadorCapitalSocial		= getValue($objRS,"capital_social");
	$strPagadorCapitalSocialEstab	= "0";
	$strPagadorNumEmpregContrib  	= "0";
	$strPagadorVlrTotalContrib		= "0";
	$strPagadorTotalEmpregados		= "0";
	$strPagadorCNAE					= getValue($objRS,"cnae_grupo"); //verificar consulta
	$iContLinhas++;
	$strLinha = 	GeraLinhaTipo3SegmentoQ($iContLinhas,$iContRegistros, $strTipoPagador, $strSacadoCNPJ, $strPagadorNome, $strPagadorEndereco, $strPagadorBairro, $strPagadorCEP, $strPagadorSulfixoCEP, $strPagadorCidade, $strPagadorUF, $strPagadorCapitalSocial, $strPagadorCapitalSocialEstab, $strPagadorNumEmpregContrib, $strPagadorVlrTotalContrib, $strPagadorTotalEmpregados, $strPagadorCNAE, $strCodigoSindical);
	fwrite($fArquivo, $strLinha . chr(13) . chr(10));
	$iCont++;
	$iContLinhas++;
	$strLinha = GeraLinhaTipo3SegmentoY53($iContLinhas,$iContRegistros, $strNumSeqRegistroLote, $strVlrMaximo, $strVlrMinimo);
	fwrite($fArquivo, $strLinha . chr(13) . chr(10));
	$iCont++;

} //final do loop
$objResult->closeCursor();

// -------------------------------------
// gera linha de footer
// -------------------------------------

//$strLinha = GeraRodapeLoteTipo5($iContLinhas,$iContRegistros,$dblTotalTitulo) ;
$strLinha = GeraRodapeLoteTipo5($iCont,$iContLinhas,$dblTotalTitulo) ;
fwrite($fArquivo, $strLinha . chr(13) . chr(10));
$iCont++;

$strLinha = GeraRodapeArquivoTipo9($iCont) ;
fwrite($fArquivo, $strLinha . chr(13) . chr(10));


fclose($fArquivo);

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin: 0px; background-color:#FFFFFF; } 
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
</style>
<script language="javascript" type="text/javascript">
function reiniciar() {
	document.location.href = "STgeraarqRemessaPasso1_Caixa.php";	
}

</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_gerar_remessa",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr><td width="30%"></td><td width="70%"></td></tr>
					<tr><td align="left" style="padding-left:5px;" colspan="2"><img src="../img/remessa_passo03.gif"></td></tr>
					<tr>
						<td colspan="2" height="40"><?php echo(getTText("arquivo_gerado",C_NONE)); ?>:&nbsp;<a href="../../<?php echo getsession(CFG_SYSTEM_NAME."_dir_cliente");?>/upload/arqbanco/cobrcnab240/<?php echo $strNomeArquivo; ?>" target="_blank"><u><?php echo $strNomeArquivo; ?></u></a></td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
							<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
								<button onClick="reiniciar();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							</td>
							</tr>
						</table>
						</td>
					</tr> 
				</table>
			</td>
		</tr>
      </table>
      <?php athEndFloatingBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>
<?php
$objConn = NULL;
?>
