<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

ob_start();

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB);

define("ICONES_NUM"        ,1);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE


/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = request("var_curpage");        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não

/***              FILTROS - OPCIONAL              ***/
/****************************************************/
$dateDataIni = requestQueryString("var_data_ini");
$dateDataFim = requestQueryString("var_data_fim");
$intCodConta = requestQueryString("var_cod_conta");
$intCodPlanoConta = requestQueryString("var_cod_plano_conta");

function showDblNumber($prNumber) {
	$prNumber = round($prNumber,2);
	$prNumber = abs($prNumber);
	$prNumber = number_format((double) $prNumber,2,",",".");
	
	return($prNumber);
}

/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
/****************************************************/
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

/***        FUNÇÕES AUXILIARES - OPCIONAL         ***/
/****************************************************/


/***        AÇÃO DE EXPORTAÇÃO DA GRADE          ***/
/***************************************************/
//Define uma variável booleana afim de verificar se é um tipo de exportação ou não
$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");

//Exportação para excel, word e adobe reader
if($boolIsExportation) {
	if($strAcao == ".pdf") {
		redirect("exportpdf.php"); //Redireciona para página que faz a exportação para adode reader
	}
	else{
		//Coloca o cabeçalho de download do arquivo no formato especificado de exportação
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_NONE) . "_". time() . $strAcao);
	}
	
	$strLimitOffSet = "";
} 

/***        AÇÃO DE ALIMENTAÇÃO DA GRADE         ***/
/***************************************************/

$dateDataIni = cDate(CFG_LANG,$dateDataIni,false);
$dateDataFim = cDate(CFG_LANG,$dateDataFim,false);

