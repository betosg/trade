<?php
include_once("../_database/athdbconn.php");
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
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Unibanco: Elizeu Alcantara                    |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE

/* $intCodContaPagarReceber		= request("var_chavereg");
$strNumImpressoes				= request("var_boleto_num_impressoes");

$strBoletoAceite				= 
$strBoletoAgencia				= 

$strBoletoCarteira				= 
$strBoletoCedenteNome 			= 
$strBoletoCedenteCNPJ			= 
$intBoletoCodBanco				= request("var_boleto_cod_banco");
$intBoletoCodBancoDV			= request("var_boleto_cod_banco_dv");
$intBoletoCodCliente			= request("var_boleto_cod_cliente");
$strBoletoConta					= 
$strBoletoContaDV				= 
$strBoletoEspecieDoc			= 
$dateBoletoDtVencimento			= 
$strBoletoEspecie				= 
$strBoletoImgLogo				= request("var_boleto_img_logo");
$strBoletoImgPromo				= request("var_boleto_img_promo");
$strBoletoInstrucoes			= 
$strBoletoLocalPgto				= 
$strBoletoNossoNumero			= 
$strBoletoNossoNumeroDV 		= calcularDDVModulo11($strBoletoNossoNumero,2,9,$intBoletoCodBanco,"DV_NOSSONUMERO");
$strBoletoNumDocumento			= request("var_boleto_num_documento");

$strBoletoSacadoBairro			= 
$strBoletoSacadoCEP				= 
$strBoletoSacadoCidade			= 
$strBoletoSacadoEndereco		= 
$strBoletoSacadoEstado			= 
$strBoletoSacadoIdentificador	= request("var_boleto_sacado_identificador");
$strBoletoSacadoNome			= 

$dblBoletoValor					=  */

//$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 2.95;
$data_venc 	   = request("var_boleto_dt_vencimento");  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = request("var_boleto_valor"); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto  = number_format((double) $valor_cobrado, 2, ',', '');

$dadosboleto["nosso_numero"] = sprintf("%014s",request("var_boleto_nosso_numero"));  // Nosso numero - REGRA: Máximo de 14 caracteres!
$dadosboleto["numero_documento"] = request("var_boleto_num_documento");	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = request("var_boleto_sacado_nome");
$dadosboleto["endereco1"] = request("var_boleto_sacado_endereco") . " - " . request("var_boleto_sacado_bairro");
$dadosboleto["endereco2"] = request("var_boleto_sacado_cidade") . " - " . request("var_boleto_sacado_estado") ." -  CEP: " . request("var_boleto_sacado_cep");

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = request("var_boleto_local_pgto");
$dadosboleto["demonstrativo2"] = "";
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = request("var_boleto_instrucoes");
$dadosboleto["instrucoes2"] = "";
$dadosboleto["instrucoes3"] = "";
$dadosboleto["instrucoes4"] = "";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = request("var_boleto_aceite");
$dadosboleto["especie"] = request("var_boleto_especie");
$dadosboleto["especie_doc"] = request("var_boleto_especie_doc");


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - UNIBANCO
$dadosboleto["agencia"] = sprintf("%04s",request("var_boleto_agencia")); // Num da agencia, sem digito
$dadosboleto["conta"] = sprintf("%06s",left(request("var_boleto_cedente_codigo"),6)); 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = request("var_boleto_cedente_codigo_dv"); 	// Digito do Num da conta

// DADOS PERSONALIZADOS - UNIBANCO
$dadosboleto["codigo_cliente"] = "2031671"; // Codigo do Cliente
$dadosboleto["carteira"] = request("var_boleto_carteira");  // Código da Carteira

// SEUS DADOS
$dadosboleto["identificacao"] = request("var_boleto_cedente_nome");
$dadosboleto["cpf_cnpj"] = request("var_boleto_cedente_cnpj");
$dadosboleto["endereco"] = "";
$dadosboleto["cidade_uf"] = "";
$dadosboleto["cedente"] = request("var_boleto_cedente_nome");

// NÃO ALTERAR!
include("include/funcoes_unibanco.php"); 
include("include/layout_unibanco.php");
?>
