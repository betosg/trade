<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
		
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
		$strSQL  = " SELECT cod_conta, cod_conta||' - '||nome AS nome FROM fin_conta WHERE dtt_inativo IS NULL ";
		$strSQL .= ($intContaBanco == "") ? "" : " AND cod_conta = ".$intContaBanco;
		$strSQL .= " ORDER BY cod_conta";
		//die($strSQL);
		$objResultConta = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// Variáveis UTILIZADAS para Saldo
	// Também variáveis utilizadas para
	// print de linha e COD_TITULO AGRUPADOR
	$dblSaldoIniContas 	   = 0;
	$dblSaldoFimContas     = 0;
	$dblTOTALGERALENTRADAPLANOSCONTA = 0;
	$dblTOTALGERALSAIDAPLANOSCONTA   = 0;
	$dblVlrEntra        = 0;
	$dblVlrSaida        = 0;
	$dblVlrSaidaPlConta = 0;
	$dblVlrEntraPlConta = 0;
	$dblVlrJuros		= 0;
	$dblVlrDesc			= 0;
	$intCodTituloAgp    = "";
	$strOutput 		    = "";
	$dblVlrLcto			= 0;

	
	// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
		// $dblVlrPlConta      = 0;
		// $dblEntradasPlConta = 0;
		// $dblSaidasPlConta   = 0;
		// $dblTotalGeral      = 0;
		// $dblDescPlConta     = 0;
		// $dblJurosPlConta    = 0;
		// $dbtVlrTotalConta   = 0;
		// $dblVlrDiferenca    = 0;
		// $dblVlrDiff		    = 0;
		// $intCodTitulo       = "";
		// $intCodPlConta      = "";
	// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
	
	// Busca TODAS AS CONTAS CADASTRADAS
	try{
		$strSQL = "SELECT cod_conta FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY cod_conta"; // $strSQL .= ($intContaBanco == "") ? "" : " AND cod_conta = ".$intContaBanco;
		$objResultConta = $objConn->query($strSQL); //die($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	foreach($objResultConta as $objRSConta){
		// Soma o saldo INICIAL DE TODAS AS CONTAS
		try{
			$strSQL   	    = "SELECT * FROM sp_saldo_ac_diario(".getValue($objRSConta,"cod_conta").",'".$dtDateInicio."') AS saldo_ini";
			$objResultSaldo = $objConn->query($strSQL);
			$objRSSaldo     = $objResultSaldo->fetch();
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
			die();
		}
		$dblSaldoIniContas = $dblSaldoIniContas + getValue($objRSSaldo,"saldo_ini");
	} // Fim Foreach soma SALDO INICIAL
		
	// Inicialização de Cores para FORM
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		return($prColor);
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
									<th height="22" width="15%" class="titulo_grade" style="font-weight:normal;text-align:right;"><?php echo(getTText("cod_reduzido",C_UCWORDS)); ?></th>
									<th height="22" width="55%" class="titulo_grade" style="font-weight:normal;text-align:left;"  ><?php echo(getTText("nome_pl_conta",C_UCWORDS)); ?></th>
									<th height="22" width="14%" class="titulo_grade" style="font-weight:normal;text-align:right;" ><?php echo(getTText("valor_entrada",C_UCWORDS)); ?></th>
									<th height="22" width="15%" class="titulo_grade" style="font-weight:normal;text-align:right;" ><?php echo(getTText("valor_saida",C_UCWORDS)); ?></th>
									<th height="22" width="01%"></th>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
									</tr>
									<tr><td colspan="5" height="3"></td></tr>
								</thead>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
							
								<!-- LINHA COM NOME DA CONTA BANCO + SALDO INICIAL NO PERIODO SELECIONADO -->
								<tr bgcolor="#EEEEEE" height="22" valign="middle">
									<td class="padrao_gde" colspan="2" align="right"><?php echo(getTText("saldo_inicial_contas",C_NONE));?>:</td>
									<td class="field_numeric"><strong><?php echo(($dblSaldoIniContas >= 0) ? number_format((double) $dblSaldoIniContas,2,',','.') : "");?></strong></td>
									<td class="field_numeric"><strong><?php echo(($dblSaldoIniContas <  0) ? number_format((double) $dblSaldoIniContas,2,',','.') : "");?></strong></td>
									<td colspan="3"></td>
								</tr>
								<!-- LINHA COM NOME DA CONTA BANCO + SALDO INICIAL NO PERIODO SELECIONADO -->
								
								<?php 
									// BUSCA TODAS AS CONTAS BANCO
									/*try{
										$strSQL = "SELECT cod_conta FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY cod_conta"; // $strSQL .= ($intContaBanco == "") ? "" : " AND cod_conta = ".$intContaBanco;
										$objResultConta = $objConn->query($strSQL); //die($strSQL);
									}catch(PDOException $e){
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
										die();
									}
									
									foreach($objResultConta as $objRSConta){*/
										
										
										// BUSCA TODOS OS PLANOS DE CONTA SELECIONADOS
										try{
											$strSQL  = " SELECT fin_plano_conta.cod_plano_conta ";
											$strSQL .= " , fin_plano_conta.cod_reduzido ";
											$strSQL .= " , fin_plano_conta.nome";
											$strSQL .= " FROM fin_plano_conta ";
											$strSQL .= " WHERE dtt_inativo IS NULL ";
											$strSQL .= ($intPlanoConta == "") ? "" : " AND fin_plano_conta.cod_plano_conta = ".$intPlanoConta;
											$strSQL .= " ORDER BY fin_plano_conta.cod_reduzido, fin_plano_conta.cod_plano_conta_pai";
											//die($strSQL);
											$objResultPlConta     = $objConn->query($strSQL);
											$objResultPlContaSinc = $objConn->query($strSQL);
										}catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
											die();
										}									
										
										// Loop de dados com base no plano de conta
										// para cada volta no LOOP, calcula-se o valor
										// somado para o plano de conta em relação aos
										// LCTOS ordinarios, TÍTULOS agrupados ou LCTOS
										// em conta
										foreach($objResultPlConta as $objRSPlConta){
											$dblENTRADAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")] = 0;
											$dblSAIDAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")]   = 0;
											$dblSAIDAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")]   = (!isset($dblSAIDAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")])) ? 0 : $dblSAIDAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")];
											$dblENTRADAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")] = (!isset($dblENTRADAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")])) ? 0 : $dblENTRADAPLANOCONTAPAI[getValue($objRSPlConta,"cod_plano_conta")];
											
											// BUSCA TODOS OS LANÇAMENTOS EM CONTA / 
											// ORDINARIOS PARA DETERMINADA CONTA OU
											// DETERMINADO Plano de Conta
											
											// Buscando todos os LCTOS EM CONTA DA
											// OPERAÇÃO 'receita'
											try{
												$strSQL  = " SELECT SUM(fin_lcto_em_conta.vlr_lcto) AS valor_total";
												$strSQL .= " FROM fin_lcto_em_conta ";
												$strSQL .= " INNER JOIN fin_plano_conta ON (fin_lcto_em_conta.cod_plano_conta = fin_plano_conta.cod_plano_conta AND fin_plano_conta.cod_plano_conta = ".getValue($objRSPlConta,"cod_plano_conta").") ";
												//$strSQL .= " INNER JOIN fin_conta ON (fin_conta.cod_conta = fin_lcto_em_conta.cod_conta AND fin_conta.cod_conta = ".getValue($objRSConta,"cod_conta").") ";
												$strSQL .= " WHERE fin_lcto_em_conta.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
												$strSQL .= " AND fin_lcto_em_conta.operacao = 'receita'";
												//die($strSQL);
												$objResultEmContaR = $objConn->query($strSQL);
											}catch(PDOException $e){
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
												die();
											}
											// Soma o valor encontrado com o Total de Saída / Entrada do Plano de Conta Atual
											$objRSEmContaR	    = $objResultEmContaR->fetch();
											$dblTotalReceita	= (getValue($objRSEmContaR,"valor_total") == "") ? 0 : getValue($objRSEmContaR,"valor_total");
											$dblVlrEntraPlConta = $dblVlrEntraPlConta + $dblTotalReceita;
												
																					
											// Buscando todos os LCTOS EM CONTA DA
											// OPERAÇÃO 'despesa'
											try{
												$strSQL  = " SELECT SUM(fin_lcto_em_conta.vlr_lcto) AS valor_total";
												$strSQL .= " FROM fin_lcto_em_conta ";
												$strSQL .= " INNER JOIN fin_plano_conta ON (fin_lcto_em_conta.cod_plano_conta = fin_plano_conta.cod_plano_conta AND fin_plano_conta.cod_plano_conta = ".getValue($objRSPlConta,"cod_plano_conta").") ";
												//$strSQL .= " INNER JOIN fin_conta ON (fin_conta.cod_conta = fin_lcto_em_conta.cod_conta AND fin_conta.cod_conta = ".getValue($objRSConta,"cod_conta").") ";
												$strSQL .= " WHERE fin_lcto_em_conta.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
												$strSQL .= " AND fin_lcto_em_conta.operacao = 'despesa'";
												//die($strSQL);
												$objResultEmContaD = $objConn->query($strSQL);
											}catch(PDOException $e){
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
												die();
											}
											$objRSEmContaD	    = $objResultEmContaD->fetch();
											$dblTotalDespesa	= (getValue($objRSEmContaD,"valor_total") == "") ? 0 : getValue($objRSEmContaD,"valor_total");
											$dblVlrSaidaPlConta = $dblVlrSaidaPlConta + $dblTotalDespesa;
											
												
																						
											// Buscando LANÇAMENTOS DE TITULOS UM PARA UM
											// NAO PODENDO ESTAR AGRUPADO, OU SEJA, SER UM
											// TITULO PAI. CASO SEJA, CAI NA CLAUSULA SEGUINTE
											try{
												$strSQL  = " SELECT fin_lcto_ordinario.vlr_lcto";
												$strSQL .= " , fin_lcto_ordinario.vlr_juros";
												$strSQL .= " , fin_lcto_ordinario.vlr_desc";
												$strSQL .= " , fin_conta_pagar_receber.pagar_receber";
												$strSQL .= " , fin_lcto_ordinario.cod_conta_pagar_receber";
												$strSQL .= " , fin_lcto_ordinario.cod_plano_conta";
												$strSQL .= " FROM fin_lcto_ordinario ";
												$strSQL .= " INNER JOIN fin_conta_pagar_receber ON (fin_lcto_ordinario.cod_conta_pagar_receber = fin_conta_pagar_receber.cod_conta_pagar_receber)";
												//$strSQL .= " INNER JOIN fin_conta ON (fin_conta.cod_conta = fin_lcto_ordinario.cod_conta AND fin_conta.cod_conta = ".getValue($objRSConta,"cod_conta").") ";
												$strSQL .= " INNER JOIN fin_plano_conta ON (fin_plano_conta.cod_plano_conta = fin_lcto_ordinario.cod_plano_conta AND fin_plano_conta.cod_plano_conta = ".getValue($objRSPlConta,"cod_plano_conta").") ";
												$strSQL .= " WHERE fin_lcto_ordinario.dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' ";
												$strSQL .= " AND (fin_conta_pagar_receber.situacao = 'lcto_parcial' OR fin_conta_pagar_receber.situacao = 'lcto_total') ";
												$strSQL .= " AND fin_lcto_ordinario.cod_conta_pagar_receber NOT IN (SELECT DISTINCT cod_agrupador FROM fin_conta_pagar_receber WHERE cod_agrupador IS NOT NULL)";
												//die($strSQL);
												$objResultOrdinario = $objConn->query($strSQL);
											}catch(PDOException $e){
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
												die();
											}
											
											// Loop dos LCTOS UM PARA UM
											foreach($objResultOrdinario as $objRSOrd){
												// Clcula entrada ou saida conforme o TIPO de CONTA A APAGAR RECEBER
												$dblVlrEntraPlConta = (getValue($objRSOrd,"pagar_receber") == FALSE) ? ($dblVlrEntraPlConta + ((getValue($objRSOrd,"vlr_lcto") + getValue($objRSOrd,"vlr_juros")) - getValue($objRSOrd,"vlr_desc"))) : ($dblVlrEntraPlConta);
												$dblVlrSaidaPlConta = (getValue($objRSOrd,"pagar_receber") == TRUE ) ? ($dblVlrSaidaPlConta + ((getValue($objRSOrd,"vlr_lcto") + getValue($objRSOrd,"vlr_juros")) - getValue($objRSOrd,"vlr_desc"))) : ($dblVlrSaidaPlConta);
											}
											
											
																					
											// Buscando TITULOS AGRUPADOS QUE NAO FORAM BUSCADOS ACIMA
											// Descobrindo TITULOS 'PAI' PARA DEPOIS FETCH. Alguns casos:
											//
											// TITULO AGRUPADO COM LCTO PARCIAL (2 LCTO + JUROS) - WORKING!
											// TITULO AGRUPADO COM LCTO PARCIAL (2 LCTO + DESC)  -
											// TITULO AGRUPADO COM LCTO PARCIAL (2 LCTO)         - WORKING!
											// TITULO AGRUPADO COM LCTO PARCIAL (1 LCTO)         - WORKING!
											// TITULO AGRUPADO COM LCTO PARCIAL (1 LCTO + JUROS) - WORKING!
											// TITULO AGRUPADO COM LCTO PARCIAL (1 LCTO + DESC)  - WORKING!
											//
											// TITULO AGRUPADO COM LCTO_TOTAL   (1 LCTO)         - WORKING!
											// TITULO AGRUPADO COM LCTO_TOTAL   (1 LCTO + JUROS) - WORKING!
											// TITULO AGRUPADO COM LCTO_TOTAL   (1 LCTO + DESC)  - WORKING!
											try{
												$strSQL  = " SELECT fin_conta_pagar_receber.cod_conta_pagar_receber ";
												$strSQL .= " , fin_conta_pagar_receber.vlr_conta ";
												$strSQL .= " , fin_conta_pagar_receber.cod_agrupador ";
    											$strSQL .= " , fin_conta_pagar_receber.pagar_receber ";
												$strSQL .= " , fin_conta_pagar_receber.cod_plano_conta ";
												$strSQL .= " , (SELECT pai.situacao FROM fin_conta_pagar_receber pai WHERE pai.cod_conta_pagar_receber = fin_conta_pagar_receber.cod_agrupador ) AS pai_situacao";
												$strSQL .= " FROM fin_conta_pagar_receber ";
												$strSQL .= " WHERE fin_conta_pagar_receber.cod_plano_conta = ".getValue($objRSPlConta,"cod_plano_conta");
												$strSQL .= " AND fin_conta_pagar_receber.situacao = 'agrupado' ";
												$strSQL .= " AND fin_conta_pagar_receber.cod_agrupador ";
												$strSQL .= " IN ( SELECT cod_conta_pagar_receber FROM fin_lcto_ordinario WHERE cod_conta_pagar_receber IS NOT NULL ";
												$strSQL .= " AND dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."' )"; //AND cod_conta = ".getValue($objRSConta,"cod_conta").")";
												$strSQL .= " AND fin_conta_pagar_receber.cod_agrupador ";
												$strSQL .= " IN ( SELECT cod_conta_pagar_receber FROM fin_conta_pagar_receber WHERE cod_conta_pagar_receber IS NOT NULL ";
												$strSQL .= " AND situacao = 'lcto_total' OR situacao = 'lcto_parcial') ORDER BY fin_conta_pagar_receber.cod_agrupador ";
												//die($strSQL);
												$objResultTitsAgrupados = $objConn->query($strSQL);
											}catch(PDOException $e){
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
												die();
											}
											
											// Loop dos LCTOS agrupados
											// para buscar DESCONTO E JUROS
											foreach($objResultTitsAgrupados as $objRSAgp){
												// Busca valor De LCTO AGRUPADO - JUROS E DESCONTO
												if(!isset($intCodTituloAgp[getValue($objRSAgp,"cod_agrupador")])){//$intCodTituloAgp == getValue($objRSAgp,"cod_agrupador")){
													try{
														$strSQL  = " SELECT DISTINCT fin_lcto_ordinario.cod_lcto_ordinario, fin_lcto_ordinario.vlr_desc, fin_lcto_ordinario.vlr_lcto, fin_conta_pagar_receber.situacao, fin_lcto_ordinario.vlr_juros ";
														$strSQL .= "      , fin_plano_conta.cod_reduzido, fin_plano_conta.nome, fin_lcto_ordinario.cod_plano_conta, fin_conta_pagar_receber.cod_conta_pagar_receber, fin_conta_pagar_receber.cod_agrupador ";
														$strSQL .= " FROM fin_lcto_ordinario ";
 														$strSQL .= " INNER JOIN fin_plano_conta ON (fin_plano_conta.cod_plano_conta = fin_lcto_ordinario.cod_plano_conta) ";
														$strSQL .= " INNER JOIN fin_conta_pagar_receber ON (fin_conta_pagar_receber.cod_conta_pagar_receber = fin_lcto_ordinario.cod_conta_pagar_receber) ";
														$strSQL .= " WHERE fin_lcto_ordinario.cod_conta_pagar_receber = ".getValue($objRSAgp,"cod_agrupador");
														$strSQL .= " ORDER BY fin_conta_pagar_receber.cod_agrupador ";
														//die($strSQL);
														$objResultAgrupado = $objConn->query($strSQL);
														//$objRSValores = $objResultAgrupado->fetch();
													}catch(PDOException $e){
														mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
														die();
													}
													
													// Foreach do RESULTSET ACIMA, QUE PODE LOCALIZAR MAIS DE UM LANÇAMENTO
													foreach($objResultAgrupado as $objRSValores){
													
														// Adicionando os valores de Desconto e Juros
														// E TAMBÉM VLR_LCTO CASO O TITULO SEJA LCTO_PARC
														if (getValue($objRSAgp,"pagar_receber") == TRUE) {
															// Soma o valor do lançamento caso a situacao
															// do TITULO AGRUPADO seja LCTO_PARCIAL
															$dblVlrLcto    = getValue($objRSValores,"vlr_lcto");
															$dblVlrEntrada = getValue($objRSValores,"vlr_desc");
															$dblVlrSaida   = getValue($objRSValores,"vlr_juros");
															if(!isset($dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")])){ $dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = 0; }
															if(!isset($dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")])){   $dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")]   = 0; }
															$dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = ($dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrEntrada);
															$dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")]   = ($dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrSaida);
															if(getValue($objRSValores,"situacao") == "lcto_parcial"){ $dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = $dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrLcto; }
															$dblVlrLcto = 0;
															$dblVlrSaida = 0;
															$dblVlrEntrada = 0;
														}
														else {
															// Soma o valor do lançamento caso a situacao
															// do TITULO AGRUPADO seja LCTO_PARCIAL
															$dblVlrLcto    = getValue($objRSValores,"vlr_lcto");
															$dblVlrEntrada = getValue($objRSValores,"vlr_juros");
															$dblVlrSaida = getValue($objRSValores,"vlr_desc");
															// DEBUG ***
															// echo "cod_lcto ".getValue($objRSValores,"cod_lcto_ordinario")."<br/>";
															// echo "cod_conta_pagar ".getValue($objRSValores,"cod_conta_pagar_receber")."<br/>";
															// echo "cod_agrupador ".getValue($objRSAgp,"cod_agrupador")."<br/>";
															// echo "lcto ".$dblVlrLcto."<br/>";
															// echo "juros ".$dblVlrEntrada."<br/>";
															// echo "desc ".$dblVlrSaida."<br/>";
															// echo "<br/><br/>";
															// Como é possível múltiplas tuplas serem retornadas,
															// e cada filho ser do mesmo plano de conta, então
															// tratamos para que caso o plano de conta DOS AGRUPADOS
															// já tenha sido utilizado, não inicializamos o array em
															// sua posição com valor 0
															if(!isset($dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")])){ $dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = 0; }
															if(!isset($dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")])){   $dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")]   = 0; }
															$dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = ($dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrEntrada);
															$dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")]   = ($dblSAIDAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrSaida);
															if(getValue($objRSValores,"situacao") == "lcto_parcial"){ $dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] = $dblENTRADAPLANOCONTAPAI[getValue($objRSValores,"cod_plano_conta")] + $dblVlrLcto; }
															$dblVlrLcto = 0;
															$dblVlrSaida = 0;
															$dblVlrEntrada = 0;
														}
													}
													
													// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //													
														/* cleverson update
														//$dblVlrEntraPlConta = (getValue($objRSAgp,"pagar_receber") == FALSE) ? (($dblVlrEntraPlConta + (getValue($objRSAgp,"vlr_conta") + $dblVlrJuros)) - $dblVlrDesc) : ($dblVlrEntraPlConta);
														//$dblVlrSaidaPlConta = (getValue($objRSAgp,"pagar_receber") == TRUE ) ? (($dblVlrSaidaPlConta + (getValue($objRSAgp,"vlr_conta") + $dblVlrJuros)) - $dblVlrDesc) : ($dblVlrSaidaPlConta);
														//$dblSAIDAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")]   = $dblENTRADAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")] + $dblVlrSaidaPlConta; 
														//---------------------------------
														//$dblVlrEntra = ($dblVlrEntraPlConta > 0) ? (($dblVlrEntraPlConta + $dblVlrJuros) - $dblVlrDesc) : ($dblVlrEntra);
														//$dblVlrSaida = ($dblVlrSaidaPlConta > 0) ? (($dblVlrSaidaPlConta + $dblVlrJuros) - $dblVlrDesc) : ($dblVlrSaida);
														//$strOutput = "
														//	<tr bgcolor='".getLineColor($strColor)."' onMouseOver='intCurrentPosMouse = this.rowIndex;navigateRow(event);'>
														//		<td class='field_numeric' style='text-align:right;'>".getValue($objRSValores,"cod_reduzido")."</td>
														//		<td class='field_text'    style='text-align:left;'  >".getValue($objRSValores,"nome")."</td>
														//		<td class='field_numeric' style='text-align:right;'>".(($dblVlrEntrada > 0) ? number_format((double) $dblVlrEntrada,2,',','.') : "")."</td>
														//		<td class='field_numeric' style='text-align:right;'>".(($dblVlrSaida > 0) ? number_format((double) $dblVlrSaida,2,',','.') : "")."</td>
														//		<td style='text-align:center;'></td>
														//	</tr>";
														//echo($strOutput);
														//---------------------------------
														cleverson update */
													// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
													
												}
												// Passa o COD_AGRUPADOR para a VARIAVEL que
												// será testada na próxima volta do foreach
												$intCodTituloAgp[getValue($objRSAgp,"cod_agrupador")] = TRUE;//getValue($objRSAgp,"cod_agrupador");
												
												// Caso a situacao do titulo PAI seja LCTO_TOTAL aí
												// sim fazemos a soma com o valor total deste plano
												// de conta, caso contrário o valor do titulo filho
												// talvez nao tenha sido completo com o valor do lcto
												// parcial do pai
												if(getValue($objRSAgp,"pai_situacao") == "lcto_total"){
													$dblVlrEntraPlConta = (getValue($objRSAgp,"pagar_receber") == FALSE) ? (($dblVlrEntraPlConta + (getValue($objRSAgp,"vlr_conta") + $dblVlrJuros)) - $dblVlrDesc) : ($dblVlrEntraPlConta);
													$dblVlrSaidaPlConta = (getValue($objRSAgp,"pagar_receber") == TRUE ) ? (($dblVlrSaidaPlConta + (getValue($objRSAgp,"vlr_conta") + $dblVlrJuros)) - $dblVlrDesc) : ($dblVlrSaidaPlConta);
												}
											}// Fim Foreach TITS AGRUPADOS
											
											// Calcula o VALOR TOTAL DO PLANO DE CONTA CORRENTE
											// O QUE É FEITO: SOMA-SE TODOS OS VALORES DE LANÇAMENTOS
											// QUE POSTERIORMENTE SÃO POSTOS NO ARRAY DE VALORES DE
											// LANÇAMENTOS. Nos casos onde o TITULO AGRUPADO POSSUI
											// JUROS ENTAO BUSCAMOS NO LANÇAMENTO DO TITULO PAI O VALOR
											// DO JUROS E DO DESCONTO, PORÉM PODE VIR EM ORDEM NAO SEQUENCIAL
											// A DO PLANO DE CONTAS QUE ESTÁ SENDO FETCH AQUI. COLOCAMOS EM
											// UM SEGUNDO ARRAY DE DESCONTOS E JUROS PARA O PLANO DE CONTA
											// 'X', QUE POSTERIORMENTE SERÁ EMPARELHADO COM O PLANO
											// DE CONTA IGUAL AO DO AGRUPADO QUE POSSUI JUROS E DESCONTO
											$dblENTRADAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")] = $dblENTRADAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")] + $dblVlrEntraPlConta;
											$dblSAIDAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")]   = $dblSAIDAPLANOCONTA[getValue($objRSPlConta,"cod_plano_conta")] + $dblVlrSaidaPlConta;
											
											// Zera Totais para o próximo PL CONTA
											$dblVlrEntraPlConta = 0;
											$dblVlrSaidaPlConta = 0;
											$dblVlrDesc 		= 0;
											$dblVlrJuros 		= 0;
											
											
											// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
												// Efetua somatório para exibição geral POSTERIOR
												// $dblEntradasPlConta	= $dblEntradasPlConta + $dblVlrEntraPlConta;
												// $dblSaidasPlConta	= $dblSaidasPlConta   + $dblVlrSaidaPlConta;
												// $dblJurosPlConta	= $dblJurosPlConta    + $dblVlrJuros;
												// $dblDescPlConta		= $dblDescPlConta     + $dblVlrDesc;
												// $dblVlrEntra = 0;
												// $dblVlrSaida = 0;
											// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //

											
										} // Fim Foreach Plano de Contas
										
										
									//}	
										foreach($objResultPlContaSinc as $objRSPlContaSinc){
											// GERA O SOMATORIO 'green' ou 'red' 
											// que aparece após o LISTAGEM DOS PLANOS DE CONTA.
											$dblTOTALGERALENTRADAPLANOSCONTA = $dblTOTALGERALENTRADAPLANOSCONTA + ($dblENTRADAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblENTRADAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")]);
											$dblTOTALGERALSAIDAPLANOSCONTA   = $dblTOTALGERALSAIDAPLANOSCONTA +  ($dblSAIDAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblSAIDAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")]);
											
											// Só PRINTA A LINHA COM O PLANO DE CONTA
											// CASO ALGUM VALOR DE LCTO, JUROS OU DESC
											// TENHA ENTRADO PARA ESTA LINHA.
											if(($dblENTRADAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] > 0) || ($dblSAIDAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] > 0) || ($dblENTRADAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")] > 0) || ($dblSAIDAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")] > 0)){
												$strOutput = "
													<tr bgcolor='".getLineColor($strColor)."' onMouseOver='intCurrentPosMouse = this.rowIndex;navigateRow(event);'>
														<td class='field_numeric' style='text-align:right;'>".getValue($objRSPlContaSinc,"cod_reduzido")."</td>
														<td class='field_text'    style='text-align:left;'  >".getValue($objRSPlContaSinc,"nome")."</td>
														<td class='field_numeric' style='text-align:right;'>".((($dblENTRADAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblENTRADAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")]) > 0) ? number_format((double) $dblENTRADAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblENTRADAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")],2,',','.') : "")."</td>
														<td class='field_numeric' style='text-align:right;'>".((($dblSAIDAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblSAIDAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")]) > 0) ? number_format((double) $dblSAIDAPLANOCONTA[getValue($objRSPlContaSinc,"cod_plano_conta")] + $dblSAIDAPLANOCONTAPAI[getValue($objRSPlContaSinc,"cod_plano_conta")],2,',','.') : "")."</td>
														<td style='text-align:center;'></td>
													</tr>";
												echo($strOutput);
											}
										} // Fim Foreach Planos de CONTA PARA SINCRONISMO DE LCTOS UM PARA UM E AGRUPADOS
																			
										// Calcula o VALOR TOTAL DE TRANSFERENCIAS OCORRIDAS NO 
										// PERÍODO, PARA QUE O SALDO TOTAL DAS CONTAS BATA CORRETAMENTE
										try{
											$strSQL = "SELECT SUM(vlr_lcto) AS total_transf FROM fin_lcto_transf WHERE dt_lcto BETWEEN '".$dtDateInicio."' AND '".$dtDateFim."'";
											$objResultTransf = $objConn->query($strSQL);
											$objRSTransf	 = $objResultTransf->fetch();
											$dblVlrTransf	 = getValue($objRSTransf,"total_transf");
										}catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
											die();
										}
										if($dblVlrTransf > 0){
											$strOutput = "
												<tr bgcolor='".getLineColor($strColor)."' onMouseOver='intCurrentPosMouse = this.rowIndex;navigateRow(event);'>
													<td class='field_numeric' style='text-align:right;'> - </td>
													<td class='field_text'    style='text-align:left;' >".getTText("transferencias_entre_contas",C_TOUPPER)."</td>
													<td class='field_numeric' style='text-align:right;'>".number_format((double) $dblVlrTransf,2,',','.')."</td>
													<td class='field_numeric' style='text-align:right;'>".number_format((double) $dblVlrTransf,2,',','.')."</td>
													<td style='text-align:center;'></td>
												</tr>";
											echo($strOutput);
										}
										
										// Soma o valor TOTAL DE TRANSFERENCIAS
										$dblTOTALGERALENTRADAPLANOSCONTA = $dblTOTALGERALENTRADAPLANOSCONTA + $dblVlrTransf;
										$dblTOTALGERALSAIDAPLANOSCONTA   = $dblTOTALGERALSAIDAPLANOSCONTA   + $dblVlrTransf;
										
										
										// Calcula o SUBTOTAL GERADO pelas contas no período - CAMPO 'TOTAL PARCIAL'
										$dblParcialPlContas   = $dblTOTALGERALENTRADAPLANOSCONTA - $dblTOTALGERALSAIDAPLANOSCONTA;
										
										// Calcula o total Gerado pelas contas no período - CAMPO 'SALDO FINAL'
										$dblVlrTotalPlContas  = $dblParcialPlContas + $dblSaldoIniContas;
										
										
										// Caso o SALDO PARCIAL, TOTAL DE ENTRADAS ('GREEN') ou TOTAL DE SAÍDAS('RED') seja maior que zero, então printa							
										if(($dblParcialPlContas > 0) || ($dblTOTALGERALENTRADAPLANOSCONTA > 0) || ($dblTOTALGERALSAIDAPLANOSCONTA > 0)){ ?>		
											<!-- SALDOS TOTAIS E NEGATIVOS / POSITIVOS -->
											<!-- 'SALDO GREEN' e 'SALDO RED' -->
											<tr bgcolor="#EEEEEE" height="22" valign="middle">
												<td style="padding-left:10px;"></td>
												<td class="padrao_gde" align="right"><?php echo(getTText("subtotais",C_NONE));?>:</td>
												<td class="field_numeric" style="color:green;font-weight:bold;  "><?php echo(number_format((double) $dblTOTALGERALENTRADAPLANOSCONTA,2,',','.'));?></td>
												<td class="field_numeric" style="color:red;font-weight:bold;    "><?php echo(number_format((double) $dblTOTALGERALSAIDAPLANOSCONTA,2,',','.'));?></td>
												<td colspan="2"></td>
											</tr>
										
											<!-- 'GREEN' - 'RED' -->
											<tr bgcolor="#EEEEEE" height="22" valign="middle">
												<td style="padding-left:10px;"></td>
												<td class="padrao_gde" align="right">  <?php echo(getTText("total_parcial",C_NONE));?>:</td>
												<td class="field_numeric" style="color:<?php echo(($dblParcialPlContas < 0) ? "red" : "green")?>;font-weight:bold;"><?php echo(($dblParcialPlContas < 0 ) ? "-".number_format((double) $dblParcialPlContas,2,',','.') : number_format((double) $dblParcialPlContas,2,',','.'));?></td>
												<td colspan="3"></td>
											</tr>
										<?php } // Fim total de ENTRADAS, SALDO PARCIAL ou SAÍDAS ?>
										<tr><td colspan="8" height="3"></td></tr>
										<tr><td colspan="8" height="3" bgcolor="#BFBFBF"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
										<tr><td colspan="8" height="6"></td></tr>
								<?php 
										// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
											// Seta as variáveis de controle de valores
											// totais de entrada / saída para 0 novamen
											// $dblVlrPlConta 	    = 0;
											// $dblEntradasPlConta  = 0;
											// $dblSaidasPlConta    = 0;
											// $dblJurosPlConta	    = 0;
											// dblDescPlConta		= 0;
											// $dblTotalGeral       = $dblTotalGeral + $dbtVlrTotalConta; //getValue($objRSSldFim,"saldo_fim")
										// *************  OLD: NÃO MAIS UTILIZADO EM FUNÇÃO DOS ARRAYS  ************* //
									//}
									
									// Calcula o Saldo Final DE TODAS AS CONTAS
									try{
										$strSQL = "SELECT cod_conta FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY cod_conta"; // $strSQL .= ($intContaBanco == "") ? "" : " AND cod_conta = ".$intContaBanco;
										$objResultConta = $objConn->query($strSQL); //die($strSQL);
									}catch(PDOException $e){
										mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
										die();
									}
									foreach($objResultConta as $objRSConta){
										// Soma o saldo INICIAL DE TODAS AS CONTAS
										try{
											$strSQL   	    = "SELECT * FROM sp_saldo_ac_diario(".getValue($objRSConta,"cod_conta").",'".dateAdd('d',1,$dtDateFim)."') AS saldo_ini";
											$objResultSaldo = $objConn->query($strSQL);
											$objRSSaldo     = $objResultSaldo->fetch();
										}catch(PDOException $e){
											mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
											die();
										}
										$dblSaldoFimContas = $dblSaldoFimContas + getValue($objRSSaldo,"saldo_ini");
									}
									?>
								
								<!-- RODAPÉ TOTAL GERAL DAS CONTAS BANCO -->
								<tr height="22" valign="middle">
									<td style="padding-left:10px;"></td>
									<td class="padrao_gde" align="right"><?php echo(getTText("saldo_final_contas",C_NONE));?>:</td>
									<td class="field_numeric"><strong><?php echo(($dblSaldoFimContas >= 0) ? number_format((double) $dblSaldoFimContas,2,',','.') : "");?></strong></td>
									<td colspan="3"></td>
								</tr>
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
	$objConn = NULL;
	ob_end_flush();
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