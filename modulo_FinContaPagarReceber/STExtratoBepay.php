<?php

include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
erroReport();


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

$varDB="";
 $intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
$varDB = request('var_db');
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

// Periodo da pesquisa do extrato
///	$PostData = "start=2000-01-01&ending=2030-12-31";
	
	$PostData = "";
	$strDateType = "";
	
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
//	echo "datapost:" . $data_post;
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
	
	print_r(curl_error($ch));curl_errno($ch);
	$resultado="";
	
	//print("<hr><br>Result Curl<br><br>");
	//print_r($result);
	//die();
	$resultado=array();			
		
	$resultado = json_decode($result,true);
	$arrDados = $resultado["data"]["statement"];
	print("<pre>");
	var_dump($resultado["data"]["statement"]);
	print("</pre>");
	$strTransaction = "";
	$sumTaxa = 0.00;
	$arrTpTransactionSum = array("PGTOSERVMEDIADOR","TARIFAS/VNDACARTAO","SRVMEDIADORBOLETO","TARIFAVDBOLETOLOJ");
	
	//print_r($arrTpTransaction);
	foreach ($arrDados as $key => $value) { 
		
		//if($arrDados[$key]["description"] != "SALDO C/C" &&  $arrDados[$key]["description"] != "TRANSF C/C BANCARIA" && $arrDados[$key]["description"] != "TARIFA TRF BANCARIA"){
			if (in_array(str_replace(" ","",$arrDados[$key]["description"]), $arrTpTransactionSum)){

			if( $strTransaction != $arrDados[$key]["transactionId"] ){
								
				 $strSQL = " SELECT t1.codigo, t1.tipo, t1.cod_conta_pagar_receber 
										  , CASE WHEN t1.tipo = 'cad_pf' THEN (SELECT nome || '(' || cod_pf || ')' FROM cad_pf WHERE cod_pf = t1.codigo)
												WHEN t1.tipo = 'cad_pj' THEN (SELECT razao_social || '(' || cod_pj || ')' FROM cad_pj WHERE cod_pj = t1.codigo)
												WHEN t1.tipo = 'cad_pj_fornec' THEN (SELECT razao_social || '(' || cod_pj_fornec || ')' FROM cad_pj_fornec WHERE cod_pj_fornec = t1.codigo)
											END AS sacado_nome
											  FROM fin_conta_pagar_receber t1 
											  WHERE t1.cod_conta_pagar_receber IN(
													SELECT cod_conta_pagar_receber 
													FROM fin_conta_pagar_receber_log_bepay WHERE processamento_despesa = false AND transaction_id = '" . $arrDados[$key]["transactionId"] . "')";
				
				$objResult = $objConn->query($strSQL);
				$objRS = $objResult->fetch();
				
				if (getValue($objRS,"codigo")!=""){
					print("<br><br>".getValue($objRS,"codigo") . "(" . getValue($objRS,"tipo") .") Titulo: ".getValue($objRS,"cod_conta_pagar_receber") );
					print("<br><br><strong>Taxas transaction " . $arrDados[$key]["transactionId"] .": valor da taxa " .floatToMoeda($sumTaxa) ."  credit date: ". $arrDados[$key]["creditDate"]."</strong><br>")	;
					$dataCredito  = substr($arrDados[$key]["creditDate"],8,2)."/".substr($arrDados[$key]["creditDate"],5,2)."/".substr($arrDados[$key]["creditDate"],0,4);					

  $strSQL = "  INSERT INTO fin_lcto_em_conta    (sys_dtt_ins      ,sys_usr_ins,operacao ,cod_conta,codigo,tipo           ,cod_centro_custo,cod_plano_conta,cod_job,  num_lcto                                    ,vlr_lcto    ,dt_lcto           ,historico                                          ,obs )"; 
echo  $strSQL .= " VALUES                            (current_timestamp,'BEPAY'    ,'despesa','29'     ,'1034','cad_pj_fornec','76'            ,'642'          ,'61'   ,".getValue($objRS,"cod_conta_pagar_receber").",".$sumTaxa.",'".$dataCredito."','TAXA BEPAY - ". getValue($objRS,"sacado_nome") ."','ID Transaciton Bepay: ".$arrDados[$key]["transactionId"]."')";
echo "<br>";
					/** Quita a conta da taxa acima */

				}
				print("<hr>");
				$sumTaxa = 0.00;
			}
			$strTransaction = $arrDados[$key]["transactionId"];
			//print("<br><br><strong>".$arrDados[$key]["description"]."</strong> / ".in_array($arrDados[$key]["description"], $arrTpTransactionSum)."<br>")	;
			
			if( $strTransaction == $arrDados[$key]["transactionId"]){				
				if (in_array(str_replace(" ","",$arrDados[$key]["description"]), $arrTpTransactionSum)){
					$sumTaxa = $arrDados[$key]["amount"] + $sumTaxa;
				}
				//print($arrDados[$key]["description"]);
				//print("<br><br>");
				//print("transaction id: ".$arrDados[$key]["transactionId"]);		
				//print("<br><br>");
				//print("valor: ".FloatToMoeda($arrDados[$key]["amount"]));
				//print("<br><br>");
				//var_dump($arrDados[$key]);				
			}
		}
	}
		
	$strSQL = " SELECT t1.codigo, t1.tipo, t1.cod_conta_pagar_receber 
										  , CASE WHEN t1.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = t1.codigo)
												WHEN t1.tipo = 'cad_pj' THEN (SELECT razao_social FROM cad_pj WHERE cod_pj = t1.codigo)
												WHEN t1.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = t1.codigo)
											END AS sacado_nome
											  FROM fin_conta_pagar_receber t1 
											  WHERE t1.cod_conta_pagar_receber IN(
													SELECT cod_conta_pagar_receber 
													FROM fin_conta_pagar_receber_log_bepay WHERE processamento_despesa = false AND transaction_id = '" . $arrDados[$key]["transactionId"] . "')";	
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
	if (getValue($objRS,"codigo")!=""){
		print("<br><br>".getValue($objRS,"codigo") . "  / " . getValue($objRS,"tipo"));
		print("<br><br><strong>Taxas transaction " . $arrDados[$key]["transactionId"] .": " .floatToMoeda($sumTaxa) ."</strong><br>")	;
		$strSQL = "  INSERT INTO fin_lcto_em_conta    (sys_dtt_ins      ,sys_usr_ins,operacao ,cod_conta,codigo,tipo           ,cod_centro_custo,cod_plano_conta,cod_job,  num_lcto                                    ,vlr_lcto    ,dt_lcto           ,historico                                          ,obs )"; 
		echo  $strSQL .= " VALUES                            (current_timestamp,'BEPAY'    ,'despesa','29'     ,'1034','cad_pj_fornec','76'            ,'642'          ,'61'   ,".getValue($objRS,"cod_conta_pagar_receber").",".$sumTaxa.",'".$dataCredito."','TAXA BEPAY - ". getValue($objRS,"sacado_nome") ."','ID Transaciton Bepay: ".$arrDados[$key]["transactionId"]."')";
		echo "<br>";
	}
	print("<hr>");
	//print("<hr>".$resultado["data"]["boletoUrl"]);

	//print("<hr><br>CurlError<br><br>");
	//header('Location: ' . $resultado['data']['boletoUrl']);
	//echo("error: " .curl_error($ch));
	
	//if (curl_error($ch)){print_r(curl_error($ch));curl_errno($ch)}

	
