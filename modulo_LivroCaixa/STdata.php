<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

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
$strMes            = requestQueryString("var_mes");             // Flag de verificação se necessita popular o session ou não
$strAno            = requestQueryString("var_ano");             // Campo de filtro
$intContaPrevista  = requestQueryString("var_conta_prevista");  // Campo de filtro
$intContaRealizada = requestQueryString("var_conta_realizada"); // Campo de filtro


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
	$strSQL .= " FROM fin_conta_pagar_receber AS pgr ";
	$strSQL .= "	, fin_lcto_ordinario AS ord ";
	$strSQL .= "	, fin_conta AS cta1 ";
	$strSQL .= "	, fin_conta AS cta2 ";
	$strSQL .= " WHERE ord.sys_dtt_cancel IS NULL ";
	$strSQL .= "   AND ord.cod_conta_pagar_receber = pgr.cod_conta_pagar_receber ";
	$strSQL .= "   AND pgr.cod_conta = cta1.cod_conta ";
	$strSQL .= "   AND ord.cod_conta = cta2.cod_conta ";
	
	if($strMes != "")  { $strSQL .= " AND date_part('month',ord.dt_lcto) = " . $strMes;  }
	if($strAno != "")  { $strSQL .= " AND date_part('year',ord.dt_lcto) = " . $strAno;  }
	
	if($intContaPrevista != "")  { $strSQL .= " AND cta1.cod_conta = " . $intContaPrevista;  }
	if($intContaRealizada != "") { $strSQL .= " AND cta2.cod_conta = " . $intContaRealizada; }
	
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
	$strSQL .= " FROM fin_lcto_em_conta AS lct ";
	$strSQL .= "	 ,fin_conta AS cta2 ";
	$strSQL .= " WHERE lct.cod_conta = cta2.cod_conta ";
	
	if($strMes != "")  { $strSQL .= " AND date_part('month',lct.dt_lcto) = " . $strMes;  }
	if($strAno != "")  { $strSQL .= " AND date_part('year',lct.dt_lcto) = " . $strAno;  }
	
	if($intContaRealizada != "") { $strSQL .= " AND cta2.cod_conta = " . $intContaRealizada; }
	
	$strSQL .= " ORDER BY 9 ";
	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) { 
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . "<br><br>" . $strSQL,"","erro",1);
	die();
}

