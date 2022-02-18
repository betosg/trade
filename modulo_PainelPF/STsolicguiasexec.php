<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// ABERTURA DE CONEXÃO COM BANCO
$strDB = request("var_db");
if ($strDB == "")
	{ $strDB = CFG_DB; }
$objConn = abreDBConn($strDB);

$dblVlrConta     		= request("var_vlr_conta");
$bclVlrCapitalSocial   	= request("var_vlr_capital_social");
$dateDtAdmissao  		= request("var_dt_admissao");
$strExercicio	 		= request("var_exercicio");
$strCodAtividade 		= (request("var_codatividade") != "") ? (str_pad(request("var_codatividade"), 3, "0", STR_PAD_LEFT)) : "745";
$strTipo 				= request("var_tipo");
$intCodigo 		 		= request("var_cod_pj");
$intCodProduto         	= request("var_cod_produto");

if (getVarEntidade($objConn,"emissor_boleto_cod_prod") != ""){
	$intCodProduto = getVarEntidade($objConn,"emissor_boleto_cod_prod");
}

$intCodJob = getVarEntidade($objConn, "fin_cod_job");
if ($intCodJob == "") $intCodJob = "NULL";

$boolPagarReceber	= "FALSE";
$strTipoDocumento	= "BOLETO_SINDICAL";
$strHistorico		= "GUIA DE RECOLHIMENTO AVULSA"; //GUIA SINDICAL AVULSA DE ATRASO
$strObs				= getsession(CFG_SYSTEM_NAME . "_emissao_avulsa");

$strButtonAction = trim(request("var_button_action"));

if ((cDate(CFG_LANG,$dateDtAdmissao,false) > dateNow())) {
	mensagem("err_sql_titulo","err_sql_desc","A data de admissão não pode ser maior que a data atual.","","aviso",1);
	die();
}

$dblVlrConta = MoedaToFloat($dblVlrConta);

//Leitura constantes das taxas
$dblPercTaxaJuros_PrimMes        = getVarEntidade($objConn,"guia_multa_taxa_juros_1o_mes");
$dblPercTaxaJuros_SegMesEmDiante = getVarEntidade($objConn,"guia_multa_taxa_juros_2o_mes_em_diante");
$dblPercTaxaMora                 = getVarEntidade($objConn,"guia_multa_taxa_mora");
$usarIndice						 = getVarEntidade($objConn,"guia_usar_indice");

if ($dateDtAdmissao == "") {
	$dateDtAdmissao = "1/1/".$strExercicio;
	$dateDtAdmissao = cDate(CFG_LANG,$dateDtAdmissao,false);
}
$dateDtAdmissao;
$data = date("d/m/Y");
$data = cDate(CFG_LANG,$data,false);
$dateDtEmissao = date("d/m/Y",strtotime(cDate(CFG_LANG,$data,false)));

$mes = date("m",strtotime(cDate(CFG_LANG,$dateDtAdmissao,false)));
$ano = date("Y",strtotime(cDate(CFG_LANG,$dateDtAdmissao,false)));

if ($mes <= 3){
	$dateDtVcto = date("d/m/Y",strtotime(cDate(CFG_LANG,"30/04/".$ano,false)));	
}
else{
	$data = cDate(CFG_LANG,"1-".$mes."-".$ano,false);
	$data = dateAdd("m", 3, $data);
	$data = dateAdd("d", -1, $data);
	$dateDtVcto = date("d/m/Y",strtotime(cDate(CFG_LANG,$data,false)));
}

$data = cDate(CFG_LANG,"1-".date("m")."-".date("Y"),false);
$data = dateAdd("m", 1, $data);
$data = dateAdd("d", -1, $data);
$dateDtVctoMensagem = date("d/m/Y",strtotime(cDate(CFG_LANG,$data,false)));
$dblVlrTaxaJuros_PrimMes		= 0;
$dblVlrTaxaJuros_SegMesEmDiante	= 0;
$dblVlrTaxaMora					= 0;
$dblAcrescimo					= 0;
$dblMulta						= 0;
$dblVlrMoraMultaTotal			= 0;

