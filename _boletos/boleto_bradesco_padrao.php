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
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa			       	  |
// | 																	                                    |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Bradesco: Ramon Soares						            |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE






// DADOS DO BOLETO PARA O SEU CLIENTE
//$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = $DadosBoleto_TAXA_BOLETO; //2.95;
$data_venc = $DadosBoleto_DT_VCTOforma1; //date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias  OU  informe data: "13/04/2006"  OU  informe "" se Contra Apresentacao;
$valor_cobrado = $DadosBoleto_VLR_TITULOforma1; //"2950,00"; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$DadosBoleto_VLR_TITULOforma2 = $DadosBoleto_VLR_TITULOforma1 + $DadosBoleto_TAXA_BOLETO; //number_format((double) $DadosBoleto_VLR_TITULOforma1 + $DadosBoleto_TAXA_BOLETO, 2, ',', '');

$dadosboleto["inicio_nosso_numero"] = $DadosBoleto_INIC_NOSSO_NUMERO; //"80";  // Carteira SR: 80, 81 ou 82  -  Carteira CR: 90 (Confirmar com gerente qual usar)
$dadosboleto["nosso_numero"] = $DadosBoleto_NOSSO_NUMERO; //"19525086";  // Nosso numero sem o DV - REGRA: Máximo de 8 caracteres!
$dadosboleto["numero_documento"] = $DadosBoleto_NUM_DOCUMENTO; //"27.030195.10";	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $DadosBoleto_DT_VCTOforma2; //$data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = $DadosBoleto_DT_EMISSAO; //date("d/m/Y"); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = $DadosBoleto_DT_PROC; //date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $DadosBoleto_VLR_TITULOforma1; // Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula
$dadosboleto["local_pgto"] = $DadosBoleto_LOCAL_PGTO;
$dadosboleto["rotulo"] = $DadosBoleto_ROTULO;
$dadosboleto["logotipo_empresa"] = $DadosBoleto_LOGOTIPO;

$dadosboleto["valor_mora_multa"] = $DadosBoleto_VLR_MORA_MULTA;
$dadosboleto["valor_outros_acresc"] = $DadosBoleto_VLR_OUTROS_ACRESC;

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $DadosBoleto_SACADO_NOME; //"Nome do seu Cliente";
$dadosboleto["sacado_endereco1"] = $DadosBoleto_SACADO_LOGRADOURO . ", " . $DadosBoleto_SACADO_NUMERO . " " . $DadosBoleto_SACADO_COMPLEMENTO; //"Endereço do seu Cliente";
$dadosboleto["sacado_endereco2"] = $DadosBoleto_SACADO_CIDADE . " - " . $DadosBoleto_SACADO_ESTADO . " - CEP: " . $DadosBoleto_SACADO_CEP; //"Cidade - Estado -  CEP: 00000-000";
$dadosboleto["sacado_codigo"] = $DadosBoleto_SACADO_CODIGO;
$dadosboleto["sacado_cnpj"] = $DadosBoleto_SACADO_CNPJ;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $DadosBoleto_INFO1; //"Pagamento de Compra na Loja Nonononono";
$dadosboleto["demonstrativo2"] = $DadosBoleto_INFO2; //"Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ ".number_format((double) $taxa_boleto, 2, ',', '');
$dadosboleto["demonstrativo3"] = $DadosBoleto_INFO3; //"BoletoPhp - http://www.boletophp.com.br";

// INSTRUÇÕES PARA O CAIXA
$dadosboleto["instrucoes1"] = $DadosBoleto_INSTRUCOES1; //"- Sr. Caixa, cobrar multa de 2% após o vencimento";
$dadosboleto["instrucoes2"] = $DadosBoleto_INSTRUCOES2; //"- Receber até 10 dias após o vencimento";
$dadosboleto["instrucoes3"] = $DadosBoleto_INSTRUCOES3; //"- Em caso de dúvidas entre em contato conosco: xxxx@xxxx.com.br";
$dadosboleto["instrucoes4"] = $DadosBoleto_INSTRUCOES4; //"&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";
$dadosboleto["instrucoes5"] = $DadosBoleto_INSTRUCOES5;

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = $DadosBoleto_ACEITE;		
$dadosboleto["especie"] = $DadosBoleto_ESPECIE; //"R$";
$dadosboleto["especie_doc"] = $DadosBoleto_ESPECIE_DOC;


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - CEF
$dadosboleto["agencia"] = $DadosBoleto_AGENCIA; //"1565"; // Num da agencia, sem digito
//$dadosboleto["agencia_dv"] = $DadosBoleto_AGENCIA_DV; //"1565"; // Num da agencia, sem digito
$dadosboleto["conta"] = $DadosBoleto_CONTA; //"13877"; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $DadosBoleto_CONTA_DV; //"4"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
$dadosboleto["conta_cedente"] = $DadosBoleto_CEDENTE; //"87000000414"; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $DadosBoleto_CEDENTE_DV; //"3"; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = $DadosBoleto_CARTEIRA; //"SR"; // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

$dadosboleto["identificacao"] = $DadosBoleto_IDENTIFICACAO; //"BoletoPhp - Código Aberto de Sistema de Boletos";

// SEUS DADOS
$dadosboleto["cedente_nome_simples"] = $DadosBoleto_CEDENTE_NOME_SIMPLES; //"Coloque a Razão Social da sua empresa aqui";
$dadosboleto["cedente_nome_completo"] = $DadosBoleto_CEDENTE_NOME_COMPLETO; //"Coloque a Razão Social da sua empresa aqui";
$dadosboleto["cedente_cpf_cnpj"] = $DadosBoleto_CEDENTE_CNPJ; //"";
$dadosboleto["cedente_endereco"] = $DadosBoleto_CEDENTE_LOGRADOURO . ", " . $DadosBoleto_CEDENTE_NUMERO . " " . $DadosBoleto_CEDENTE_COMPLEMENTO . ", " . $DadosBoleto_CEDENTE_BAIRRO; //"Coloque o endereço da sua empresa aqui";
$dadosboleto["cedente_cidade_uf"] = $DadosBoleto_CEDENTE_CIDADE . " / " . $DadosBoleto_CEDENTE_ESTADO . " - CEP:" . $DadosBoleto_CEDENTE_CEP; //"Cidade / Estado - CEP:";

// NÃO ALTERAR!
include("include/funcoes_bradesco_padrao.php"); 
include("include/layout_bradesco_padrao.php");
?>
