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
		//echo("O n�mero n�o foi validado.");
		return false;
	} else {
		//echo("Telefone v�lido.");
		return true;
	}        
}


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");



$varDB="";
$strMsg = "";
 //$intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
$varDB = request('var_db');
if ($varDB == ""){$varDB = CFG_DB;}

$objConn = abreDBConn("tradeunion_abfm");

$strTipo             = request("var_tipo");
//$strNomeCartao       = request("var_nome_cartao");
//$strCPFCartao        = request("var_cpf_titular");
//$strNumCartao        = request("var_cartao");
//$strCvvCartao        = request("var_cod_cartao");
//$strMesCartao        = request("var_mes_cartao");
//$strAnoCartao        = request("var_ano_cartao");
//$strBandeira         = request("var_bandeira");
//$intParcela          = request("var_parcela");
//if ($intParcela ==""){$intParcela = 1;}

$intCodDado          = request("var_cod_conta_pagar_receber");

//die();


session_id();
rand();
rand();
substr(rand(),0,2);
substr(rand(),0,2);
$strSessionCard = substr(rand(),0,2).substr(rand(),0,-2);







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
					

				
		$objResult = $objConn->query($strSQL);
		if($objResult->rowCount() <= 0) {
			mensagem("Transa��o Efetivada","Sua transa��o foi feita, e est� em processamento.","","","erro",1);
			die();
		}
		$objRS = $objResult->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

	$DadosBoleto_SACADO_CNPJ 	    = getValue($objRS,"sacado_cnpj");
	$DadosBoleto_SACADO_NOME 	    = getValue($objRS,"sacado_nome");
	$DadosBoleto_SACADO_EMAIL 	    = getValue($objRS,"cli_endcobr_email");	
	$DadosBoleto_SACADO_CODIGO	    = getValue($objRS,"sacado_codigo");
	$DadosBoleto_HISTORICO          = getValue($objRS,"historico");	
	
	$DadosBoleto_VLR_TITULO         = str_replace(",",".",str_replace(".","",getValue($objRS,"vlr_conta")));
   
		//$DadosBoleto_SACADO_LOGRADOURO  = getValue($objRS,"cli_endcobr_logradouro");
	//$DadosBoleto_SACADO_NUMERO      = getValue($objRS,"cli_endcobr_numero");
	//$DadosBoleto_SACADO_COMPLEMENTO = getValue($objRS,"cli_endcobr_complemento");
	//$DadosBoleto_SACADO_BAIRRO		= getValue($objRS,"cli_endcobr_bairro");
	//$DadosBoleto_SACADO_CIDADE 		= getValue($objRS,"cli_endcobr_cidade");
	//$DadosBoleto_SACADO_ESTADO 		= getValue($objRS,"cli_endcobr_estado");
	//$DadosBoleto_SACADO_CEP 		= trim(getValue($objRS,"cli_endcobr_cep"));
	//$DadosBoleto_SACADO_CEP         = trim(str_replace(".","", str_replace("-","",$DadosBoleto_SACADO_CEP)));
	//$DadosBoleto_SACADO_FONE        = trim(str_replace(" ","",str_replace(".","",str_replace("-","", str_replace(")","", str_replace("(","",getValue($objRS,"cli_endcobr_fone")))))));
	//$strCelular = getValue($objRS,"cli_endcobr_fone");
	//$DadosBoleto_DT_VCTO            = getValue($objRS,"dt_vcto");	
	//$DadosBoleto_DT_EMISSAO         = getValue($objRS,"dt_emissao");
	
	//$DadosBoleto_OBS                = getValue($objRS,"obs");
	//$strPendencia                   = getValue($objRS,"pendencia");
	//$DadosBoleto_CEDENTE_NOME_SIMPLES = getVarEntidade($objConn, "nome_fan");


//if (getValue($objRS,"dt_vcto") < now()) {
//	echo("Data de venciamento invalida.");
//	die();
//}


// $urlCallBack = getVarEntidade($objConn, "callbackBepay");

	







$strCPF = $DadosBoleto_SACADO_CNPJ;
$strValorConta = $DadosBoleto_VLR_TITULO;

if ($DadosBoleto_SACADO_EMAIL == ""){
	$strMsg .= " - E-mail.\\r\\n";
}


$strPARAM = "";

if ($strMsg !=""){ ?>
	<script language="javascript">
		<?php if (!isset($_GET["var_basename"])){?>
			alert('<?php echo("Atualize seus dados pessoais, e verifique os campos abaixo para emiss�o da cobran�a.\\r\\n".$strMsg);?>');		
			window.parent.document.location.href = "../modulo_PainelPF/STCadPFOpen.php"
		<?php } else {?>
			alert('<?php echo("Atualize seus dados pessoais, e verifique os campos abaixo para emiss�o da cobran�a.\\r\\n".$strMsg);?>');		
			history.go(-1);	 
	    <?php } ?>
		//
		//history.go(-1);
	</script>
	
<?php die();} 

