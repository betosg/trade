<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa                |
// |                                                                      |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+


	// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
	// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//
	
	// Se for LABORAL � uma guia gerada para casos de multa e, no geral, ser� chamada de fora 
	// do sistema. Ent�o ignora CNAE porque na guia gerada pela MIRAH isso n�o era obrigat�rio
	if ($DadosBoleto_CATEGORIA != "LABORAL") {
		// ESTA Guia � LIGADA aos CNAES, PORTANTO
		// � importante que esteja relacionado at�
		// o n�vel de GRUPO, para o boleto. o CODIGO
		// DA ATIVIDADE independe do GRUPO
		if($DadosBoleto_SACADO_CNAE_N3 == ""){
			mensagem("err_dados_titulo","err_dados_submit_desc",getTText("cnae_nao_cadastrado",C_NONE),"","aviso",1);
			die();
		}
	}
	// Atribui o GRUPO para o Codigo de Barras
	$dadosboleto["cod_cnae_grupo"] = $DadosBoleto_SACADO_CNAE_N3; // CNAE_GRUPO
	
	// Atribui o codigo da atividade para GUIA SINDICAL
	if($DadosBoleto_SACADO_CNAE_N1 != ""){ $dadosboleto["cod_atividade"]  = $DadosBoleto_SACADO_CNAE_N1; /* CNAE_SECAO */}
	if($DadosBoleto_SACADO_CNAE_N2 != ""){ $dadosboleto["cod_atividade"]  = $DadosBoleto_SACADO_CNAE_N2; /* CNAE_DIVIS */}
	if($DadosBoleto_SACADO_CNAE_N3 != ""){ $dadosboleto["cod_atividade"]  = $DadosBoleto_SACADO_CNAE_N3; /* CNAE_GRUPO */}
	if($DadosBoleto_SACADO_CNAE_N4 != ""){ $dadosboleto["cod_atividade"]  = $DadosBoleto_SACADO_CNAE_N4; /* CNAE_CLASS */}
	if($DadosBoleto_SACADO_CNAE_N5 != ""){ $dadosboleto["cod_atividade"]  = $DadosBoleto_SACADO_CNAE_N5; /* CNAE_SCLAS */}
	
			
	// DADOS DO BOLETO PARA O SEU CLIENTE
	// $dias_de_prazo_para_pagamento = 5;
	// 2.95;
	$taxa_boleto 	= $DadosBoleto_TAXA_BOLETO;
	// date("d/m/Y",time()+($dias_de_prazo_para_pagamento*86400));// Prazo deXdias OU data:"13/04/2006"OU ""seContra Apresentacao;
	$data_venc 		= $DadosBoleto_DT_VCTOforma1;
	// "2950,00";// Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
	$valor_cobrado 	= $DadosBoleto_VLR_TITULOforma1;
	// number_format((double) $DadosBoleto_VLR_TITULOforma1 + $DadosBoleto_TAXA_BOLETO, 2, ',', '');
	$DadosBoleto_VLR_TITULOforma2 = $DadosBoleto_VLR_TITULOforma1 + $DadosBoleto_TAXA_BOLETO;
	
	
	// "80";//Carteira SR: 80, 81 ou 82-Carteira CR: 90 (Confirmar com gerente qual usar)
	$dadosboleto["inicio_nosso_numero"] = $DadosBoleto_INIC_NOSSO_NUMERO; 
	
	
	// Update: NOSSO_NUMERO NA GUIA SINDICAL DEVE SER USADO O CNPJ
	// SEM O DV. USANDO CNPJ DO CEDENTE / SACADO, SOMENTE 12 DIGTS
	// "19525086";  // Nosso numero sem o DV - REGRA: M�ximo de 8 caracteres!
	//$dadosboleto["nosso_numero"] 	= $DadosBoleto_NOSSO_NUMERO;
	if ($DadosBoleto_CATEGORIA == "LABORAL")
		$dadosboleto["nosso_numero"] = $DadosBoleto_NOSSO_NUMERO;
	else
		$dadosboleto["nosso_numero"] = substr($DadosBoleto_SACADO_CNPJ,0,12);
	
	
	// "27.030195.10"; // Num do pedido ou do documento
	$dadosboleto["numero_documento"] 	= $DadosBoleto_NUM_DOCUMENTO;
	// $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_vencimento"] 	= $DadosBoleto_DT_VCTOforma2;
	// Ano de VCTO - 2009, 2010, 2011
	$dadosboleto["exercicio"]			= $DadosBoleto_DT_ANO_VCTO;
	// date("d/m/Y"); // Data de emiss�o do Boleto
	$dadosboleto["data_documento"] 		= $DadosBoleto_DT_EMISSAO; 
	// date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["data_processamento"] 	= $DadosBoleto_DT_PROC;
	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
	$dadosboleto["valor_boleto"] 			= $DadosBoleto_VLR_TITULOforma1;
	
	$dadosboleto["valor_desc_abatim"]		= $DadosBoleto_VLR_DESC_ABATIM;
	$dadosboleto["valor_outras_deducoes"]	= $DadosBoleto_VLR_OUTRAS_DEDUCOES;
	$dadosboleto["valor_mora_multa"]		= $DadosBoleto_VLR_MORA_MULTA;
	$dadosboleto["valor_outros_acresc"]		= $DadosBoleto_VLR_OUTROS_ACRESC;
	$dadosboleto["valor_cobrado"]			= $DadosBoleto_VLR_COBRADO;
	
	$dadosboleto["local_pgto"]			= $DadosBoleto_LOCAL_PGTO;
	$dadosboleto["rotulo"] 				= $DadosBoleto_ROTULO;
	$dadosboleto["logotipo_empresa"]	= $DadosBoleto_LOGOTIPO;
	
	if ($DadosBoleto_CATEGORIA == "LABORAL")
		$dadosboleto["total_empregados"] = "";
	else
		$dadosboleto["total_empregados"] = "0";
	
	// DADOS DO SEU CLIENTE
	// "Nome do seu Cliente";
	$dadosboleto["sacado"] 				= $DadosBoleto_SACADO_NOME;
	// "Endere�o do seu Cliente";
	$dadosboleto["sacado_endereco1"] 	= $DadosBoleto_SACADO_LOGRADOURO.", ".$DadosBoleto_SACADO_NUMERO." ".
										  $DadosBoleto_SACADO_COMPLEMENTO;
	// "Cidade-Estado-CEP:00000-000";
	$dadosboleto["sacado_endereco2"] 	= $DadosBoleto_SACADO_CIDADE." - ".$DadosBoleto_SACADO_ESTADO." - CEP: ".
										  $DadosBoleto_SACADO_CEP; 
	$dadosboleto["sacado_codigo"] 		= $DadosBoleto_SACADO_CODIGO;
	$dadosboleto["sacado_cnpj"] 		= $DadosBoleto_SACADO_CNPJ;
	
	
	
	// INFORMACOES PARA O CLIENTE
	// "Pagamento de Compra na Loja Nonononono";
	$dadosboleto["demonstrativo1"] 	= $DadosBoleto_INFO1;
	// "Mensalidade referente a nonon nonooon nononon<br>Taxa banc�ria - R$ ".number_format((double) $taxa_boleto, 2, ',', '');
	$dadosboleto["demonstrativo2"] 	= $DadosBoleto_INFO2;
	// "BoletoPhp - http://www.boletophp.com.br";
	$dadosboleto["demonstrativo3"] 	= $DadosBoleto_INFO3;
	
	
	
	// INSTRU��ES PARA O CAIXA
	$dadosboleto["instrucoes1"] = $DadosBoleto_INSTRUCOES1; //"- Sr. Caixa, cobrar multa de 2% ap�s o vencimento";
	$dadosboleto["instrucoes2"] = $DadosBoleto_INSTRUCOES2; //"- Receber at� 10 dias ap�s o vencimento";
	$dadosboleto["instrucoes3"] = $DadosBoleto_INSTRUCOES3; //"- Em caso de d�vidas entre em contato conosco: xxxx@xxxx.com.br";
	$dadosboleto["instrucoes4"] = $DadosBoleto_INSTRUCOES4; //"Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";
	$dadosboleto["instrucoes5"] = $DadosBoleto_INSTRUCOES5;
	
	
	// MENSAGEM EXTRA
	$dadosboleto["msg_extra_1"] = $DadosBoleto_MSG_EXTRA_1;	
	$dadosboleto["msg_extra_2"] = $DadosBoleto_MSG_EXTRA_2;	
	$dadosboleto["msg_extra_3"] = $DadosBoleto_MSG_EXTRA_3;	
	$dadosboleto["msg_extra_4"] = $DadosBoleto_MSG_EXTRA_4;	
	
	
	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] 		= "";
	$dadosboleto["valor_unitario"] 	= "";
	$dadosboleto["aceite"] 			= $DadosBoleto_ACEITE;		
	$dadosboleto["especie"] 		= $DadosBoleto_ESPECIE; //"R$";
	$dadosboleto["especie_doc"] 	= $DadosBoleto_ESPECIE_DOC;
	
	
	// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
	
	
	// DADOS DA SUA CONTA - CEF
	$dadosboleto["agencia"] 		= $DadosBoleto_AGENCIA; //"1565"; 	// Num da agencia, sem digito
	$dadosboleto["conta"] 			= $DadosBoleto_CONTA; 	//"13877"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] 		= $DadosBoleto_CONTA_DV;//"4"; 		// Digito do Num da conta
	
	
	// DADOS PERSONALIZADOS - CEF
	$dadosboleto["conta_cedente"] 	= $DadosBoleto_CEDENTE;    //"87000000414";//ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente_dv"]= $DadosBoleto_CEDENTE_DV; //"3";//Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] 		= $DadosBoleto_CARTEIRA;   //"SR"; // C�digo da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)
	$dadosboleto["identificacao"] 	= $DadosBoleto_IDENTIFICACAO; //"BoletoPhp - C�digo Aberto de Sistema de Boletos";
	
	
	// SEUS DADOS
	$dadosboleto["cedente_nome_simples"]  = $DadosBoleto_CEDENTE_NOME_SIMPLES; 	//"Coloque a Raz�o Social da sua empresa aqui";
	$dadosboleto["cedente_nome_completo"] = $DadosBoleto_CEDENTE_NOME_COMPLETO; //"Coloque a Raz�o Social da sua empresa aqui";
	$dadosboleto["cedente_cpf_cnpj"] 	  = $DadosBoleto_CEDENTE_CNPJ; 			//"";
	//"Coloque o endere�o da sua empresa aqui";
	$dadosboleto["cedente_endereco"] 	  = $DadosBoleto_CEDENTE_LOGRADOURO.", ".$DadosBoleto_CEDENTE_NUMERO." ".
											$DadosBoleto_CEDENTE_COMPLEMENTO.", ".$DadosBoleto_CEDENTE_BAIRRO;
	$dadosboleto["cedente_cidade_uf"] 	  = $DadosBoleto_CEDENTE_CIDADE." / ".$DadosBoleto_CEDENTE_ESTADO." - CEP:".
											$DadosBoleto_CEDENTE_CEP; //"Cidade / Estado - CEP:";
											
	
	
	// ===========================================//
	// 			  ALTERA��ES PARA GRCSU           //
	// ===========================================//
												
	// DADOS PARA GRCSU - Endere�o
	// UTILIZADO APENAS PARA GRCSU NO MOMENTO
	// Tamb�m pode ser utilizado por outro tipo
	// de boleto que desmembre o ENDERE�O do
	// CEDENTE em partes.
	$dadosboleto["cedente_logradouro_end"]	= $DadosBoleto_CEDENTE_LOGRADOURO;
	$dadosboleto["cedente_numero_end"]	  	= $DadosBoleto_CEDENTE_NUMERO;
	$dadosboleto["cedente_complemento_end"]	= $DadosBoleto_CEDENTE_COMPLEMENTO;
	$dadosboleto["cedente_bairro_end"]	  	= $DadosBoleto_CEDENTE_BAIRRO;
	$dadosboleto["cedente_cidade_end"]	  	= $DadosBoleto_CEDENTE_CIDADE;
	$dadosboleto["cedente_estado_end"]	  	= $DadosBoleto_CEDENTE_ESTADO;
	$dadosboleto["cedente_cep_end"]	  		= $DadosBoleto_CEDENTE_CEP;
	
	// ENDERE�O Desmembrado do SACADO
	$dadosboleto["sacado_logradouro_end"]	= $DadosBoleto_SACADO_LOGRADOURO;
	$dadosboleto["sacado_numero_end"]	  	= $DadosBoleto_SACADO_NUMERO;
	$dadosboleto["sacado_complemento_end"]	= $DadosBoleto_SACADO_COMPLEMENTO;
	$dadosboleto["sacado_bairro_end"]	  	= $DadosBoleto_SACADO_BAIRRO;
	$dadosboleto["sacado_cidade_end"]	  	= $DadosBoleto_SACADO_CIDADE;
	$dadosboleto["sacado_estado_end"]	  	= $DadosBoleto_SACADO_ESTADO;
	$dadosboleto["sacado_cep_end"]	  		= $DadosBoleto_SACADO_CEP;
	
	// COD_ENTIDADE SINDICAL
	$dadosboleto["cedente_cod_entsindical"] = $DadosBoleto_COD_ENT_SINDICAL;
	
	// Path base para incluir imagens e outros arquivos dentro das fun��es de boleto de banco
	$dadosboleto["path_base"] = $DadosBoleto_PATH_BASE;
	
	include("include/funcoes_cef_grcsu.php"); 
	include("include/layout_cef_grcsu.php" );
?>
