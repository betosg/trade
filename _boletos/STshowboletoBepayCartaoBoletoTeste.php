<?php

include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
include_once("../_database/athsendmail.php");
erroReport();
$strUserLogado = getsession(CFG_SYSTEM_NAME . "_id_usuario");

function phoneValidate($phone){
	//$regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/';
	$regex = '/^\(?[1-9]{2}\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/';

	if (preg_match($regex, $phone) == false) {
		//echo("O número não foi validado.");
		return false;
	} else {
		//echo("Telefone válido.");
		return true;
	}        
}


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$strRequest =  request('var_dados')."|";


$varDB="";
 //$intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
$varDB = request('var_db');
if ($varDB == ""){$varDB = CFG_DB;}
$objConn = abreDBConn($varDB);

$strRequest  =  explode("|",$strRequest);
//echo "<br>".count($strRequest)."<br>";
$strTipo             = request("var_tipo");
$strNomeCartao       = request("var_nome_cartao");
$strCPFCartao        = request("var_cpf_titular");
$strNumCartao        = request("var_cartao");
$strCvvCartao        = request("var_cod_cartao");
$strMesCartao        = request("var_mes_cartao");
$strAnoCartao        = request("var_ano_cartao");
$strBandeira         = request("var_bandeira");
$intCodDado          = request("var_cod_conta_pagar_receber");
$intParcela          = request("var_parcela");
if ($intParcela ==""){$intParcela = 1;}


//die();


session_id();
rand();
rand();
substr(rand(),0,2);
substr(rand(),0,2);
$strSessionCard = substr(rand(),0,2).substr(rand(),0,-2);




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
					,  CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT email FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   /*WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_estado FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_estado FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)*/
					  END AS cli_endcobr_email
					,  CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT endprin_fone2 FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   /*WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT endprin_estado FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT end_estado FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)*/
					  END AS cli_endcobr_fone
					
					
					, prd_pedido.cli_cep         AS ped_endcobr_cep
					, prd_pedido.cli_rotulo      AS ped_endcobr_rotulo
					, prd_pedido.cli_logradouro  AS ped_endcobr_logradouro
					, prd_pedido.cli_num         AS ped_endcobr_numero
					, prd_pedido.cli_complemento AS ped_endcobr_complemento
					, prd_pedido.cli_bairro      AS ped_endcobr_bairro
					, prd_pedido.cli_cidade      AS ped_endcobr_cidade
					, prd_pedido.cli_estado      AS ped_endcobr_estado			
					, prd_pedido.it_descricao    AS ped_it_descricao
					, cad_pf.cod_pf
					, fin_conta_pagar_receber.link_boleto
					, to_char(fin_conta_pagar_receber.dt_vcto,'yyyy-mm-dd') as vcto_calc 
                	, to_char(CURRENT_DATE,'yyyy-mm-dd')                    as emissao_calc 
					, sequencial_boleto+1 AS sequecial_boleto
					, cad_pf_curriculo.pendencia
					FROM fin_conta_pagar_receber 
					INNER JOIN cad_pf 		       ON (fin_conta_pagar_receber.codigo = cad_pf.cod_pf)
					LEFT  JOIN cad_pf_curriculo    ON (cad_pf.cod_pf = cad_pf_curriculo.cod_pf)
					LEFT  JOIN cfg_boleto 	   	   ON (fin_conta_pagar_receber.cod_cfg_boleto = cfg_boleto.cod_cfg_boleto)
					LEFT  OUTER JOIN prd_pedido 	   ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido)
					WHERE fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodDado ."
					AND fin_conta_pagar_receber.situacao in('aberto', 'lcto_parcial')";
					
//echo $strSQL;
//						FROM fin_conta_pagar_receber t1 
//						LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) 
//						INNER JOIN cad_pf ON (cad_pf.cod_pf = t1.codigo)
//						WHERE t1.tipo = 'cad_pf' AND t1.codigo = ".$intCodDado."

				
		$objResult = $objConn->query($strSQL);
		if($objResult->rowCount() <= 0) {
			mensagem("Transação Efetivada","Sua transação foi feita, e está em processamento.","","","erro",1);
			die();
		}
		$objRS = $objResult->fetch();
