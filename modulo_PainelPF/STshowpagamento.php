<?php
	// HEADERS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	erroReport();
	// REQUESTS
	
	function phoneValidate($phone){
		
//		51 8138-8573
		$strFirstTest = str_replace("-","",str_replace(" ","",str_replace(")","",str_replace("(","",$phone))));
		if (strlen($strFirstTest)<=10){
			return false;
		}
		//$regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/';
		$regex = '/^\(?[1-9]{2}\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/';
		//$phone = str_replace(" ","",$phone);
		//$regex ='/^(0[1-2][1-9]9\d{8})|(0[3-9][1-9]\d{8})$/';;
		//echo "<br>".$phone;
		if (preg_match($regex, $phone) == false) {
			//echo("O número não foi validado.");
			return false;
		} else {
			//echo("Telefone válido.");
			return true;
		}        
	


	
	}

	function validateCPF($cpf) {
 
		// Extrai somente os números
		$cpf = preg_replace( '/[^0-9]/is', '', $cpf );
		 
		// Verifica se foi informado todos os digitos corretamente
		if (strlen($cpf) != 11) {
			return false;
		}
	
		// Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
		if (preg_match('/(\d)\1{10}/', $cpf)) {
			return false;
		}
	
		// Faz o calculo para validar o CPF
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf[$c] * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf[$c] != $d) {
				return false;
			}
		}
		return true;
	
	}
	
	$intCodDado = request("var_chavereg");					// COD_CONTA_PAGAR_RECEBER
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

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
						   LEFT  JOIN cad_pf_curriculo     ON (cad_pf.cod_pf = cad_pf_curriculo.cod_pf)
						   LEFT  JOIN cfg_boleto 	   	   ON (fin_conta_pagar_receber.cod_cfg_boleto = cfg_boleto.cod_cfg_boleto)
						   LEFT OUTER JOIN prd_pedido 	   ON (fin_conta_pagar_receber.cod_pedido = prd_pedido.cod_pedido)						   
						   WHERE fin_conta_pagar_receber.situacao <> 'lcto_total' AND fin_conta_pagar_receber.cod_conta_pagar_receber = ".$intCodDado;
	   //echo $strSQL;
	   //						FROM fin_conta_pagar_receber t1 
	   //						LEFT OUTER JOIN prd_pedido t2 ON (t1.cod_pedido = t2.cod_pedido) 
	   //						INNER JOIN cad_pf ON (cad_pf.cod_pf = t1.codigo)
	   //						WHERE t1.tipo = 'cad_pf' AND t1.codigo = ".$intCodDado."
	   
					   
			   $objResult = $objConn->query($strSQL);
			   $objRS = $objResult->fetch();
			}
			   catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
			
	if ($objResult -> rowCount() <=0){
		mensagem("Titulo Pago","Este título já consta como pago","","","",1);
		
		die();
	}

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
	$strPendencia					= getValue($objRS,"pendencia");
	//$DadosBoleto_VLR_TITULO = 225.00;
	$valor = getValue($objRS,"vlr_conta");

	$DadosBoleto_CEDENTE_NOME_SIMPLES = getVarEntidade($objConn, "nome_fan");
//if (getValue($objRS,"dt_vcto") < now()) {
//	echo("Data de venciamento invalida.");
//	die();
//}

//print_r($data_post);


$parcelaQtdeMax    = getVarEntidade($objConn, "CartaoQtdeMaxParcelas");
$parcelaVlrMin     = getVarEntidade($objConn, "CartaoParcelaMinima");


$strPARCELAS =  getVarEntidade($objConn, "CartaoQtdeMaxParcelas");
if ($strPARCELAS == "") { $strPARCELAS = 1;}


$strPARCELA_VLR_MINIMO = getVarEntidade($objConn, "CartaoParcelaMinima");
//echo intval($DadosBoleto_VLR_TITULO / $strPARCELAS) ;
if ($strPARCELA_VLR_MINIMO != "") {
  If (intval($DadosBoleto_VLR_TITULO / $strPARCELAS) < $strPARCELA_VLR_MINIMO) {
	$strPARCELAS = intval($DadosBoleto_VLR_TITULO / $strPARCELA_VLR_MINIMO);
  }
}
$intNumParcelas = $strPARCELAS;

