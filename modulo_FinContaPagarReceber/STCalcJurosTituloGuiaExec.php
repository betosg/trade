<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

// ABERTURA DE CONEXÃO COM BANCO
$objConn = abreDBConn(CFG_DB);

$intCodDado      = request("var_chavereg");
$dblVlrConta     = request("var_vlr_titulo");
$dateDtAdmissao  = request("var_dt_admissao");
$strExercicio    = request("var_exercicio");
$intCodCfgBoleto = request("var_cod_cfg_boleto");
//Passamos a receber o Tipo de Documento pois, conforme informação 
//do Alexandre (sindieventos) na guia Assistencial o calc. de juros é diferente.
//by Vini - 23.01.2013
$strTipoDocumento = request("var_tipo_documento"); 
$dateDtVctoOriginal = request("var_dt_vcto"); //pega a data de vcto original da conta pagar/receber - sera usado quando for BOLETO_ASSITENCIAL

$strLog = "";

if ((cDate(CFG_LANG,$dateDtAdmissao,false) > dateNow())) {
	mensagem_local("","Erro data emissão","A data de admissão não pode ser maior que a data atual.","","erro",1);
	die();
}

$dblVlrConta = MoedaToFloat($dblVlrConta);

$dblPercTaxaJuros_PrimMes        = getVarEntidade($objConn,"guia_multa_taxa_juros_1o_mes");
$dblPercTaxaJuros_SegMesEmDiante = getVarEntidade($objConn,"guia_multa_taxa_juros_2o_mes_em_diante");
$dblPercTaxaMora                 = getVarEntidade($objConn,"guia_multa_taxa_mora");

$strLog .= "<br>USUARIO";
$strLog .= "<br>".getsession(CFG_SYSTEM_NAME . "_id_usuario")." ".now();
$strLog .= "<br>".basename(getcwd());
$strLog .= "<br>";
$strLog .= "<br>PARAMETROS INFORMADOS/LIDOS";
$strLog .= "<br>var_vlr_titulo: ".$dblVlrConta;
$strLog .= "<br>var_dt_admissao: ".$dateDtAdmissao;
$strLog .= "<br>var_exercicio: ".$strExercicio;
$strLog .= "<br>var_dt_vcto (orig): ".$dateDtVctoOriginal;
$strLog .= "<br>var_tipo_documento: ".$strTipoDocumento;
$strLog .= "<br>var_cod_cfg_boleto: ".$intCodCfgBoleto;
$strLog .= "<br>guia_multa_taxa_juros_1o_mes: ".$dblPercTaxaJuros_PrimMes;
$strLog .= "<br>guia_multa_taxa_juros_2o_mes_em_diante: ".$dblPercTaxaJuros_SegMesEmDiante;
$strLog .= "<br>guia_multa_taxa_mora: ".$dblPercTaxaMora;
$strLog .= "<br>";
$strLog .= "<br>";


//data emissao
$data = date("d/m/Y");
$data = cDate(CFG_LANG,$data,false);
$dateDtEmissao = date("d/m/Y", strtotime(cDate(CFG_LANG,$data,false)));

$mes = date("m", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false)));
$ano = date("Y", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false)));

if(strtoupper($strTipoDocumento) == 'BOLETO_ASSISTENCIAL') {
    /*---------------------------------------------------------------------------------
	  Quando o boleto for assistencial a data de vcto permanece a mesma.
	  Informação passada pelo Alexandre no dia 06.02.2013 - Chamado 5071 ToDo 15351
	---------------------------------------------------------------------------------*/
	$dateDtVcto = date("d/m/Y", strtotime(cDate(CFG_LANG,$dateDtVctoOriginal,false)));	
}
else {	
	//data vcto
	if ($mes <= 3){
		$dateDtVcto = date("d/m/Y", strtotime(cDate(CFG_LANG,"30/04/".$ano,false)));	
	}
	else{
		$data = cDate(CFG_LANG,"1-".$mes."-".$ano,false);
		$data = dateAdd("m", 3, $data);
		$data = dateAdd("d", -1, $data);
		$dateDtVcto = date("d/m/Y", strtotime(cDate(CFG_LANG,$data,false)));
	}
}
//data vcto da mensagem
$data = cDate(CFG_LANG,"1-".date("m")."-".date("Y"),false);
$data = dateAdd("m", 1, $data);
$data = dateAdd("d", -1, $data);

//A data de vcto da mensagem precisa ser um dia útil, então avalia e altera se for preciso
//A pedido do Alexandre (Sindieventos) via chamado 18388, disse que já tinha solicitado 
//antes mas não encontrei nada - by Clv 03/04/2013
$intVoltaDias = 0;
if (date("w",strtotime(cDate(CFG_LANG,$data,false))) == 0) $intVoltaDias = 2; //dom
if (date("w",strtotime(cDate(CFG_LANG,$data,false))) == 6) $intVoltaDias = 1; //sab
if ($intVoltaDias > 0) {
	$data = dateAdd("d", $intVoltaDias * -1, $data);
}

