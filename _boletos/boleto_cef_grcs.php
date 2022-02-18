<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa                |
// |                                                                      |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+


	// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
	// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//
	
	
	// ESTA Guia é LIGADA aos CNAES, PORTANTO
	// é importante que esteja relacionado até
	// o nível de GRUPO, para o boleto. o CODIGO
	// DA ATIVIDADE independe do GRUPO
	if($DadosBoleto_SACADO_CNAE_N3 == ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",getTText("cnae_nao_cadastrado",C_NONE),"","aviso",1);
		die();
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
	// "19525086";  // Nosso numero sem o DV - REGRA: Máximo de 8 caracteres!
	$dadosboleto["nosso_numero"] 		= $DadosBoleto_NOSSO_NUMERO;
	// "27.030195.10"; // Num do pedido ou do documento
	$dadosboleto["numero_documento"] 	= $DadosBoleto_NUM_DOCUMENTO;
	// $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_vencimento"] 	= $DadosBoleto_DT_VCTOforma2;
	// Ano de VCTO - 2009, 2010, 2011
	$dadosboleto["exercicio"]			= $DadosBoleto_DT_ANO_VCTO;
	// date("d/m/Y"); // Data de emissão do Boleto
	$dadosboleto["data_documento"] 		= $DadosBoleto_DT_EMISSAO; 
	// date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["data_processamento"] 	= $DadosBoleto_DT_PROC;
	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
	$dadosboleto["valor_boleto"] 		= $DadosBoleto_VLR_TITULOforma1;
	$dadosboleto["local_pgto"] 			= $DadosBoleto_LOCAL_PGTO;
	$dadosboleto["rotulo"] 				= $DadosBoleto_ROTULO;
	$dadosboleto["logotipo_empresa"] = $DadosBoleto_LOGOTIPO;
	
	
	
	// DADOS DO SEU CLIENTE
	// "Nome do seu Cliente";
	$dadosboleto["sacado"] 				= $DadosBoleto_SACADO_NOME;
	// "Endereço do seu Cliente";
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
	// "Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ ".number_format((double) $taxa_boleto, 2, ',', '');
	$dadosboleto["demonstrativo2"] 	= $DadosBoleto_INFO2;
	// "BoletoPhp - http://www.boletophp.com.br";
	$dadosboleto["demonstrativo3"] 	= $DadosBoleto_INFO3;
	
	
	
	// INSTRUÇÕES PARA O CAIXA
	$dadosboleto["instrucoes1"] = $DadosBoleto_INSTRUCOES1; //"- Sr. Caixa, cobrar multa de 2% após o vencimento";
	$dadosboleto["instrucoes2"] = $DadosBoleto_INSTRUCOES2; //"- Receber até 10 dias após o vencimento";
	$dadosboleto["instrucoes3"] = $DadosBoleto_INSTRUCOES3; //"- Em caso de dúvidas entre em contato conosco: xxxx@xxxx.com.br";
	$dadosboleto["instrucoes4"] = $DadosBoleto_INSTRUCOES4; //"&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";
	
	
	
	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] 		= "";
	$dadosboleto["valor_unitario"] 	= "";
	$dadosboleto["aceite"] 			= $DadosBoleto_ACEITE;		
	$dadosboleto["especie"] 		= $DadosBoleto_ESPECIE; //"R$";
	$dadosboleto["especie_doc"] 	= $DadosBoleto_ESPECIE_DOC;
	
	
	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //
	
	
	// DADOS DA SUA CONTA - CEF
	$dadosboleto["agencia"] 		= $DadosBoleto_AGENCIA; //"1565"; 	// Num da agencia, sem digito
	$dadosboleto["conta"] 			= $DadosBoleto_CONTA; 	//"13877"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] 		= $DadosBoleto_CONTA_DV;//"4"; 		// Digito do Num da conta
	
	
	// DADOS PERSONALIZADOS - CEF
	$dadosboleto["conta_cedente"] 	= $DadosBoleto_CEDENTE;    //"87000000414";//ContaCedente do Cliente, sem digito (Somente Números)
	$dadosboleto["conta_cedente_dv"]= $DadosBoleto_CEDENTE_DV; //"3";//Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] 		= $DadosBoleto_CARTEIRA;   //"SR"; // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)
	$dadosboleto["identificacao"] 	= $DadosBoleto_IDENTIFICACAO; //"BoletoPhp - Código Aberto de Sistema de Boletos";
	
	
	// SEUS DADOS
	$dadosboleto["cedente_nome_simples"]  = $DadosBoleto_CEDENTE_NOME_SIMPLES; 	//"Coloque a Razão Social da sua empresa aqui";
	$dadosboleto["cedente_nome_completo"] = $DadosBoleto_CEDENTE_NOME_COMPLETO; //"Coloque a Razão Social da sua empresa aqui";
	$dadosboleto["cedente_cpf_cnpj"] 	  = $DadosBoleto_CEDENTE_CNPJ; 			//"";
	//"Coloque o endereço da sua empresa aqui";
	$dadosboleto["cedente_endereco"] 	  = $DadosBoleto_CEDENTE_LOGRADOURO.", ".$DadosBoleto_CEDENTE_NUMERO." ".
											$DadosBoleto_CEDENTE_COMPLEMENTO.", ".$DadosBoleto_CEDENTE_BAIRRO;
	$dadosboleto["cedente_cidade_uf"] 	  = $DadosBoleto_CEDENTE_CIDADE." / ".$DadosBoleto_CEDENTE_ESTADO." - CEP:".
											$DadosBoleto_CEDENTE_CEP; //"Cidade / Estado - CEP:";
											
	
	
	// ===========================================//
	// 			  ALTERAÇÕES PARA GRCSU           //
	// ===========================================//
												
	// DADOS PARA GRCSU - Endereço
	// UTILIZADO APENAS PARA GRCSU NO MOMENTO
	// Também pode ser utilizado por outro tipo
	// de boleto que desmembre o ENDEREÇO do
	// CEDENTE em partes.
	$dadosboleto["cedente_logradouro_end"]	= $DadosBoleto_CEDENTE_LOGRADOURO;
	$dadosboleto["cedente_numero_end"]	  	= $DadosBoleto_CEDENTE_NUMERO;
	$dadosboleto["cedente_complemento_end"]	= $DadosBoleto_CEDENTE_COMPLEMENTO;
	$dadosboleto["cedente_bairro_end"]	  	= $DadosBoleto_CEDENTE_BAIRRO;
	$dadosboleto["cedente_cidade_end"]	  	= $DadosBoleto_CEDENTE_CIDADE;
	$dadosboleto["cedente_estado_end"]	  	= $DadosBoleto_CEDENTE_ESTADO;
	$dadosboleto["cedente_cep_end"]	  		= $DadosBoleto_CEDENTE_CEP;
	
	// ENDEREÇO Desmembrado do SACADO
	$dadosboleto["sacado_logradouro_end"]	= $DadosBoleto_SACADO_LOGRADOURO;
	$dadosboleto["sacado_numero_end"]	  	= $DadosBoleto_SACADO_NUMERO;
	$dadosboleto["sacado_complemento_end"]	= $DadosBoleto_SACADO_COMPLEMENTO;
	$dadosboleto["sacado_bairro_end"]	  	= $DadosBoleto_SACADO_BAIRRO;
	$dadosboleto["sacado_cidade_end"]	  	= $DadosBoleto_SACADO_CIDADE;
	$dadosboleto["sacado_estado_end"]	  	= $DadosBoleto_SACADO_ESTADO;
	$dadosboleto["sacado_cep_end"]	  		= $DadosBoleto_SACADO_CEP;
	
	// COD_ENTIDADE SINDICAL
	$dadosboleto["cedente_cod_entsindical"] = $DadosBoleto_COD_ENT_SINDICAL;
		
	include("include/funcoes_cef.php"); 
	include("include/layout_cef_grcsNEW.php");
	// include("include/layout_cef_grcs.php");
?>