if ($intNumParcelas <=0 ){$intNumParcelas =1;}

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
//	$strMsg .= substr($strCelular,0,1) ."  " .$strCelular." - CELULAR INVï¿½LIDO.\\r\\n";
//}

if ($DadosBoleto_SACADO_CNPJ == ""){
	$strMsg .= " - CPF.\\r\\n";
}
if (!validateCPF($DadosBoleto_SACADO_CNPJ)){
	$strMsg .= " - CPF Inválido.\\r\\n";
}
if ($DadosBoleto_SACADO_EMAIL == ""){
	$strMsg .= " - E-mail.\\r\\n";
}

if ($strPendencia !=""){?>
	<script language="javascript">
	alert('<?php echo("Regularize as pendências abaixo\\r\\n".$strPendencia);?>');		
			history.go(-1);	
	</script>
<?php die();
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
	
	

	
	
	// INICIALIZA VARIïáVEL PARA PINTAR LINHA
	$strColor = CL_CORLINHA_1;
	
	// FUNÇÃO PINTA LINHA
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	
?>

<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<script type="text/javascript" src="../_scripts/jquery-3.5.1.min.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				border-bottom:1px solid #E9E9E9;
				border-left:  1px solid #E9E9E9;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
            .toHide{ display: none;}
            .toVisible{ display: block;}
			.td40{width: 150px;font-size:11px;}
			.td60{width: 300px; font-size:11px;}
			.inputText{font-size:11px; height: 20px;}


			/* The Modal (background) */
			.modal {
				display: none; /* Hidden by default */
				position: fixed; /* Stay in place */
				z-index: 1; /* Sit on top */
				padding-top: 100px; /* Location of the box */
				left: 0;
				top: 0;
				width: 100%; /* Full width */
				height: 100%; /* Full height */
				overflow: auto; /* Enable scroll if needed */
				background-color: rgb(0,0,0); /* Fallback color */
				background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
			}

			/* Modal Content */
			.modal-content {
				background-color: #fefefe;
				margin: auto;
				padding: 20px;
				border: 1px solid #888;
				text-align: center;
				color: red;
				width: 80%;
			}

			/* The Close Button */
			.close {
				color: #aaaaaa;
				float: right;
				font-size: 28px;
				font-weight: bold;
			}

			.close:hover,
			.close:focus {
				color: #000;
				text-decoration: none;
				cursor: pointer;
			}
			</style>


		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				
				//fixei boleto para gerar automatico pagseguro
				document.getElementById("var_tipo").value="boleto";
				
				
				if (document.getElementById("var_tipo").value==""){alert("Escolha uma forma de pagamento.");return false;}
				if (document.getElementById("var_tipo").value=="cartao"){										
					if(!validateRequestedFields("formstatic_resp")){
						//alert("Preencha os campos obrigatï¿½rios");
						return false;
					}else {
						document.getElementById("btn_ok").style.display = "none";
						document.getElementById("btn_cancel").style.display = "none";
						submitCartao();}
				}else{ //boleto
					document.getElementById("btn_ok").style.display = "none";
					document.getElementById("btn_cancel").style.display = "none";
					submitCartao();
				}
			}

			function cancelar() {
				window.history.back();
			}

			function submeterForm() {
				//document.formstatic_resp.DEFAULT_LOCATION.value = strLocation;
				//document.formstatic_resp.submit();
			}
				
		</script>
	</head>
<body bgcolor="#FFFFFF"  style="margin:10px 0px 10px 0px;">

<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <!--span class="close">&times;</span-->
    <p><h1>Aguarde! Gerando Link para Pagamento</h1></p>
  </div>

</div>

