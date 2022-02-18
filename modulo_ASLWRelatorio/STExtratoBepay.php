<?php

include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
erroReport();



// INI: INCLUDE requests ORDIÀRIOS -------------------------------------------------------------------------------------
/*
 Por definição esses são os parâmetros que a página anterior de preparação (execaslw.php) manda para os executores.
 Cada executor pode utilizar os parâmetros que achar necessário, mas por definição queremos que todos façam os
 requests de todos os parâmetros enviados, como no caso abaixo:
 Variáveis e Carga:
	 -----------------------------------------------------------------------------
	 variável          | "alimentação"
	 -----------------------------------------------------------------------------
	 $data_ini         | DataHora início do relatório
	 $intRelCod		   | Código do relatórioRodapé do relatório
	 $strRelASL		   | ASL - Conulta com parâmetros processados, mas TAGs e Modificadores 
	 $strRelSQL		   | SQL - Consulta no formato SQL (com parâmetros processados e "limpa" de TAGs e Modificadores)
	 $strRelTit		   | Nome/Título do relatório
	 $strRelDesc	   | Descrição do relatório	
	 $strRelHead	   | Cabeçalho do relatório
	 $strRelFoot	   | Rodapé do relatório		
	 $strRelInpts	   | Usado apenas para o log
	 $strDBCampoRet	   | O nome do campo na consulta que deve ser retornado
	 $strDBCampoRet    | **Usado no repasse entre ralatórios - sem o nome da tabela do campo que será retornado
	 -----------------------------------------------------------------------------  */
     include_once("_include_aslRunRequest.php");
     include_once("_include_aslRunBase.php");
    //die();

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


$varDB="";
 $intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
$varDB = request('var_db');
//print(substr($arrDados[$key]["creditDate"],8,2)."/".substr($arrDados[$key]["creditDate"],5,2)."/".substr($arrDados[$key]["creditDate"],0,4));

$dt_inicio 	= substr(request('dt_inicio_date'),6,4) . "-" .substr(request('dt_inicio_date'),3,2) ."-".substr(request('dt_inicio_date'),0,2);
$dt_fim 	= substr(request('dt_fim_date'),6,4)    . "-" .substr(request('dt_fim_date'),3,2)    ."-".substr(request('dt_fim_date'),0,2); 
$strDateType = request("FiltrarPorData_COMBO(32)");
if ($varDB == ""){$varDB = CFG_DB;}
$objConn = abreDBConn($varDB);




/*dados oficiais*/
$ApiAccessKey      = "49D25D01-B062-4A6C-B9B5-570329228983";
$secret            = "AA511697-7681-416C-82F2-DD5C83E5AE67";
$MediatorAccountId = "D0A2FFE1-46A2-0086-D033-3AC41A82C085";
$SellerAccountId   = "1C8758AE-3FD4-22F3-F861-3A77A5A254C2";
$url = "https://api.bepay.com/v1/accounts/".$SellerAccountId."/statement?";
/*fim dados oficiais*/

/* dados para teste
		echo "<br>Api access key:    ".$ApiAccessKey      = "2F10A163-5420-4ED1-A2A8-63D5D3F28F61";
		echo "<br>secret:            ".$secret            = "C801505D-E51F-4A12-B0D4-8E03D1D252AB";
		echo "<br>MediatorAccountId: ".$MediatorAccountId = "A21D5F48-A434-8F73-A3AB-78822FF6FF71";
		echo "<br>SellerAccountId:   ".$SellerAccountId   = "A21D5F48-A434-8F73-A3AB-78822FF6FF71";		
		$url = "https://homolog-api.bepay.com/v1/accounts/".$SellerAccountId."/statement?";
*/

$valoresHash =trim($SellerAccountId);


$AuthorizationHeaderBase64 = trim(hash_hmac('sha256', $valoresHash, $secret));


if( trim($dt_inicio) !="--" || trim($dt_fim) != "--"){
	$PostData = "start=".$dt_inicio."&ending=".$dt_fim;
}else{
	$PostData = "start=2000-01-01&ending=2031-12-01";
}

	
	if ($strDateType == "ENTRY") {
		$PostData = $PostData . "&dateType=ENTRY";
	}else {
		$PostData = $PostData . "&dateType=REALIZATION";
	}

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
//	echo "url:" . $url.$PostData;
//	die();
	$ch = curl_init();
	//echo $ch;
	//echo $data_post;
	//die();
	curl_setopt($ch, CURLOPT_URL, $url.$PostData);
	//curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	//echo($ch);
	// extract header
	$result = curl_exec($ch);
	//$headerSize = curl_getinfo($result);
	////$header = substr($result, 0, $headerSize);
	////$header = getHeaders($header);
