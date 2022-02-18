<?php

include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");




$varDB="";
 $intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
$varDB = request('var_db');
if ($varDB == ""){$varDB = CFG_DB;}
$objConn = abreDBConn($varDB);

//$intCodDado = "35130";





try{
 $strSQL = " SELECT
					  to_char(fin_conta_pagar_receber.vlr_conta,'99999999999999d99') AS vlr_conta 
					, to_char(fin_conta_pagar_receber.vlr_mora_multa,'99999999999999d99') AS vlr_mora_multa
					, to_char(fin_conta_pagar_receber.vlr_outros_acresc,'99999999999999d99') AS vlr_outros_acresc
					, fin_conta_pagar_receber.num_documento
					, fin_conta_pagar_receber.nosso_numero
					, current_date /*fin_conta_pagar_receber.*/ as dt_emissao
					, fin_conta_pagar_receber.dt_vcto
					, fin_conta_pagar_receber.obs
					, fin_conta_pagar_receber.instrucoes_boleto
					, fin_conta_pagar_receber.situacao
					, fin_conta_pagar_receber.ano_vcto
					, fin_conta_pagar_receber.historico
					
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT cod_pf FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT cod_pj FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT cod_pj_fornec FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_codigo
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT razao_social FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_nome
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT cpf FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT cnpj FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT cnpj FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS sacado_cnpj
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_cep FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_cep FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_cep FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_cep
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endcobr_rotulo FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_rotulo
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_logradouro FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_logradouro FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_logradouro FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_logradouro
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_numero FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_numero FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_numero FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_numero 
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_complemento FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_complemento FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_complemento FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_complemento 
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_bairro FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_bairro FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_bairro FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_bairro 
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_cidade FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_cidade FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_cidade FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_cidade 
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_estado FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_estado FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_estado FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS cli_endcobr_estado 
					
					
					
					, prd_pedido.cli_cep         AS ped_endcobr_cep
					, prd_pedido.cli_rotulo      AS ped_endcobr_rotulo
					, prd_pedido.cli_logradouro  AS ped_endcobr_logradouro
					, prd_pedido.cli_num         AS ped_endcobr_numero
					, prd_pedido.cli_complemento AS ped_endcobr_complemento
					, prd_pedido.cli_bairro      AS ped_endcobr_bairro
					, prd_pedido.cli_cidade      AS ped_endcobr_cidade
					, prd_pedido.cli_estado      AS ped_endcobr_estado			
					, fin_conta_pagar_receber.link_boleto
					
					FROM fin_conta_pagar_receber 
					INNER JOIN cad_pj 		       ON (fin_conta_pagar_receber.codigo = cad_pj.cod_pj)
					INNER JOIN cfg_boleto 	   	   ON (fin_conta_pagar_receber.cod_cfg_boleto = cfg_boleto.cod_cfg_boleto)
					LEFT OUTER JOIN prd_pedido 	   ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido)
					WHERE fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodDado;
				
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
//		$impresso = getValue($objRS,"impresso");
		$linkBoleto = getValue($objRS,"link_boleto");
		//die();
		//echo "<br><br><strong>ORIGEM: </strong>".$strFtransOrigem = setsession(CFG_SYSTEM_NAME . "_cod_evento"		      , $intCodEvento); ;
//		setsession(CFG_SYSTEM_NAME . "_tbl_origem"		      , getValue($objRS,"tbl_origem"));
		//echo("<strong>session: </strong>".getsession("datawide_tbl_origem"));
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

//se tem boleto vai direto para o link do boleto
//se nao tem boleto, gera novamente o mesmo, pois tivemos problemas com boletos passados em função da biblioteca do bradesco.
//caso nao tenha, serão lidos os dados e inseridos novamente para que ganhe um novo ID do boleto, bem como sera excluido o boleto antigo para fins de segurança

if ($linkBoleto != ""){
	header("Location: ".$linkBoleto);	
}