<!-- USO -->
<center>
<?php athBeginFloatingBox("700","","",CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" width="600" bgcolor="#FFFFFF" class="table_master">
<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
		<span style="font-size:11px;"><strong>Dados do titulo:</strong></span>
		<br>Pagamento referente a: <?php echo(getValue($objRS,"historico"));?>, R$ <?php echo($valor);?>
		</td>
    </tr>
	
    <tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
       	<img src="logo_pagseguro244x50.png">
       
            <input type="hidden" name="tipo_pgto"  id="tipo_pgto" >     
       
		</td>
    </tr>

	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
            
        <table  border="0" width="100%">
            <tr><td>
            <form name="formstatic_resp" id="formstatic_resp" action="<?php echo($strActionForm)?>" method="post">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>" />
				<input type="hidden" id="var_cod_conta_pagar_receber" name="var_cod_conta_pagar_receber" value="<?php echo($intCodDado);?>">
				<input type="hidden" name="DEFAULT_LOCATION" value="" />
				<input type="hidden" id="var_tipo" name="var_tipo" value="" />
                <!--div id="dados_cartao" style="display:none;visibility:hidden;"-->
                        <table id="dados_cartao" border="0" width="100%">
							<tr class="toHide" bgcolor="#FFFFFF">
								<td class="td40" align="right">&nbsp;</td>
								<td class="td60" align="left" class="destaque_gde">
									<strong>Preencha os dados do cartï¿½o</strong>
								</td>
							</tr>
                       		<tr class="toHide"><td colspan="2" height="2"><img src="https://tradeunion.proevento.com.br/_tradeunion/img/line_dialog.jpg"></td></tr>
                        
							<tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
								<td  class="td40" align="right" ><strong>*Nome:</strong></td>
								<td  class="td60" align="left">
									<input type="text" class="inputText" required name="var_nome_cartao" id="var_nome_cartao_ô" placeholder="Nome impresso no cartão" size="60" maxlength="100" value="<?php echo($DadosBoleto_SACADO_NOME);?>" />
								</td>
							</tr>

							<tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
								<td  class="td40" align="right"><strong>*CPF:</strong></td>
								<td  class="td60" align="left">
									<input type="text" class="inputText" required placeholder="CPF" id="cartao_var_cpf_titular_ô" size="30" name="var_cpf_titular"  onkeypress="Javascript:return validateNumKey(event);return false;" maxlength="11" value="<?php echo($DadosBoleto_SACADO_CNPJ);?>">
								</td>
							</tr>

                        <tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td class="td40" align="right"><strong>* Número Cartão / CVV:</strong></td>
                            <td class="td60" align="left">
								<input required type="text" class="inputText" placeholder="Numero cartão" id="cartao_var_num_cartao_ô" name="var_cartao" size="30"  maxlength="16" onBlur="checkCreditCardFlag(this.value)" onKeyPress="Javascript:return validateNumKey(event);return false;">
								<input required type="text" class="inputText" placeholder="CVV"           id="cartao_var_cod_cartao_ô" name="var_cod_cartao" size="10" maxlength="5" autofocus onKeyPress="Javascript:return validateNumKey(event);return false;">
							</td>				
						</tr>

						<tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td class="td40" align="right"><strong>* Validade:</strong></td>
                            <td class="td60" align="left">    
                                <select class="inputText" name="var_mes_cartao" id="cartao_var_mes_cartao_ô" style="width:50px" required>                                                    
                                    <option value="">MÊS</option>
                                    <option value="1">1</option>                                                    
                                    <option value="2">2</option>                                                    
                                    <option value="3">3</option>                                                    
                                    <option value="4">4</option>                                                    
                                    <option value="5">5</option>                                                    
                                    <option value="6">6</option>                                                    
                                    <option value="7">7</option>                                                    
                                    <option value="8">8</option>                                                    
                                    <option value="9">9</option>                                                    
                                    <option value="10">10</option>                                                    
                                    <option value="11">11</option>                                                    
                                    <option value="12">12</option>                                                    
                                </select>
                                <select class="inputText" name="var_ano_cartao" id="cartao_var_ano_cartao_ô"  style="width:50px" required>
                                    <option value="">ANO</option>
                                <?php for ($x = date("Y"); $x <= date("Y")+12; $x++) {?>
                                    <option value="<?php echo($x)?>"><?php echo($x)?></option>
                                <?php } ?>
                                </select>
                            </td>
						</tr>
						
						
						<tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td class="td40" align="right"><strong>* Parcela(s):</strong></td>
                            <td class="td60" align="left">  
								<select class="inputText" name="var_parcela" id="var_parcela_ô"  style="width:100px" required>											
								<?php for ($x = 1; $x <= $intNumParcelas; $x++) {?>
									<option value="<?php echo($x)?>"><?php echo($x. "  x R$ ". FloatToMoeda($DadosBoleto_VLR_TITULO/$x,2) );?></option>
								<?php } ?>
								</select> &nbsp;&nbsp;<strong>SEM JUROS</strong>
							</td>
						</tr>
						
						<tr class="toHide" bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td class="td40" align="right"><strong></strong></td>
                            <td class="td60" align="left">
                                <input type="hidden" id="bandeira" name="var_bandeira">
                                <div style="width:100%; padding: 5px;">
                                        <div id="band_visa" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="../img/band_visa.jpg" border="0"></div>
                                        <div id="band_master" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="../img/band_mastercard.jpg" border="0"></div>
                                        <!--div id="band_amex" style="margin:auto; width:100px; height:100%; display:inline-block; opacity:0.3;"><img src="./img/band_amex.jpg" border="0"></div//-->
                                </div> 
                            </td>                    
						</tr>
						<tr>
							<td class="td40" colspan="2" align="left">
								<strong><a href="" target="_blank" id="boleto_link" style="display: none;">
								<span style="color: red;font-weight: bolder; font-size:15px" id="info">Clique aqui para acessar o PAGSEGURO</span></a>
								<span style="color: red;font-weight: bolder;font-size:15px; display: none;" id="aviso" ></span></a></strong>
							</td>
							<!--td class="td60" align="left">
									
							</td-->
						</tr>                        
                    </table>
                </form>
                </td></tr>
				
				<!-- DADOS NOVA RESPOSTA -->
				
				<tr class="toHide">
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
			</table>			
		
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
    <?php if(getValue($objRS,"historico") != "TAXA - PROVA ESPECIALISTA") {?>
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="20%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr><td align="right" width="23%" style="padding-right:8px;"></td></tr>
						</table>
					</td>
					<!-- goNext() -->
                     <?php if(getValue($objRS,"historico") != "TAXA - PROVA ESPECIALISTA") {?>
                    <td width="55%" align="right"><button onClick="ok();" class="inputcleanActionOk" id="btn_ok">Pagar</button></td>
					<td width="25%" align="left" ><button onClick="cancelar();return false;" class="inputcleanActionCancelar" id="btn_cancel">Cancelar</button></td>                
                    
                    <?php } else {?>    
                        Entre em contato com a ABFM para solicitar um meio de pagamento.
                    <?php } ?>
					
				</tr>
			</table>
		</td>
	</tr>
    <?php } ?>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTõES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">

        function exibeForm(prDado){
            
            if (prDado=='boleto'){
            acao = "none";
			document.getElementById("var_tipo").value = "boleto";
			document.getElementById("boleto_link").style.display = "none";
			document.getElementById("cartao_var_num_cartao_ô").value ="";
			document.getElementById("cartao_var_cod_cartao_ô").value ="";
			document.getElementById("cartao_var_mes_cartao_ô").value ="";
			document.getElementById("cartao_var_ano_cartao_ô").value ="";
			document.getElementById("var_parcela_ô").value = "";

            }else{
            acao = 'block';
			document.getElementById("var_tipo").value = "cartao";
			document.getElementById("boleto_link").style.display = "none";
            document.getElementById("btn_ok").style.display = "block";
			document.getElementById("btn_cancel").style.display = "block";
			}

            var x, i;
            x = document.querySelectorAll(".toHide");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = acao;
            }
            
           
            return false;


        }
        function checkCreditCardFlag(cardNumber){
            
            document.getElementById('band_visa').style.opacity = 0.3;
            document.getElementById('band_master').style.opacity = 0.3;
            //document.getElementById('band_amex').style.opacity = 0.3;
            
            var isValid = false;
            var ccCheckRegExp = /[^\d ]/;
            isValid = !ccCheckRegExp.test(cardNumber);

            var cardNumbersOnly = cardNumber.replace(/ /g,"");
            var cardNumberLength = cardNumbersOnly.length;
            var lengthIsValid = false;
            var prefixIsValid = false;
            var prefixRegExp;

            //Master
            prefixRegExp = /^5[0-9][0-9]{14}/;
            prefixIsValid = prefixRegExp.test(cardNumbersOnly);
            if(prefixIsValid) { document.getElementById("bandeira").value = 'MASTER'; document.getElementById('band_master').style.opacity = 1; }

            //Visa
            prefixRegExp = /^4[0-9]{12}(?:[0-9]{3})?/;
            prefixIsValid = prefixRegExp.test(cardNumbersOnly);
            if(prefixIsValid) { document.getElementById("bandeira").value = 'VISA'; document.getElementById('band_visa').style.opacity = 1; }

            
        }

		


		
		function submitCartao(){
			//document.getElementById("aviso").style.display = "block";
			//document.getElementById("aviso").innerHTML = "Aguarde, em processamento...";
			modal.style.display = "block";
			event.preventDefault();
			var data = $('#formstatic_resp').serializeArray();
			// console.log(data);
		$(document).ready(function() {
                            $.ajax({ type: "POST"
                                    , url: "../_boletos/STshowboletoPagSeguro.php"                                    
									, data: data
									, success: function(result){
                                            var resultado = result;						
                                            var arrReturn = result.split("|"); 
                                            console.clear;
                                            console.log("resultado: "+ resultado);
											if (resultado.indexOf("error") != -1){
												if (!checkCPF(document.getElementById("cartao_var_cpf_titular_ô").value, false)){
													resultado = "CPF inválido, entre em contato com a administração da ABFM para regularizar seu cadastro.<br>"+resultado;
												};
												document.getElementById("aviso").innerHTML = resultado;
												return false;
											}
											if (document.getElementById("var_tipo").value == "boleto"){
												document.getElementById("boleto_link").href = resultado;
												document.getElementById("boleto_link").style.display = "block";
												document.getElementById("aviso").style.display = "none";
												document.getElementById("aviso").innerHTML = "";
												document.getElementById("btn_ok").style.display = "none";
												document.getElementById("btn_cancel").style.display = "none";
											}else{
												if (resultado.indexOf("https")>0){
												document.getElementById("boleto_link").href = resultado;
												document.getElementById("info").innerHTML = "Clique aqui para imprimir o seu recibo";
												document.getElementById("boleto_link").style.display = "block";
												document.getElementById("aviso").style.display = "none";
												document.getElementById("aviso").innerHTML = "";
												document.getElementById("btn_ok").style.display = "none";
												document.getElementById("btn_cancel").style.display = "none";
												}else{
													document.getElementById("aviso").innerHTML = resultado;
													document.getElementById("aviso").style.display = "block";
													document.getElementById("boleto_link").style.display = "none";
													document.getElementById("btn_ok").style.display = "block";
													document.getElementById("btn_cancel").style.display = "block";
												}
											}
											document.getElementById("cartao_var_num_cartao_ô").value = ""
											document.getElementById("cartao_var_cod_cartao_ô").value = ""
											document.getElementById("cartao_var_mes_cartao_ô").value = ""
											document.getElementById("cartao_var_ano_cartao_ô").value = ""
											modal.style.display = "none";
                                            
                                            
                                      
                                    }});
                        });	
		}

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
//var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
//  modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
//span.onclick = function() {
//  modal.style.display = "none";
//}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}


  // Quando esta pï¿½gina for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>