//
	//// extract body
	//$body = substr($result, $headerSize);
	//print_r(curl_getinfo($result));
	
	if(curl_error($ch)){
	print_r(curl_error($ch));
	print("<br>url:" . $url.$PostData);
	die();
	}
	$resultado="";
	
	//print("<hr><br>Result Curl<br><br>");
	//print_r($result);
	//die();
	$resultado=array();			
		
	$resultado = json_decode($result,true);
	$arrDados = $resultado["data"]["statement"];
	//var_dump($resultado["data"]["statement"]);
	$strTransaction = "";
	$sumTaxa = 0.00;
	



/* Este Executor não provê saida visual, ele apenas gera o arquivo html do relatório em questão */
function echoOnFile($ptFile,$str) {
	/*
	 Os HTML gerados vão para uma pasta do cliente, portanto os caminhos para css, javascript, imagens
	 e chamada para outras págiansphp, devem ser ajustados. O código abaixo ajusta estes caminhos. 
	 **Acho que mais adiante poderemos melhorar essa lógica
	*/ 
	$str = str_replace("../_css/"		,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/_css/"							 , $str);
	$str = str_replace("../_scripts/"	,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/_scripts/"						 , $str);
	$str = str_replace("../img/"		,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/img/"							 	 , $str);
	$str = str_replace("'execaslw.php"	,"'https://tradeunion.proevento.com.br/_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/execaslw.php" , $str);
	$str = str_replace("'aslExport.php"	,"'https://tradeunion.proevento.com.br/_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/aslExport.php", $str);
	fputs($ptFile,"$str");	// Grava 
 }

 $dirCli  = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
 $arqNome = $intRelCod . "_" . date("Ymd-His") . ".html";
 $local	  = realpath("../../" . $dirCli . "/asl_html/") . "/" . $arqNome;
 
 try { 
 	touch($local); /* Acesso ao arquivo, e se ele nao existir, ele é criado. */ 
	$fp=fopen($local,"w");	// Abre o arquivo pra escrita
 }
 catch(PDOException $e){
	mensagem("Erro de arquivo", "Problema na geração do arquivo HTML deste relatório", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
    die();
 }

    
$strToFile  = ("<html> ");
$strToFile .= ("<head> ");
$strToFile .= ("<title>".CFG_SYSTEM_TITLE." - Extrato BePay</title> ");
$strToFile .= ("<link rel='stylesheet' href='../_css/".CFG_SYSTEM_NAME.".css' type='text/css'> ");
$strToFile .= ("<link rel='stylesheet' type='text/css' href='../_css/tablesort.css'> ");
$strToFile .= ("<script type='text/javascript' src='../_scripts/tablesort.js'></script> ");
$strToFile .= ("<style> ");
$strToFile .= (".debito  { color: red; } ");
$strToFile .= (".credito { color: blue; } ");
$strToFile .= ("</style> ");
$strToFile .= ("</head> ");
$strToFile .= ("<body> ");
$strToFile .= ("<div style='text-align:right;font-size:12px;'>Emitido em: <strong>".date("d/m/Y H:i:s")."</strong></div>");
$strToFile .= ("    <table align='center' cellpadding='0' cellspacing='1' style='width:100%' class='tablesort' id='fd-table-1'>");
$strToFile .= ("        <thead>");
$strToFile .= ("            <tr>");          
$strToFile .= ("                <th class='sortable fd-column-0'><b>Título</b></td>");
$strToFile .= ("                <th class='sortable fd-column-1'><b>Associado</b></td>");
$strToFile .= ("                <th ><b>Nosso n&uacute;mero</b></td>");
$strToFile .= ("                <th class='sortable fd-column-3'><b>Opera&ccedil;&atilde;o</b></td>");
$strToFile .= ("                <th class='sortable fd-column-4'><b>Descri&ccedil;&atilde;o</b></td>");
$strToFile .= ("                <th class='sortable fd-column-5'><b>Info</b></td>");
$strToFile .= ("                <th class='sortable fd-column-6'><b>Data Trans</b></td>");
$strToFile .= ("                <th class='sortable fd-column-7'><b>Data Cred</b></td>");
$strToFile .= ("                <th class='sortable fd-column-8'><b>Valor</b></td>");
$strToFile .= ("            </tr>");
$strToFile .= ("        </thead>");
$strToFile .= ("        <tbody>");
   
	?>
	<script language="javascript">
	console.log(<?php print_r($arrDados);?>)
 	</script>
	<?php
foreach ($arrDados as $key => $value) { 
	//echo ($arrDados[$key]["description"] . " " .$arrDados[$key]["transactionId"]."<br>");
	//if ($arrDados[$key]["description"] != "SALDO C/C" && $arrDados[$key]["description"] !="SALDO DISPONIVEL"){
                $strSql  = "SELECT nome_sacado,email_sacado, cod_conta_pagar_receber  ";
                $strSql .= " FROM fin_conta_pagar_receber_log_bepay WHERE transaction_id = '".$arrDados[$key]["transactionId"]."'";
              try{  
                $objResult = $objConn->query($strSql);
                $objRS = $objResult->fetch();
                //print("<br><strong>".getValue($objRS,"nome_sacado")."/".getValue($objRS,"cod_conta_pagar_receber")."</strong>");
              }catch(PDOException $e){
                echo($e->getMessage());
                die();
              }
                if ($arrDados[$key]["type"] == 'C'){
                    $strClass = "color:green;";
                }else{
                    $strClass = "color:red;";
                }
              ?>
        <?php //if (getValue($objRS,"cod_conta_pagar_receber") != ""){
				$strToFile .= ("<tr > ");
				$strToFile .= ("<td style='".$strClass."'>");
							if ( $arrDados[$key]["type"] == 'C'){ 
								$strToFile .= (getValue($objRS,"cod_conta_pagar_receber"));
							}
					$strToFile .=("</td> ");
					$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["counterpart"]["name"]) && $arrDados[$key]["type"] == 'C'){$strToFile .=(utf8_decode($arrDados[$key]["counterpart"]["name"]));}
						$strToFile .=("</td> ");
						$strToFile .=("<td style='".$strClass."'>");
							if (isset($arrDados[$key]["transactionId"])){ $strToFile .=($arrDados[$key]["transactionId"]);}
							$strToFile .=("</td> ");
							$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["type"])){ $strToFile .=($arrDados[$key]["type"]);}
						$strToFile .=("</td> ");
						$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["description"])){ $strToFile .=($arrDados[$key]["description"]);}
					$strToFile .=("</td> ");
					$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["additionalInfo"])) {$strToFile .=($arrDados[$key]["additionalInfo"]);}
					$strToFile .=("</td> ");
					$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["entryDate"])){$strToFile .=(substr($arrDados[$key]["entryDate"],8,2)."/".substr($arrDados[$key]["entryDate"],5,2)."/".substr($arrDados[$key]["entryDate"],0,4) ." ". substr($arrDados[$key]["entryDate"],12,8));}
					$strToFile .=("</td> ");
					$strToFile .=("<td style='".$strClass."'>");
						if (isset($arrDados[$key]["creditDate"])) {$strToFile .=(substr($arrDados[$key]["creditDate"],8,2)."/".substr($arrDados[$key]["creditDate"],5,2)."/".substr($arrDados[$key]["creditDate"],0,4));}
					$strToFile .=("</td> ");
					$strToFile .=("<td style='".$strClass."' align='right'>");
						if (isset($arrDados[$key]["amount"])){$strToFile .=(floatToMoeda($arrDados[$key]["amount"]));}
					$strToFile .=("</td> ");
				$strToFile .=("</tr> ");		
		 //}
	//}	
}
$strToFile .=("       </tbody>");
$strToFile .=("   </table>");
$strToFile .=("</body>");
$strToFile .=("</html>");