//		$impresso = getValue($objRS,"impresso");
		//$linkBoleto = getValue($objRS,"link_boleto");
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

//if ($linkBoleto != ""){
//	header("Location: ".$linkBoleto);	
//}

//	substr($dtStr,0,60)

	$strBoletoInstucao              = getVarEntidade($objConn, "nome_fan") . "  |  ".getValue($objRS,"ped_it_descricao");
	$DadosBoleto_NUM_DOCUMENTO	    = getValue($objRS,"num_documento");
	$DadosBoleto_SACADO_NOME 	    = getValue($objRS,"sacado_nome");
	$DadosBoleto_SACADO_CODIGO	    = getValue($objRS,"sacado_codigo");
	$DadosBoleto_SACADO_CNPJ 	    = getValue($objRS,"sacado_cnpj");
	$DadosBoleto_HISTORICO          = getValue($objRS,"historico");	   
	$DadosBoleto_SACADO_LOGRADOURO  = getValue($objRS,"cli_endcobr_logradouro");
	$DadosBoleto_SACADO_NUMERO      = getValue($objRS,"cli_endcobr_numero");
	$DadosBoleto_SACADO_COMPLEMENTO = getValue($objRS,"cli_endcobr_complemento");
	$DadosBoleto_SACADO_BAIRRO		= getValue($objRS,"cli_endcobr_bairro");
	$DadosBoleto_SACADO_CIDADE 		= getValue($objRS,"cli_endcobr_cidade");
	$DadosBoleto_SACADO_ESTADO 		= getValue($objRS,"cli_endcobr_estado");
	$DadosBoleto_SACADO_CEP 		= trim(getValue($objRS,"cli_endcobr_cep"));
	$DadosBoleto_SACADO_CEP         = trim(str_replace(".","", str_replace("-","",$DadosBoleto_SACADO_CEP)));
	$DadosBoleto_SACADO_FONE        = trim(str_replace(" ","",str_replace(".","",str_replace("-","", str_replace(")","", str_replace("(","",getValue($objRS,"cli_endcobr_fone")))))));
	$strCelular = getValue($objRS,"cli_endcobr_fone");
	$DadosBoleto_SACADO_EMAIL 	    = getValue($objRS,"cli_endcobr_email");	
	
	$DadosBoleto_DT_VCTO            = getValue($objRS,"dt_vcto");	
	$DadosBoleto_DT_EMISSAO         = getValue($objRS,"dt_emissao");
	$DadosBoleto_VLR_TITULO         = str_replace(",",".",str_replace(".","",getValue($objRS,"vlr_conta")));
	$DadosBoleto_OBS                = getValue($objRS,"obs");
	$strPendencia                   = getValue($objRS,"pendencia");
	//$DadosBoleto_VLR_TITULO = "1,00";

	$DadosBoleto_CEDENTE_NOME_SIMPLES = getVarEntidade($objConn, "nome_fan");
//if (getValue($objRS,"dt_vcto") < now()) {
//	echo("Data de venciamento invalida.");
//	die();
//}
 $urlCallBack = getVarEntidade($objConn, "callbackBepay");

	




/*dados oficiais*/
$ApiAccessKey      = "49D25D01-B062-4A6C-B9B5-570329228983";
$secret            = "AA511697-7681-416C-82F2-DD5C83E5AE67";
$MediatorAccountId = "D0A2FFE1-46A2-0086-D033-3AC41A82C085";
$SellerAccountId   = "1C8758AE-3FD4-22F3-F861-3A77A5A254C2";
$url =      "https://api.bepay.com/v1/payments";
/*fim dados oficiais*/

/* dados para teste
echo "<br>Api access key:    ".$ApiAccessKey      = "2F10A163-5420-4ED1-A2A8-63D5D3F28F61";
echo "<br>secret:            ".$secret            = "C801505D-E51F-4A12-B0D4-8E03D1D252AB";
echo "<br>MediatorAccountId: ".$MediatorAccountId = "A21D5F48-A434-8F73-A3AB-78822FF6FF71";
echo "<br>SellerAccountId:   ".$SellerAccountId   = "A21D5F48-A434-8F73-A3AB-78822FF6FF71";	
echo "<br>url: ".$url =      "https://homolog-api.bepay.com/v1/payments";

 */