if(is_date($dateDataIni) && is_date($dateDataFim) && ($intCodConta != "")) {
	try {
		$strSQL =  " SELECT pgr.cod_conta_pagar_receber AS codigo ";
		$strSQL .= "	, cta1.nome as conta_prevista ";
		$strSQL .= "	, CASE WHEN pgr.pagar_receber OR pgr.pagar_receber IS NULL THEN 'saida' ";
		$strSQL .= "           WHEN NOT pgr.pagar_receber THEN 'entrada' ";
		$strSQL .= "     END AS operacao ";
		$strSQL .= "	, 'titulos' AS modulo ";
		$strSQL .= "	, pgr.tipo ";
		$strSQL .= "	, pgr.codigo AS cod_entidade ";
		$strSQL .= "	, pgr.historico ";
		$strSQL .= "	, ord.vlr_lcto AS valor ";
		$strSQL .= "	, ord.dt_lcto AS data ";
		$strSQL .= "	, cta2.nome AS conta_realizada ";
		$strSQL .= "	, ord.vlr_desc AS valor_desc ";
		$strSQL .= "	, ord.vlr_juros AS valor_juros ";
		$strSQL .= " FROM fin_conta_pagar_receber AS pgr ";
		$strSQL .= "	, fin_lcto_ordinario AS ord ";
		$strSQL .= "	, fin_conta AS cta1 ";
		$strSQL .= "	, fin_conta AS cta2 ";
		$strSQL .= " WHERE ord.sys_dtt_cancel IS NULL ";
		$strSQL .= "   AND ord.cod_conta_pagar_receber = pgr.cod_conta_pagar_receber ";
		$strSQL .= "   AND pgr.cod_conta = cta1.cod_conta ";
		$strSQL .= "   AND ord.cod_conta = cta2.cod_conta ";
		$strSQL .= "   AND pgr.situacao <> 'cancelado' AND pgr.situacao <> 'agrupado' ";
		$strSQL .= " AND ord.dt_lcto BETWEEN '" . $dateDataIni . "' AND '" . $dateDataFim . "'";
		$strSQL .= " AND cta2.cod_conta = " . $intCodConta;
		if ($intCodPlanoConta != "") $strSQL .= " AND ord.cod_plano_conta = " . $intCodPlanoConta;
		
		$strSQL .= " UNION ";
		
		$strSQL .= " SELECT lct.cod_lcto_em_conta AS codigo ";
		$strSQL .= "	   ,'' AS conta_prevista ";
		$strSQL .= "	   , CASE WHEN lct.operacao = 'despesa' OR lct.operacao = '' THEN 'saida' ";
		$strSQL .= "              WHEN lct.operacao = 'receita' THEN 'entrada' ";
		$strSQL .= "         END AS operacao ";
		$strSQL .= "	   ,'lctoconta' AS modulo ";
		$strSQL .= "	   ,lct.tipo ";
		$strSQL .= "       ,lct.codigo AS cod_entidade";
		$strSQL .= "	   ,lct.historico ";
		$strSQL .= "	   ,lct.vlr_lcto AS valor ";
		$strSQL .= "	   ,lct.dt_lcto AS data ";
		$strSQL .= "	   ,cta2.nome AS conta_realizada ";
		$strSQL .= "       ,0 AS valor_desc ";
		$strSQL .= "       ,0 AS valor_juros ";
		$strSQL .= " FROM fin_lcto_em_conta AS lct ";
		$strSQL .= "	 ,fin_conta AS cta2 ";
		$strSQL .= " WHERE lct.cod_conta = cta2.cod_conta ";
		$strSQL .= " AND lct.dt_lcto BETWEEN '" . $dateDataIni . "' AND '" . $dateDataFim . "'";
		$strSQL .= " AND cta2.cod_conta = " . $intCodConta;
		if ($intCodPlanoConta != "") $strSQL .= " AND lct.cod_plano_conta = " . $intCodPlanoConta;
		
		$strSQL .= " UNION ";
		
		$strSQL .= " SELECT ";
		$strSQL .= "     ltf.cod_lcto_transf AS codigo ";
		$strSQL .= "    ,'' AS conta_prevista ";
		$strSQL .= "    ,'saida' AS operacao ";
		$strSQL .= "    ,'transf' AS modulo ";
		$strSQL .= "    ,'' AS tipo ";
		$strSQL .= "    ,NULL AS cod_entidade ";
		$strSQL .= "	,ltf.historico ";
		$strSQL .= "	,ltf.vlr_lcto AS valor ";
		$strSQL .= "	,ltf.dt_lcto AS data ";
		$strSQL .= "	,cta.nome AS conta_realizada ";
		$strSQL .= "	,0 AS valor_desc ";
		$strSQL .= "	,0 AS valor_juros ";
		$strSQL .= " FROM  fin_lcto_transf AS ltf ";
		$strSQL .= "	 , fin_conta AS cta ";
		$strSQL .= " WHERE ltf.cod_conta_orig = cta.cod_conta ";
		$strSQL .= " AND ltf.dt_lcto BETWEEN '" . $dateDataIni . "' AND '" . $dateDataFim . "'";
		$strSQL .= " AND ltf.cod_conta_orig = " . $intCodConta;
		
		$strSQL .= " UNION ";
		
		$strSQL .= " SELECT";
		$strSQL .= "	 ltf.cod_lcto_transf AS codigo ";
		$strSQL .= "	,'' AS conta_prevista ";
		$strSQL .= "	,'entrada' AS operacao ";
		$strSQL .= "    ,'transf' AS modulo ";		
		$strSQL .= "	,'' AS tipo ";
		$strSQL .= "    ,NULL AS cod_entidade ";
		$strSQL .= "	,ltf.historico ";
		$strSQL .= "	,ltf.vlr_lcto AS valor ";
		$strSQL .= "	,ltf.dt_lcto AS data ";
		$strSQL .= "	,cta.nome AS conta_realizada ";
		$strSQL .= "	,0 AS valor_desc ";
		$strSQL .= "	,0 AS valor_juros ";
		$strSQL .= " FROM fin_lcto_transf AS ltf ";
		$strSQL .= "	 , fin_conta AS cta ";
		$strSQL .= " WHERE ltf.cod_conta_dest = cta.cod_conta ";
		$strSQL .= " AND ltf.dt_lcto BETWEEN '" . $dateDataIni . "' AND '" . $dateDataFim . "'";
		$strSQL .= " AND ltf.cod_conta_dest = " . $intCodConta;
		
		$strSQL .= " ORDER BY 9 ";
		
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e) { 
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . "<br><br>" . $strSQL,"","erro",1);
		die();
	}
	
	?>
	<html>
	<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<?php 
			if(!$boolIsExportation || $strAcao == "print") {
				echo("<link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
					  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
					");
			}
		?>
		<script language="javascript" type="text/javascript">
			var intCurrentPos = 1;
			var intCurrentPosMouse;
			var strDefaultAction = "<?php echo(GRADE_ACAO_DEFAULT); ?>"; 
			var intTotalPaginas = null;
			
			function aplicarFuncao(prValue) {
				if(prValue != "") {
					location.href = prValue;
				}
			}
			
			function setOrderBy(prStrOrder,prStrDirect) {
				location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_order_column=" + prStrOrder + ".var_order_direct=" + prStrDirect;
			}
			
			function switchColor(prObj, prColor){
				prObj.style.backgroundColor = prColor;
			}
			
			var somaCurrentPosDetailUp = 1;
			var somaCurrentPosDetailDown = 1;
			var voltaSetaDown = 1;
			function navigateRow(e) {
				if(!e) { e = window.event; }
				
				objTable = document.getElementById("tableContent");
				
				if(e.keyCode == 40){
					switchColor(objTable.rows[intCurrentPos], "");
					if(intCurrentPos < objTable.rows.length-2) {
						intCurrentPos += somaCurrentPosDetailUp;
						switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = objTable.rows.length-1;
					}
				}
				else if(e.keyCode == 38){
					switchColor(objTable.rows[intCurrentPos], "");
					if(intCurrentPos > 2){
						intCurrentPos -= somaCurrentPosDetailDown;
						switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = voltaSetaDown;
					}
				} 
				else if ((e.keyCode == 0 || e.keyCode == null) && e.type == "mouseover") {
					switchColor(objTable.rows[intCurrentPos], "");
					switchColor(objTable.rows[intCurrentPosMouse], "#CCCCCC");
					intCurrentPos = intCurrentPosMouse;
				}
				else if (e.keyCode == 13) {
					if(strDefaultAction != "" && objTable.rows[intCurrentPos].cells[1] != null){
						location.href = strDefaultAction.replace("{0}",objTable.rows[intCurrentPos].cells[1].innerHTML);
					}
				}
				
				if (e.keyCode != 8 && e.keyCode != 13 && (!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106))){
					return false;
				}
			}
			
			var allTrTags = new Array();
			var detailTrFrameAnt = '';
			var moduloDetailAnt = '';
			function showDetailGrid(prChave_reg,prLink, prField) {
				
				if(prLink.indexOf("?") == -1) {
					strConcactQueryString = "?"
				} else {
					strConcactQueryString = "&"
				}
				var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
				if(detailTr == 'none') {
					SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"kernelps_detailiframe_"+prChave_reg);
					
					var allTrTags  = document.getElementsByTagName("tr");
					for(i=0;i < allTrTags.length;i++){
						if(allTrTags[i].className == 'iframe_detail') {
							allTrTags[i].style.display = 'none';
						}
					}
					document.getElementById("detailtr_"+prChave_reg).style.display = '';
				} else {
					if( moduloDetailAnt == prLink) {
							document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
					}else{
						if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
							 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"kernelps_detailiframe_"+prChave_reg);
						}
					}
				}
				moduloDetailAnt = prLink;
			}
			
			function SetIFrameSource(prPage,prId) {
				document.getElementById(prId).src = prPage;
			}
			
			document.onkeydown = navigateRow;
		</script>
		<?php
			include_once("../_scripts/scripts.js");
			include_once("../_scripts/STscripts.js");
		?>
		<style type="text/css" media="all"> 
			li, ul { margin:0px; }	
			
			.field_numeric { text-align:right; padding:0px 5px; height:22px; }
			.field_text    { text-align:left; padding:0px 5px; height:22px; }
			
			.text_green { color:green; }
			.text_red { color:red; }
		</style>
	</head>
	<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<center>
	<?php athBeginWhiteBox("98%"); ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="padrao_gde" colspan="2" align="left" width="50%" valign="top"><b><?php echo(getTText(getsession($strSesPfx . "_titulo"),C_NONE)); ?></b></td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<?php if($objResult->rowCount() > 0) { ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr height="22">
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<td class="titulo_grade"><?php echo(getTText("data",C_UCWORDS)); ?></td>
									<td class="titulo_grade"><?php echo(getTText("entidade",C_UCWORDS)); ?></td>
									<td class="titulo_grade"><?php echo(getTText("historico",C_UCWORDS)); ?></td>
									<td class="titulo_grade" nowrap="nowrap"><?php echo(getTText("valor_entrada",C_UCWORDS)); ?></td>
									<td class="titulo_grade" nowrap="nowrap"><?php echo(getTText("valor_saida",C_UCWORDS)); ?></td>
									<td class="titulo_grade"><?php echo(getTText("valor_desc",C_UCWORDS)); ?></td>
									<td class="titulo_grade"><?php echo(getTText("valor_juros",C_UCWORDS)); ?></td>
									<td class="titulo_grade" nowrap="nowrap"><?php echo(getTText("valor_total",C_UCWORDS)); ?></td>
									<td >&nbsp;</td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="9" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php
									$dblSaldo = 0;
									$dblTotalEntrada = 0;
									$dblTotalSaida = 0;
									$dblTotalDesc = 0;
									$dblTotalJuros = 0;
									
									// Para soma do total geral, calculado
									// em função do 'lcto_final' de cada
									// linha, lcto + juros - desc
									$dblTotalGeral = 0;
									
									$strMes = "";
									$strMesAnt = "";
									
									foreach($objResult as $objRS) {
										
										ob_flush();
										
										$strMes = date("m",strtotime(getValue($objRS,"data")));
										if ($strMesAnt == "") $strMesAnt = $strMes;
										
										if($strMesAnt != $strMes){
											?>
											<tr height="22" bgcolor="#EEEEEE">
												<td colspan="4" align="right" width="95%" class="padrao_gde"><strong>Valor Total:</strong></td>
												<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalEntrada);?></strong></td>
												<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalSaida);?></strong></td>
												<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalDesc);?></strong></td>
												<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalJuros);?></strong></td>
												<?php
												if($dblSaldo >= 0)
													echo("<td width=\"1%\" class=\"field_numeric text_green\"><strong>" . showDblNumber($dblSaldo) . "</strong></td>");
												else
													echo("<td width=\"1%\" class=\"field_numeric text_red\"><strong>" . showDblNumber($dblSaldo) . "</strong></td>");
												?>
												<td>&nbsp;</td>
											</tr>
											<?php
											$strMesAnt = $strMes;
											
											//$dblSaldo = 0;
											$dblTotalEntrada = 0;
											$dblTotalSaida = 0;
											$dblTotalDesc = 0;
											$dblTotalJuros = 0;
										}
										
										$strValor = (getValue($objRS,"valor") != "") ? number_format((double) getValue($objRS,"valor"),2,",",".") : "0,00";
										
										$strVlrIn = 0;
										$strVlrOut = 0;
										$dblVlrDesc = doubleval(getValue($objRS,"valor_desc"));
										$dblVlrJuros = doubleval(getValue($objRS,"valor_juros"));
										$dblTotalParcial = 0;
										
										if(getValue($objRS,"operacao") != "entrada") {
											$strVlrOut = doubleval(getValue($objRS,"valor"));
											$dblTotalSaida += $strVlrOut;
											$dblTotalParcial += $strVlrOut + $dblVlrJuros - $dblVlrDesc;
											$dblTotalGeral += $dblTotalParcial;
										} else {
											$strVlrIn = doubleval(getValue($objRS,"valor"));
											$dblTotalEntrada += $strVlrIn;
											$dblTotalParcial += $strVlrIn + $dblVlrJuros - $dblVlrDesc;
											$dblTotalGeral -= $dblTotalParcial;
										}
										$dblTotalDesc += $dblVlrDesc;
										$dblTotalJuros += $dblVlrJuros;
										$dblSaldo = $dblSaldo + $strVlrIn - $strVlrOut + $dblVlrJuros - $dblVlrDesc;
										
										if(getValue($objRS,"cod_entidade") != "") {
											try {
												$strSQL =  " SELECT ";
												$strSQL .= ((getValue($objRS,"tipo") == "cad_pf") ? " nome " : " nome_fantasia ") . " AS nome ";
												$strSQL .= " FROM " . getValue($objRS,"tipo");
												// $strSQL .= " WHERE " . ((getValue($objRS,"tipo") == "cad_pf") ? " cod_pf " : " cod_pj ") . " = " . getValue($objRS,"cod_entidade");
												switch(strtolower(getValue($objRS,"tipo"))){
													case "cad_pf" :
													$strSQL .= " WHERE cod_pf = " . getValue($objRS,"cod_entidade");
													break;
													
													case "cad_pj" :
													$strSQL .= " WHERE cod_pj = " . getValue($objRS,"cod_entidade");
													break;
													
													case "cad_pj_fornec" :
													$strSQL .= " WHERE cod_pj_fornec = " . getValue($objRS,"cod_entidade");
													break;
												}
												$objResultAux = $objConn->query($strSQL);
												
												$objRSAux = $objResultAux->fetch();
												$strEntidade = getValue($objRS,"cod_entidade") . " - " . getValue($objRSAux,"nome");
													
												$objResultAux->closeCursor();
											} catch(PDOException $e) {
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . " - " . $strSQL,"","erro",0);
												die();
											}
										
										} else {
											$strEntidade = "";
										}
										
										$strBgColor = (!isset($strBgColor) || $strBgColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
										
										if(strlen(getValue($objRS,"historico")) > 70) {
											$strHistoricoTitle = strip_tags(str_replace("<li>","&nbsp;",getValue($objRS,"historico")));
											$strHistorico = strip_tags(substr($strHistoricoTitle,0,70)) . "&nbsp;...";
										} else {
											$strHistorico = getValue($objRS,"historico");
											$strHistoricoTitle = "";
										}
								?>
								<tr bgcolor="<?php echo($strBgColor); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<?php if(!$boolIsExportation) { ?>
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM); ?>">
											<tr>
												<td width="<?php echo ICONES_WIDTH;?>">
												<?php
												if (getValue($objRS,"modulo") == "titulos")   echo("<a href='STshowtitulos.php?var_chavereg=" . getValue($objRS,"codigo") . "'><img src='../img/icon_zoom.gif' border='0'></a>");
												if (getValue($objRS,"modulo") == "lctoconta") echo("<a href='STshowlctoconta.php?var_chavereg=" . getValue($objRS,"codigo") . "'><img src='../img/icon_zoom.gif' border='0'></a>");
												if (getValue($objRS,"modulo") == "transf")    echo("<a href='STshowlctotransf.php?var_chavereg=" . getValue($objRS,"codigo") . "'><img src='../img/icon_zoom.gif' border='0'></a>");
												?>
												</td>
											</tr>
										</table>
										<?php } ?>
									</td>
									<td class="field_text"><?php echo dDate(CFG_LANG,getValue($objRS,"data"),false);?></td>
									<td class="field_text"><?php echo $strEntidade;?></td>
									<td class="field_text"><?php echo $strHistorico;?></td>
									<td class="field_numeric"><?php echo ($strVlrIn != "") ? number_format((double) $strVlrIn,2,",",".") : ""?></td>
									<td class="field_numeric"><?php echo ($strVlrOut != "") ? number_format((double) $strVlrOut,2,",",".") : ""?></td>
									<td class="field_numeric"><?php echo ($dblVlrDesc != 0) ? number_format((double) $dblVlrDesc,2,",",".") : ""?></td>
									<td class="field_numeric"><?php echo ($dblVlrJuros != 0) ? number_format((double) $dblVlrJuros,2,",",".") : ""?></td>
									<td class="field_numeric"><?php echo number_format((double) $dblTotalParcial,2,",",".");?></td>
									<td class="field_text">
										<?php if(!$boolIsExportation) { ?>
										<img src="../img/icon_livro_caixa_<?php echo getValue($objRS,"operacao");?>.gif">
										<?php } ?>
									</td>
								</tr>
								<?php
									}
									if(($strMes != "") ){
										?>
										<tr height="22" bgcolor="#EEEEEE">
											<td colspan="4" align="right" width="95%" class="padrao_gde"><strong>Valor Total:</strong></td>
											<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalEntrada);?></strong></td>
											<td width="1%" class="field_numeric"><strong><?php echo showDblNumber($dblTotalSaida);?></strong></td>
											<td width="1%" class="field_numeric"><!--strong><?php //echo showDblNumber($dblTotalDesc);?></strong--></td>
											<td width="1%" class="field_numeric"><!--strong><?php //echo showDblNumber($dblTotalJuros);?></strong--></td>
											<?php
											if($dblSaldo >= 0)
												echo("<td width=\"1%\" class=\"field_numeric text_green\"><strong>" . showDblNumber($dblTotalGeral) . "</strong></td>");
											else
												echo("<td width=\"1%\" class=\"field_numeric text_red\"><strong>" . showDblNumber($dblTotalGeral) . "</strong></td>");
											?>
											<td>&nbsp;</td>
										</tr>
										<?php
										$dblSaldo = 0;
										$dblTotalEntrada = 0;
										$dblTotalSaida = 0;
										$dblTotalDesc = 0;
										$dblTotalJuros = 0;
									}
								?>
								<tr><td colspan="9" height="3"></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
					} else {
						mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
					}
				?>			
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
	</table>
	<?php athEndWhiteBox(); ?>
	</center>
	</body>
</html>
<?php
}
ob_end_flush(); 
?>