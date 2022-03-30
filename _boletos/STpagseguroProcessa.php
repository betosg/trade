<?php
//erroReport();
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
$var_db = "tradeunion_abfm";
$objConn = abreDBConn($var_db);



$strSql = "
SELECT t1.cod_conta_pagar_receber
FROM fin_conta_pagar_receber t1
WHERE t1.sys_dtt_cancel IS NULL 
/*AND (t1.situacao ILIKE 'aberto' OR t1.situacao ILIKE 'lcto_parcial') */
AND t1.dt_vcto BETWEEN TO_DATE('01/01/2020','DD/MM/YYYY') AND TO_DATE('30/04/2022','DD/MM/YYYY')
AND t1.cod_conta = 31 
ORDER BY t1.dt_vcto limit 10";
        $objResult = $objConn->query($strSql);
		//$objRS = $objResult->fetch();
    foreach($objResult as $objRS){ 
//		$impresso = getValue($objRS,"impresso");
		$reference = getValue($objRS,"cod_conta_pagar_receber");

        $url = "https://ws.pagseguro.uol.com.br/v2/transactions?";
        $email = "tesouraria@abfm.org.br";
        $token = "080d5f3f-9d29-43cf-92df-45997a047ecc53088ea14094b6999a203660a07245390c53-cdb4-4c83-8ea5-7605985fa87d";
        
            echo $url = $url . "email=".$email ."&token=".$token."&reference=".$reference;

            $headers = array("Content-Type: application/x-www-form-urlencoded");

            $curl = curl_init();

            $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => $strPARAM,
            CURLOPT_HTTPHEADER => $headers
                            );

            curl_setopt_array($curl,$options );

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $xml = new SimpleXMLElement($response);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            print_r("<pre>");
            print_r($array);
            print_r("</pre>");
            }
//foreach($xml as $key => $value) {
//	//if($key == "code"){
//	//	$strAuth = $value;
//    print($key ." : ".$value ."<br>");
//	//}    
//}
//print_r("<pre>");
//var_dump($xml);
//print_r("</pre>");

?>