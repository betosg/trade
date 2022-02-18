<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	//SESSION
	$idevento  		= getsession(CFG_SYSTEM_NAME."_id_evento");
	$idmercado 		= getsession("id_empresa");

	// REQUESTS
	$flagMENU 		= request("var_flag_menu"); // FLAG PARA EXIBIÇÃO DE MENU | RESIZE	

	/*
	===============================
	DEBUG REQUESTS
	// echo "<br> DA: ".$data_antes	  		 		= request("var_data_antes");
	// echo "<br> DD: ".$data_depois 		 		= request("var_data_depois");
	// echo "<br> TA: ".$vlr_titulo_antes    		= request("var_vlr_titulo_antes");
	// echo "<br> TD: ".$vlr_titulo_depois   		= request("var_vlr_titulo_depois");
	// echo "<br> PA: ".$plano_conta_antes  		= request("var_cod_conta_antes");
	// echo "<br> PD: ".$plano_conta_depois  		= request("var_cod_conta_depois");
	// echo "<br> CA: ".$centro_custo_antes  		= request("var_cod_centro_antes");
	// echo "<br> CD: ".$centro_custo_depois 		= request("var_cod_centro_depois");
	// echo "<br> JA: ".$job_antes 					= request("var_job_antes");
	// echo "<br> JD: ".$job_depois 				= request("var_job_depois");
	echo "<br>".	$cfg_boleto_antes 			= request("var_cfg_boleto_antes");
    echo "<br>".	$cfg_boleto_depois 			= request("var_cfg_boleto_depois");
	=================================
	*/
	
	$strIdentificador	 		= request("var_data_antes");
	
	
	
	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// Faz busca de TODOS OS DADOS
	try{
			
			$strSQL = "SELECT fin_conta_pagar_receber.cod_conta_pagar_receber 
							, fin_conta_pagar_receber.codigo AS cod_pj
							, cad_pj.razao_social
							, fin_conta_pagar_receber.nosso_numero
							, fin_conta_pagar_receber.situacao 
							, fin_conta_pagar_receber.vlr_conta
							, fin_conta_pagar_receber.vlr_saldo
							, fin_conta_pagar_receber.dt_emissao 
							, fin_conta_pagar_receber.dt_vcto
							, fin_conta_pagar_receber.obs
							, fin_plano_conta.cod_reduzido || ' ' || fin_plano_conta.nome AS plano_conta_nome
							, fin_centro_custo.nome AS centro_custo_nome
							, fin_job.nome AS job_nome						  
						  FROM  
							fin_conta_pagar_receber 
							INNER JOIN cad_pj ON (cad_pj.cod_pj = fin_conta_pagar_receber.codigo)
							LEFT OUTER JOIN prd_pedido ON (prd_pedido.cod_pedido = fin_conta_pagar_receber.cod_pedido)
							LEFT OUTER JOIN fin_plano_conta ON fin_conta_pagar_receber.cod_plano_conta = fin_plano_conta.cod_plano_conta
							LEFT OUTER JOIN fin_centro_custo ON fin_conta_pagar_receber.cod_centro_custo = fin_centro_custo.cod_centro_custo
							LEFT OUTER JOIN fin_job ON fin_conta_pagar_receber.cod_job = fin_job.cod_job
						  WHERE fin_conta_pagar_receber.tipo = 'cad_pj' 
							AND fin_conta_pagar_receber.situacao = 'aberto' 
							AND fin_conta_pagar_receber.identificador_lote = ".$strIdentificador;
						
							
				
		//die($strSQL);
		$objResult = $objConn->query($strSQL);
		//$objResult = $objConn->query("SET enable_nestloop = ON");								
								
	

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
	
	
	<?php
	// Testa se existe alguma resposta inserida
	// caso contrário, exibe mensagem de vazio
/*	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc","Nenhum Registro","","aviso",1,"","");
	} else{*/
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="32%" class="sortable" nowrap>razao_social</th>
				<th width="16%" class="sortable" nowrap>plano_conta</th>
				<th width="16%" class="sortable" nowrap>centro_custo</th>
				<th width="16%" class="sortable" nowrap>job</th>
				<th width="10%" class="sortable" nowrap>valor_atual</th>
				<th width="10%" class="sortable-date-dmy" nowrap>dt_vcto</th>
				
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="#FFFFFF">
				<td width="32%" align="left" 	style="vertical-align:middle;"><?php echo(getValue($objRS,"razao_social"));?></td>
				<td width="16%" align="left" 	style="vertical-align:middle;"><?php echo(getValue($objRS,"plano_conta_nome"));?></td>
				<td width="16%" align="left" 	style="vertical-align:middle;"><?php echo(getValue($objRS,"centro_custo_nome"));?></td>
				<td width="16%" align="left" 	style="vertical-align:middle;"><?php echo(getValue($objRS,"job_nome"));?></td>
				<td width="16%" align="right"   style="vertical-align:middle;"><?php echo(FloatToMoeda(getValue($objRS,"vlr_conta")));?></td>   				
				<td width="10%" align="center" 	style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false));?></td>
				
			</tr>            
		<?php }?>
		</tbody>
	</table>    
	<?php //}?>
</body>
<?php if($flagMENU != ""){?>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
<?php }?>
</html>
<?php
	$objConn = NULL;
	$objResult->closeCursor();
?>