//Mudou a pedido do Alexandre via chamado 13671 - by Clv 18/09/2012
/*
$diferencaMes = diffMes($dateDtEmissao,date("d/m/Y", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false))));
if (date("m", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false))) == "01") {
	$diferencaMes = $diferencaMes-1; //Caso a data de admissao seja em janeiro subtrai 1
}
*/
$diferencaMes = diffMes($dateDtEmissao,date("d/m/Y",strtotime(cDate(CFG_LANG,$dateDtVcto,false))));


/* INI: Baseado na $diferencaMes p/ atender Chamado 7347.21167 ------------------------- */
if ($diferencaMes > 0) {
	// Originalmente nessa STsolicicguiaexec.php, a guia modelo utilizada para 
	// impressão era SEMPRE a que estivesse configurada nas variáveis de configuração 
	// da Empresa (Módulo Cadastro/Config. Empresa) abaixo
	$intCodConta 	   = getVarEntidade($objConn, "guia_atraso_cod_conta");
	$intCodCentroCusto = getVarEntidade($objConn, "guia_atraso_cod_centro_custo");
	$intCodPlanoConta  = getVarEntidade($objConn, "guia_atraso_cod_plano_conta");
	$intCodCFGBoleto   = getVarEntidade($objConn, "guia_atraso_cod_boleto");
} else { 
	// Para atender a demanda solicitada (chamado.tarefa: 7347.21167, 
	// neste momento, foi adicionado o código para que no caso de o não 
	// reconhecimento de ATRASO buscar a guia que estiver nas outras 
	// varíáveis de configuração da empresa (no caso pra GRCS):
	$intCodConta 	   = getVarEntidade($objConn, "grcs_cod_conta_banco_padrao");
	$intCodCentroCusto = getVarEntidade($objConn, "grcs_cod_centro_custo_padrao");
	$intCodPlanoConta  = getVarEntidade($objConn, "grcs_cod_plano_conta_padrao");
	$intCodCFGBoleto   = getVarEntidade($objConn, "grcs_cod_cfg_boleto_padrao");
}
//die();
/* FIM: Baseado na $diferencaMes p/ atender Chamado 7347.21167 -- by Aless 05.11.2013 --*/


