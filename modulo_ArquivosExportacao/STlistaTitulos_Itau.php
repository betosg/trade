<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// REQUESTS
	$dateDtEmissaoIni = request("var_dt_emissao_ini");
	$dateDtEmissaoFim = request("var_dt_emissao_fim");
	$dateDtVctoIni = request("var_dt_vcto_ini");
	$dateDtVctoFim = request("var_dt_vcto_fim");
	$strHistorico = request("var_historico");
	$strTipoDocumento = request("var_tipo_documento");
	
	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	try{
		$strSQL = " SELECT t1.cod_conta_pagar_receber, t1.nosso_numero, t1.num_documento, t1.dt_vcto, t1.dt_emissao
						 , t1.vlr_conta, t2.cnpj AS sacado_cnpj, t2.razao_social AS sacado_nome, t2.endprin_logradouro
						 , t2.endprin_numero, t2.endprin_complemento, t2.endprin_bairro, t2.endprin_cidade
						 , t2.endprin_estado, t2.endprin_cep AS sacado_cep
					FROM fin_conta_pagar_receber t1, cad_pj t2, fin_conta t3, fin_banco t4
					WHERE t1.pagar_receber = FALSE
					AND t1.situacao ILIKE 'aberto'
					AND t1.codigo = t2.cod_pj
					AND t1.tipo = 'cad_pj' 
					AND t1.cod_conta = t3.cod_conta
					AND t3.cod_banco = t4.cod_banco
					AND t4.num_banco = '341' "; //FIXO POR ENQUANTO
		if (($dateDtEmissaoIni != "") && ($dateDtEmissaoFim != "")) $strSQL .= " AND t1.dt_emissao BETWEEN TO_TIMESTAMP('".$dateDtEmissaoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtEmissaoFim."', 'DD/MM/YYYY') ";
		if (($dateDtVctoIni != "") && ($dateDtVctoFim != "")) $strSQL .= " AND t1.dt_vcto BETWEEN TO_TIMESTAMP('".$dateDtVctoIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtVctoFim."', 'DD/MM/YYYY') ";
		if ($strHistorico != "") $strSQL .= " AND t1.historico ILIKE '".$strHistorico."%' ";
		if ($strTipoDocumento != "") $strSQL .= " AND t1.tipo_documento ILIKE '".$strTipoDocumento."' ";
		$strSQL .= " ORDER BY t2.razao_social, t1.dt_emissao, t1.num_documento ";
		
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// Inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? "#F5FAFA" : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="_css/default.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin: 0px; background-color:#FFFFFF; } 
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
</style>
</head>
<body bgcolor="#FFFFFF">
<table align="center" cellpadding="0" cellspacing="1" style="width:97%;" class="tablesort">
<thead>
	<tr>
		<th width="01%" class="sortable" nowrap><?php echo(getTText("cod",C_NONE)); ?></th>
		<th width="48%" class="sortable"><?php echo(getTText("cliente",C_NONE)); ?></th>
		<th width="15%" class="sortable" nowrap><?php echo(getTText("cnpj",C_NONE)); ?></th>
		<th width="20%" class="sortable"><?php echo(getTText("documento",C_NONE)); ?></th>
		<th width="8%" class="sortable-date-dmy" nowrap><?php echo(getTText("emissao",C_NONE)); ?></th>
		<th width="8%" class="sortable-date-dmy" nowrap><?php echo(getTText("vcto",C_NONE)); ?></th>
		<th width="10%" class="sortable-currency" nowrap><?php echo(getTText("valor",C_NONE)); ?></th>
	</tr>
</thead>
<tbody>
<?php foreach($objResult as $objRS){?>
	<tr bgcolor="<?php echo getLineColor($strColor); ?>">
		<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"cod_conta_pagar_receber"));?></td>
		<td align="left"   style="vertical-align:middle;"><?php echo(getValue($objRS,"sacado_nome"));?></td>
		<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"sacado_cnpj"));?></td>
		<td align="center" style="vertical-align:middle;"><?php echo(getValue($objRS,"num_documento"));?></td>
		<td align="center" style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false));?></td>
		<td align="center" style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false));?></td>
		<td align="right"  style="vertical-align:middle;"><?php echo(FloatToMoeda(getValue($objRS,"vlr_conta")));?></td>
	</tr>
<?php }?>
</tbody>
</table>
</body>
</html>
<?php
	$objConn = NULL;
	$objResult->closeCursor();
?>