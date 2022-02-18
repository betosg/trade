<?php

include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
//erroReport();




     //include_once("_include_aslRunRequest.php");
     //include_once("_include_aslRunBase.php");
    //die();

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


$varDB="tradeunion_abfm";
 $intCodDado = request("var_chavereg");
//$intCodDado = $_REQUEST['var_chavereg'];
//$varDB = request('var_db');
//print(substr($arrDados[$key]["creditDate"],8,2)."/".substr($arrDados[$key]["creditDate"],5,2)."/".substr($arrDados[$key]["creditDate"],0,4));

$dt_inicio 	= substr(request('dt_inicio_date'),6,4) . "-" .substr(request('dt_inicio_date'),3,2) ."-".substr(request('dt_inicio_date'),0,2);
$dt_fim 	= substr(request('dt_fim_date'),6,4)    . "-" .substr(request('dt_fim_date'),3,2)    ."-".substr(request('dt_fim_date'),0,2); 

//$d=strtotime("- 1 days");
//$dt_inicio 	= date("Y-m-d", $d) ;
//$d=strtotime("+ 1 days");
//$dt_fim     =  date("Y-m-d", $d) ;
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
//$strDateType = "ENTRY";
	
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
	//echo "url:" . $url.$PostData;
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
	



/* Este Executor nï¿½o provï¿½ saida visual, ele apenas gera o arquivo html do relatï¿½rio em questï¿½o */
function echoOnFile($ptFile,$str) {
	/*
	 Os HTML gerados vï¿½o para uma pasta do cliente, portanto os caminhos para css, javascript, imagens
	 e chamada para outras pï¿½giansphp, devem ser ajustados. O cï¿½digo abaixo ajusta estes caminhos. 
	 **Acho que mais adiante poderemos melhorar essa lï¿½gica
	*/ 
	$str = str_replace("../_css/"		,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/_css/"							 , $str);
	$str = str_replace("../_scripts/"	,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/_scripts/"						 , $str);
	$str = str_replace("../img/"		,"https://tradeunion.proevento.com.br/_"  . CFG_SYSTEM_NAME . "/img/"							 	 , $str);
	$str = str_replace("'execaslw.php"	,"'https://tradeunion.proevento.com.br/_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/execaslw.php" , $str);
	$str = str_replace("'aslExport.php"	,"'https://tradeunion.proevento.com.br/_" . CFG_SYSTEM_NAME . "/modulo_ASLWRelatorio/aslExport.php", $str);
	fputs($ptFile,"$str");	// Grava 
 }

 $dirCli  = getsession(CFG_SYSTEM_NAME . "_dir_cliente");
 $arqNome = "extratoBePay_" . date("Ymd-His") . ".html";
 $local	  = realpath("../../" . $dirCli . "/asl_html/") . "/" . $arqNome;
 
 try { 
 	touch($local); /* Acesso ao arquivo, e se ele nao existir, ele ï¿½ criado. */ 
	$fp=fopen($local,"w");	// Abre o arquivo pra escrita
 }
 catch(PDOException $e){
	mensagem("Erro de arquivo", "Problema na gera��o do arquivo HTML deste relat�rio", "Arquivo: " . $arqNome,  "javascript:window.close();","standarderro",1);
    die();
 }

    
echo("<html> ");
echo("<head> ");
echo("<title>".CFG_SYSTEM_TITLE." - Extrato BePay</title> ");
echo("<link rel='stylesheet' href='../_css/".CFG_SYSTEM_NAME.".css' type='text/css'> ");
echo("<link rel='stylesheet' type='text/css' href='../_css/tablesort.css'> ");
echo("<script type='text/javascript' src='../_scripts/tablesort.js'></script> ");
echo("<script type='text/javascript' src='../_scripts/jquery-3.5.1.min.js'></script> ");
echo("<style> ");
echo(".debito  { color: red; } ");
echo(".credito { color: blue; } ");
echo("</style> ");
echo("</head> ");
echo("<body> ");
echo("<div style='text-align:right;font-size:12px;'>Emitido em: <strong>".date("d/m/Y H:i:s")."</strong></div>");
echo("    <table align='center' cellpadding='0' cellspacing='1' style='width:100%' class='tablesort' id='fd-table-1'>");
echo("        <thead>");
echo("            <tr>");          
echo("                <th class='sortable fd-column-0'><b>T��tulo</b></td>");
echo("                <th class='sortable fd-column-1'><b>Associado</b></td>");
echo("                <th ><b>Nosso n&uacute;mero</b></td>");
echo("                <th class='sortable fd-column-3'><b>Opera&ccedil;&atilde;o</b></td>");
echo("                <th class='sortable fd-column-4'><b>Descri&ccedil;&atilde;o</b></td>");
echo("                <th class='sortable fd-column-5'><b>Info</b></td>");
echo("                <th class='sortable fd-column-6'><b>Data Trans</b></td>");
echo("                <th class='sortable fd-column-7'><b>Data Cred</b></td>");
echo("                <th class='sortable fd-column-8'><b>Valor</b></td>");
echo("                <th class='sortable fd-column-8'><b>Lan�ar</b></td>");
echo("            </tr>");
echo("        </thead>");
echo("        <tbody>");
   
	?>
	<script language="javascript">
	//console.log(<?php //print_r($arrDados);?>)
 	</script>
	 <script language="javascript">
function insLcto(prDescricao,prNomeExtrato,prSpan,prTitulo,prTransaction, prCreditDate, prValor, prOperacao, prTipo){
    console.log(prNomeExtrato,prTitulo, prTransaction + " " + prCreditDate + " "+ prValor + " "+ prOperacao + " " + prTipo) ;


    $(document).ready(function() {
                            $.ajax({ type: "POST"
                                    , url: "../_ajax/STquitaBoleto.php?var_tipo="+prTipo+"&var_descricao="+prDescricao+"&var_nome="+prNomeExtrato+"&var_titulo="+prTitulo+"&var_transaction=" + prTransaction + "&var_creditDate=" + prCreditDate + "&var_valor="+ prValor + "&var_operacao="+ prOperacao
									//, data: data
									, success: function(result){
                                            var resultado = result;						                                           
                                            console.clear;
                                            console.log("resultado: "+ resultado);
                                            document.getElementById(prSpan).innerHTML = "OK";
                                            document.getElementById(prSpan).removeAttribute("onclick");
                                    }});
                        });




}
</script>
	<?php $i=0;
foreach ($arrDados as $key => $value) {
	
   // if ( strpos($arrDados[$key]["description"],'BOLETO') ){
        $i++;
	//echo ($arrDados[$key]["description"] . " " .$arrDados[$key]["transactionId"]."<br>");
	//if ($arrDados[$key]["description"] != "SALDO C/C" && $arrDados[$key]["description"] !="SALDO DISPONIVEL"){
                $strSql  = "SELECT nome_sacado,email_sacado, cod_conta_pagar_receber, processamento_boleto, processamento_mediator, processamento_despesa  ";
                $strSql .= " FROM fin_conta_pagar_receber_log_bepay WHERE transaction_id = '".$arrDados[$key]["transactionId"]."'";
              try{  
                $objResult = $objConn->query($strSql);
				$objRS = $objResult->fetch();
				//print("<br><strong>".getValue($objRS,"nome_sacado")."/".getValue($objRS,"cod_conta_pagar_receber")." / ".$arrDados[$key]["transactionId"]."</strong>");
				//echo "<br>processamento_boleto:  ".$objRS["processamento_boleto"];
				//echo "<br>processamento_mediator:".getValue($objRS,"processamento_mediator");
				//echo "<br>processamento_despesa: ".getValue($objRS,"processamento_despesa");
                //print("<hr>");
              }catch(PDOException $e){
                echo($e->getMessage());
                die();
              }
                if ($arrDados[$key]["type"] == 'C'){
                    $strClass = "color:green;";
                }else{
                    $strClass = "color:red;";
				}
				if (stripos($arrDados[$key]["description"] , 'BOLETO') !== false) {
					//if ($arrDados[$key]["description"] == "TARIFA VD BOLETO LOJ"){
						$strTipo = "BOLETO";
					}else{
						$strTipo = "CART�O";
					}
              ?>
       			
						<tr > 
						
						
								<td style="<?php echo($strClass);?>">
										<?php	if ( $arrDados[$key]["type"] == 'C'){  
													echo(getValue($objRS,"cod_conta_pagar_receber"));
												} ?>
								</td>
								<td style="<?php echo($strClass);?>">
									<?php	if (isset($arrDados[$key]["counterpart"]["name"]) && $arrDados[$key]["type"] == 'C'){echo(utf8_decode($arrDados[$key]["counterpart"]["name"]));$strNomeExtrato = utf8_decode($arrDados[$key]["counterpart"]["name"]);} ?>
								</td>
								<td style="<?php echo($strClass);?>">
										<?php	if (isset($arrDados[$key]["transactionId"])){ echo($arrDados[$key]["transactionId"]);}?>
								</td>
								<td style="<?php echo($strClass);?>">
										<?php if (isset($arrDados[$key]["type"])){ echo($arrDados[$key]["type"]);}?>
								</td>
								<td style="<?php echo($strClass);?>">
								<?php if (isset($arrDados[$key]["description"])){ echo($arrDados[$key]["description"]);}?>
								</td>
								<td style="<?php echo($strClass);?>">
								<?php if (isset($arrDados[$key]["additionalInfo"])) {echo($arrDados[$key]["additionalInfo"]);}?>
								</td>
								
								<td style="<?php echo($strClass);?>">
									<?php if (isset($arrDados[$key]["entryDate"]))
										{
												echo(substr($arrDados[$key]["entryDate"],8,2)."/".substr($arrDados[$key]["entryDate"],5,2)."/".substr($arrDados[$key]["entryDate"],0,4) ." ". substr($arrDados[$key]["entryDate"],12,8));
										}
									?>
								</td>
								
								
								<td style="<?php echo($strClass);?>">
									<?php if (isset($arrDados[$key]["creditDate"])) 
										{
											echo(substr($arrDados[$key]["creditDate"],8,2)."/".substr($arrDados[$key]["creditDate"],5,2)."/".substr($arrDados[$key]["creditDate"],0,4));
										}
									?>
								</td>
								<td style="<?php echo($strClass);?>">
									<?php if (isset($arrDados[$key]["amount"]))
										{
											echo(floatToMoeda($arrDados[$key]["amount"]));
										}
										?>
								</td>


								<td style="<?php echo($strClass);?>" align="right">
								<?php  //     processamento_boleto, processamento_mediator, processamento_despesa 
									if ( $arrDados[$key]["type"] == 'C'){ 
											if (getValue($objRS,"processamento_boleto")!=1){
									?>
											<span style='cursor:pointer;' id='lcto_<?php echo($i);?>' onClick='javascript:insLcto("<?php echo(utf8_decode($arrDados[$key]["description"]));?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "receita","<?php echo($strTipo);?>");'>QUITA BOLETO/CARTAO</span>
											<script language="javascript">
												//insLcto("<?php echo(utf8_decode($arrDados[$key]["description"]));?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "receita","<?php echo($strTipo);?>");
											</script>
											<?php }else{ ?>
												<span id='lcto_<?php echo($i);?>'>OK</span>	
								<?php	}?>
										
								<?php 	}
								if ( $arrDados[$key]["type"] == 'D'){ 
									//echo($arrDados[$key]["description"] . " / pos: ".strpos(" ".$arrDados[$key]["description"],"TAR VND CART"));
												if ( ($arrDados[$key]["description"] == "TARIFA VD BOLETO LOJ") ){													
													if (getValue($objRS,"processamento_despesa")!=1){?>
														<span style='cursor:pointer;' id='lcto_<?php echo($i);?>' onClick='javascript:insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");'>LCTO DESP</span>
														<script language="javascript">$
															//insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");
														</script>
												<?php	}else{ ?>
													<span id='lcto_<?php echo($i);?>'>OK</span>
												<?php }
												}
												
												if ( ($arrDados[$key]["description"] == "TARIFA S/VNDA CARTAO") ){													
													if (getValue($objRS,"processamento_despesa")!=1){?>
														<span style='cursor:pointer;' id='lcto_<?php echo($i);?>' onClick='javascript:insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");'>LCTO DESP</span>
														<script language="javascript">
															//insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");
														</script>
												<?php	}else{ ?>
													<span id='lcto_<?php echo($i);?>'>OK</span>
												<?php }
												}
												
												if ( strpos($arrDados[$key]["description"] ,"VND CART") ){
													
													if (getValue($objRS,"processamento_despesa")!=1){?>
														<span style='cursor:pointer;' id='lcto_<?php echo($i);?>' onClick='javascript:insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");'>LCTO DESP</span>
														<script language="javascript">
															//insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");
														</script>

												<?php	}else{ ?>
													<span id='lcto_<?php echo($i);?>'>OK</span>
												<?php }
													}
												if (strpos($arrDados[$key]["description"] ,"MEDIADOR")){
														if (getValue($objRS,"processamento_mediator")!=1)  {?>
															<span style='cursor:pointer;' id='lcto_<?php echo($i);?>' onClick='javascript:insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");'>LCTO MDR</span>
															<script language="javascript">
																//insLcto("<?php echo($arrDados[$key]["description"]);?>","<?php echo($strNomeExtrato);?>","lcto_<?php echo($i);?>","<?php echo(getValue($objRS,"cod_conta_pagar_receber"))?>","<?php echo($arrDados[$key]["transactionId"]);?>","<?php echo($arrDados[$key]["creditDate"]);?>","<?php echo(floatToMoeda($arrDados[$key]["amount"]));?>", "despesa","<?php echo($strTipo);?>");
															</script>
												<?php   }else{ ?>
															<span id='lcto_<?php echo($i);?>'>OK</span>
												<?php }											
													}
											}																					
										?>																					
								
								</td> 
						</tr>		
<?php		 
   // }
}
echo("       </tbody>");
echo("   </table>");


?>


</script>
</body>
</html>
