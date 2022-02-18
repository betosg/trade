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
	$dateDtIni 		= request("var_data_ini");
	$dateDtFim 		= request("var_data_fim");
	$strRPSinicio 	= request("var_rps_ini");
	$strRPSfim		= request("var_rps_fim");
	$strTipo		= request("var_tipo") ;

	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);

	// Faz busca de TODOS OS DADOS
	try{
		if ($strTipo == 'rps') 
		{ 
			if ($strRPSinicio == "") $strRPSinicio = "000000";
			if ($strRPSfim == "") $strRPSfim = "000000";
			
			$strSQL = "SELECT 						  
							  nroduplicata
							, valorpar
							, datafat
							, nronf							
						FROM ped_pedidos_parcelamento
						WHERE CAST(nronf AS INTEGER) BETWEEN CAST('".$strRPSinicio."' AS INTEGER) AND CAST('".$strRPSfim."' AS INTEGER)
							  AND ped_pedidos_parcelamento.idmercado = '".$idmercado."'							  
						UNION
						SELECT 						  
							  nroduplicata
							, valorped
							, datafat
							, nronf							
						FROM ped_servico_parcelamento
						WHERE CAST(nronf AS INTEGER) BETWEEN CAST('".$strRPSinicio."' AS INTEGER) AND CAST('".$strRPSfim."' AS INTEGER)
							  AND ped_servico_parcelamento.idmercado = '".$idmercado."'							  
						ORDER BY nronf
						LIMIT 100";
						
		}
		else{
			if ($dateDtIni == "") $dateDtIni = "17/11/1980";
			if ($dateDtFim == "") $dateDtFim = "17/11/1980";
			
			$strSQL = "SELECT nroduplicata
							, valorpar
							, datafat
							, nronf							
						FROM ped_pedidos, ped_pedidos_parcelamento
						WHERE ped_pedidos.idmercado = ped_pedidos_parcelamento.idmercado
							  AND ped_pedidos.idpedido = ped_pedidos_parcelamento.idpedido
							  AND vencimentoped BETWEEN TO_TIMESTAMP(".$dateDtIni." ,'DD/MM/YYYY') AND TO_TIMESTAMP(".$dateDtFim." ,'DD/MM/YYYY')
							  AND ped_pedidos.idevento = '" .$idevento."'
  						ORDER BY nroduplicata
						LIMIT 100";
			}
		
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
	if($objResult->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc","Nenhum Registro","","aviso",1,"","");
	} else{
	?>
	
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="25%" class="sortable" nowrap>DUPLICATA</th>
				<th width="25%" class="sortable" nowrap>VALOR</th>
				<th width="25%" class="sortable-date-dmy" nowrap>RPS Nº</th>
				<th width="25%" class="sortable" nowrap>DATA RPS</th>
				
			</tr>
		</thead>
		<tbody>
		<?php foreach($objResult as $objRS){?>
			<tr bgcolor="<?php echo((getValue($objRS,"bloqueado")==FALSE) ? "#F5FAFA" : "#FFFFFF");?>">
				<td width="25%" align="left" style="vertical-align:middle;"><?php echo(getValue($objRS,"nroduplicata"));?></td>
				<td width="25%" align="right"   style="vertical-align:middle;"><?php echo(FloatToMoeda(getValue($objRS,"valorpar")));?></td>
   				<td width="25%" align="left"   style="vertical-align:middle;"><?php echo(getValue($objRS,"nronf"));?></td>
				<td width="25%" align="center" style="vertical-align:middle;"><?php echo(dDate(CFG_LANG,getValue($objRS,"datafat"),false));?></td>
				
			</tr>            
		<?php }?>
		</tbody>
	</table>    
	<?php }?>
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