$boolContinua = true;
while ($boolContinua) {
	$intDia = date("d", strtotime(cDate(CFG_LANG,$data,false)));
	$intMes = date("m", strtotime(cDate(CFG_LANG,$data,false)));
	$intAno = date("Y", strtotime(cDate(CFG_LANG,$data,false)));
	
	try{
		$strSQL = " SELECT cod_feriado
					FROM cad_feriado
					WHERE dtt_inativo IS NULL 
					AND (((data_dia IS NOT NULL AND data_dia = ".$intDia.") AND (data_mes IS NOT NULL AND data_mes = ".$intMes.") AND (data_ano IS NOT NULL AND data_ano = ".$intAno.")) OR 
						 ((data_dia IS NOT NULL AND data_dia = ".$intDia.") AND (data_mes IS NOT NULL AND data_mes = ".$intMes.") AND (data_ano IS NULL))) ";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intCodFeriado = getValue($objRS,"cod_feriado");
	
	if ($intCodFeriado == "") {
		$boolContinua = false;
	}
	else {
		//Se bateu num feriado volta um dia
		$data = dateAdd("d", -1, $data);
		
		//Avalia novamente se não é dom ou sab
		$intVoltaDias = 0;
		if (date("w",strtotime(cDate(CFG_LANG,$data,false))) == 0) $intVoltaDias = 2; //dom
		if (date("w",strtotime(cDate(CFG_LANG,$data,false))) == 6) $intVoltaDias = 1; //sab
		if ($intVoltaDias > 0) {
			$data = dateAdd("d", $intVoltaDias * -1, $data);
		}
	}
}
$dateDtVctoMensagem = date("d/m/Y", strtotime(cDate(CFG_LANG,$data,false)));

$strLog .= "<br>DADOS CALCULADOS";
$strLog .= "<br>dateDtEmissao: ".$dateDtEmissao;
$strLog .= "<br>dateDtVcto: ".$dateDtVcto;
$strLog .= "<br>dateDtVctoMensagem: ".$dateDtVctoMensagem;


//calculos
//Mudou a pedido do Alexandre via chamado 13671 - by Clv 18/09/2012
/*
$diferencaMes = diffMes($dateDtEmissao,date("d/m/Y", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false))));
if (date("m", strtotime(cDate(CFG_LANG,$dateDtAdmissao,false))) == "01") {
	$diferencaMes = $diferencaMes-1; //Caso a data de admissao seja em janeiro subtrai 1
}
*/
$diferencaMes = diffMes($dateDtEmissao,date("d/m/Y",strtotime(cDate(CFG_LANG,$dateDtVcto,false))));

$strLog .= "<br>diferencaMes: ".$diferencaMes;

$dblVlrTaxaJuros_PrimMes = 0;
$dblVlrTaxaJuros_SegMesEmDiante = 0;
$dblVlrTaxaMora = 0;

$dblAcrescimo = 0;
$dblMulta = 0;

$dblVlrMoraMultaTotal = 0;
/*-------------------------------------------------------------------------------*/
/*                     Forma de cálculo do Juros/Despesas                        */
/* By Vini - 29.10.12                                                            */
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
	
	$strLog .= "<br>dblVlrTaxaJuros_PrimMes: ".$dblVlrTaxaJuros_PrimMes;
	$strLog .= "<br>dblVlrTaxaJuros_SegMesEmDiante: ".$dblVlrTaxaJuros_SegMesEmDiante;
	$strLog .= "<br>dblVlrTaxaMora: ".$dblVlrTaxaMora;
	
	/*--------------------------------------------------------------------------*/
	/*  Conforme orientação do Alexandre, quando for Guia Assistencial          */
	/*  a regra é : multa 10% e 1% de mora ao mês. (chamado 5071 e ToDo 15351). */
	/*  By Vini - 23.01.2013                                                    */
	/*--------------------------------------------------------------------------*/ 
    if(strtoupper($strTipoDocumento) == 'BOLETO_ASSISTENCIAL') {
	    $dblAcrescimo = $diferencaMes * $dblVlrTaxaMora;
		$dblVlrMoraMultaTotal = $dblVlrTaxaJuros_PrimMes;
		
		$strLog .= "<br>(tipo doc = assistencial)";
		$strLog .= "<br>dblAcrescimo: ".$dblAcrescimo;
		$strLog .= "<br>dblVlrMoraMultaTotal: ".$dblVlrMoraMultaTotal;
	}
	else {
		//quando não é assistencial continua como antes...
		/*-------------------------------------------------*/
		/* Conforme o chamado 4761 (ToDo 14582)            */
		/* Alexandre pediu para que a mora seja cobrada a  */
		/* partir do primeiro mês do vencimento.           */
		/* By Vinicius - 26/10/2012                        */
		/*-------------------------------------------------*/
		//if ($diferencaMes >= 3){	
		//	$dblAcrescimo = (($diferencaMes - 2)*$dblVlrTaxaMora);	
		//}
		$strLog .= "<br>(tipo doc <> assistencial)";
		
		$dblAcrescimo = $diferencaMes * $dblVlrTaxaMora;
		$strLog .= "<br>dblAcrescimo: ".$dblAcrescimo;
			
		if ($diferencaMes >= 2){	
			$dblMulta = (($diferencaMes - 1)*$dblVlrTaxaJuros_SegMesEmDiante);	
			$strLog .= "<br>dblMulta (difer mes >= 2): ".$dblMulta;
		}
		
		$dblVlrMoraMultaTotal = $dblMulta + $dblVlrTaxaJuros_PrimMes;
		$strLog .= "<br>dblVlrMoraMultaTotal (multa + vlr juros 1o mes): ".$dblVlrMoraMultaTotal;
		
		//Uso da Taxa SELIC (quando tiver)
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
		
		$objRS = $objResult->fetch();
		$dblTxJurosSelic = getValue($objRS,"valor");
		$objResult->closeCursor();
		
		$strLog .= "<br>dblTxJurosSelic: ".$dblTxJurosSelic;

		if ($dblTxJurosSelic > 0) {
			$strLog .= "<br>dblVlrMoraMultaTotal (antes de somar selic): ".$dblVlrMoraMultaTotal;
			$dblVlrJurosSelic = $dblVlrConta * ($dblTxJurosSelic / 100);
			$strLog .= "<br>dblVlrJurosSelic: ".$dblVlrJurosSelic;
			$dblVlrMoraMultaTotal += $dblVlrJurosSelic;
			$strLog .= "<br>dblVlrMoraMultaTotal (+ selic): ".$dblVlrMoraMultaTotal;
		}
	}
}

