<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

/***           		   INCLUDES                   ***/
/****************************************************/
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");



$id_evento	    = getsession(CFG_SYSTEM_NAME."_id_evento");
$stridmercado	= getsession("id_empresa");
$dateDtFat 		= request("var_dataemi");
$dateDtIni 		= request("var_dtinicio");
$dateDtFim 		= request("var_dtfim");
$strPedIni      = request("var_ped_ini");
$strPedFim      = request("var_ped_fim");

$objConn = abreDBConn(CFG_DB);


$strPopulate = "yes";
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE"); //Verificação de acesso do usuário corrente
	
	
		// SQL PADRÃO DA LISTAGEM - DAS CREDENCIAIS
	try{
		//expositores
			$strSQL = " SELECT DISTINCT  ped_pedidos.idpedido
										,cad_cadastro.cnpjcob AS cgcmfnf
										,cad_cadastro.razaocob AS razaonf
										,CASE WHEN ped_nota_fiscal.valornf IS NULL THEN
												CASE WHEN (ped_pedidos_parcelamento.valorpar * 0.015) < 10 THEN 0
													 ELSE ped_pedidos_parcelamento.valorpar * 0.015 END
											ELSE ped_nota_fiscal.valorir
										 END AS irrf
										,'IRRF' AS expr1
										,(CAST('01/' || DATE_PART('MONTH', ped_pedidos_parcelamento.vencimentoped) || '/' || DATE_PART('YEAR', ped_pedidos_parcelamento.vencimentoped) AS TIMESTAMP)+ INTERVAL '1 MONTH') - INTERVAL '1 DAY' AS apur
										,CAST(('10/'|| CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 1
										   ELSE  DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped)+1 END ||'/'||                     
										   CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 
										   DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped)+1
										   ELSE DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped) END) AS DATE) - 
										   CAST((CASE WHEN (EXTRACT(DOW FROM CAST (('10/'|| CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 1
											ELSE  DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped)+1 END 
											||'/'|| CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 
											DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped)+1
											ELSE DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped) END) AS TIMESTAMP))+1)=7 THEN 1
											ELSE CASE WHEN (EXTRACT(DOW FROM CAST (('10/'|| CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 1
											ELSE  DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped)+1 END
											||'/'|| CASE WHEN DATE_PART('MONTH',ped_pedidos_parcelamento.vencimentoped) = 12 THEN 
											DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped)+1
											ELSE DATE_PART('YEAR',ped_pedidos_parcelamento.vencimentoped) END) AS TIMESTAMP))+1)
											= 1 THEN 2 ELSE 0 END END) AS INTEGER) AS vencimento
						FROM cad_evento
						INNER JOIN (((ped_pedidos
										INNER JOIN ped_pedidos_parcelamento ON(ped_pedidos.idpedido LIKE ped_pedidos_parcelamento.idpedido) 
																		AND(ped_pedidos.idmercado LIKE ped_pedidos_parcelamento.idmercado))
										INNER JOIN cad_cadastro ON(ped_pedidos.idmercado LIKE cad_cadastro.idmercado) 
																AND(ped_pedidos.codigope LIKE cad_cadastro.codigo))
										LEFT JOIN ped_nota_fiscal ON(ped_pedidos_parcelamento.nronf LIKE ped_nota_fiscal.idnotafiscal)
																	AND(ped_pedidos_parcelamento.idmercado LIKE ped_nota_fiscal.idmercado))
						ON (cad_evento.idevento LIKE ped_pedidos.idevento) 
						WHERE (ped_pedidos_parcelamento.vencimentoped BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFim."', 'DD/MM/YYYY')) 
						AND (ped_pedidos_parcelamento.datafat BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFat."', 'DD/MM/YYYY')) ";
						if (($strPedIni != "") && ($strPedFim != "")) {
							$strSQL .= " AND (CAST(SUBSTRING(ped_pedidos.idpedido FROM 1 FOR 6) AS INTEGER) BETWEEN ".$strPedIni." AND ".$strPedFim.") ";
						}
						$strSQL .= "
						AND (CASE WHEN ped_nota_fiscal.valornf IS NULL THEN
												CASE WHEN (ped_pedidos_parcelamento.valorpar * 0.015) < 10 THEN 0
													 ELSE ped_pedidos_parcelamento.valorpar * 0.015 END
											ELSE ped_nota_fiscal.valorir
										END) > 10
						AND ped_pedidos_parcelamento.datapgto IS NULL
						AND ped_pedidos.idmercado LIKE '".$stridmercado."'
						ORDER BY cad_cadastro.razaocob";


			//prestadores
			$strSQLservico = " SELECT DISTINCT  	 ped_servico.idservico
												,cad_montador.cgcmf AS cgcmfnf
												,cad_montador.telefone1
												,cad_montador.nomemont AS razaonf
												,CASE WHEN ped_nota_fiscal.valornf IS NULL THEN
														CASE WHEN (tmp_ped_servico_parcelamento.valorped * 0.015) < 10 THEN 0
															 ELSE tmp_ped_servico_parcelamento.valorped * 0.015 END
													ELSE ped_nota_fiscal.valorir
												END AS irrf
												,CAST(tmp_ped_servico_parcelamento.vencimentoped AS DATE) + CAST((7 - (EXTRACT(DOW FROM tmp_ped_servico_parcelamento.vencimentoped)+1)) AS INTEGER) AS apur
												,CAST(tmp_ped_servico_parcelamento.vencimentoped AS DATE) + CAST((11 - (EXTRACT(DOW FROM tmp_ped_servico_parcelamento.vencimentoped)+1)) AS INTEGER) AS vencimento
								FROM (cad_evento 
								INNER JOIN (ped_servico 
											INNER JOIN (tmp_ped_servico_parcelamento
														LEFT JOIN ped_nota_fiscal ON (tmp_ped_servico_parcelamento.idmercado ILIKE ped_nota_fiscal.idmercado) 
																					AND (tmp_ped_servico_parcelamento.nronf LIKE ped_nota_fiscal.idnotafiscal)) 
											ON (ped_servico.idservico LIKE tmp_ped_servico_parcelamento.idservico) 
											AND (ped_servico.idmercado ILIKE tmp_ped_servico_parcelamento.idmercado)) 
								ON cad_evento.idevento LIKE ped_servico.ideventose) 
								INNER JOIN cad_montador ON ped_servico.idmontse LIKE cad_montador.idmont
								WHERE (tmp_ped_servico_parcelamento.vencimentoped BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFim."', 'DD/MM/YYYY')) 
								AND (tmp_ped_servico_parcelamento.datafat BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFat."', 'DD/MM/YYYY')) ";
								if (($strPedIni != "") && ($strPedFim != "")) {
									$strSQLservico .= " AND (CAST(SUBSTRING(ped_servico.idservico FROM 1 FOR 6) AS INTEGER) BETWEEN ".$strPedIni." AND ".$strPedFim.") ";
								}
								$strSQLservico .= "
								AND (CASE WHEN ped_nota_fiscal.valornf IS NULL THEN
										  CASE WHEN (tmp_ped_servico_parcelamento.valorped * 0.015) < 10 THEN 0
											   ELSE tmp_ped_servico_parcelamento.valorped * 0.015 END
										ELSE ped_nota_fiscal.valorir
									END) > 10
								AND tmp_ped_servico_parcelamento.datapgto IS NULL
								AND ped_servico.idmercado ILIKE '".$stridmercado."'
								ORDER BY cad_montador.nomemont";
			
			$objResult 		= $objConn->query($strSQL);
			$objResultsev 	= $objConn->query($strSQLservico);
		$objResult = $objConn->query($strSQL);		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	
	// inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? "#F5FAFA" : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="_css/default.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style type="text/css">
	/* suas adaptações css aqui */
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin:10px;background-color:#FFFFFF; } ul{ margin-top:0px;margin-bottom:0px; } li{ margin-left:0px; }
</style>

<script type="text/javascript">
	/* seu código javascript aqui */
	
function linkPage(prLink){
	var strLink = (prLink == "") ? "#" : prLink;
	location.href = strLink;
}

function imprime(prPedido){	
	strLink = '../modulo_ASLWRelatorio/STguia_DARF_faturamento.php?var_idpedido='+prPedido+'&var_dataemi=<?php echo $dateDtFat; ?>&var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>';;	
	location.href = strLink;	
}

	
function imprimeTodas(){	
	strLink = '../modulo_ASLWRelatorio/STguia_DARF_faturamento.php?var_dataemi=<?php echo $dateDtFat; ?>&var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>';	
	location.href = strLink;	
}	
</script>
		
	</head>
<body bgcolor="#FFFFFF">
	
	<!-- MENU PURE CSS SUPERIOR . COMENTÁRIOS DE UTILIZAÇÃO NO INTERIOR DO CONJUNTO DE FUNÇÕES MENU CSS -->
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td width="19%" align="left">			
			<?php
				athBeginCssMenu();
					athCssMenuAddItem("","_self",getTText("darf_irrf",C_TOUPPER),1);
					athBeginCssSubMenu();	
						athCssMenuAddItem("javascript:imprimeTodas()",
										  "_self",getTText("imprimir_todas",C_UCWORDS));						
					athEndCssSubMenu();
				athEndCssMenu();		
			?>			</td>
			<td width="81%">
		</td>
		</tr>
	</table>
	<!-- MENU PURE CSS SUPERIOR . FIM -->
	
	<?php
	if(($objResult->rowCount() == 0) and ($objResultsev->rowCount() == 0) ){
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("no_contato",C_NONE),"","aviso",1,"","","");
	} else {
	?>
	
	<!-- TABLESORT DA MINI APP ink
	. INICIO -->
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
      <thead>
        <tr>
          <th width="1%"></th>
          <th width="11%" class="sortable" nowrap><?php echo(getTText("idpedido",C_TOUPPER));?></th>
          <th width="48%" class="sortable" nowrap><?php echo(getTText("razaonf",C_TOUPPER));?></th>
          <th width="20%" class="sortable" nowrap><?php echo(getTText("cgcmfnf",C_TOUPPER));?></th>
          <th width="10%" class="sortable" nowrap><?php echo(getTText("vlr",C_TOUPPER));?></th>
		  <th width="10%" class="sortable" nowrap><?php echo(getTText("venc",C_TOUPPER));?></th>
        </tr>
      </thead>
      <tbody>
        <?php 	 
				 $int_quant = 0;
				 foreach($objResult as $objRS){   
					 $int_quant++;	
		?>
        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
          <td align="center" style="vertical-align:middle;">
		  				<img src="../img/icon_impressao_darf.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" 
						 title="<?php echo(getTText("imprimir",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onclick="imprime('<?php echo getValue($objRS,"idpedido"); ?>');" /> </td>
          <td align="left"><?php echo(getValue($objRS,"idpedido"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"cgcmfnf")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRS,"irrf"),2,',','.'));?></td>
		  <td align="center"><?php echo(Ddate("PTB",getValue($objRS,"vencimento"),""));?></td>
		</tr>
		<?php } 
		
				 foreach($objResultsev as $objRSsev){   
					 $int_quant++;	
		?>
        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
          <td align="center" style="vertical-align:middle;">
		  				<img src="../img/icon_impressao_darf.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" 
						 title="<?php echo(getTText("imprimir",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onclick="imprime(<?php echo getValue($objRSsev,"idpedido"); ?>);" /> </td>
          <td align="left"><?php echo(getValue($objRSsev,"idpedido"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRSsev,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRSsev,"cgcmfnf")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRSsev,"irrf"),2,',','.'));?></td>
		  <td align="center"><?php echo(Ddate("PTB",getValue($objRSsev,"vencimento"),""));?></td>
		</tr>
			 <?php } ?>
		       
      </tbody>
      <!-- TFOOT TABLESORT . INICIO #CASO SEJA NECESSÁRIO UTILIZAR ALGUM TIPO DE AÇÃO NO FOOTER, DESCOMENTAR -->
      <tfoot>
        <tr bgcolor="#DDDDDD">
          <td colspan="8" align="right">Total: <?php echo $int_quant;?></td>
        </tr>
      </tfoot>
      <!-- TFOOT TABLESORT . FIM -->
    </table>
	<!-- TABLESORT DA MINI APP . FIM -->
    <?php } ?>
</body>
	
</html>
<?php
	// SETA O OBJETO DE CONEXÃO COM BANCO PARA NULO
	// ALÉM DISSO, FECHA O CURSOR DO RESULTSET
	$objConn = NULL;
	$objResult->closeCursor();
?>