echoOnFile($fp,$strToFile);
//echoOnFile($fpCSV,$strToFile);

include_once("_include_aslRunHtmlLog.php");   // Grava Log de execução do Relatório (com o nome do HTML gerado)
include_once("_include_aslRunHtmlClear.php"); // Apaga arquivos html de relatórios antigos

mensagem("info_relgerado_titulo"
        ,"info_relgerado_desc"
		,"<img src='../img/icon_html_view_big.gif' onClick=\"window.open('../../".$dirCli."/asl_html/".$arqNome."','','width=640,height=480,top=30,left=30,scrollbars=1,resizable=yes,status=yes,directories=no,location=0,menubar=no,toolbar=no,titlebar=no');\" style='cursor:pointer; ' border='0' alt='view' title='view'>"
		 ."&nbsp;&nbsp;&nbsp;&nbsp;<a href='aslDownload.php?var_file=../../".$dirCli."/asl_html/".$arqNome."' target='_blank' alt='download HTML' title='download HTML'><img src='../img/icon_html_download_big.gif' border='0'></a>&nbsp;"
		 ."<!--a href='aslDownload.php?var_file=../../".$dirCli."/asl_html/".str_replace(".html",".csv",$arqNome)."' target='_blank' alt='download CSV' title='download CSV'><img src='../img/icon_csv_download_big.gif' border='0'></a-->&nbsp;"
		 ."&nbsp;(". str_replace(".html","",$arqNome). ")"
		,"javascript:history.back();"
		,"info"
		,1);
?>



