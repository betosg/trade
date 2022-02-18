<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athkernelfunc.php");

$objConn = abreDBConn(CFG_DB);

$intCodProduto 	= request("var_cod_produto");
$intCodPJ		= request("var_cod_pj");
$strOpcao 		= request("var_opcao");
$strHistorico 	= request("var_historico");
$strObs			= request("var_obs");

//$intCodPF = (request("var_cod_pf") == "") ? NULL : request("var_cod_pf");

if($intCodProduto == ""){
	echo (" <center>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
			<tr>
			<td align=\"center\" valign=\"middle\" width=\"100%\">");
	mensagem("err_dados_titulo","err_sql_desc_card","Produto não especificado na listagem de produtos.","STsolicguias.php","aviso",1);
	echo ("	</td>
			</tr>
			</table>
			</center>");
	die();
}

try{
	$strSQL = "	SELECT cod_pedido
				FROM prd_pedido
				WHERE cod_pj = ".$intCodPJ." 
				AND	dtt_inativo IS NULL
				AND	it_tipo = 'homo'
				AND	CURRENT_DATE BETWEEN it_dt_ini_val_produto AND it_dt_fim_val_produto
				AND	situacao = 'aberto'";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

// caso a pj ja possua um pedido aberto de homo ou outro diferente de homo
if($objResult->rowCount() > 0){
	echo ("	<center>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\">
			<tr>
			<td align=\"center\" valign=\"middle\" width=\"100%\">");
	mensagem("err_dados_titulo","err_sql_desc_card","Esta PJ já possui um pedido aberto para este tipo de solicitação.","STColabAtivos.php","aviso",1);
	echo ("	</td>
			</tr>
			</table>
			</center>");
	die();
}

// busca valor do produto para inserção no pedido
try{
	$strSQL = "	SELECT valor FROM prd_produto WHERE cod_produto = ".$intCodProduto;
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS 	  = $objResult->fetch();
$dblValor = getValue($objRS,"valor");


/*** RECEBE PARAMETROS ***/

$intCodConta 		= getVarEntidade($objConn, "pedido_cod_conta_banco_padrao");
$intCodPlanoConta 	= getVarEntidade($objConn, "pedido_cod_plano_conta_padrao");
$intCodCentroCusto 	= getVarEntidade($objConn, "pedido_cod_centro_custo_padrao");
$intCodCFGBoleto 	= getVarEntidade($objConn, "cod_cfg_boleto_padrao");
$intCodJob          = getVarEntidade($objConn, "fin_cod_job");

/*** BUSCA DADOS COMPLEMENTARES DA PF ***/
try{
	$strSQL2 = "
			SELECT 	t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj,t1.razao_social,
					count(t5.cod_credencial) AS qtde_credencial,
					count(t6.cod_pedido)     AS qtde_ped_homo,
        			(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora 
			FROM cad_pj t1 
			INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
			INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) 
			LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) 
			LEFT OUTER JOIN sd_credencial t5 ON (t5.dtt_inativo is NULL AND t5.cod_pf = t2.cod_pf 
									 AND CURRENT_DATE <= dt_validade) 
			LEFT OUTER JOIN prd_pedido t6 ON (t6.situacao <> 'cancelado' AND t6.it_tipo = 'homo' 
										AND t6.it_cod_pf = t2.cod_pf AND t6.cod_pj = t3.cod_pj
										AND t6.it_cod_pf = t3.cod_pf AND t3.dt_demissao IS NULL
										AND t6.dtt_inativo IS NULL) 
			WHERE t1.cod_pj = ".$intCodPJ." 
			GROUP BY t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj, t1.razao_social,
					(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' 
			ORDER BY t2.cod_pf";
	$objResult2 = $objConn->query($strSQL2);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = '';

if($strOpcao == "") $strMsg .= "Selecionar para quem será gerado pedido<br>";
if(($intCodPJ == "") && ($strOpcao == "uma_empresa")) $strMsg .= "Informar empresa<br>";
if($intCodProduto == "") $strMsg .= "Informar produto<br>";
/*if ($strGerar == 'pedido_e_titulo') {
	if($dtVcto == "") $strMsg .= "Informar data de vencimento<br>";
	if($intCodConta == "") $strMsg .= "Informar conta banco<br>";
	if($intCodPlanoConta == "") $strMsg .= "Informar plano de conta<br>";
	if($intCodCentroCusto == "") $strMsg .= "Informar centro de custo<br>";
	if($strHistorico == "") $strMsg .= "Informar histórico<br>";
}*/

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

$dtVcto	= date("d/m/Y"); //dDate(CFG_LANG,now(),false);

if ($dblValor == "") $dblValor = "0";
$dblValor = str_replace(".","",$dblValor);
$dblValor = str_replace(",",".",$dblValor);		

$intParcelas = "NULL";
$strFrequencia = "";

if ($intCodJob == "") $intCodJob = "NULL";

//-------------------------------------------------------------------------
//Chama a PROC que gera as contribuições (uma apenas ou em lote)
//-------------------------------------------------------------------------
foreach($objResult2 as $objRS2){
	$objConn->beginTransaction();
	try{
		$strSQL = " SELECT * FROM sp_gera_pedido('apenas_pedido', ".$intCodPJ.", ".getValue($objRS2,"cod_pf").", ".$intCodProduto.", '".$dtVcto."', ".$dblValor.", ".$intCodConta.", ".$intCodPlanoConta.", ".$intCodCentroCusto.", ".$intCodJob.", ".$intCodCFGBoleto.", 'BOLETO', '".$strHistorico."', '".$strObs."', '".$strObs."', ".$intParcelas.", '".$strFrequencia."', '".getsession(CFG_SYSTEM_NAME."_id_usuario")."') ";
		$objConn->query($strSQL);
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
}

$objConn = NULL;
redirect("STColabAtivos.php");
?>