die();





			//var_dump(get_resource_type($ch));
						if ($urlBoleto != ""){
							

							try{
								$strSQL = "INSERT INTO fin_conta_pagar_receber_log_bepay 
											(nome_sacado,email_sacado, cod_conta_pagar_receber, data_post,	return_post, sys_usr_ins, sys_dtt_ins, transaction_id, identificador, link_boleto)
									values  ('".$DadosBoleto_SACADO_NOME."','".$DadosBoleto_SACADO_EMAIL."',".$intCodDado.", '".$data_post."', '" . $result . "', '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."',now(), '".$transactionId."','".$strIdentificador."','" . $urlBoleto ."') ";

								$objResult = $objConn->query($strSQL);

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
								header('Location: ' . $urlBoleto);
								//header("Content-Disposition", "inline; filename=".$urlBoleto);
								//header("Content-Type: application/pdf");
								//header("Content-Disposition: inline; filename=".$urlBoleto);
							?>
								<!--html><body>
								<a href="<?php echo($urlBoleto);?>" id="downBoleto" download>Export</a>
								<script language="javascript">
									document.getElementById("downBoleto").click();
									history.go(-1);
								</script>
								</body></html-->
							<?php
							//echo $urlBoleto;
							}else {print_r($resultado);die();}
						}
						else { ?>
							<script language="javascript">
								console.log('<?php print_r(curl_error($ch));?>')
								console.log('<?php print_r(curl_errno($ch));?>')
								console.log('<?php print_r($data_post);?>');
								console.log('<?php print($valoresHash);?>');
							</script>
						<?php 
						mensagem("err_sql_titulo","Falha na geração do boleto","","","erro",1);
							die();
						}
				//}
						$objResult->closeCursor();
						$objConn = NULL;







?>