$strCPF = $DadosBoleto_SACADO_CNPJ;
$strValorConta = $DadosBoleto_VLR_TITULO;

//////$valoresHash =trim($strCPF.intval($strValorConta).$SellerAccountId.intval($strValorConta));
//$valoresHash = "1500991686010A21D5F48-A434-8F73-A3AB-78822FF6FF7110";
//echo "<br>". $hash = "ad0b53423e6bb5b5c9806a15b89261003d6644a589b151a54a1287002b1f63b5";
if($strTipo == "boleto"){
    $valoresHash =trim($strCPF.intval($strValorConta).$SellerAccountId.intval($strValorConta));
    $strIdentificador = "10812688000168ABFM".getValue($objRS,"sequecial_boleto").getValue($objRS,"cod_pf").$intCodDado;
}else{
   $valoresHash = trim($strCPF . $strNumCartao .intval($strValorConta). $SellerAccountId . intval($strValorConta));
   $strIdentificador = "10812688000168ABFM".$strSessionCard.getValue($objRS,"cod_pf").$intCodDado;
}

$AuthorizationHeaderBase64 = trim(hash_hmac('sha256', $valoresHash, $secret));

$date1 = (getValue($objRS,"vcto_calc"));
$date2 = (getValue($objRS,"emissao_calc"));
$strVcto = "C0".dateDifference($date1, $date2);
$strVcto = "C005";




//print_r($data_post);
$strMsg = "";
if ($DadosBoleto_SACADO_LOGRADOURO == ""){
	$strMsg .= " - Logradouro.\\r\\n";
}
if ($DadosBoleto_SACADO_NUMERO == ""){
	$strMsg .= " - Número do endereço.\\r\\n";
}

if ($DadosBoleto_SACADO_BAIRRO == ""){
	$strMsg .= " - Bairro.\\r\\n";
}
if ($DadosBoleto_SACADO_CIDADE == ""){
	$strMsg .= " - Cidade.\\r\\n";
}
if ($DadosBoleto_SACADO_ESTADO == ""){
	$strMsg .= " - Estado.\\r\\n";
}
if ($DadosBoleto_SACADO_CEP == ""){
	$strMsg .= " - CEP.\\r\\n";
}
if ($DadosBoleto_SACADO_FONE == ""){
	$strMsg .= " - CELULAR.\\r\\n";
}

//die(phoneValidate($strCelular));

//$strCelular = substr($DadosBoleto_SACADO_FONE, 1, strlen($DadosBoleto_SACADO_FONE));
//if (strlen($DadosBoleto_SACADO_FONE)!=11){
if (!phoneValidate($strCelular)){
	$strMsg .=  " - CELULAR inválido.\\r\\n O número deve ser composto por 11 98888-7777.";
}
//if (substr($strCelular,0,1)!=9){
//	$strMsg .= substr($strCelular,0,1) ."  " .$strCelular." - CELULAR INVÁLIDO.\\r\\n";
//}

if ($DadosBoleto_SACADO_CNPJ == ""){
	$strMsg .= " - CPF.\\r\\n";
}
if ($DadosBoleto_SACADO_EMAIL == ""){
	$strMsg .= " - E-mail.\\r\\n";
}




if ($strMsg !=""){ ?>
	<script language="javascript">
		<?php if (!isset($_GET["var_basename"])){?>
			alert('<?php echo("Atualize seus dados pessoais, e verifique os campos abaixo para emissão do seu boleto.\\r\\n".$strMsg);?>');		
			window.parent.document.location.href = "../modulo_PainelPF/STCadPFOpen.php"
		<?php } else {?>
			alert('<?php echo("Atualize seus dados pessoais, e verifique os campos abaixo para emissão do seu boleto.\\r\\n".$strMsg);?>');		
			history.go(-1);	 
	    <?php } ?>
		//
		//history.go(-1);
	</script>
	
<?php die();} 