//	substr($dtStr,0,60)
	$DadosBoleto_NOSSO_NUMERO	 = getValue($objRS,"nosso_numero");
	$DadosBoleto_NUM_DOCUMENTO	 = getValue($objRS,"num_documento");
	$DadosBoleto_SACADO_NOME 	 = getValue($objRS,"sacado_nome");
	$DadosBoleto_SACADO_CODIGO	 = getValue($objRS,"sacado_codigo");
	$DadosBoleto_SACADO_CNPJ 	 = getValue($objRS,"sacado_cnpj");
	$DadosBoleto_HISTORICO       = getValue($objRS,"historico");
	   
	$DadosBoleto_SACADO_LOGRADOURO  = getValue($objRS,"cli_endcobr_logradouro");
	$DadosBoleto_SACADO_NUMERO      = getValue($objRS,"cli_endcobr_numero");
	$DadosBoleto_SACADO_COMPLEMENTO = getValue($objRS,"cli_endcobr_complemento");
	$DadosBoleto_SACADO_BAIRRO		= getValue($objRS,"cli_endcobr_bairro");
	$DadosBoleto_SACADO_CIDADE 		= getValue($objRS,"cli_endcobr_cidade");
	$DadosBoleto_SACADO_ESTADO 		= getValue($objRS,"cli_endcobr_estado");
	$DadosBoleto_SACADO_CEP 		= trim(getValue($objRS,"cli_endcobr_cep"));
	$DadosBoleto_SACADO_CEP         = trim(str_replace(".","", str_replace("-","",$DadosBoleto_SACADO_CEP)));
	//$DadosBoleto_SACADO_CEP         = ($DadosBoleto_SACADO_CEP != "") ? "CEP: ".$DadosBoleto_SACADO_CEP : "";
	$DadosBoleto_DT_VCTO    = getValue($objRS,"dt_vcto");
	//$DadosBoleto_DT_VCTO    = "2018-10-25";
	$DadosBoleto_DT_EMISSAO = getValue($objRS,"dt_emissao");
	$DadosBoleto_VLR_TITULO = getValue($objRS,"vlr_conta");
	$DadosBoleto_OBS        = getValue($objRS,"obs");
	//$DadosBoleto_VLR_TITULO = "1,00";

	$DadosBoleto_CEDENTE_NOME_SIMPLES = getVarEntidade($objConn, "nome_fan");
if (getValue($objRS,"dt_vcto") < now()) {
	echo("Data de venciamento invalida.");
	die();
}
//AQUI INICIAM AS FUNÇÕES PARA GERAÇÃO DO JSON DO SHOP FACIL
$merchantId = getVarEntidade($objConn, "bradesco_shopfacil_merchanid");

$chaveSeguranca = getVarEntidade($objConn, "bradesco_shopfacil_chave_seguranca");


$data_service_pedido = array(
							 "numero" => utf8_encode($DadosBoleto_NUM_DOCUMENTO),
							 "valor" => utf8_encode(str_replace(".","",str_replace(",","",$DadosBoleto_VLR_TITULO))),
							 "descricao" => utf8_encode($DadosBoleto_HISTORICO)
							 );
						
$data_service_comprador_endereco = array(
										 "cep" => utf8_encode(trim($DadosBoleto_SACADO_CEP)),
										 "logradouro" => utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_LOGRADOURO)),
										 "numero" => utf8_encode($DadosBoleto_SACADO_NUMERO),
										 "complemento" => utf8_encode($DadosBoleto_SACADO_COMPLEMENTO),
										 //"numero" => utf8_encode("."),
										 //"complemento" => utf8_encode(""),
										 "bairro" => utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_BAIRRO)),
										 "cidade" =>utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_CIDADE)),
										 "uf" => utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_ESTADO))
										);

										
$data_service_comprador = array(
								"nome" => utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_NOME)),
								"documento" => utf8_encode($DadosBoleto_SACADO_CNPJ),
								"endereco" => ($data_service_comprador_endereco),								
								"ip" => utf8_encode($_SERVER["REMOTE_ADDR"]),
								"user_agent" => utf8_encode($_SERVER["HTTP_USER_AGENT"])
								);

$data_service_boleto_registro = null;
$data_service_boleto_instrucoes = array(
										"instrucao_linha_1" =>  utf8_encode(substr($DadosBoleto_OBS,0,60)),
										"instrucao_linha_2" =>  utf8_encode(substr($DadosBoleto_OBS,61,121)),
										"instrucao_linha_3" =>  utf8_encode(substr($DadosBoleto_OBS,122,182)),
										"instrucao_linha_4" =>  utf8_encode(substr($DadosBoleto_OBS,183,245)),
										"instrucao_linha_5" =>  null,
										"instrucao_linha_6" =>  null,
										"instrucao_linha_7" =>  null,
										"instrucao_linha_8" =>  null,
										"instrucao_linha_9" =>  null,
										"instrucao_linha_10" => null,
										"instrucao_linha_11" => null,
										"instrucao_linha_12" => null,
										);

							 //"valor_titulo" => $DadosBoleto_VLR_TITULOforma1,			
							 //"data_emissao" => utf8_encode("2017-11-30"),
							 //"data_vencimento" => utf8_encode("2017-12-25"),	
							 //echo "dtvcto: ".$DadosBoleto_DT_VCTOforma1;
							 //echo utf8_encode($DadosBoleto_DT_VCTOforma1);
