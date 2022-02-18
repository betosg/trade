<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	 
	$objConn = abreDBConn(CFG_DB); // Abertura de banco
	
	/***            VERIFICAÇÃO DE ACESSO              ***/
	/*****************************************************/
	$strSesPfx 	   = strtolower(str_replace("modulo_","",basename(getcwd())));          //Carrega o prefixo das sessions
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente
	
	
	/***           DEFINIÇÃO DE CONSTANTES             ***/
	/*****************************************************/
	define("ICONES_NUM"     ,4);     // NÚMERO DE ÍCONES DA GRADE
	define("ICONES_WIDTH"   ,18);    // LARGURA DOS ÍCONES DA GRADE
	define("GRADE_NUM_ITENS",20);    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
	define("GRADE_ACAO_DEFAULT",""); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
	
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$strOrderCol      = request("var_order_column");   // Índice da coluna para ordenação
	$strOrderDir      = request("var_order_direct");   // Direção da ordenação (ASC ou DESC)
	$intNumCurPage    = request("var_curpage");        // Página corrente
	$strAcao   	      = request("var_acao");           // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
	$strSQLParam      = request("var_sql_param");      // Parâmetro com o SQL vindo do bookmark
	$strPopulate      = request("var_populate");       // Flag de verificação se necessita popular o session ou não
	$intCodDado 	  = request("var_cod_atividade");
	$intCategoria	  = request("var_cod_categoria");
	$strSituacao      = request("var_situacao");
	$strPrioridade 	  = request("var_prioridade");
	$strTitulo		  = request("var_titulo");
	$strIDResponsavel = request("var_id_responsavel");
	$dtPeriodoMin	  = request("var_periodo_min");
	$dtPeriodoMax	  = request("var_periodo_max");
	
	
	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
		
	
	/***        AÇÃO DE ALIMENTAÇÃO DA GRADE         ***/
	/***************************************************/
	// $strSQL = getsession($strSesPfx . "_select_orig");
	try{
		$strSQL = "
			SELECT 
				  bs_atividade.cod_atividade
				, CASE
				  WHEN bs_atividade.tipo = 'cad_pf' THEN
				  (SELECT cad_pf.nome FROM cad_pf WHERE cod_pf = bs_atividade.codigo)  
				  WHEN bs_atividade.tipo = 'cad_pj' THEN
				  (SELECT cad_pj.razao_social FROM cad_pj WHERE cod_pj = bs_atividade.codigo) 
				  WHEN bs_atividade.tipo = 'cad_pj_fornec' THEN
				  (SELECT cad_pj_fornec.razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = bs_atividade.codigo) 
				  END AS cliente
				, bs_categoria.nome AS categoria
				, MIN(tl_todolist.prev_dt_ini) AS de_grid
				, MAX(tl_todolist.prev_dt_ini) AS ate_grid
				, (SELECT out_previsao_horas FROM spi_previsao_horas_atividade(bs_atividade.cod_atividade)) AS prev_horas
				, (SELECT out_bar_progress FROM spi_gera_bar_progresso_atividade(bs_atividade.cod_atividade)) AS rendimento
				, bs_atividade.titulo 
				, CASE 
				  WHEN bs_atividade.situacao = 'aberto' THEN 
					  '<img src=''../img/icon_situacao_aberto.png'' alt=''ABERTO'' title=''ABERTO''>'
				  WHEN bs_atividade.situacao = 'executando' THEN 
					  '<img src=''../img/icon_situacao_executando.png'' alt=''EXECUTANDO'' title=''EXECUTANDO''>'
				  WHEN bs_atividade.situacao = 'fechado' THEN 
					  '<img src=''../img/icon_situacao_fechado.png'' alt=''FECHADO'' title=''FECHADO''>'
				  END AS situacao_grid 
				, CASE
				  WHEN bs_atividade.prioridade = 'baixa' THEN 
					  '<img src=''../img/icon_prioridade_baixa.png'' alt='' PRIORIDADE BAIXA'' title=''PRIORIDADE BAIXA''>'
				  WHEN bs_atividade.prioridade = 'normal' THEN 
					  '<img src=''../img/icon_prioridade_normal.png'' alt='' PRIORIDADE NORMAL'' title=''PRIORIDADE NORMAL''>'
				  WHEN bs_atividade.prioridade = 'media' THEN 
					  '<img src=''../img/icon_prioridade_media.png'' alt='' PRIORIDADE MEDIA'' title=''PRIORIDADE MEDIA''>'
				  WHEN bs_atividade.prioridade = 'alta' THEN 
					  '<img src=''../img/icon_prioridade_alta.png'' alt='' PRIORIDADE ALTA'' title=''PRIORIDADE ALTA''>'
				  END AS prioridade_grid 
				, '<img src=''../img/icon_responsavel.gif'' alt=''RESPONSÁVEL: '||bs_atividade.id_responsavel||''' title=''RESPONSÁVEL: '||bs_atividade.id_responsavel||'''>' AS responsavel_grid 
			FROM bs_atividade 
			LEFT JOIN bs_categoria ON (bs_categoria.cod_categoria = bs_atividade.cod_categoria)  
			LEFT JOIN tl_todolist  ON (tl_todolist.cod_atividade = bs_atividade.cod_atividade) 
			WHERE 1 = 1 
			AND ('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_usuario FROM bs_equipe WHERE cod_atividade = bs_atividade.cod_atividade) OR bs_atividade.id_responsavel = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."')";
			
			$strSQL .= ($intCodDado != "") ? " AND bs_atividade.cod_atividade = ".$intCodDado : "";
			$strSQL .= ($intCategoria != "") ? " AND bs_atividade.cod_categoria = ".$intCategoria : "";
			$strSQL .= ($strSituacao != "") ? " AND bs_atividade.situacao = '".$strSituacao."'" : "";
			$strSQL .= ($strPrioridade != "") ? " AND bs_atividade.prioridade = '".$strPrioridade."'" : "";
			$strSQL .= ($strIDResponsavel != "") ? " AND bs_atividade.id_responsavel = '".$strIDResponsavel."'" : "";
			$strSQL .= ($strTitulo != "") ? " AND bs_atividade.titulo ILIKE '".$strTitulo."%'" : "";
			$strSQL .= (($dtPeriodoMin != "") && ($dtPeriodoMax == "")) ? " AND tl_todolist.prev_dt_ini = '".$dtPeriodoMin."'" : "";
			$strSQL .= (($dtPeriodoMin == "") && ($dtPeriodoMax != "")) ? " AND tl_todolist.prev_dt_ini = '".$dtPeriodoMax."'" : "";
			$strSQL .= (($dtPeriodoMin != "") && ($dtPeriodoMax != "")) ? " AND tl_todolist.prev_dt_ini BETWEEN '".$dtPeriodoMin."' AND '".$dtPeriodoMax."'" : "";
			
			$strSQL .= " 
			GROUP BY 
				 bs_atividade.cod_atividade
			   , bs_atividade.codigo
			   , bs_categoria.nome
			   , bs_atividade.tipo
			   , bs_atividade.titulo
			   , bs_atividade.prioridade
			   , bs_atividade.id_responsavel
			   , bs_atividade.situacao";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	$intTotalRegistros = 20;
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_2;
		
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
  <head>
	<title><?php echo(CFG_SYSTEM_TITLE);?></title>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css">
  	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script language="JavaScript" type="text/javascript">
		var intCurrentPos = 1;
		var intCurrentPosMouse;
		var strDefaultAction = "<?php echo(GRADE_ACAO_DEFAULT); ?>"; 
		var intTotalPaginas = parseInt("<?php echo(GRADE_NUM_ITENS); ?>");

		function aplicarFuncao(prValue) {
			if(prValue != "") {
				location.href = prValue;
			}
		}
		
		function setOrderBy(prStrOrder,prStrDirect) {
			location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
		}
		
		function paginar(prPagina){
			if(prPagina > 0 && prPagina <= intTotalPaginas){
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
		
		document.onkeydown = navigateRow;
	
		function collapseItem(prCodBookmark){
			if(document.getElementById("bookmark_" + prCodBookmark).style.display == "block"){
				document.getElementById("bookmark_" + prCodBookmark).style.display = "none";
				document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_close.gif";
			}
			else{
				document.getElementById("bookmark_" + prCodBookmark).style.display = "block";
				document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_open.gif";
			}
		}
		
			var allTrTags = new Array();
			var detailTrFrameAnt = '';
			var moduloDetailAnt = '';
			function showDetailGrid(prChave_reg,prLink, prField){
		 
				if(prLink.indexOf("?") == -1){
					strConcactQueryString = "?"
				}else{
					strConcactQueryString = "&"
				}
				var detailTr = document.getElementById("detailtr_"+prChave_reg).style.display;
				if(detailTr == 'none'){
					 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"tradeunion_detailiframe_"+prChave_reg);
			
					var allTrTags  = document.getElementsByTagName("tr");
					for( i=0; i < allTrTags.length; i++){
						if(allTrTags[i].className == 'iframe_detail'){
							allTrTags[i].style.display = 'none';
						}
					}
					document.getElementById("detailtr_"+prChave_reg).style.display = '';
				}else{
					if( moduloDetailAnt == prLink){
							document.getElementById("detailtr_"+prChave_reg).style.display = 'none';
					}else{
						if(detailTrFrameAnt != "detailtr_"+prChave_reg ){
							 SetIFrameSource(prLink+strConcactQueryString+'var_field_detail='+prField+'&var_chavereg='+prChave_reg,"tradeunion_detailiframe_"+prChave_reg);
						}
					}
		 
				}
				moduloDetailAnt = prLink;
			}
		 
			function ativaMenu(){
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
	</script>
  </head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<center>
<?php athBeginWhiteBox("98%"); ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="padrao_gde" align="left" width="50%" valign="top"><b><?php echo(getTText(getsession($strSesPfx . "_titulo"),C_NONE)); ?></b></td>
			<td align="right">
				<button onClick="location.href='STinsatividade.php';"><?php echo(getTText("inserir",C_NONE));?></button>
				<button onClick="location.href='STinsatividademodelo.php';"><?php echo(getTText("inserir_do_modelo",C_NONE));?></button>
			</td>
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
									<!--td height="22">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="1%">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr><td><a href="javascript:setOrderBy('0','ASC');"><img src="../img/gridlnkASC.gif"  border="0" align="absmiddle"></a></td></tr>
														<tr><td><a href="javascript:setOrderBy('0','DESC');"><img src="../img/gridlnkDESC.gif" border="0" align="absmiddle"></a></td></tr>
													</table>
												</td>
											</tr>
										</table>
									</td-->
									<td class="titulo_grade" width="01%" nowrap><?php echo(getTText("cod_atividade",C_NONE));?></td>
									<td class="titulo_grade" width="25%" nowrap><?php echo(getTText("cliente",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("categoria",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("de_grid",C_NONE));?></td>
									<td class="titulo_grade" width="10%" nowrap><?php echo(getTText("ate_grid",C_NONE));?></td>
									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("prev_horas",C_NONE));?></td>
									<td class="titulo_grade" width="01%" nowrap><?php echo(getTText("rendimento",C_NONE));?></td>
									<td class="titulo_grade" width="30%" nowrap><?php echo(getTText("titulo",C_NONE));?></td>
									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("situacao_grid",C_NONE));?></td>
									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("prioridade_grid",C_NONE));?></td>
									<td class="titulo_grade" width="05%" nowrap><?php echo(getTText("responsavel_grid",C_NONE));?></td>
									<!-- CABEÇALHO DA GRADE - [FIM] -->
								</tr>
								<tr><td colspan="12" height="3"></td></tr>
								<!-- CONTEÚDO DA GRADE - [INÍCIO] -->
								<?php foreach($objResult as $objRS){?>
								<tr bgcolor="<?php echo(getLineColor($strColor)); ?>" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
									<td width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
										<table border="0" cellspacing="0" cellpadding="0" width="<?php echo(ICONES_WIDTH * ICONES_NUM); ?>">
											<tr>
												<td width="<?php echo(ICONES_WIDTH)?>"><a href="../modulo_BsAtividade/STupdatividade.php?var_chavereg=<?php echo(getValue($objRS,"cod_atividade"));?>&var_location=../modulo_BsAtividade/STindex.php" ><img src="../img/icon_write.gif" border="0" title="<?php echo(getTText("editar",C_NONE));?>" /></a></td>
												<td width="<?php echo(ICONES_WIDTH)?>"><a href="../modulo_BsAtividade/STvieatividade.php?var_chavereg=<?php echo(getValue($objRS,"cod_atividade"));?>&var_location=../modulo_BsAtividade/STindex.php" ><img src="../img/icon_zoom.gif" border="0" title="<?php echo(getTText("visualizar",C_NONE));?>" /></a></td>
												<td width="<?php echo(ICONES_WIDTH)?>"><a onClick="showDetailGrid('<?php echo(getValue($objRS,"cod_atividade"));?>','../modulo_BsAtividade/STifrtarefas.php?var_chavereg=<?php echo(getValue($objRS,"cod_atividade"));?>','')" style="cursor:pointer"><img src="../img/icon_tarefas.gif" border="0" title="<?php echo(getTText("tarefas",C_NONE));?>" /></a></td>
												<!--td width="<php echo(ICONES_WIDTH)?>"><a onClick="showDetailGrid('<php echo(getValue($objRS,"cod_atividade"));?>','../modulo_BsAtividade/STifrequipe.php?var_chavereg=<php echo(getValue($objRS,"cod_atividade"));?>','')" style="cursor:pointer"><img src="../img/icon_equipe.gif" border="0" title="<php echo(getTText("equipe",C_NONE));?>" /></a></td-->
												<td width="<?php echo(ICONES_WIDTH)?>"><img src="../img/icon_equipe.gif" border="0" title="<?php echo(getTText("equipe",C_NONE));?>" style="cursor:pointer;" onClick="AbreJanelaPAGE('STequipe.php?var_chavereg=<?php echo(getValue($objRS,"cod_atividade"));?>','500','200');" /></td>
											</tr>
										</table>
									</td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"cod_atividade"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"cliente"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"categoria"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"de_grid"),false));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(dDate(CFG_LANG,getValue($objRS,"ate_grid"),false));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"prev_horas"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"rendimento",false));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"titulo"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"situacao_grid"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"prioridade_grid"));?></td>
									<td height="22" align="left" style="padding:0px 0px 0px 10px;"><?php echo(getValue($objRS,"responsavel_grid"));?></td>
								</tr>
								<tr id="detailtr_<?php echo(getValue($objRS,"cod_atividade"));?>" bgColor="#FFFFFF" style="display:none;" class="iframe_detail">
									<td colspan="12"><iframe name="<?php echo(CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_atividade"));?>" id="<?php echo(CFG_SYSTEM_NAME."_detailiframe_".getValue($objRS,"cod_atividade"));?>" width="99%" src="" frameborder="0" scrolling="no"></iframe></td>
								</tr>
								<?php
									}
								?>
								<tr><td colspan="12" height="3"></td></tr>
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
		<?php if(GRADE_NUM_ITENS != 0){ ?>
		<tr>
			<!-- td align="left"><1php echo($intTotalRegistros . " " . getTText("reg_encontrados",C_TOLOWER)); ?></td-->
			<td></td>
			<td align="right">
				<!--table border="0" cellpadding="0" cellspacing="0">
				  <form name="formpaginacao" action="data.php" method="post">
					<input type="hidden" name="var_order_column" value="<php echo($strOrderCol); ?>">
					<input type="hidden" name="var_order_direct" value="<php echo($strOrderDir); ?>">
					<input type="hidden" name="var_cod_dialog_grid" value="<php echo($intCodDialogGrid); ?>">
					<tr>
						<td><img src="../img/grid_arrow_left.gif" onClick="paginar(<1php echo($intNumCurPage - 1)?>)"></td>
						<td style="padding:0px 10px 0px 10px;"><php echo(getTText("pagina",C_TOLOWER)); ?> <input type="text" name="var_curpage" value="<php echo($intNumCurPage)?>" size="3"> <php echo(getTText("de",C_TOLOWER) . " " . $intTotalPaginas); ?></td>
						<td><img src="../img/grid_arrow_right.gif" onClick="paginar(<php echo($intNumCurPage + 1)?>)"></td>
					</tr>
				  </form>
				</table-->
			</td>
		</tr>
		<?php } ?>
	</table>
 <?php athEndWhiteBox(); ?>
</center>
 </body>
</html>
<?php
//Se a grade tem uma ação default e ela retornou apenas uma linha ela dispara a ação default
//Atenção: usamos índice rows[2] devido ao leiaute da grade que tem duas linhas antes dos dados (barra e cabeçalho)
if ($intTotalRegistros == 1) {
?>
<script language="JavaScript" type="text/javascript">
	if(strDefaultAction != ""){
		objTableDummy = document.getElementById("tableContent");
		location.href = strDefaultAction.replace("{0}",objTableDummy.rows[2].cells[1].innerHTML);
	}
</script>
<?php
}
$objConn = NULL;
?>