$arrPhone [] = 	array(
	"country"     => utf8_encode("BRA"),
	"phoneNumber" => utf8_encode($DadosBoleto_SACADO_FONE)
);
$arrTaxIdentifier = array(
	"taxId"    => utf8_encode($DadosBoleto_SACADO_CNPJ), //cpf
	"country"  => utf8_encode("BRA")
		);
$arrClient = array(
	"name"          => utf8_encode($DadosBoleto_SACADO_NOME),	
	"mobilePhones"  => $arrPhone,
    "taxIdentifier" => $arrTaxIdentifier,
    "email"         => utf8_encode($DadosBoleto_SACADO_EMAIL)
);
$arrClient = array("client" => $arrClient);



$arrBoleto = array(
	"bank"              =>  utf8_encode("237"),
	"shopperStatement"  =>  utf8_encode(strtoupper($strBoletoInstucao . "   (" . getValue($objRS,"cod_pf").".".$intCodDado.")")),
	"accountingMethod"  =>  utf8_encode($strVcto)
);

$arrAddress = array(	
	"logradouro"  => utf8_encode(getNormalStringASLXml(removeAcento($DadosBoleto_SACADO_LOGRADOURO))),
	"numero"      => utf8_encode(trim($DadosBoleto_SACADO_NUMERO)),
	"complemento" => utf8_encode($DadosBoleto_SACADO_COMPLEMENTO),		
	"bairro"      => utf8_encode(getNormalStringASLXml(removeAcento($DadosBoleto_SACADO_BAIRRO))),
	"cidade"      => utf8_encode(getNormalStringASLXml(removeAcento($DadosBoleto_SACADO_CIDADE))),
	"estado"      => utf8_encode(getNormalStringASLXml($DadosBoleto_SACADO_ESTADO)),
	"cep"         => utf8_encode($DadosBoleto_SACADO_CEP),
	"pais"        => utf8_encode("BRA")
   );


/* os tres arrays abaixo sao utilizados somente para o cartao */
if ($strTipo == "cartao"){
    $arrHolderTax = array(
            "taxId"    => $strCPFCartao,
            "country"  => "BRA" 
    );
    $arrCreditCard = array(         
            "cardType"         => $strBandeira      ,        
            "cardNumber"       => $strNumCartao     ,                             
            "expirationMonth"  => $strMesCartao     ,      
            "expirationYear"   => $strAnoCartao     ,       
            "cvv"              => $strCvvCartao     ,       
            "softDescriptor"   => "Bepay*ABFM"      ,  
            "nameOnCard"       => $strNomeCartao    ,                
            "holderTaxId"      => $arrHolderTax     ,
            "installments"     => $intParcela
        );
    $arrOrder = array(
        "orderId"     => str_pad($intCodDado,10,"0",STR_PAD_LEFT),
        "dateTime"    => date("Y-m-d")."T".date("H:i:s")."+03:00",
        "description" => strtoupper($strBoletoInstucao . "   (" . getValue($objRS,"cod_pf").".".$intCodDado.")")
    );
    $arrPaymentInfo = array(
        "transactionType" => utf8_encode("CreditCard"),
        "creditCard"          => $arrCreditCard
        
	);
	if (getVarEntidade($objConn, "mediatorFeeCartao") == "0"){
		//	$mediatorFee = getVarEntidade($objConn, "mediatorFeeCartaoPorcentual");
		$mediatorFee = $DadosBoleto_VLR_TITULO * (getVarEntidade($objConn, "mediatorFeeCartaoPorcentual")/100);
		//die($mediatorFee);
	}else{
		$mediatorFee = getVarEntidade($objConn, "mediatorFeeCartao");
	}

	
	
}else{
    $arrPaymentInfo = array(
        "transactionType" => utf8_encode("Boleto"),
        "boleto"          => $arrBoleto,
        "billingAddress"  => $arrAddress
	);
	if (getVarEntidade($objConn, "mediatorFeeBoleto") != "0"){
	$mediatorFee = getVarEntidade($objConn, "mediatorFeeBoleto");
	}else{
		$mediatorFee = getVarEntidade($objConn, "mediatorFeeBoletoPorcentual");
	}
}