include_once("../_scripts/scripts.js");
?>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<?php 
		if(!$boolIsExportation || $strAcao == "print") {
			echo("
				  <link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\">
				  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
				");
		}
	?>
	<script language="JavaScript" type="text/javascript">
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
					if(allTrTags[i].className == 'iframe_detail'){
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
	<style>
		li, ul { margin:0px; }
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
				<?php if($objResult->rowCount() > 0){ ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<td height="22" class="titulo_grade"><?php echo(getTText("data",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade"><?php echo(getTText("entidade",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap><?php echo(getTText("conta_realizada",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade"><?php echo(getTText("historico",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap><?php echo(getTText("valor_entrada",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap><?php echo(getTText("valor_saida",C_UCWORDS)); ?></td>
									<td height="22">&nbsp;</td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="4" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php
									try {
										$strSQL  = " SELECT DISTINCT t1.cod_conta ";
										$strSQL .= " FROM fin_saldo_ac AS t1, fin_conta AS t2 ";
										$strSQL .= " WHERE t1.cod_conta = t2.cod_conta ";
										$objResultConta = $objConn->query($strSQL);
									} catch(PDOException $e) {
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
										die();
									}
									
									$dblSaldoAnterior = 0;
									$dblTotalEntrada = 0;
									$dblTotalSaida = 0;
									
									foreach($objResultConta as $objRSConta) {
										try {
											if($strMes != "") {
												$strSQL =  " SELECT valor AS saldo_anterior, to_date('01/' || mes || '/' || ano,'DD/MM/YYYY') AS data_ac ";
												$strSQL .= "   FROM fin_saldo_ac ";												
												$strSQL .= "  WHERE mes < " . $strMes;
												$strSQL .= "    AND ano = " . $strAno; 
												$strSQL .= "    AND cod_conta = " . getValue($objRSConta,"cod_conta"); 
												$strSQL .= " ORDER BY 2 DESC "; 
											} else {
												$strSQL =  " SELECT valor AS saldo_anterior, to_date('01/' || mes || '/' || ano,'DD/MM/YYYY') AS data ";
												$strSQL .= "   FROM fin_saldo_ac ";
												$strSQL .= "  WHERE ano < " . $strAno;
												$strSQL .= "    AND cod_conta = " . getValue($objRSConta,"cod_conta"); 
												$strSQL .= " ORDER BY 2 ";
											}
											$objResultAc = $objConn->query($strSQL);
										} catch(PDOException $e) {
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
											die();
										}
										
										if($objRSAc = $objResultAc->fetch()) {
											//objRSb.MoveLast
											if(getValue($objRSAc,"saldo_anterior") != "") { 
												$dblSaldoAnterior += getValue($objRSAc,"saldo_anterior");
											}
										} else {
											try {
												$strSQL  = " SELECT vlr_saldo_ini "; 
												$strSQL .= "   FROM fin_conta ";
												$strSQL .= "  WHERE cod_conta = " . getValue($objRSConta,"cod_conta"); 
												$objResultAux = $objConn->query($strSQL);
												
												$objRSAux = $objResultAux->fetch();
												$dblSaldoAnterior += getValue($objRSAux,"valor_saldo_ini");
												
												$objResultAux->closeCursor();
												
											} catch(PDOException $e) {
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
												die();
											}											
										}
									}
									
									$dblSaldoAnterior = number_format((double) $dblSaldoAnterior,2,",","");
									$strTR = "<tr bgcolor=\"" . CL_CORLINHA_2 . "\" height=\"22\" valign=\"middle\" onMouseOver=\"intCurrentPosMouse = this.rowIndex;navigateRow(event);\">
												<td colspan=\"4\"></td>
												<td>Saldo Total Anterior</td>";
									if($dblSaldoAnterior > 0) {
										$dblTotalEntrada = doubleval($dblSaldoAnterior);
										$strTR .= "<td align=\"right\">" . $dblSaldoAnterior . "</td><td colspan=\"2\">
										</tr>";
									} else {
										$dblTotalSaida = doubleval($dblSaldoAnterior);
										$strTR .= "<td></td><td align=\"right\">" . $dblSaldoAnterior . "</td><td></td></tr>";
									}
									
									foreach($objResult as $objRS) { 
										
										echo($strTR);
										
										if($strMes != date("m",strtotime(getValue($objRS,"data"))) && $strMes != "") {
									?>
									<tr height="22" bgcolor="#EEEEEE" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
										<td colspan="5" align="right" width="98%" class="padrao_gde"><strong>Valor Total:</strong></td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblTotalEntrada,2,",",""))?></strong></td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblTotalSaida,2,",",""))?></strong></td>
										<td>&nbsp;</td>
									</tr>
									<tr height="22" bgcolor="#EEEEEE" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
										<td colspan="5" align="right" width="98%" class="padrao_gde"><strong>Saldo no período:</strong></td>
										<td width="01%" align="right">&nbsp;</td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblSaldoAnterior,2,",",""))?></strong></td>
										<td>&nbsp;</td>
									</tr>
									<tr><td colspan="8" height="1" class="line_dialog" bgcolor="#666666"></td></tr>
									<tr height="22" bgcolor="<?php echo($strBgColor)?>" valign="middle">
										<td colspan="4"></td>
										<td align="left">Saldo Total Anterior</td>
									<?php 
											$dblSaldoAnterior = number_format((double) $dblSaldoAnterior,2,",",".");
											$strTR = "";
											if($dblSaldoAnterior > 0) {
												$dblTotalEntrada = doubleval($dblSaldoAnterior);
												echo("<td align=\"right\"><strong>" . $dblSaldoAnterior . "</strong></td><td colspan=\"2\">
												</tr>");
											} else {
												$dblTotalSaida = doubleval($dblSaldoAnterior);
												echo("<td></td><td align=\"right\"><strong>" . $dblSaldoAnterior . "</strong></td><td></td></tr>");
											}
										}
										
										$strMes = date("m",strtotime(getValue($objRS,"data")));
										
										$strTR = "";
										
										$strValor = (getValue($objRS,"valor") != "") ? number_format((double) getValue($objRS,"valor"),2,",",".") : "0,00";
										
										$strVlrIn = "";
										$strVlrOut = "";
										
										if(getValue($objRS,"operacao") != "entrada") {
											$strVlrOut = $strValor;
											$dblTotalSaida += doubleval($strVlrOut);
											$dblSaldoAnterior -= doubleval($strVlrOut);					
										} else {
											$strVlrIn = $strValor;
											$dblTotalEntrada += doubleval($strVlrIn);
											$dblSaldoAnterior += doubleval($strVlrIn);
										}
										
										
										try {
											$strSQL =  " SELECT ";
											$strSQL .= ((getValue($objRS,"tipo") == "cad_pf") ? " nome " : " nome_fantasia ") . " AS nome ";
											$strSQL .= " FROM " . getValue($objRS,"tipo");
											$strSQL .= " WHERE " . ((getValue($objRS,"tipo") == "cad_pf") ? " cod_pf " : " cod_pj ") . " = " . getValue($objRS,"cod_entidade");
											$objResultAux = $objConn->query($strSQL);
											
											$objRSAux = $objResultAux->fetch();
											$strNomeEntidade = getValue($objRSAux,"nome");
												
											$objResultAux->closeCursor();
										} catch(PDOException $e) {
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
											die();
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
												<td width="<?php echo(ICONES_WIDTH)?>">
													<a href="STshow<?php echo(getValue($objRS,"modulo"))?>.php?var_chavereg=<?php echo(getValue($objRS,"codigo"))?>"><img src="../img/icon_zoom.gif" border="0"></a-->
												</td>
											</tr>
										</table>
										<?php } ?>
									</td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"data"),false)); ?></td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo(getValue($objRS,"cod_entidade") . " - " . $strNomeEntidade); ?></td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo(getValue($objRS,"conta_realizada")); ?></td>
									<td height="22" align="left" style="padding:0px 5px;"><?php echo($strHistorico); ?></td>
									<td height="22" align="right" style="padding:0px 5px;"><?php echo($strVlrIn); ?></td>
									<td height="22" align="right" style="padding:0px 5px;"><?php echo($strVlrOut); ?></td>
									<td height="22" align="left" style="padding:0px 5px;">
										<?php if(!$boolIsExportation) { ?>
										<img src="../img/icon_livro_caixa_<?php echo(getValue($objRS,"operacao")); ?>.gif">
										<?php } ?>
									</td>
								</tr>
								<?php if(!$boolIsExportation) { ?>
								<tr id="detailtr_<?php echo(getValue($objRS,"codigo"))?>" style="display:none;" class="iframe_detail">
									<td colspan="8">
										<iframe id="<?php echo(CFG_SYSTEM_NAME)?>_detailiframe_<?php echo(getValue($objRS,"codigo"))?>" name="" frameborder="0" scrolling="no" src="" width="99%"></iframe>
									</td>
								</tr>
								<?php
										}
									}
								if($strMes != "") {
								?>
									<tr height="22" bgcolor="#EEEEEE" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
										<td colspan="5" align="right" width="98%" class="padrao_gde"><strong>Valor Total:</strong></td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblTotalEntrada,2,",","."))?></strong></td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblTotalSaida,2,",",""))?></strong></td>
										<td>&nbsp;</td>
									</tr>
									<tr height="22" bgcolor="#EEEEEE" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
										<td colspan="5" align="right" width="98%" class="padrao_gde"><strong>Saldo no período:</strong></td>
										<td width="01%" align="right">&nbsp;</td>
										<td width="01%" align="right"><strong><?php echo(number_format((double) $dblSaldoAnterior,2,",","."))?></strong></td>
										<td>&nbsp;</td>
									</tr>
								<?php
								} 
								?>
								<tr><td colspan="4" height="3"></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<?php
					} 
					else{
						mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
					}
				?>			
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#BFBFBF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
		<?php /* if(!$boolIsExportation){ ?>
		<!--tr>
			<td align="left"><?php echo($intTotalRegistros . " " . getTText("reg_encontrados",C_TOLOWER)); ?></td>
			<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="STdata.php" method="post">
					<input type="hidden" name="var_order_column" value="<?php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<?php echo($strOrderDir); ?>">
					<tr>
						<td><img src="../img/grid_arrow_left.gif" onClick="paginar(<?php echo($intNumCurPage - 1)?>)"></td>
						<td style="padding:0px 10px 0px 10px;"><?php echo(getTText("pagina",C_TOLOWER)); ?> <input type="text" name="var_curpage" value="<?php echo($intNumCurPage)?>" size="3"> <?php echo(getTText("de",C_TOLOWER) . " " . $intTotalPaginas); ?></td>
						<td><img src="../img/grid_arrow_right.gif" onClick="paginar(<?php echo($intNumCurPage + 1)?>)"></td>
					</tr>
				  </form>
				</table>
			</td>
		</tr-->
		<?php } */?>
	</table>
 <?php athEndWhiteBox(); ?>
</center>
 </body>
</html>
<?php
//Se a grade tem uma ação default e ela retornou apenas uma linha ela dispara a ação default
//Atenção: usamos índice rows[2] devido ao leiaute da grade que tem duas linhas antes dos dados (barra e cabeçalho)
/*if ($intLinha == 1) {
?>
<script language="JavaScript" type="text/javascript">
	if(strDefaultAction != ""){
		objTableDummy = document.getElementById("tableContent");
		location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
	}
</script>
<?php } */?>