<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// HEADERS ANTI-CACHE
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");

	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intVlrCampoChaveDetail  = request("var_chavereg");
	$strNomeCampoChaveDetail = request("var_field_detail");

	// ABRE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);

	// SQL
	try{
		$strSQL = "	
			SELECT 
				  lct.cod_lcto_ordinario
				, lct.cod_conta_pagar_receber
				, lct.vlr_lcto
				, lct.vlr_multa
				, lct.vlr_juros
				, lct.vlr_desc
				, lct.dt_lcto
				, lct.historico
				, lct.obs
				, lct.sys_dtt_ins
				, lct.sys_usr_ins
				, lct.tipo_documento
				, lct.extra_documento
				, pcont.cod_reduzido
				, pcont.nome AS plano_conta
			FROM  fin_lcto_ordinario lct
			JOIN fin_conta_pagar_receber cont ON lct.cod_conta_pagar_receber = cont.cod_conta_pagar_receber AND cont.cod_conta_pagar_receber = ".$intVlrCampoChaveDetail."
			LEFT JOIN fin_plano_conta pcont ON lct.cod_plano_conta = pcont.cod_plano_conta
			ORDER BY cod_conta_pagar_receber DESC, dt_lcto DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
	?>
		<script type="text/javascript">
  		// Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
		resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
		// ----------------------------------------------------------------------------------------------------------
		</script>
	<?php
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	if($objResult->rowCount() == 0){
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc","", "","aviso",1,"");
		echo('<script type="text/javascript">
		// Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
		resizeIframeParent("'.CFG_SYSTEM_NAME.'_detailiframe_'.request("var_chavereg").'",20);
		// ----------------------------------------------------------------------------------------------------------
		</script>');
		die();
	}
	?>
<html>
<head>
	 <title></title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	 <link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
	 <script type="text/javascript" src="../_scripts/tablesort.js"></script>
	 <style>
		ul{ margin-top: 0px; margin-bottom: 0px; }
		li{ margin-left: 0px; }
	 </style>
</head>
<body style="margin:10px;" bgcolor="#FFFFFF">
<table align="center" cellpadding="0" cellspacing="1" style="width:100%" class="tablesort">
	<thead>
		<tr>
			<th width="15%" nowrap>Plano de Conta</th>
			<th width="10%" nowrap>Data</th>
			<th width="06%" nowrap>Lcto</th>
			<th width="06%" nowrap>Multa</th>
			<th width="06%" nowrap>Juros</th>
			<th width="06%" nowrap>Desc</th>
			<th width="16%" nowrap>Ocorrência</th>
			<th width="10%" nowrap>Usuario</th>
			<th width="10%" nowrap>Tipo</th>
			<th width="08%" nowrap>Histórico</th>
			<th width="07%" nowrap>Info Extra</th>
			<th width="01%"></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$Ct				=1;
		$dblValotTotal 	= 0;
		$strCOLOR 		= "";
		$strTituloAnt 	= "";
		$boolShowResult = true;

		foreach($objResult as $objRS){
		$strCOLOR = (($Ct++%2)==0)?"#FFFFFF":"#F5FAFA";
	?>
		<tr bgcolor=<?php echo($strCOLOR) ?>>	
				<td style="text-align:center;font-weight:normal;"><?php echo(getValue($objRS,"cod_reduzido") . " " . getValue($objRS,"plano_conta"));?></td>
				<td style="text-align:center;"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_lcto"),false)); ?></td>
				<td style="text-align:right;" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_lcto"),2,",","")); ?></td>
				<td style="text-align:right;" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_multa"),2,",","")); ?></td>
				<td style="text-align:right;" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_juros"),2,",","")); ?></td>
				<td style="text-align:right;" align="right"><?php echo(number_format((double) getValue($objRS,"vlr_desc"),2,",","")); ?></td>
				<td style="text-align:right;" align="right"><?php echo(dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true)); ?></td>
				<td style="text-align:center;" align="left"><?php echo(getValue($objRS,"sys_usr_ins")); ?></td>
				<td style="text-align:center;" align="left"><?php echo(getValue($objRS,"tipo_documento")); ?></td>
				<td style="text-align:center;" align="left"><?php echo(getValue($objRS,"historico")); ?></td>
				<td style="text-align:center;" align="left"><?php echo(getValue($objRS,"extra_documento")); ?></td>
				<td style="text-align:center;" align="center">
				<?php if(getValue($objRS,"obs") != ""){?>
					<img src="../img/icon_obs.gif" alt="<?php echo(getValue($objRS,"obs")); ?>" border="0" style="cursor: pointer;">
				<?php }?>
				</td>
		</tr>
	<?php }?>
	</tbody>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php
$objResult->closeCursor();
$objConn = NULL;
?>