$arrMyAccount = array(
	"accountId" => $MediatorAccountId
);

$arrAccountId = array(
	"accountId" => $SellerAccountId
);

if ($strTipo=="cartao"){
    $arrRecipients[]= array(
        "account"       => $arrAccountId,
        "order"         => $arrOrder,
        "amount"        => utf8_encode(trim($DadosBoleto_VLR_TITULO)),
        "mediatorFee"   => utf8_encode(round($mediatorFee,2)),
        "currency"      => utf8_encode("BRL")
    );
}else{
    $arrRecipients[]= array(
        "account"       => $arrAccountId,
        "amount"        => utf8_encode(trim($DadosBoleto_VLR_TITULO)),
        "mediatorFee"   => utf8_encode(round($mediatorFee,2)),
        "currency"      => utf8_encode("BRL")
    );
}

//$arrTransacao = array(
//	"totalAmount"      	   => utf8_encode($DadosBoleto_VLR_TITULO),
//	"currency"             => utf8_encode("BRL"),
//	"externalIdentifier"   => utf8_encode($strIdentificador),
//	"callbackAddress"      => utf8_decode("https:/tradeunion.proevento.com.br/_tradeunion/_boletos/STboletoBepayCallback.php?var_db=tradeunion_abfm"),
//	"sender"               => $arrClient ,
//	"paymentInfo"          => $arrPaymentInfo,
//	"myAccount"            => $arrMyAccount, 
//	"recipients"           => $arrRecipients
//);


$arrTransacao = array(
	"totalAmount"      	   => utf8_encode(trim($DadosBoleto_VLR_TITULO)),
	"currency"             => utf8_encode("BRL"),
	"paymentInfo"          => $arrPaymentInfo,	
	"sender"               => $arrClient ,	
	"myAccount"            => $arrMyAccount, 
	"recipients"           => $arrRecipients,
	"externalIdentifier"   => utf8_encode($strIdentificador),
	"callbackAddress"      => utf8_decode($urlCallBack)
);



//print_r($arrTransacao)			  ;
$data_post = json_encode($arrTransacao,true);

    
//print("<hr><br>Data Post<br><br>");
//print_r($data_post);

//print("<hr>");


//Configuracao do cabecalho da requisicao
$mediaType = "application/json";
$charSet   = "utf-8";
//die();
$headers = array();
$headers[] = "Accept: ".$mediaType;
$headers[] = "Accept-Charset: ".$charSet;
$headers[] = "Accept-Encoding: ".$mediaType;
$headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
$headers[] = "Api-Access-Key:". $ApiAccessKey;
$headers[] = "Transaction-Hash: ".trim($AuthorizationHeaderBase64);
	