$data_service_boleto = array(
							 "beneficiario" => utf8_encode($DadosBoleto_CEDENTE_NOME_SIMPLES),
							 "carteira" => utf8_encode("26"),
							 "nosso_numero" => utf8_encode($DadosBoleto_NOSSO_NUMERO),							 
							 "valor_titulo" => utf8_encode(str_replace(".","",str_replace(",","",$DadosBoleto_VLR_TITULO))),
							 "data_emissao" => utf8_encode($DadosBoleto_DT_EMISSAO),							 
							 "data_vencimento" => utf8_encode($DadosBoleto_DT_VCTO),				 
							 "url_logotipo" => null,
							 "mensagem_cabecalho" => null,
							 "tipo_renderizacao" => utf8_encode("2"),
							 "instrucoes" => ($data_service_boleto_instrucoes),
							 "registro" => ($data_service_boleto_registro)
							);

$data_service_request = array(
							  "merchant_id" => utf8_encode($merchantId),
							  "meio_pagamento" => utf8_encode("300"),
							  "pedido" => $data_service_pedido,
							  "comprador" => $data_service_comprador,
							  "boleto" => $data_service_boleto,
							  "token_request_confirmacao_pagamento" => null
							  );
							  

			  
$data_post = json_encode(($data_service_request));
//print_r($data_post);
//die();
//$url = "https://homolog.meiosdepagamentobradesco.com.br/apiboleto/transacao";
//$url = "https://homolog.meiosdepagamentobradesco.com.br/apiboleto/transacao";
$url = "https://meiosdepagamentobradesco.com.br/apiboleto"."/transacao";
//Configuracao do cabecalho da requisicao
$mediaType = "application/json";
$charSet   = "utf-8";
//die();
$headers = array();
$headers[] = "Accept: ".$mediaType;
$headers[] = "Accept-Charset: ".$charSet;
$headers[] = "Accept-Encoding: ".$mediaType;
$headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
$AuthorizationHeader = $merchantId.":".$chaveSeguranca;
$AuthorizationHeaderBase64 = base64_encode($AuthorizationHeader);
$headers[] = "Authorization: Basic ".$AuthorizationHeaderBase64;
	
	
	
	//print_r($headers);
//	echo "datapost:" . $data_post;
//	die();
	$ch = curl_init();
	//echo $ch;
	//echo $data_post;
	//die();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	//echo($ch);
	$resultado="";
	$result = curl_exec($ch);
	echo("error: " .curl_error($ch));
	//$resultado = json_decode($result,true);
	//print_r($resultado);
	//die();
	//if (curl_error($ch)) {
	 //   echo $error_msg = curl_error($result);
	//	die();
	//}else{


			//var_dump(get_resource_type($ch));
						if ($result){
							$resultado = array();
							$link      = array();
							$resultado = json_decode($result,true);
							//print_r($resultado);
							$link = $resultado["boleto"];
							print_r($link);

							//echo		$strSQL = "update fin_conta_pagar_receber set link_boleto = '". $link["url_acesso"]."' where cod_conta_pagar_receber = ".$intCodDado;	
							try{
								$strSQL = "update fin_conta_pagar_receber set link_boleto = '". $link["url_acesso"]."' where cod_conta_pagar_receber = ".$intCodDado;
								$objResult = $objConn->query($strSQL);
							}
							catch(PDOException $e){
								mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
								die();
							}
							//echo("<br><br><strong>LINK: </strong>".$link["url_acesso"]);
							$linkBoleto = $link["url_acesso"];
							//header("Location: ".$linkBoleto);
							if ($link["url_acesso"]!=""){
							?>
								<script language="javascript">
										location.href='<?php echo($link["url_acesso"]);?>';
								</script>
							<?php
							}else {print_r($resultado);die();}
						}
						else {
							mensagem("err_sql_titulo","err_titulo_nao_encontrado","","","erro",1);
							die();
						}
				//}
						$objResult->closeCursor();
						$objConn = NULL;
?>