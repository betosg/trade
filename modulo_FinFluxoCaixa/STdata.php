<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athutils.php");

//ob_start();

/***            VERIFICAÇÃO DE ACESSO              ***/
/*****************************************************/
$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente


/***           DEFINIÇÃO DE CONSTANTES             ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB);
define("ICONES_NUM"        ,4);     // NÚMERO DE ÍCONES DA GRADE
define("ICONES_WIDTH"      ,20);    // LARGURA DOS ÍCONES DA GRADE
define("GRADE_NUM_ITENS",20);		// NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
define("GRADE_ACAO_DEFAULT","");    // AÇÃO PADRÃO DA TECLA ENTER NA GRADE

/***           DEFINIÇÃO DE PARÂMETROS            ***/
/****************************************************/
$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
$intNumCurPage    = (request("var_curpage") != "") ? request("var_curpage") : 1;        // Página corrente
$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não

/***              FILTROS - OPCIONAL              ***/
/****************************************************/
$intCodContaPR		= request("var_codigo");
$intCodConta		= request("var_cod_conta");
$intCodPlanoConta	= request("var_cod_plano_conta");
$intCodCentroCusto	= request("var_cod_centro_custo");
$strTipoConta		= request("var_tipo_conta");
$strPeriodo			= request("var_periodo");

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
	$strSQL =  " SELECT 
	                  SUM(fin_conta_pagar_receber.vlr_conta) AS vlr_total
					, CASE WHEN fin_conta_pagar_receber.pagar_receber = true THEN 'saida'
						   WHEN fin_conta_pagar_receber.pagar_receber = false THEN 'entrada'
					  END AS tipo
				 FROM fin_conta_pagar_receber
					  INNER JOIN fin_conta AS fc ON (fin_conta_pagar_receber.cod_conta = fc.cod_conta)
					  INNER JOIN fin_plano_conta AS fpc ON (fin_conta_pagar_receber.cod_plano_conta = fpc.cod_plano_conta)
					  INNER JOIN fin_centro_custo AS fcc ON (fin_conta_pagar_receber.cod_centro_custo = fcc.cod_centro_custo)
				 WHERE (fin_conta_pagar_receber.situacao <=> 'aberto' OR fin_conta_pagar_receber.situacao <=> 'lcto_parcial') ";
	
	if ($intCodContaPR != "") $strSQL .= " AND fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodContaPR;
	if ($intCodConta != "") $strSQL .= " AND fin_conta_pagar_receber.cod_conta = " . $intCodConta;
	if ($intCodPlanoConta != "") $strSQL .= " AND fin_conta_pagar_receber.cod_plano_conta = " . $intCodPlanoConta;
	if ($intCodCentroCusto != "") $strSQL .= " AND fin_conta_pagar_receber.cod_centro_custo = " . $intCodCentroCusto;
	if ($strTipoConta == "pagar") $strSQL .= " AND fin_conta_pagar_receber.pagar_receber = true ";
	if ($strTipoConta == "receber") $strSQL .= " AND fin_conta_pagar_receber.pagar_receber = false ";
	if ($strPeriodo != "") {
		if ($strPeriodo == "ate_hoje") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE ";
		if ($strPeriodo == "sete_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '7 days' ";
		if ($strPeriodo == "quinze_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '15 days' ";
		if ($strPeriodo == "trinta_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '30 days' ";
		if ($strPeriodo == "sessenta_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '60 days' ";
	}
	
	$strSQL .= " GROUP BY fin_conta_pagar_receber.pagar_receber ";
	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) { 
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . "<br><br>" . $strSQL,"","erro",1);
	die();
}

$dblTotalEntrada = 0;
$dblTotalSaida = 0;
if($objResult->rowCount() > 0){
	foreach($objResult as $objRS) {
		if (getValue($objRS,"tipo") == "entrada") $dblTotalEntrada += getValue($objRS,"vlr_total");
		if (getValue($objRS,"tipo") == "saida") $dblTotalSaida += getValue($objRS,"vlr_total");
	}
} 
else{
	//Se não veio nada deixa que consulta debaixo exiba mensagem
	//mensagem("alert_consulta_vazia_titulo", "alert_consulta_vazia_desc", "", "", "aviso", 0);
}
$objResult->closeCursor();
$dblTotalSaldo = $dblTotalEntrada - $dblTotalSaida;

try {
	$strSQL =  " SELECT
					  fin_conta_pagar_receber.cod_conta_pagar_receber AS codigo
					, fin_conta_pagar_receber.codigo AS cod_entidade
					, CASE WHEN fin_conta_pagar_receber.tipo = 'cad_pf' THEN (SELECT nome FROM cad_pf WHERE cod_pf = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj' THEN (SELECT razao_social FROM cad_pj WHERE cod_pj = fin_conta_pagar_receber.codigo)
						   WHEN fin_conta_pagar_receber.tipo = 'cad_pj_fornec' THEN (SELECT razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = fin_conta_pagar_receber.codigo)
					  END AS entidade
					, CASE WHEN fin_conta_pagar_receber.pagar_receber = true THEN 'saida'
						   WHEN fin_conta_pagar_receber.pagar_receber = false THEN 'entrada'
					  END AS tipo
					, fcc.cod_reduzido, fcc.nome AS centro_custo
					, fpc.cod_reduzido, fpc.nome AS plano_conta
					, fin_conta_pagar_receber.historico
					, fc.nome AS conta
					, fin_conta_pagar_receber.num_documento
					, fin_conta_pagar_receber.dt_vcto
					, CASE WHEN (current_date - fin_conta_pagar_receber.dt_vcto) > 0 THEN (current_date - fin_conta_pagar_receber.dt_vcto)
					  END AS num_dias
					, fin_conta_pagar_receber.situacao
					, fin_conta_pagar_receber.vlr_conta
				 FROM fin_conta_pagar_receber
					  INNER JOIN fin_conta AS fc ON (fin_conta_pagar_receber.cod_conta = fc.cod_conta)
					  INNER JOIN fin_plano_conta AS fpc ON (fin_conta_pagar_receber.cod_plano_conta = fpc.cod_plano_conta)
					  INNER JOIN fin_centro_custo AS fcc ON (fin_conta_pagar_receber.cod_centro_custo = fcc.cod_centro_custo)
				 WHERE (fin_conta_pagar_receber.situacao <=> 'aberto' OR fin_conta_pagar_receber.situacao <=> 'lcto_parcial') ";
	
	if ($intCodContaPR != "") $strSQL .= " AND fin_conta_pagar_receber.cod_conta_pagar_receber = " . $intCodContaPR;
	if ($intCodConta != "") $strSQL .= " AND fin_conta_pagar_receber.cod_conta = " . $intCodConta;
	if ($intCodPlanoConta != "") $strSQL .= " AND fin_conta_pagar_receber.cod_plano_conta = " . $intCodPlanoConta;
	if ($intCodCentroCusto != "") $strSQL .= " AND fin_conta_pagar_receber.cod_centro_custo = " . $intCodCentroCusto;
	if ($strTipoConta == "pagar") $strSQL .= " AND fin_conta_pagar_receber.pagar_receber = true ";
	if ($strTipoConta == "receber") $strSQL .= " AND fin_conta_pagar_receber.pagar_receber = false ";
	if ($strPeriodo != "") {
		if ($strPeriodo == "ate_hoje") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE ";
		if ($strPeriodo == "sete_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '7 days' ";
		if ($strPeriodo == "quinze_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '15 days' ";
		if ($strPeriodo == "trinta_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '30 days' ";
		if ($strPeriodo == "sessenta_dias") $strSQL .= " AND fin_conta_pagar_receber.dt_vcto <= CURRENT_DATE + interval '60 days' ";
	}
	
	$strSQL .= " ORDER BY fin_conta_pagar_receber.dt_vcto DESC 
				 LIMIT " . GRADE_NUM_ITENS . " OFFSET " . strval(GRADE_NUM_ITENS * ($intNumCurPage - 1));
	
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) { 
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage() . "<br><br>" . $strSQL,"","erro",1);
	die();
}

$intTotalPaginas = 0;

function showDblNumber($prNumber) {
	$prNumber = round($prNumber,2);
	$prNumber = abs($prNumber);
	$prNumber = number_format((double) $prNumber,2,",",".");
	
	return($prNumber);
}

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
		
		include_once("../_scripts/scripts.js");
		include_once("../_scripts/STscripts.js");
	?>
	<script language="JavaScript" type="text/javascript">
		var intCurrentPos = 1;
		var intCurrentPosMouse;
		var strDefaultAction = "<?php echo(GRADE_ACAO_DEFAULT); ?>"; 
		
		function aplicarFuncao(prValue) {
			if(prValue != "") {
				location.href = prValue;
			}
		}
		
		function setOrderBy(prStrOrder,prStrDirect) {
			location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_order_column=" + prStrOrder + ".var_order_direct=" + prStrDirect;
		}
		
		function paginar(prPagina){
			if(prPagina > 0){
				document.formpaginacao.var_curpage.value = prPagina;
				document.formpaginacao.submit();
			}	
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
			}else if(e.keyCode == 39) {
				proximaPagina = parseInt(document.formpaginacao.var_curpage.value) + 1;
				paginar(proximaPagina);
			}else if(e.keyCode == 37) {
				paginaAnterior = parseInt(document.formpaginacao.var_curpage.value) - 1;
				paginar(paginaAnterior);
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
				SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_"+prChave_reg);
				
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
						 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_"+prChave_reg);
					}
				}
			}
			moduloDetailAnt = prLink;
		}
		
		function ativaMenu() {
			over = function() {
				var sfEls = document.getElementById("menu_img").getElementsByTagName("li");
				for (var i = 0; i < sfEls.length; i++) {
					sfEls[i].onmouseover = function() {
						this.className += " over";
					}
					sfEls[i].onmouseout = function() {
						this.className = this.className.replace(new RegExp(" over\\b"), "");
					}
				}
			}
			if (window.attachEvent) window.attachEvent("onload", over);
		}
		
		function SetIFrameSource(prPage,prId) {
			document.getElementById(prId).src = prPage;
		}
		
		document.onkeydown = navigateRow;
	</script>
	<style type="text/css"> 
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
				<?php if($objResult->rowCount() > 0){ ?> <!-- SE A CONSULTA VIER VAZIA NÃO PASSA AQUI, ENTRARÁ NO ELSE DESSE IF -->
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<tr>
									<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
									<td></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
									<td height="22" class="titulo_grade"><?php echo(getTText("entidade",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap="nowrap"><?php echo(getTText("centro_custo",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap="nowrap"><?php echo(getTText("plano_conta",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade"><?php echo(getTText("historico",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap="nowrap"><?php echo(getTText("num_documento",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" nowrap="nowrap"><?php echo(getTText("dt_vcto",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" align="right" nowrap="nowrap"><?php echo(getTText("num_dias",C_UCWORDS)); ?></td>
									<td height="22" class="titulo_grade" align="right" nowrap="nowrap"><?php echo(getTText("valor",C_UCWORDS)); ?></td>
									<td height="22" class="field_text"></td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="10" height="3"></td></tr>
								<?php
								$strBgColor = CL_CORLINHA_2;
								
								$dblParcialEntrada = 0;
								$dblParcialSaida = 0;
								
								foreach($objResult as $objRS) {
								?>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<tr bgcolor="<?php echo($strBgColor); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<?php if(!$boolIsExportation) { ?>
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM); ?>">
											<tr>
												<td width="<?php echo ICONES_WIDTH;?>"><?php echo("<a href='STcancelapagarreceber.php?var_oper=CANCEL&var_chavereg=" . getValue($objRS,"codigo") . "'><img src='../img/icon_cancel.gif' border='0' alt='cancelar' title='cancelar' style='cursor:pointer'></a>"); ?></td>
												<td width="<?php echo ICONES_WIDTH;?>"><?php echo("<a href='STshowtitulos.php?var_chavereg=" . getValue($objRS,"codigo") . "'><img src='../img/icon_zoom.gif' border='0' alt='cancelar' title='visualizar' style='cursor:pointer'></a>"); ?></td>
												<td width="<?php echo ICONES_WIDTH;?>"><a onClick="showDetailGrid('<?php echo(getValue($objRS,"codigo")); ?>','STifrlancamento.php','cod_conta_pagar_receber');"><img src='../img/icon_ver_lancamento.gif' border='0' alt='ver lançamentos' title='ver lançamentos' style='cursor:pointer'></a></td>
												<td width="<?php echo ICONES_WIDTH;?>"><a onClick="window.open('../modulo_FinContaPagarReceber/STshowBoleto.php?var_chavereg=<?php echo(getValue($objRS,"codigo")); ?>','','width=800,height=600,scrollbars=1');"><img src='../img/icon_boleto.gif' border='0' alt='ver boleto' title='ver boleto' style='cursor:pointer'></a></td>
											</tr>
										</table>
										<?php } ?>
									</td>
									<td class="field_text"><?php echo(getValue($objRS,"cod_entidade") . " - " . getValue($objRS,"entidade")); ?></td>
									<td class="field_text"><?php echo(getValue($objRS,"centro_custo")); ?></td>
									<td class="field_text"><?php echo(getValue($objRS,"plano_conta")); ?></td>
									<td class="field_text"><?php echo(getValue($objRS,"historico")); ?></td>
									<td class="field_text"><?php echo(getValue($objRS,"num_documento")); ?></td>
									<td class="field_text" nowrap="nowrap"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false)); ?></td>
									<td class="field_numeric"><?php echo(getValue($objRS,"num_dias")); ?></td>
									<td class="field_numeric"><?php echo(number_format((double) getValue($objRS,"vlr_conta"),2,",",".")); ?></td>
									<td class="field_text">
										<?php if(!$boolIsExportation) { ?>
										<img src="../img/icon_livro_caixa_<?php echo(getValue($objRS,"tipo")); ?>.gif">
										<?php } ?>
									</td>

								</tr>
								<tr id="detailtr_<?php echo(getValue($objRS,"codigo")); ?>" bgColor="#FFFFFF" style="display:none;" class="iframe_detail">
									<td colspan="20">
									<iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_<?php echo(getValue($objRS,"codigo")); ?>" id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe_<?php echo(getValue($objRS,"codigo")); ?>" width="99%" src="" frameborder="0" scrolling="no"></iframe>
									</td>
								</tr>
								<?php
									$strBgColor = (!isset($strBgColor) || $strBgColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
									
									if (getValue($objRS,"tipo") == "entrada") $dblParcialEntrada += getValue($objRS,"vlr_conta");
									if (getValue($objRS,"tipo") == "saida") $dblParcialSaida += getValue($objRS,"vlr_conta");
								}
								$dblParcialSaldo = $dblParcialEntrada - $dblParcialSaida;
								?>
								<tr height="22" bgcolor="#EEEEEE">
									<td colspan="8" align="right" width="98%" class="padrao_gde"><strong>Parcial a Receber:</strong></td>
									<td width="01%" class="field_numeric"><strong>&nbsp;<?php echo showDblNumber($dblParcialEntrada);?></strong></td>
									<td>&nbsp;</td>
								</tr>
								<tr height="22" bgcolor="#EEEEEE">
									<td colspan="8" align="right" width="98%" class="padrao_gde"><strong>Parcial a Pagar:</strong></td>
									<td width="01%" class="field_numeric"><strong>&nbsp;<?php echo showDblNumber($dblParcialSaida);?></strong></td>
									<td>&nbsp;</td>
								</tr>
								<tr height="22" bgcolor="#EEEEEE">
									<td colspan="8" align="right" width="98%" class="padrao_gde"><strong>Saldo:</strong></td>
									<?php
									if ($dblParcialSaldo >= 0) 
										echo("<td width='01%' class='field_numeric text_green'><strong>&nbsp;" . showDblNumber($dblParcialSaldo) . "</strong></td>");
									else
										echo("<td width='01%' class='field_numeric text_red'><strong>&nbsp;" . showDblNumber($dblParcialSaldo) . "</strong></td>");
									?>
									<td></td>
								</tr>
								<tr><td colspan="10" height="3"></td></tr>
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
		<?php if(($objResult->rowCount() > 0) && (!$boolIsExportation) && (GRADE_NUM_ITENS != 0)){ ?>
		<tr>
			<td colspan="2" align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
					<input type="hidden" name="var_order_column" value="<?php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<?php echo($strOrderDir); ?>">
					<input type="hidden" name="var_cod_dialog_grid" value="<?php echo($intCodDialogGrid); ?>">
					<input type="hidden" name="var_sql_param" value="<?php echo($strSQLParam); ?>">
					
					<input type="hidden" name="var_codigo" value="<?php echo($intCodContaPR);?>">
					<input type="hidden" name="var_cod_conta" value="<?php echo($intCodConta); ?>">
					<input type="hidden" name="var_cod_plano_conta" value="<?php echo($intCodPlanoConta); ?>">
					<input type="hidden" name="var_cod_centro_custo" value="<?php echo($intCodCentroCusto); ?>">
					<input type="hidden" name="var_tipo_conta" value="<?php echo($strTipoConta); ?>">
					<input type="hidden" name="var_periodo" value="<?php echo($strPeriodo); ?>">
					<tr>
						<td><img src="../img/grid_arrow_left.gif" onClick="paginar(<?php echo($intNumCurPage - 1)?>)"></td>
						<td style="padding:0px 10px 0px 10px;">página <input type="text" name="var_curpage" value="<?php echo($intNumCurPage)?>" size="3"></td>
						<td><img src="../img/grid_arrow_right.gif" onClick="paginar(<?php echo($intNumCurPage + 1)?>)"></td>
					</tr>
				  </form>
				</table>
			</td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr><td height="3" colspan="2" bgcolor="#FFFFFF"></td></tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr height="22" bgcolor="#FFFFFF">
						<td align="right" width="99%" class="padrao_gde"><strong>Total a Receber:</strong></td>
						<td width="01%" class="field_numeric"><strong>&nbsp;<?php echo showDblNumber($dblTotalEntrada);?></strong></td>
					</tr>
					<tr height="22" bgcolor="#FFFFFF">
						<td align="right" width="99%" class="padrao_gde"><strong>Total a Pagar:</strong></td>
						<td width="01%" class="field_numeric"><strong>&nbsp;<?php echo showDblNumber($dblTotalSaida);?></strong></td>
					</tr>
					<tr height="22" bgcolor="#FFFFFF">
						<td align="right" width="99%" class="padrao_gde"><strong>Saldo:</strong></td>
						<?php
						if ($dblTotalSaldo >= 0) 
							echo("<td width='01%' class='field_numeric text_green'><strong>&nbsp;" . showDblNumber($dblTotalSaldo) . "</strong></td>");
						else
							echo("<td width='01%' class='field_numeric text_red'><strong>&nbsp;" . showDblNumber($dblTotalSaldo) . "</strong></td>");
						?>
					</tr>
				</table>
				</td>
			</table>
			</td>
		</tr>
		<?php } ?>
	</table>
 <?php athEndWhiteBox(); ?>
</center>
 </body>
</html>
<?php
//ob_end_flush();

//Se a grade tem uma ação default e ela retornou apenas uma linha ela dispara a ação default
//Atenção: usamos índice rows[2] devido ao leiaute da grade que tem duas linhas antes dos dados (barra e cabeçalho)
if ($objResult->rowCount() == 1) {
?>
<script language="javascript" type="text/javascript">
	//if(strDefaultAction != ""){
	//	objTableDummy = document.getElementById("tableContent");
	//	location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
	//}
</script>
<?php
}
$objResult->closeCursor();
$objConn = NULL;
?>