//objSvrHTTP.setRequestHeader "Content-Type", "application/json"
//objSvrHTTP.setRequestHeader "Accept", "application/json"
//objSvrHTTP.setRequestHeader "Api-Access-Key", ApiAccessKey
//objSvrHTTP.setRequestHeader "Transaction-Hash", Hash
	
	//print("<hr><br>header<br><br>");
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
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
	$result = curl_exec($ch);
	
	
	
	$resultado="";
	
	
	$resultado=array();				
	$resultado = json_decode($result,true);
    //print_r($resultado);
    if (curl_error($ch)){
		print(curl_error($ch));
		die();
	};
	//print("<hr>".$resultado["data"]["boletoUrl"]);
	//die();
	//print("<hr><br>CurlError<br><br>");
	//header('Location: ' . $resultado['data']['boletoUrl']);
	//echo("error: " .curl_error($ch));
	
	//if (curl_error($ch)){print_r(curl_error($ch));curl_errno($ch)}
	
	
	$sumTotalPago = 0;

	
				
	
	if ($strTipo == "cartao"){
        //print_r($resultado);
        if (trim($resultado["data"]["financialStatement"]["status"])=="APPROVED") {
            $urlStatus       = $resultado['data']['financialStatement']['status'];
			$transactionId   = $resultado['data']['transactionId'];
			
            $transactionDate = $resultado['data']['transactionDate'];
            $totalPago       = $resultado['data']['totalAmount'];
			$strJson = $result;
			

        }else{
			print($resultado["data"]["financialStatement"]["status"] . " ".utf8_decode($resultado["data"]["financialStatement"]["userMessage"]));
			$result = $resultado["data"]["financialStatement"]["status"] . " ".utf8_decode($resultado["data"]["financialStatement"]["userMessage"]);
			$objConn->beginTransaction();
			try{
					$strSQL = "INSERT INTO fin_conta_pagar_receber_log_bepay 
									(nome_sacado,email_sacado, cod_conta_pagar_receber, data_post,	return_post, sys_usr_ins, sys_dtt_ins, transaction_id, identificador, cartao_parcela)
							values  ('".$DadosBoleto_SACADO_NOME."','".$DadosBoleto_SACADO_EMAIL."',".$intCodDado.", '".$data_post."', '" . $result . "', '".$strUserLogado."',now(), null,'".$strIdentificador."', 1) ";
					$objResult = $objConn->query($strSQL);
					$objConn->commit();
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				$objConn->rollBack();
				die();
			}


			die();
        }
    }else{
        $urlBoleto = $resultado['data']['boletoUrl'];
        $transactionId  = $resultado['data']['transactionId'];
    }

print($urlBoleto);
die();

