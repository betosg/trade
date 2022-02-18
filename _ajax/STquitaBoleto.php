<?php
/***************************************************************/
/**        SCRIPTS PHP PARA AJAX - INSTRUÇÕES BÁSICAS         **/
/***************************************************************/
/** 1- Deve-se retirar qualquer caracter que estiver fora     **/
/**    da marcação PHP (<?php | ?>), pois afetará no retorno  **/
/**    de dados para o AJAX, inclusive espaços e caracteres   **/
/**    invisíveis                                             **/
/**                                                           **/
/** 2- O separador de dados padrão em coluna é o pipe "|"     **/
/**    e o de linhas é a quebra de linha "\n"     			  **/
/**                                                           **/
/** 3- Os cabeçalhos HTTP devem ser usados conforme o caso.   **/
/**    Por padrão inicial, ele não poe em cache os dados mas  **/
/**    pode ser modificado de acordo com a especificação do   **/
/**    script                                                 **/
/**                                                           **/
/** 4- Os tratamentos de erros podem ser customizados mas     **/
/**    OBRIGATORIAMENTE precisa ter ACIMA da saída de dados a **/
/**    linha "header("HTTP/1.0 500 Server internal error");"  **/
/**                                                           **/
/***************************************************************/
/** Sugestão:                                                 **/
/** Após a leitura das instruções remova esse comentário do   **/
/** novo script                                               **/
/***************************************************************/

/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");
$intI = 0;
/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB); 

/***            DEFINIÇÃO DE PARÂMETROS            ***/
/*****************************************************/
echo "<br>". $intTitulo            = request("var_titulo");
echo "<br>". $strTransaction       = request("var_transaction");
echo "<br>". $strCreditDate        = request("var_creditDate");
echo "<br>". $strValor             = request("var_valor");
echo "<br>". $strOperacao          = request("var_operacao");
echo "<br>". $strNomeExtrato       = request("var_nome");
echo "<br>". $strDescricao         = request("var_descricao");
echo "<br>". $strTipo              = request("var_tipo");
$strUserLogado = getsession(CFG_SYSTEM_NAME . "_id_usuario");



