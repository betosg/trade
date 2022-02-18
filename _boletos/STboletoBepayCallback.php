<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");
include_once("../_database/athsendmail.php");
erroReport();

$strJson = file_get_contents('php://input');
$arrDados = json_decode($strJson, true);

//print_r($arrDados);

if (isset($arrDados["transactionId"])){
//print($arrDados["receivedAmount"]."<br>");
//print($arrDados["transactionId"]."<br>");

$varDB = request('var_db');
if ($varDB == ""){$varDB = CFG_DB;}
$objConn = abreDBConn($varDB);
//----------------------------
// Insere dados do lançamento 
//----------------------------
    $objConn->beginTransaction();
    try{
            $strSQL = " select  email_sacado, nome_sacado, data_post , cast(data_post AS json) as dtPost";
            $strSQL .= " FROM  fin_conta_pagar_receber_log_bepay where transaction_id = '".$arrDados["transactionId"]."'";
            $objResult = $objConn->query($strSQL);
            $objRS = $objResult->fetch();
            $strNomeSacado  = getValue($objRS,"nome_sacado");
            $strEmailSacado = getValue($objRS,"email_sacado");
            
            
            $strSQL = " UPDATE fin_conta_pagar_receber_log_bepay SET callback_data = '".$strJson."', callback_valor = ".$arrDados["receivedAmount"] .", callback_dt_processamento = current_timestamp ";
            $strSQL .= " where transaction_id = '".$arrDados["transactionId"]."'";
            $objConn->query($strSQL);

            $strSQL  = "select cod_conta_pagar_receber,   cod_conta , cod_plano_conta ,cod_centro_custo ,cod_job, tipo, codigo, historico ";
            $strSQL .= " FROM fin_conta_pagar_receber  where cod_conta_pagar_receber in( select  cod_conta_pagar_receber ";
            $strSQL .= "                                                                FROM  fin_conta_pagar_receber_log_bepay where transaction_id = '".$arrDados["transactionId"]."')";
            $objResult = $objConn->query($strSQL);
            $objRS = $objResult->fetch();

            

            $intCodDado        = getValue($objRS,"cod_conta_pagar_receber");
            $intCodConta       = getValue($objRS,"cod_conta");
            $intCodPlanoConta  = getValue($objRS,"cod_plano_conta");
            $intCodCentroCusto = getValue($objRS,"cod_centro_custo");
            $intCodJob         = getValue($objRS,"cod_job");
            $strTipo           = getValue($objRS,"tipo");
            $intCodigo         = getValue($objRS,"codigo");
            $strHistorico      = getValue($objRS,"historico");
            $strObs            = "Lançamento automatico callback ".$arrDados["transactionId"];
            $strDocumento  = "BOLETO";
            $strNumLcto = "TITULO: ". getValue($objRS,"cod_conta_pagar_receber");
            $dblVlrLctoNorm =  formatcurrency($arrDados["receivedAmount"],2); 
            $dblVlrMulta = 0;
            $dblVlrJuros = 0;
            $dblVlrDesc = 0;
            $numDocumento = "";
            $strExtraDocumento = "" ;
            $strSysUsuario = "BEPAY";
            
            $strSQL  = " INSERT INTO fin_lcto_ordinario (cod_conta_pagar_receber, tipo              , codigo            , cod_conta           , cod_plano_conta          , cod_centro_custo          , cod_job           , historico              , obs              , num_lcto             , dt_lcto     ,    vlr_lcto               , vlr_multa           , vlr_juros          , vlr_desc           , sys_dtt_ins      , sys_usr_ins             , tipo_documento         , num_documento          , extra_documento) ";
            $strSQL .= "                     VALUES (" . $intCodDado . "        , '" . $strTipo . "', " . $intCodigo . ", " . $intCodConta . ", " . $intCodPlanoConta . ", " . $intCodCentroCusto . ", " . $intCodJob . ", '" . $strHistorico . "', '" . $strObs . "', '" . $strNumLcto . "', current_date, "   . $dblVlrLctoNorm . ", " . $dblVlrMulta . ", " . $dblVlrJuros . ", " . $dblVlrDesc . ", CURRENT_TIMESTAMP, '" . $strSysUsuario . "', '" . $strDocumento . "', '" . $numDocumento . "', '" . $strExtraDocumento . "') ";
            $objConn->query($strSQL);

            $strSQL  = " UPDATE fin_conta_pagar_receber ";
            $strSQL .= " SET sys_dtt_ult_lcto = CURRENT_TIMESTAMP ";
            $strSQL .= "   , sys_usr_ult_lcto = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
            $strSQL .= " WHERE cod_conta_pagar_receber = " . $intCodDado;
            
            $objConn->query($strSQL);

            $objConn->commit();

            
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
    
    $strCorpo = "Olá <b>".$strNomeSacado."</b><br><br>Seu pagamento foi processado e recebido na data de ".date('d/m/Y')."<br><br>" . "<b><a href='".$strLink."' target='_blank'>Clique aqui para acessar seu recibo." . "</a></b><br><br>";
    emailNotify($strCorpo, "ABFM Informa recibo pagamento titulo: ".$intCodDado, $strEmailSacado, CFG_EMAIL_SENDER);
                    
    //$headerSize = curl_getinfo($result);
	////$header = substr($result, 0, $headerSize);
	////$header = getHeaders($header);
//
	//// extract body
	//$body = substr($result, $headerSize);
	//print_r(curl_getinfo($result));
	print("titulo_baixado");
	
	
    }
    catch(PDOException $e){
        mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
        $objConn->rollBack();
        die();
    }

}
?>