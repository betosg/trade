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



$id_evento		= getsession(CFG_SYSTEM_NAME."_id_evento");
$stridmercado 	= getsession("id_empresa");
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
		//expositores ///--DoCmd.OpenQuery "qry_Faturamento DARF Inserir PED 10833 BLOQ"
			$strSQL = "SELECT DISTINCT  ped_pedidos.idpedido
										,ped_pedidos_parcelamento.vencimentoped
										,ped_pedidos_parcelamento.datafat
										,cad_cadastro.cnpjcob AS cgcmfnf
										,cad_cadastro.telefone1
										,cad_cadastro.razaocob AS razaonf
										,'".$dateDtFat."' AS DATANF1
										,ped_nota_fiscal.datanf
										,ped_nota_fiscal.observacao
										,cad_evento.descrevento
										,ped_nota_fiscal.descricao4
										,CASE WHEN cad_cadastro.ret_10833 THEN ped_pedidos_parcelamento.valorpar * 0.0465 ELSE 0 END AS vlr
										,'10833' AS expr1
										,CAST(ped_pedidos_parcelamento.vencimentoped AS DATE) + CAST((7 - (EXTRACT(DOW FROM ped_pedidos_parcelamento.vencimentoped)+1)) AS INTEGER) AS apur
										,CAST(ped_pedidos_parcelamento.vencimentoped AS DATE) + CAST((11 - (EXTRACT(DOW FROM ped_pedidos_parcelamento.vencimentoped)+1)) AS INTEGER) AS venc
						FROM cad_evento 
						INNER JOIN (((ped_pedidos 
									  INNER JOIN ped_pedidos_parcelamento ON (ped_pedidos.idpedido LIKE ped_pedidos_parcelamento.idpedido) 
																	 AND (ped_pedidos.idmercado ILIKE ped_pedidos_parcelamento.idmercado)) 
									  INNER JOIN cad_cadastro ON (ped_pedidos.idmercado ILIKE cad_cadastro.idmercado) 
												AND (ped_pedidos.codigope LIKE cad_cadastro.codigo)) 
									  LEFT JOIN ped_nota_fiscal ON (ped_pedidos_parcelamento.nronf LIKE ped_nota_fiscal.idnotafiscal) 
													AND (ped_pedidos_parcelamento.idmercado ILIKE ped_nota_fiscal.idmercado)) 
						ON cad_evento.idevento LIKE ped_pedidos.idevento
						WHERE (ped_pedidos_parcelamento.vencimentoped BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFim."', 'DD/MM/YYYY')) ";
						if (($strPedIni != "") && ($strPedFim != "")) {
							$strSQL .= " AND (CAST(SUBSTRING(ped_pedidos.idpedido FROM 1 FOR 6) AS INTEGER) BETWEEN ".$strPedIni." AND ".$strPedFim.") ";
						}
						$strSQL .= "
						AND ped_pedidos_parcelamento.datafat IS NULL
						AND (CASE WHEN cad_cadastro.ret_10833 THEN ped_pedidos_parcelamento.valorpar * 0.0465 ELSE 0 END) > 0
						AND ped_pedidos_parcelamento.datapgto IS NULL
						AND ped_nota_fiscal.idnotafiscal  IS NULL 
						AND ped_pedidos.idmercado ILIKE '".$stridmercado."'
						ORDER BY cad_cadastro.razaocob;";


			//--DoCmd.OpenQuery "qry_Faturamento DARF Inserir PED 10833".			
			$strSQL2 = "SELECT DISTINCT  ped_pedidos.idpedido
										,ped_pedidos_parcelamento.vencimentoped
										,ped_pedidos_parcelamento.datafat
										,ped_nota_fiscal.idnotafiscal
										,ped_nota_fiscal.idnfe
										,cad_cadastro.cnpjcob AS cgcmfnf
										,cad_cadastro.telefone1
										,cad_cadastro.razaocob AS razaonf
										,'".$dateDtFat."' AS datanf1
										,ped_nota_fiscal.datanf
										,ped_nota_fiscal.observacao
										,cad_evento.descrevento
										,ped_nota_fiscal.descricao4
										,ped_nota_fiscal.valorcsll + ped_nota_fiscal.valorcofins + ped_nota_fiscal.valorpis AS vlr
										,'10833' AS expr1
										,ped_nota_fiscal.datanf as apur
										,sp_calc_css(CAST('".$dateDtFat."' AS DATE)) AS Venc
						FROM cad_evento 
						INNER JOIN (((ped_pedidos 
									 INNER JOIN ped_pedidos_parcelamento ON (ped_pedidos.idpedido LIKE ped_pedidos_parcelamento.idpedido) 
																			 AND (ped_pedidos.idmercado LIKE ped_pedidos_parcelamento.idmercado)) 
									 INNER JOIN cad_cadastro ON (ped_pedidos.idmercado LIKE cad_cadastro.idmercado) 
															 AND (ped_pedidos.codigope LIKE cad_cadastro.codigo)) 
									 LEFT JOIN ped_nota_fiscal ON (ped_pedidos_parcelamento.nronf LIKE ped_nota_fiscal.idnotafiscal) 
															   AND (ped_pedidos_parcelamento.idmercado LIKE ped_nota_fiscal.idmercado)) 
						ON cad_evento.idevento = ped_pedidos.idevento
						WHERE (ped_pedidos_parcelamento.vencimentoped BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFim."', 'DD/MM/YYYY')) 
						AND (ped_pedidos_parcelamento.datafat BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFat."', 'DD/MM/YYYY')) ";
						if (($strPedIni != "") && ($strPedFim != "")) {
							$strSQL2 .= " AND (CAST(SUBSTRING(ped_pedidos.idpedido FROM 1 FOR 6) AS INTEGER) BETWEEN ".$strPedIni." AND ".$strPedFim.") ";
						}
						$strSQL2 .= "
						AND (ped_nota_fiscal.valorcsll + ped_nota_fiscal.valorcofins + ped_nota_fiscal.valorpis) > 0 
						AND ped_pedidos_parcelamento.datapgto IS NULL
						AND ped_pedidos.idmercado ILIKE '".$stridmercado."'
						ORDER BY cad_cadastro.razaocob;";
			//--DoCmd.OpenQuery "qry_Faturamento DARF Inserir SER 10833"
			$strSQL3 = "SELECT DISTINCT  ped_servico.idservico as idpedido
										,tmp_ped_servico_parcelamento.vencimentoped
										,tmp_ped_servico_parcelamento.datafat
										,ped_nota_fiscal.idnotafiscal
										,ped_nota_fiscal.idnfe
										,cad_montador.cgcmf AS cgcmfnf
										,cad_montador.telefone1
										,cad_montador.nomemont AS razaonf
										,'".$dateDtFat."' AS datanf1
										,ped_nota_fiscal.datanf
										,ped_nota_fiscal.observacao
										,cad_evento.descrevento
										,ped_nota_fiscal.descricao4
										,ped_nota_fiscal.valorcsll + ped_nota_fiscal.valorcofins + ped_nota_fiscal.valorpis AS vlr
										,'10833' AS expr1
										,ped_nota_fiscal.datanf as apur
										,sp_calc_css(CAST(tmp_ped_servico_parcelamento.vencimentoped AS DATE)) AS venc
						FROM (cad_evento 
						INNER JOIN (ped_servico 
									INNER JOIN (tmp_ped_servico_parcelamento 
												LEFT JOIN ped_nota_fiscal ON (tmp_ped_servico_parcelamento.idmercado ILIKE ped_nota_fiscal.idmercado) 
																			AND (tmp_ped_servico_parcelamento.nronf ILIKE ped_nota_fiscal.idnotafiscal)) 
									ON (ped_servico.idservico LIKE tmp_ped_servico_parcelamento.idservico) 
									AND (ped_servico.idmercado ILIKE tmp_ped_servico_parcelamento.idmercado)) 
						ON cad_evento.idevento LIKE ped_servico.ideventose) 
						INNER JOIN cad_montador ON ped_servico.idmontse LIKE cad_montador.idmont
						WHERE (tmp_ped_servico_parcelamento.vencimentoped BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFim."', 'DD/MM/YYYY')) 
						AND (tmp_ped_servico_parcelamento.datafat BETWEEN TO_TIMESTAMP('".$dateDtIni."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$dateDtFat."', 'DD/MM/YYYY')) ";
						if (($strPedIni != "") && ($strPedFim != "")) {
							$strSQL3 .= " AND (CAST(SUBSTRING(ped_servico.idservico FROM 1 FOR 6) AS INTEGER) BETWEEN ".$strPedIni." AND ".$strPedFim.") ";
						}
						$strSQL3 .= "
						AND tmp_ped_servico_parcelamento.datapgto IS NULL 
						AND ped_servico.idmercado LIKE '".$stridmercado."'
						ORDER BY cad_montador.nomemont;";		
				
			$objResult 		= $objConn->query($strSQL);
			$objResult2 	= $objConn->query($strSQL2);
			$objResult3 	= $objConn->query($strSQL3);
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
	strLink = '../modulo_ASLWRelatorio/STguia_DARF_10833_faturamento.php?var_idpedido='+prPedido+'&var_dataemi=<?php echo $dateDtFat; ?>&var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>';	;	
	location.href = strLink;	
}

	
function imprimeTodas(){	
	strLink = '../modulo_ASLWRelatorio/STguia_DARF_10833_faturamento.php?var_dataemi=<?php echo $dateDtFat; ?>&var_dtinicio=<?php echo $dateDtIni; ?>&var_dtfim=<?php echo $dateDtFim; ?>';	
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
					athCssMenuAddItem("","_self",getTText("darf_10833",C_TOUPPER),1);
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
	if(($objResult->rowCount() == 0) and ($objResult2->rowCount() == 0) and ($objResult3->rowCount() == 0 )){
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
						 onclick="imprime(<?php echo getValue($objRS,"idpedido"); ?>);" /> </td>
          <td align="left"><?php echo(getValue($objRS,"idpedido"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS,"cgcmfnf")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRS,"irrf"),2,',','.'));?></td>
		  <td align="center"><?php echo(Ddate("PTB",getValue($objRS,"vencimento"),""));?></td>
		</tr>
		<?php } 

				 foreach($objResult2 as $objRS2){   
				 $int_quant++;	
		?>
        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
          <td align="center" style="vertical-align:middle;">
		  				<img src="../img/icon_impressao_darf.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" 
						 title="<?php echo(getTText("imprimir",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onclick="imprime(<?php echo getValue($objRS2,"idpedido"); ?>);" /> </td>
          <td align="left"><?php echo(getValue($objRS2,"idpedido"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS2,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS2,"cgcmfnf")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRS2,"irrf"),2,',','.'));?></td>
		  <td align="center"><?php echo(Ddate("PTB",getValue($objRS2,"vencimento"),""));?></td>
		</tr>
	<?php } 

		 foreach($objResult3 as $objRS3){   
				 $int_quant++;	
		?>
        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
          <td align="center" style="vertical-align:middle;">
		  				<img src="../img/icon_impressao_darf.gif" alt="<?php echo(getTText("imprimir",C_NONE));?>" 
						 title="<?php echo(getTText("imprimir",C_NONE));?>"
						 border="0" style="cursor:pointer;"
						 onclick="imprime(<?php echo getValue($objRS3,"idpedido"); ?>);" /> </td>
          <td align="left"><?php echo(getValue($objRS3,"idpedido"));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS3,"razaonf")));?></td>
          <td align="left"><?php echo(strtoupper(getValue($objRS3,"cgcmfnf")));?></td>
          <td align="right"><?php echo(number_format((double) getValue($objRS3,"irrf"),2,',','.'));?></td>
		  <td align="center"><?php echo(Ddate("PTB",getValue($objRS3,"vencimento"),""));?></td>
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