$strLog .= "<br>";
$strLog .= "<br>";

$dblAcrescimo = MoedaToFloat($dblAcrescimo);
$dblVlrMoraMultaTotal = MoedaToFloat($dblVlrMoraMultaTotal);

//Leitura das instruções do modelo de boleto

//$strInstrucoes  = "BLOQUETO DE CONTRIBUICAO SINDICAL URBANA<br>";
//$strInstrucoes .= "<center>MULTA E JUROS CALCULADOS ATE ".$dateDtVctoMensagem."</center><br>";
//$strInstrucoes .= "<center>PAGAMENTO SOMENTE PODE SER EFETUADO NAS AGENCIAS DA CAIXA ECONOMICA FEDERAL</center><br>";
//$strInstrucoes .= "<center>GUIA VALIDA ATE ".$dateDtVctoMensagem."</center><br>";
//$strInstrucoes .= "<center>APOS ESTA DATA RETIRE OUTRA GUIA NO SITE DA ENTIDADE</center>";

try{
	$strSQL = " SELECT instrucoes_1, instrucoes_2, instrucoes_3, instrucoes_4, instrucoes_5
				FROM cfg_boleto
				WHERE cod_cfg_boleto = ".$intCodCfgBoleto."
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

//Como a mensagem é sobre MULTA então poderá ter que imprimir a Data de Vcto da Mensagem
//Para as guias da CAIXA ela é obrigatória, então está inserida nas instruções do modelo de boleto
$strInstrucoes = str_replace("<TAG_DT_VCTO_MSG>",$dateDtVctoMensagem,$strInstrucoes);

//Gravação dos valores no boleto
try{
	$strSQL = " UPDATE fin_conta_pagar_receber 
				SET instrucoes_boleto = '".$strInstrucoes."' 
				  , vlr_conta = ".$dblVlrConta."
				  , vlr_saldo = ".$dblVlrConta."
				  , vlr_mora_multa = ".$dblVlrMoraMultaTotal."
				  , vlr_outros_acresc = ".$dblAcrescimo."
				  , dt_vcto = '".cDate(CFG_LANG,$dateDtVcto,false)."'
				  , cod_cfg_boleto = ".$intCodCfgBoleto."
				  , sys_dtt_upd = CURRENT_TIMESTAMP
				  , sys_usr_upd = '".getsession(CFG_SYSTEM_NAME . "_id_usuario")."'
				  , sys_obs_calc = CASE WHEN sys_obs_calc IS NULL THEN '' ELSE sys_obs_calc END || '".$strLog."'
				WHERE cod_conta_pagar_receber = ".$intCodDado;
	$objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

?>
<script language="javascript">
	AbreJanelaPAGE('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo($intCodDado); ?>', '750', '580');
	window.document.location.href = "index.php"; /* Está em modulo_FinContaPagarReceber, volta pra index.php */
</script>