$objConn->beginTransaction();
								try{
										$strSQL = "INSERT INTO fin_conta_pagar_receber_log_bepay 
														(nome_sacado,email_sacado, cod_conta_pagar_receber, data_post,	return_post, sys_usr_ins, sys_dtt_ins, transaction_id, identificador, cartao_parcela)
												values  ('".$DadosBoleto_SACADO_NOME."','".$DadosBoleto_SACADO_EMAIL."',".$intCodDado.", '".$data_post."', '" . $result . "', '".$strUserLogado."',now(), '".$transactionId."','".$strIdentificador."', ".$intParcela.") ";
										$objResult = $objConn->query($strSQL);
										$objConn->commit();
								}catch(PDOException $e){
									mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
									$objConn->rollBack();
									die();
								}




			//var_dump(get_resource_type($ch));
						if ($strTipo == "boleto"){
							
							try{
								
								$strSQL = " update fin_conta_pagar_receber set 
												 /* bepay_identificador_boleto = '".$strIdentificador."'*/
												 /*, link_boleto = '" . $urlBoleto ."'*/
												 sequencial_boleto = sequencial_boleto+1 
											where cod_conta_pagar_receber = ".$intCodDado;
								$objResult = $objConn->query($strSQL);
							}
							catch(PDOException $e){
								mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
								die();
							}
							
							if ($urlBoleto!=""){
                                print($urlBoleto);
							//echo $urlBoleto;
							}else {print_r($resultado);die();}
						}
						else { 
                            $objConn->beginTransaction();
                            try{
                                 

                                    $strSQL = " select  email_sacado, nome_sacado, data_post , cast(data_post AS json) as dtPost";
                                    $strSQL .= " FROM  fin_conta_pagar_receber_log_bepay where transaction_id = '".$transactionId."'";
                                    $objResult = $objConn->query($strSQL);
                                    $objRS = $objResult->fetch();
                                    
                                    
                                    
                                    $strSQL = " UPDATE fin_conta_pagar_receber_log_bepay SET callback_data = '".$strJson."', callback_valor = ".$totalPago .", callback_dt_processamento = current_timestamp ";
                                    $strSQL .= " where transaction_id = '".$transactionId."'";
                                    $objConn->query($strSQL);
                        
                                    $strSQL  = "select cod_conta_pagar_receber,   cod_conta , cod_plano_conta ,cod_centro_custo ,cod_job, tipo, codigo, historico ";
                                    $strSQL .= " FROM fin_conta_pagar_receber  where cod_conta_pagar_receber in( select  cod_conta_pagar_receber ";
                                    $strSQL .= "                                                                FROM  fin_conta_pagar_receber_log_bepay where transaction_id = '".$transactionId."')";
                                    $objResult = $objConn->query($strSQL);
                                    $objRS = $objResult->fetch();
                        
                                    $objConn->commit();
                        
                                    $intCodDado        = getValue($objRS,"cod_conta_pagar_receber");
                                    $intCodConta       = getValue($objRS,"cod_conta");
                                    $intCodPlanoConta  = getValue($objRS,"cod_plano_conta");
                                    $intCodCentroCusto = getValue($objRS,"cod_centro_custo");									
									$intCodJob         = "61";									
                                    $strTipo           = getValue($objRS,"tipo");
                                    $intCodigo         = getValue($objRS,"codigo");
                                    $strHistorico      = getValue($objRS,"historico");

                                    $strObs            = "Lançamento automatico callback ".$transactionId;
                                    $strDocumento  = "CARTAO";
                                    $strNumLcto    = "TITULO: ". getValue($objRS,"cod_conta_pagar_receber");
									 
									$dblVlrLctoNorm = $totalPago;
                                    $dblVlrMulta = 0;
                                    $dblVlrJuros = 0;
                                    $dblVlrDesc  = 0;
                                    $numDocumento = "";
                                    $strExtraDocumento = "" ;
                                    $strSysUsuario = "BEPAY";
                


		setsession(CFG_SYSTEM_NAME."_dir_cliente","ABFM");
		setsession(CFG_SYSTEM_NAME."_id_usuario","BEPAY");
		setsession(CFG_SYSTEM_NAME."_db","tradeunion_abfm");
		

		//Configuracao do cabecalho da requisicao
$url = "https://tradeunion.proevento.com.br/_tradeunion/modulo_FinContaPagarReceberPF/STshowrecibonormal.php?var_dir_cliente=abfm&var_db=tradeunion_abfm&var_duas_vias=sim&var_somente_arquivo=sim&var_chavereg=".$intCodDado;
$mediaType = "application/json";
$charSet   = "utf-8";
//die();
$headers = array();
$headers[] = "Accept: */*";
//$headers[] = "Accept-Charset: ".$charSet;
$headers[] = "Accept-Encoding: gzip, deflate, br";
$headers[] = "Content-Type: charset=".$charSet;
//$headers[] = "Api-Access-Key:". $ApiAccessKey;
//$headers[] = "Transaction-Hash: ".trim($AuthorizationHeaderBase64);


//print("<hr><br>header<br><br>");
//print_r($headers);
//	echo "datapost:" . $data_post;
//	die();
$ch = curl_init();
//echo $ch;
//echo $data_post;
//die();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_FAILONERROR, false);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//echo($ch);
// extract header
$result = curl_exec($ch);
//    curl_getinfo($result);
//echo "pos: ".strpos( $result,"[");
$strLinkFile = substr($result,strpos( $result,"[")+1,strpos($result,"]")-2);
// echo 'Curl error: ' . curl_error($ch);
$strLink = ("https://tradeunion.proevento.com.br/".str_replace ("]","",str_replace ("../../","",$strLinkFile)));
print($strLink);

$strCorpo = "Olá <b>".$DadosBoleto_SACADO_EMAIL."</b><br><br>Seu pagamento foi processado e recebido na data de ".date('d/m/Y')."<br><br>" . "<b><a href='".$strLink."' target='_blank'>Clique aqui para acessar seu recibo." . "</a></b><br><br>";
emailNotify($strCorpo, "ABFM Informa recibo pagamento titulo: ".$intCodDado, $DadosBoleto_SACADO_EMAIL, CFG_EMAIL_SENDER);
				
//$headerSize = curl_getinfo($result);
////$header = substr($result, 0, $headerSize);
////$header = getHeaders($header);
//
//// extract body
//$body = substr($result, $headerSize);
//print_r(curl_getinfo($result));





                            
                    }
                    catch(PDOException $e){
                        mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
                        $objConn->rollBack();
                        die();
                    }
                
                }
					
					setsession(CFG_SYSTEM_NAME."_id_usuario", $strUserLogado);
				//}
						$objResult->closeCursor();
						$objConn = NULL;







?>