/***            CONSULTA FONTE DOS DADOS           ***/
/*****************************************************/
try {
    $strSQLBusca   = "select cod_conta_pagar_receber,   cod_conta , cod_plano_conta ,cod_centro_custo ,cod_job, tipo, codigo, historico ";
    $strSQLBusca  .= " FROM fin_conta_pagar_receber  where cod_conta_pagar_receber in( select  cod_conta_pagar_receber ";
    $strSQLBusca  .= "                                                                FROM  fin_conta_pagar_receber_log_bepay where transaction_id = '".$strTransaction."'  )";
    $objResult = $objConn->query($strSQLBusca);
    $objRS = $objResult->fetch();
                                    $intCodDadoExtrato        = getValue($objRS,"cod_conta_pagar_receber");
                                    $intCodContaExtrato       = getValue($objRS,"cod_conta");
                                    $intCodPlanoContaExtrato  = getValue($objRS,"cod_plano_conta");
                                    $intCodCentroCustoExtrato = getValue($objRS,"cod_centro_custo");									
                                    $intCodJobExtrato         = "61";									
                                    $strTipoExtrato           = getValue($objRS,"tipo");
                                    $intCodigoExtrato         = getValue($objRS,"codigo");
                                    $strHistoricoExtrato      = getValue($objRS,"historico");
                                    $totalPagoExtrato         = str_replace(",",".",$strValor);
                                    $strObsExtrato            = "Lançamento automatico Transaction: ".$strTransaction;
                                    $strDocumentoExtrato      = $strTipo;
                                    $strNumLctoExtrato        = "TITULO: ". getValue($objRS,"cod_conta_pagar_receber");									     
                                    $dblVlrLctoNormExtrato    = $totalPagoExtrato;
                                    $dblVlrMultaExtrato       = 0;
                                    $dblVlrJurosExtrato       = 0;
                                    $dblVlrDescExtrato        = 0;
                                    $numDocumentoExtrato      = "";
                                    $strExtraDocumentoExtrato = "" ;
                                    $strSysUsuarioExtrato     = "BEPAY";
                                    $creditDateExtrato        = date_create($strCreditDate);                                    
                                    $creditDateExtrato        = date_format($creditDateExtrato,'d/m/Y');
                                    $idTransaction            = $strTransaction;

                                    if ($strOperacao =="receita"){
                                       
                                        
                                        $strSQLQuitacao   = "INSERT INTO fin_lcto_ordinario (cod_conta_pagar_receber, tipo                     , codigo                   , cod_conta                  , cod_plano_conta                 , cod_centro_custo                 , cod_job                  , historico                     , obs                     , num_lcto                    , dt_lcto    , dt_cred                  ,    vlr_lcto                , vlr_multa                  , vlr_juros                  , vlr_desc                  , sys_dtt_ins      , sys_usr_ins                    , tipo_documento                     , num_documento                 , extra_documento) ";
                                        $strSQLQuitacao  .= "             VALUES ("         . $intCodDadoExtrato . ", '" . $strTipoExtrato . "', " . $intCodigoExtrato . ", " . $intCodContaExtrato . ", " . $intCodPlanoContaExtrato . ", " . $intCodCentroCustoExtrato . ", " . $intCodJobExtrato . ", '" . $strHistoricoExtrato . "', '" . $strObsExtrato . "', '" . $strNumLctoExtrato . "',CURRENT_DATE, '".$creditDateExtrato ."', "   . $totalPagoExtrato . ", " . $dblVlrMultaExtrato . ", " . $dblVlrJurosExtrato . ", " . $dblVlrDescExtrato . ", CURRENT_TIMESTAMP, '" . $strSysUsuarioExtrato . "', '" . $strDocumentoExtrato . "'     , '" . $numDocumentoExtrato . "', '" . $strExtraDocumentoExtrato . "'); ";                                      
                                        $objConn->query($strSQLQuitacao);                                  

                                        $strSQL  = " UPDATE fin_conta_pagar_receber ";
                                        $strSQL .= " SET sys_dtt_ult_lcto = CURRENT_TIMESTAMP , cod_job =  " . $intCodJobExtrato;
                                        $strSQL .= "   , sys_usr_ult_lcto = '" . $strUserLogado . "' ";
                                        $strSQL .= " WHERE cod_conta_pagar_receber = " . $intCodDadoExtrato;                                    
                                        $objConn->query($strSQL);

                                        $strSQL = "UPDATE fin_conta_pagar_receber_log_bepay SET processamento_boleto = true where transaction_id = '".$strTransaction."'";
                                        $objConn->query($strSQL);
                                      
                                    }else{//despesa
                                        $codContaDespesa = 29;
                                        $codFornecDespesa = 1034;
                                        $codPlanoContaDespesa = 642;
                                        $codCentroCustoDespesa = 76;
                                        $codJobDespesa = 61;
                                        $strHistoricoDespesa2 = "TAXA BEPAY BOLETO - ". $strNomeExtrato ." (".$intCodDadoExtrato.")";
                                        $strSQL = "UPDATE fin_conta_pagar_receber_log_bepay SET processamento_despesa = true where transaction_id = '".$strTransaction."'";
                                        if (strpos($strDescricao,"MEDIADOR")){
                                            $strHistoricoDespesa2 = "TAXA BEPAY BOLETO MDR - ". $strNomeExtrato ." (".$intCodDadoExtrato.")" ;
                                            $strSQL = $strSQL = "UPDATE fin_conta_pagar_receber_log_bepay SET processamento_mediator = true where transaction_id = '".$strTransaction."'";
                                        }
                                        $strAdicionalInfoParcela = "";
                                        
                                        //$strObsExtrato
                                        $strSQLQuitacao  = "INSERT INTO fin_lcto_em_conta (cod_conta                , operacao ,   codigo               , tipo               , historico                  , cod_plano_conta           , cod_centro_custo             , num_lcto              , dt_lcto      , vlr_lcto             , obs , cod_evento, sys_dtt_ins, sys_usr_ins, sys_dtt_upd, sys_usr_upd, cod_job            , dt_cred) ";
                                        $strSQLQuitacao .="     VALUES (                  ". $codContaDespesa." ,'despesa' , ".$codFornecDespesa ." , 'cad_pj_fornec'    , '".$strHistoricoDespesa2."', ".$codPlanoContaDespesa." , ".$codCentroCustoDespesa."   , ".$intCodDadoExtrato.", CURRENT_DATE, ".$totalPagoExtrato." , NULL, NULL      , NULL       , NULL       , NULL       , NULL       , ".$codJobDespesa." , '".$creditDateExtrato."') ";
                                        $objConn->query($strSQLQuitacao); 
                                        $objConn->query($strSQL);
                                        
                                   }


                                        
	
	//$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	/***               TRATAMENTO DE ERRO              ***/
	/*****************************************************/
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}


?>