/*-------------------------------------------------------------------------------*/
/* Forma de cálculo do Juros/Despesas                         By Vini - 29.10.12 */
/*-------------------------------------------------------------------------------*/
/*  1°) Cálcula-se a diferença entre a data de emissão e a data de vencimento    */
/*  2°) A partir de 1 mês de atraso, cálculamos a MORA.                          */
/*  3°) A partir de 2 meses de atraso é calculado a taxa "Segundo mês em diante" */
/*  4°) A partir de 1 mês de atraso, cálculamos CORREÇÃO MONETÁRIA (TX SELIC).   */
/*-------------------------------------------------------------------------------*/
if ($diferencaMes > 0) {
	$dblVlrTaxaJuros_PrimMes = $dblVlrConta * ($dblPercTaxaJuros_PrimMes / 100);
	$dblVlrTaxaJuros_SegMesEmDiante = $dblVlrConta * ($dblPercTaxaJuros_SegMesEmDiante / 100);
	$dblVlrTaxaMora = $dblVlrConta * ($dblPercTaxaMora / 100);
	/*-------------------------------------------------*/
    /* Conforme o chamado 4761 (ToDo 14582)            */
    /* Alexandre pediu para que a mora seja cobrada a  */
    /* partir do primeiro mês do vencimento.           */
    /* By Vinicius - 26/10/2012                        */
    /*-------------------------------------------------*/
    //if ($diferencaMes >= 3){	
	//	$dblAcrescimo = (($diferencaMes - 2)*$dblVlrTaxaMora);	
	//}
    $dblAcrescimo = $diferencaMes * $dblVlrTaxaMora;	  

	if ($diferencaMes >= 2){	
		$dblMulta = (($diferencaMes - 1) * $dblVlrTaxaJuros_SegMesEmDiante);	
	}
	
	$dblVlrMoraMultaTotal = $dblMulta + $dblVlrTaxaJuros_PrimMes;
	
	// --------------------------------------------------
	// Uso da Taxa SELIC (quando tiver)
	// --------------------------------------------------
	try{
		$strSQL = " SELECT valor FROM fin_indice 
					WHERE tipo LIKE 'taxa_juros_selic' 
					AND CURRENT_DATE BETWEEN dt_ini AND dt_fim 
					ORDER BY valor ";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	$dblTxJurosSelic = 0;
	
	if($objResult->rowCount()>0){
		$objRS = $objResult->fetch();
		$dblTxJurosSelic = getValue($objRS,"valor");
	}
	$objResult->closeCursor();
	
	if ($dblTxJurosSelic > 0) {
		$dblVlrMoraMultaTotal += ($dblVlrConta * ($dblTxJurosSelic / 100));
	}
}

$dblAcrescimo			= MoedaToFloat($dblAcrescimo);
$dblVlrMoraMultaTotal	= MoedaToFloat($dblVlrMoraMultaTotal);


if($usarIndice == "usar_indice") {	
	try{
		$strSQL = "SELECT 
					  tipo,
					  dt_ini,
					  dt_fim,
					  valor,
					  valor_adicional, 
					  tipo_guia
					FROM 
					  public.fin_indice
					WHERE CURRENT_DATE BETWEEN dt_ini AND dt_fim
					AND ".$bclVlrCapitalSocial." between capital_min AND capital_max
					AND tipo = 'tab_calc_sindical'
					AND dt_inativo IS NULL";
		
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	//esse calculo trata-se da alteração solicitada pelo R.Cabrera no chamado nro: 31205	
	$dblVlrConta = ((getValue($objRS,"valor")/100)*$bclVlrCapitalSocial) + getValue($objRS,"valor_adicional");
	//die();
}
//INI: Buscas as instruções do modelo ----------------------------------
try{
	$strSQL = " SELECT instrucoes_1, instrucoes_2, instrucoes_3, instrucoes_4, instrucoes_5
				FROM cfg_boleto
				WHERE cod_cfg_boleto = ".$intCodCFGBoleto."
				AND dtt_inativo IS NULL ";
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$objRS = $objResult->fetch();
$strInstrucoes = getValue($objRS,"instrucoes_1")."<br>".
                 getValue($objRS,"instrucoes_2")."<br>".
				 getValue($objRS,"instrucoes_3")."<br>".
				 getValue($objRS,"instrucoes_4")."<br>".
				 getValue($objRS,"instrucoes_5");
$objResult->closeCursor();
//FIM: Buscas as instruções do modelo ----------------------------------


//Como a mensagem é sobre MULTA então poderá ter que imprimir a Data de Vcto da Mensagem
//Para as guias da CAIXA ela é obrigatória, então está inserida nas instruções do modelo de boleto
$strInstrucoes = str_replace("<TAG_DT_VCTO_MSG>",$dateDtVctoMensagem,$strInstrucoes);

$objConn->beginTransaction();
try{                                            
	$strEntidadeHist = getVarEntidade($objConn,"emissor_boleto_avulso_historico");
	$strSQL = "select t1.cod_conta_pagar_receber as codigo from fin_conta_pagar_receber t1 ";
	$strSQL .= "inner join prd_pedido t2 ON t1.cod_pedido = t2.cod_pedido AND t1.tipo = 'cad_pj' where t1.codigo = ".$intCodigo." AND t2.it_cod_produto = ". $intCodProduto;
//	$strSQL .= "t1.situacao <> 'cancelado' AND t1.situacao <> 'agrupado'";
	if ( $strEntidadeHist != "")
		{
			$strSQL .= " and upper(t1.historico) = upper('". getVarEntidade($objConn,"emissor_boleto_avulso_historico")."')";
		}
	//die();
	
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
	
	$intCodGuiaMulta = getValue($objRS,"codigo");
	
	if ($intCodGuiaMulta !=""){
		if($usarIndice == "usar_indice"){
			$strSQL = "update fin_conta_pagar_receber set vlr_conta = ". MoedaToFloat($dblVlrConta)." where cod_conta_pagar_receber = " . $intCodGuiaMulta;
			//die();
			$objConn->query($strSQL);
		}
	}
	else{
			//Insere pedido
			if ($usarIndice == "usar_indice"){
				$dateDtVcto = date("d/m/Y",strtotime(cDate(CFG_LANG,"31/01/".$ano,false)));	
			}
			$strSQL = " INSERT INTO prd_pedido ( cod_pj       , it_cod_produto    , situacao, sys_dtt_ins      , sys_usr_ins) 
						VALUES (                ".$intCodigo.", ".$intCodProduto.", 'aberto', CURRENT_TIMESTAMP, '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."') ";
			$objConn->query($strSQL);
			
			//Busca código-chave do registro recém gravado
			$strSQL = " SELECT currval('prd_pedido_cod_pedido_seq') AS codigo FROM prd_pedido ";
			$objResult = $objConn->query($strSQL);
			$objRS = $objResult->fetch();
			$intCodPedido = getValue($objRS,"codigo");
			$objResult->closeCursor();
			
			//Insere guia de multa
			$strSQL = " INSERT INTO fin_conta_pagar_receber ( pagar_receber        , cod_grupo, tipo          , codigo        , cod_conta       , cod_plano_conta      , cod_centro_custo      , cod_job       , historico              , obs              , tipo_documento             , dt_emissao                                    , dt_vcto                                , vlr_conta           , vlr_pago, vlr_saldo           , vlr_mora_multa            , vlr_outros_acresc, situacao, cod_cfg_boleto      , instrucoes_boleto   , cod_pedido       , sys_dtt_ins      , sys_usr_ins) 
						VALUES (                              ".$boolPagarReceber.", NULL     , '".$strTipo."', ".$intCodigo.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", '" . $strHistorico . "', '" . $strObs . "', '" . $strTipoDocumento . "', '" . cDate(CFG_LANG,$dateDtEmissao,false) . "', '".cDate(CFG_LANG,$dateDtVcto,false)."', " . MoedaToFloat($dblVlrConta) . "    , 0       , " . MoedaToFloat($dblVlrConta) . ", ".MoedaToFloat($dblVlrMoraMultaTotal) .", ".MoedaToFloat($dblAcrescimo).", 'aberto', ".$intCodCFGBoleto.", '".$strInstrucoes."', ".$intCodPedido.", CURRENT_TIMESTAMP, '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."') ";
			$objConn->query($strSQL);
			
			//Busca código-chave do registro recém gravado
			$strSQL = " SELECT currval('fin_conta_pagar_receber_cod_conta_pagar_receber_seq') AS codigo FROM fin_conta_pagar_receber ";
			$objResult = $objConn->query($strSQL);
			$objRS = $objResult->fetch();
			$intCodGuiaMulta = getValue($objRS,"codigo");
			$objResult->closeCursor();
	}
		
		$objConn->commit();
}catch(PDOException $e) {
	$objConn->rollBack();
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($usarIndice == "usar_indice") {
?>
<form name="externo" id="externo" action="../modulo_FinContaPagarReceber/STshowBoleto.php" method="post">
	<input type="hidden" name="var_chavereg" id="var_chavereg" value="<?php echo($intCodGuiaMulta); ?>" />
    <input type="hidden" name="var_db"       id="var_db"       value="<?php echo($strDB);?>" />
</form>
<script language="javascript">
	document.getElementById("externo").submit();
</script>
<?php } ?>
<script language="javascript">
	AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo($intCodGuiaMulta); ?>&var_db=<?php echo($strDB);?>', '750', '580');
	<?php if (getsession(CFG_SYSTEM_NAME . "_emissao_avulsa") == ""){ ?>
		window.document.location.href = "../modulo_PainelPJ/STindex.php";
	<?php } ?>
</script>