<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	ob_start();
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// Carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// Verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");
	
	// REQUESTS FILTRO
	$dtDateInicio  = (request("var_dt_ini") == "" || !is_date(cDate(CFG_LANG,request("var_dt_ini"),false))) ? cDate(CFG_LANG,dateNow(),false) : cDate(CFG_LANG,request("var_dt_ini"),false);
	$dtDateFim     = (request("var_dt_fim") == "" || !is_date(cDate(CFG_LANG,request("var_dt_fim"),false))) ? cDate(CFG_LANG,dateNow(),false) : cDate(CFG_LANG,request("var_dt_fim"),false);
	$intContaBanco = request("var_conta_banco");
	$intPlanoConta = request("var_plano_conta");
	
	// Abertura de conexão com o BD
	$objConn = abreDBConn(CFG_DB);
	
	// Faz Busca da Conta
	try{
		$strSQL  = "SELECT cod_conta, cod_conta||' - '||nome AS nome FROM fin_conta WHERE dtt_inativo IS NULL ";
		$strSQL .= ($intContaBanco == "") ? "" : " AND cod_conta = ".$intContaBanco;
		$objResultConta = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// Variáveis para Saldo
	$dblTotalEntrada  = 0;
	$dblTotalSaida    = 0;
	$dblTotalGeral    = 0;
	$dbtVlrTotalConta = 0;
	$dblVlrDiferenca  = 0;
	$dblVlrDiff		  = 0;
	$dblTotalParcial  = 0;
	$intCodTitulo     = "";
	
	
	$dblVlrPlConta    = 0;
	
	// Inicialização de Cores para FORM
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	
	
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
	$strMes = request("var_mes");
	$strAno = request("var_ano");

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
	<style type="text/css"> 
		li, ul { margin:0px; }	
		.field_numeric 	{ text-align:right; padding:0px 5px; height:22px; }
		.field_text    	{ text-align:left; padding:0px 5px; height:22px; }
		
		.text_green		{ color:green; }
		.text_red 		{ color:red; }
		.font_gray		{ color:#999;font-size:9px;padding-left:5px; }
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
				<table cellpadding="0" cellspacing="3" width="100%" style="border:1px #EEEEEE solid;" bgcolor="#F7F7F7">
					<tr><td height="5" bgcolor="#BFBFBF"></td></tr>
					<tr>
						<td>
							<table id="tableContent" border="0" cellpadding="0" cellspacing="0" width="100%" background="../img/grid_backheader.gif" style="background-repeat:repeat-x;">
								<thead>
									<tr>
										<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
										<th width="32"></th> 
										<!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
										<th width="63" height="22" class="titulo_grade" style="font-weight:normal;text-align:center;"><?php echo(getTText("data",C_UCWORDS)); ?></th>
										<th width="298" height="22" class="titulo_grade" style="font-weight:normal;text-align:left;"  ><?php echo(getTText("historico",C_UCWORDS)); ?></th>
										<th width="276" height="22" class="titulo_grade" style="font-weight:normal;text-align:left;"  ><?php echo(getTText("plano_conta",C_UCWORDS)); ?></th>
										<th width="111" height="22" class="titulo_grade" style="font-weight:normal;text-align:right;" ><?php echo(getTText("valor_entrada",C_UCWORDS)); ?></th>
										<th width="110" height="22" class="titulo_grade" style="font-weight:normal;text-align:right;" ><?php echo(getTText("valor_saida",C_UCWORDS)); ?></th>
										<th width="80">&nbsp;</th>
										<!-- CABEÇALHO DA GRADE - [FIM] -->
									</tr>
								</thead>
								<tr><td colspan="5" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php 
									foreach($objResultConta as $objRSConta){
										// PROCEDURE PARA LOCALIZAR
										// SALDO ANTERIOR DA CONTA DO
										// ROW CORRENTE
										try{
											$strSQL   	 = "SELECT * FROM sp_saldo_ac_diario(".getValue($objRSConta,"cod_conta").",'".$dtDateInicio."') AS saldo_ini";
											$objSaldoIni = $objConn->query($strSQL);
											$objRSSldIni = $objSaldoIni->fetch();
										}catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
											die();
										}
								?>
								
								<!-- LINHA COM NOME DA CONTA BANCO + SALDO INICIAL NO PERIODO SELECIONADO -->
								<tr bgcolor="#EEEEEE" height="22" valign="middle">
									<td colspan="2" style="padding-left:10px;"><strong><?php echo(getValue($objRSConta,"nome"));?></strong></td>
									<td class="padrao_gde" colspan="2" align="right"><?php echo(getTText("saldo_inicial",C_NONE));?>:</td>
									<td class="field_numeric"><strong><?php echo((getValue($objRSSldIni,"saldo_ini") >= 0) ? number_format((double) getValue($objRSSldIni,"saldo_ini"),2,',','.') : "");?></strong></td>
									<td class="field_numeric"><strong><?php echo((getValue($objRSSldIni,"saldo_ini") <  0) ? number_format((double) getValue($objRSSldIni,"saldo_ini"),2,',','.') : "");?></strong></td>
									<td colspan="2"></td>
								</tr>
								<!-- LINHA COM NOME DA CONTA BANCO + SALDO INICIAL NO PERIODO SELECIONADO -->
						
									<?php 
										try{
											// LCTOS EM CONTA - Soma e EXIBE TODOS OS 
											// LCTOS EM CONTA, PARA TODAS AS CONTAS 
											// SELECIONADAS NO FILTRO
											$strSQL  = " SELECT 'lcto_em_conta' AS tipo ";
											$strSQL .= "      , fin_lcto_em_conta.cod_lcto_em_conta AS codigo ";
											$strSQL .= "      , fin_lcto_em_conta.cod_lcto_em_conta||' - '||fin_lcto_em_conta.historico AS hist_lcto ";
											$strSQL .= "      , fin_lcto_em_conta.dt_lcto ";
											$strSQL .= "      , fin_lcto_em_conta.vlr_lcto ";
											$strSQL .= "      , fin_lcto_em_conta.operacao ";
											$strSQL .= "      , fin_plano_conta.cod_reduzido||' - '||fin_plano_conta.nome AS plano_conta ";
											$strSQL .= "      , fin_plano_conta.nome AS nome_plano_conta ";
											$strSQL .= "      , NULL AS destino ";
											$strSQL .= "      , NULL AS cod_destino ";
											$strSQL .= "      , NULL AS origem ";
											$strSQL .= "      , NULL AS cod_origem ";
											$strSQL .= "      , CAST(NULL AS DOUBLE PRECISION) AS vlr_juros";
											$strSQL .= "      , CAST(NULL AS DOUBLE PRECISION) AS vlr_desc";
											$strSQL .= "      , CAST(NULL AS INTEGER) AS cod_conta_pagar_receber";
											$strSQL .= "      , NULL AS situacao";
											$strSQL .= "      , CAST(NULL AS BOOLEAN) AS pagar_receber";
											$strSQL .= " FROM fin_lcto_em_conta, fin_plano_conta ";
											$strSQL .= " WHERE fin_lcto_em_conta.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
											$strSQL .= " AND fin_lcto_em_conta.cod_conta = ".getValue($objRSConta,"cod_conta");
											$strSQL .= " AND fin_lcto_em_conta.cod_plano_conta = fin_plano_conta.cod_plano_conta ";
											if($intPlanoConta != "") $strSQL .= " AND fin_plano_conta.cod_plano_conta = ".$intPlanoConta;
										
											if($intPlanoConta == ""){
												
												$strSQL .= " UNION ";
												
												// TODOS OS LANÇAMENTOS DE TRANSFERENCIA
												// LCTOS DE TRANSFERENCIA SÓ SÃO EXIBIDOS
												// QUANDO NENHUM PLANO DE CONTA É ESPECIFI-
												// CADO.
												$strSQL .= " SELECT 'lcto_transf' ";
												$strSQL .= "      , fin_lcto_transf.cod_lcto_transf ";
												$strSQL .= "      , fin_lcto_transf.cod_lcto_transf||' - '||fin_lcto_transf.historico ";
												$strSQL .= "      , fin_lcto_transf.dt_lcto ";
												$strSQL .= "      , fin_lcto_transf.vlr_lcto ";
												$strSQL .= "      , NULL ";
												$strSQL .= "      , 'SAF' ";
												$strSQL .= "      , NULL ";
												$strSQL .= "      , conta_destino.nome ";
												$strSQL .= "      , conta_destino.cod_conta ";
												$strSQL .= "      , conta_origem.nome ";
												$strSQL .= "      , conta_origem.cod_conta ";
												$strSQL .= "      , CAST(NULL AS DOUBLE PRECISION) ";
												$strSQL .= "      , CAST(NULL AS DOUBLE PRECISION) ";
												$strSQL .= "      , CAST(NULL AS INTEGER) ";
												$strSQL .= "      , NULL ";
												$strSQL .= "      , CAST(NULL AS BOOLEAN) ";
												$strSQL .= " FROM fin_lcto_transf ";
												$strSQL .= " INNER JOIN fin_conta conta_destino ON (fin_lcto_transf.cod_conta_dest = conta_destino.cod_conta) ";
												$strSQL .= " INNER JOIN fin_conta conta_origem ON (fin_lcto_transf.cod_conta_orig = conta_origem.cod_conta) ";
												$strSQL .= " INNER JOIN fin_conta ON ((fin_lcto_transf.cod_conta_dest = fin_conta.cod_conta OR fin_lcto_transf.cod_conta_orig = fin_conta.cod_conta) AND fin_conta.cod_conta = ".getValue($objRSConta,"cod_conta").")";
												$strSQL .= " WHERE fin_lcto_transf.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
											}
										
											// LCTOS ORDINÁRIOS
											// Busca todos os LCTOS_ORDINARIOS
											// Com base no especificado no FILTRO 
											
											$strSQL .= " UNION ";
											
											$strSQL .= " SELECT 'lcto_ordinario' ";
											$strSQL .= "      , fin_lcto_ordinario.cod_lcto_ordinario ";
											$strSQL .= "      , fin_lcto_ordinario.cod_lcto_ordinario||' - '||fin_lcto_ordinario.historico ";
											$strSQL .= "      , fin_lcto_ordinario.dt_lcto ";
											$strSQL .= "      , fin_lcto_ordinario.vlr_lcto ";
											$strSQL .= "      , NULL ";
											$strSQL .= "      , fin_plano_conta.cod_reduzido||' - '||fin_plano_conta.nome ";
											$strSQL .= "      , fin_plano_conta.nome ";
											$strSQL .= "      , NULL ";
											$strSQL .= "      , NULL ";
											$strSQL .= "      , NULL ";
											$strSQL .= "      , NULL ";
											$strSQL .= "      , fin_lcto_ordinario.vlr_juros ";
											$strSQL .= "      , fin_lcto_ordinario.vlr_desc ";
											$strSQL .= "      , fin_conta_pagar_receber.cod_conta_pagar_receber ";
											$strSQL .= "      , fin_conta_pagar_receber.situacao ";
											$strSQL .= "      , fin_conta_pagar_receber.pagar_receber ";
											$strSQL .= " FROM fin_lcto_ordinario ";
											$strSQL .= " INNER JOIN fin_conta_pagar_receber ON (fin_conta_pagar_receber.cod_conta_pagar_receber = fin_lcto_ordinario.cod_conta_pagar_receber) ";
											$strSQL .= " INNER JOIN fin_conta ON (fin_lcto_ordinario.cod_conta = fin_conta.cod_conta AND fin_conta.cod_conta = ".getValue($objRSConta,"cod_conta").") "; 
											$strSQL .= " INNER JOIN fin_plano_conta ON (fin_plano_conta.cod_plano_conta = fin_lcto_ordinario.cod_plano_conta ";
											if ($intPlanoConta != "") $strSQL .= " AND fin_plano_conta.cod_plano_conta = ".$intPlanoConta;
											$strSQL .= " ) ";
											$strSQL .= " WHERE fin_lcto_ordinario.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
											if ($intPlanoConta != "") $strSQL .= " OR fin_lcto_ordinario.cod_conta_pagar_receber IN (SELECT t1.cod_agrupador FROM fin_conta_pagar_receber t1, fin_lcto_ordinario t2 WHERE t1.cod_conta_pagar_receber = t2.cod_conta_pagar_receber AND t2.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' AND t2.cod_plano_conta = ".$intPlanoConta.") ";
											
											$strSQL .= " ORDER BY 4, 7 ";
											
											$objResultGeral = $objConn->query($strSQL);
										}catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
											die();
										}
									
										// Foreach dos LCTOS LOCALIZADOS
										foreach($objResultGeral as $objRSGeral){
											// Somatorio dos LCTOS EM CONTA
											if (getValue($objRSGeral,"tipo") == "lcto_em_conta"){
												$dblTotalSaida   = (getValue($objRSGeral,"operacao") == "despesa") ? ($dblTotalSaida   + getValue($objRSGeral,"vlr_lcto")) : $dblTotalSaida; //$dblVlrPlConta = $dblVlrPlConta - getValue($objRSGeral,"vlr_lcto"); 
												$dblTotalEntrada = (getValue($objRSGeral,"operacao") == "receita") ? ($dblTotalEntrada + getValue($objRSGeral,"vlr_lcto")) : $dblTotalEntrada; //$dblVlrPlConta = $dblVlrPlConta + getValue($objRSGeral,"vlr_lcto"); 
											}
											
											// Somatorio dos LCTOS TRANSFERENCIA
											if (getValue($objRSGeral,"tipo") == "lcto_transf") {
												$dblTotalSaida   = (getValue($objRSGeral,"cod_origem") == getValue($objRSConta,"cod_conta")) ? ($dblTotalSaida   + getValue($objRSGeral,"vlr_lcto")) : $dblTotalSaida; //$dblVlrPlConta = $dblVlrPlConta - getValue($objRSGeral,"vlr_lcto"); 
												$dblTotalEntrada = (getValue($objRSGeral,"cod_origem") != getValue($objRSConta,"cod_conta")) ? ($dblTotalEntrada + getValue($objRSGeral,"vlr_lcto")) : $dblTotalEntrada; //$dblVlrPlConta = $dblVlrPlConta + getValue($objRSGeral,"vlr_lcto"); 
											}
											
											// Somatorio dos LCTOS ORDINARIOS - AGRUP. OU NÃO
											if(getValue($objRSGeral,"tipo") == "lcto_ordinario"){
												$dblTotalSaida 	 = (getValue($objRSGeral,"pagar_receber") == TRUE ) ? (($dblTotalSaida   + (getValue($objRSGeral,"vlr_lcto") + getValue($objRSGeral,"vlr_juros"))) - getValue($objRSGeral,"vlr_desc")) : $dblTotalSaida;
												$dblTotalEntrada = (getValue($objRSGeral,"pagar_receber") == FALSE) ? (($dblTotalEntrada + (getValue($objRSGeral,"vlr_lcto") + getValue($objRSGeral,"vlr_juros"))) - getValue($objRSGeral,"vlr_desc")) : $dblTotalEntrada;
											}
											
											
											// EXIBIÇÃO DAS LINHAS DE LCTOS
											// COMEÇAMOS POR LINHA DE LCTO EM CONTA									
											if (getValue($objRSGeral,"tipo") == "lcto_em_conta") {
										?>
												<tr bgcolor="<?php echo(getLineColor($strColor));?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
													<td width="32">
														<?php if(!$boolIsExportation) { ?>
														<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM);?>">
														<tr>
															<td width="<?php echo ICONES_WIDTH;?>" style="padding-left:15px;"><?php echo("<a href='STshowlctoconta.php?var_chavereg=" . getValue($objRSGeral,"codigo") . "'><img src='../img/icon_zoom.gif' border='0' style='cursor:pointer'></a>"); ?></td>
														</tr>
														</table>
														<?php } ?>
												  </td>
													<td class="field_text"><?php echo(dDate(CFG_LANG,getValue($objRSGeral,"dt_lcto"),false));?></td>
													<td class="field_text"><?php echo(getTText("lcto_no",C_NONE).getValue($objRSGeral,"hist_lcto"));?></td>
													<td class="field_text"><?php echo(getValue($objRSGeral,"plano_conta")); ?></td>
													<td class="field_numeric"><?php echo((getValue($objRSGeral,"operacao") == "despesa") ? "" : number_format((double) getValue($objRSGeral,"vlr_lcto"),2,',','.'));?></td>
													<td class="field_numeric"><?php echo((getValue($objRSGeral,"operacao") == "receita") ? "" : number_format((double) getValue($objRSGeral,"vlr_lcto"),2,',','.'));?></td>
													<td class="field_text">
														<?php if(!$boolIsExportation) { ?>
															<img src="../img/<?php echo((getValue($objRSGeral,"operacao") == "despesa") ? "icon_fincontapagar.gif" : "icon_fincontareceber.gif" );?>" />
														<?php } ?>
													</td>
												</tr>
										<?php } // Fim if lcto em conta
										
										// LINHA LCTO TRANSFERENCIA
										if (getValue($objRSGeral,"tipo") == "lcto_transf"){
										?>
											<tr bgcolor="<?php echo(getLineColor($strColor));?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
												<td width="32">
													<?php if(!$boolIsExportation) { ?>
													<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM);?>">
													<tr>
														<td width="<?php echo ICONES_WIDTH;?>" style="padding-left:15px;"><?php echo("<a href='STshowlctotransf.php?var_chavereg=" . getValue($objRSGeral,"codigo") . "'><img src='../img/icon_zoom.gif' border='0' style='cursor:pointer'></a>"); ?></td>
													</tr>
													</table>
													<?php } ?>
											  </td>
												<td class="field_text"><?php echo(dDate(CFG_LANG,getValue($objRSGeral,"dt_lcto"),false));?></td>
												<td class="field_text">
													<?php echo(getTText("lcto_no",C_NONE).getValue($objRSGeral,"hist_lcto"));?>
													<?php echo("&nbsp;&nbsp; - ".getTText("saida_de",C_NONE).getValue($objRSGeral,"origem").getTText("para",C_NONE).getValue($objRSGeral,"destino"));?>
												</td>
												<td class="field_text"><!-- PLANO DE CONTA --><?php echo(getTText("transferencia",C_TOUPPER));?></td>
												<td class="field_numeric"><?php echo((getValue($objRSGeral,"cod_origem")   == getValue($objRSConta,"cod_conta")) ? "" : number_format((double) getValue($objRSGeral,"vlr_lcto"),2,',','.'));?></td>
												<td class="field_numeric"><?php echo((getValue($objRSGeral,"cod_destino")  == getValue($objRSConta,"cod_conta")) ? "" : number_format((double) getValue($objRSGeral,"vlr_lcto"),2,',','.'));?></td>
												<td class="field_text"><?php if(!$boolIsExportation) { ?><img src="../img/<?php echo((getValue($objRSGeral,"cod_origem") == getValue($objRSConta,"cod_conta")) ? "icon_fincontapagar.gif" : "icon_fincontareceber.gif" );?>" /><?php } ?></td>
											</tr>
									<?php } // Fim if lcto transferencia
										
										// LINHA LCTO ORDINARIO
										if(getValue($objRSGeral,"tipo") == "lcto_ordinario"){
										//echo(getValue($objRSGeral,"vlr_desc")."<br/>");
										//echo(getValue($objRSGeral,"vlr_juros")."<br/>");
										?>
											<tr bgcolor="<?php echo(getLineColor($strColor));?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
												<td width="32">
													<?php if(!$boolIsExportation) { ?>
													<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(CL_LINK_WIDTH * ICONES_NUM);?>">
													<tr>
														<td width="<?php echo ICONES_WIDTH;?>" style="padding-left:15px;"><?php echo("<a href='STshowtitulos.php?var_chavereg=" . getValue($objRSGeral,"cod_conta_pagar_receber") . "'><img src='../img/icon_zoom.gif' border='0' style='cursor:pointer'></a>"); ?></td>
													</tr>
													</table>
													<?php } ?>
											  </td>
												<td class="field_text" style="text-align:left;"><?php echo(dDate(CFG_LANG,getValue($objRSGeral,"dt_lcto"),false));?></td>
												<td class="field_text"><?php echo(getTText("lcto_no",C_NONE).getValue($objRSGeral,"hist_lcto"));?></td>
												<td class="field_text"><?php echo(getValue($objRSGeral,"plano_conta"));?></td>
												<td class="field_numeric"><?php echo((getValue($objRSGeral,"pagar_receber") == TRUE ) ? "" : number_format((double) ((getValue($objRSGeral,"vlr_lcto") + getValue($objRSGeral,"vlr_juros")) - getValue($objRSGeral,"vlr_desc")),2,',','.'));?></td>
												<td class="field_numeric"><?php echo((getValue($objRSGeral,"pagar_receber") == FALSE) ? "" : number_format((double) ((getValue($objRSGeral,"vlr_lcto") + getValue($objRSGeral,"vlr_juros")) - getValue($objRSGeral,"vlr_desc")),2,',','.'));?></td>
												<td class="field_text"><?php if(!$boolIsExportation) { ?><img src="../img/<?php echo((getValue($objRSGeral,"pagar_receber") == TRUE) ? "icon_fincontapagar.gif" : "icon_fincontareceber.gif" );?>"><?php } ?>
												</td>
											</tr>
									<?php } // Fim lcto ordinario
								
									} // Fim foreach totais
								
								// Calcula o total Gerado pela conta no período
								// $dbtVlrTotalConta = ($dblTotalEntrada + getValue($objRSSldIni,"saldo_ini")) - $dblTotalSaida;
								
								// PROCEDURE PARA LOCALIZAR
								// SALDO ANTERIOR DA CONTA DO
								// ROW CORRENTE
								try{
									$strSQL   	 = "SELECT * FROM sp_saldo_ac_diario(".getValue($objRSConta,"cod_conta").",'".dateAdd('d',1,$dtDateFim)."') AS saldo_fim";
									$objSaldoFim = $objConn->query($strSQL);
									$objRSSldFim = $objSaldoFim->fetch();
								}catch(PDOException $e){
									mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
									die();
								}
								
								
								// Exibe ou nao a Linha de Total de entradas~saídas
								if($dblTotalEntrada > 0 || $dblTotalSaida > 0){
									$dblTotalParcial = $dblTotalEntrada - $dblTotalSaida;
								?>
								<!-- SALDOS TOTAIS E NEGATIVOS / POSITIVOS -->
								<tr bgcolor="#EEEEEE" height="22" valign="middle">
									<td colspan="3" style="padding-left:10px;"></td>
									<td class="padrao_gde" align="right"><?php echo(getTText("subtotais",C_NONE));?>:</td>
									<td class="field_numeric" style="color:green;font-weight:bold;"><?php echo(number_format((double) $dblTotalEntrada,2,',','.'));?></td>
									<td class="field_numeric" style="color:red;font-weight:bold;  "><?php echo(number_format((double) $dblTotalSaida,2,',','.'));?></td>
									<td colspan="2"></td>
								</tr>
								<tr bgcolor="#EEEEEE" height="22" valign="middle">
									<td colspan="3" style="padding-left:10px;"></td>
									<td class="padrao_gde" align="right"><?php echo(getTText("total_parcial",C_NONE));?>:</td>
									<td class="field_numeric" style="color:<?php echo(($dblTotalParcial < 0) ? "red" : "green")?>;font-weight:bold;"><?php echo(($dblTotalParcial < 0 ) ? "-".number_format((double) $dblTotalParcial,2,',','.') : number_format((double) $dblTotalParcial,2,',','.'));?></td>
									<td colspan="2"></td>
								</tr>
								<?php }?>
								<tr bgcolor="#EEEEEE" height="22" valign="middle">
									<td colspan="2" style="padding-left:10px;"></td>
									<td class="padrao_gde" colspan="2" align="right"><?php echo(getTText("saldo_final",C_NONE));?>:</td>
									<td class="field_numeric" width="111"><strong><?php echo(number_format((double) getValue($objRSSldFim,"saldo_fim"),2,',','.'));?></strong></td>
									<td colspan="3"></td>
								</tr>
								<tr>
								<tr><td colspan="8" height="3"></td></tr>
								<tr><td colspan="8" height="3" bgcolor="#BFBFBF"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<tr><td colspan="8" height="6"></td></tr>
								<?php 
									// Seta as variáveis de controle de valores
									// totais de entrada / saída para 0 novamen
									$dblTotalEntrada = 0;
									$dblTotalSaida   = 0;
									$dblVlrPlConta 	 = 0;
									$dblTotalGeral   = $dblTotalGeral + getValue($objRSSldFim,"saldo_fim"); //getValue($objRSSldFim,"saldo_fim")
								}
								if($intContaBanco == ""){
								?>
								<tr height="22" valign="middle">
									<td colspan="2" style="padding-left:10px;"></td>
									<td class="padrao_gde" align="right" colspan="2"><?php echo(getTText("total_geral",C_NONE));?>:</td>
									<td class="field_numeric" ><strong><?php echo(($dblTotalGeral >= 0) ? number_format((double) $dblTotalGeral,2,',','.') : "");?></strong></td>
									<td class="field_numeric"><strong><?php echo(($dblTotalGeral >= 0) ?  "" : number_format((double) $dblTotalGeral,2,',','.'));?></strong></td>
									<td colspan="2"></td>
								</tr>
								<?php }?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php athEndWhiteBox(); ?>
</center>
 </body>
</html>
<?php
	ob_end_flush();
	$objConn = NULL;
	// Se a grade tem uma ação default e ela retornou apenas uma 
	// linha ela dispara a ação default - Atenção: usamos índice 
	// rows[2] devido ao leiaute da grade que tem duas linhas antes 
	// dos dados (barra e cabeçalho)
	/*
	if ($intLinha == 1) {
	<script language="JavaScript" type="text/javascript">
		if(strDefaultAction != ""){
			objTableDummy = document.getElementById("tableContent");
			location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
		}
	</script>
	*/
?>