/*dados oficiais pague seguro*/

$ApiAccessKey      = "080d5f3f-9d29-43cf-92df-45997a047ecc53088ea14094b6999a203660a07245390c53-cdb4-4c83-8ea5-7605985fa87d";
$SellerAccountId   = "tesouraria@abfm.org.br";
$url			   = "https://ws.pagseguro.uol.com.br/v2/checkout/";
/*fim dados oficiais*/

if($strUserLogado == "95065750025"){
	$ApiAccessKey      = "82A1D756E0EE418B8596A485798DF234";
	//$ApiAccessKey      = "080d5f3f-9d29-43cf-92df-45997a047ecc53088ea14094b6999a203660a07245390c53-cdb4-4c83-8ea5-7605985fa87d";
	$SellerAccountId   = "tesouraria@abfm.org.br";
	$url			   = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/";
}




$strPARAM =  "email=".$SellerAccountId;
$strPARAM = $strPARAM . "&token=".$ApiAccessKey ;
$strPARAM = $strPARAM . "&currency=BRL" ;

$strPARAM = $strPARAM . "&itemId1=".$intCodDado;
$strPARAM = $strPARAM . "&itemDescription1=".$DadosBoleto_HISTORICO . " - " .$DadosBoleto_SACADO_NOME." (".$intCodDado.")";
$strPARAM = $strPARAM . "&itemAmount1=".$DadosBoleto_VLR_TITULO;
$strPARAM = $strPARAM . "&itemQuantity1=1";
$strPARAM = $strPARAM . "&itemWeight1=0";

$strPARAM = $strPARAM . "&reference=".$intCodDado;

$strPARAM = $strPARAM . "&senderName=".$DadosBoleto_SACADO_NOME;
$strPARAM = $strPARAM . "&senderCPF=".$strCPF;
$strPARAM = $strPARAM . "&senderAreaCode=";
$strPARAM = $strPARAM . "&senderPhone=";
$strPARAM = $strPARAM . "&senderEmail=".$DadosBoleto_SACADO_EMAIL;
$strPARAM = $strPARAM . "&shippingType=3";
$strPARAM = $strPARAM . "&shippingAddressStreet=";
$strPARAM = $strPARAM . "&shippingAddressNumber=";
$strPARAM = $strPARAM . "&shippingAddressComplement=";
$strPARAM = $strPARAM . "&shippingAddressDistrict=";
$strPARAM = $strPARAM . "&shippingAddressPostalCode=";
$strPARAM = $strPARAM . "&shippingAddressCity=";
$strPARAM = $strPARAM . "&shippingAddressState=";
$strPARAM = $strPARAM . "&shippingAddressCountry=BRA";

//$strPARAM = "email=rodrigo@proevento.com.br&token=08418ff7-08ca-4576-8b73-9f0c4fc6714a12381bd04ee4a190c09f7a5fe5282588d853-7384-41e1-974d-bdfbf01b9dc1&currency=BRL&itemId1=100099&itemDescription1=12� Semin�rio UNIDAS - 05 e 06/agosto 21 - Inscricao 100099 Grupo: 100099&itemAmount1=1250.75&itemQuantity1=1&itemWeight1=0&reference=100099&senderName=TESTE TESTE&senderCPF=93046900058&senderAreaCode=&senderPhone=&senderEmail=testeww@hotmail.com&shippingType=3&shippingAddressStreet=&shippingAddressNumber=&shippingAddressComplement=&shippingAddressDistrict=&shippingAddressPostalCode=&shippingAddressCity=&shippingAddressState=&shippingAddressCountry=BRA";

$headers = array("Content-Type: application/x-www-form-urlencoded");

$curl = curl_init();

$options = array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $strPARAM,
  CURLOPT_HTTPHEADER => $headers
                );

curl_setopt_array($curl,$options );

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$xml = new SimpleXMLElement($response);

foreach($xml as $key => $value) {
	if($key == "code"){
		$strAuth = $value;
	}    
}
var_dump($xml);

if ($err) {
//  echo "cURL Error #:" . $err;
} else {
	if($strUserLogado == "95065750025"){
		$urlBoleto = "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=".$strAuth;
	} else {
        $urlBoleto = "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=".$strAuth;
	}
  
}



			//var_dump(get_resource_type($ch));
						if ($strTipo == "boleto"){										
							if ($urlBoleto!=""){
                                print($urlBoleto);
							//echo $urlBoleto;
							}else {print_r($err);die();}
						}
					
					setsession(CFG_SYSTEM_NAME."_id_usuario", $strUserLogado);
				//}
						$objResult->closeCursor();
						$objConn = NULL;







?>



