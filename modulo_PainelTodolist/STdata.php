<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodDado    	 = request("var_chavereg");			 // COD_TAREFA
	$intCodAtividade = request("var_cod_atividade");	 // COD_ATIVIDADE / BS
	$intLimitTarefas = request("var_limit_tarefas");
	$strSituacao	 = request("var_situacao_tarefa");
	$strTipoUsuario	 = request("var_tipo_tarefa_usuario");
	$strTipoExibicao = request("var_tipo_exibicao");
	
	$strSesPfx   = strtolower(str_replace("modulo_","",basename(getcwd())));
		
	// DEFINES
	@define("DATA_ICONES_NUM"     ,4);     // NÚMERO DE ÍCONES DA GRADE
	@define("DATA_ICONES_WIDTH"   ,17);    // LARGURA DOS ÍCONES DA GRADE
	@define("DATA_GRADE_NUM_ITENS",20);    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
	@define("DATA_GRADE_ACAO_DEFAULT",""); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
	@define("DATA_LIMIT_DEFAULT",25); 	 // LIMIT DEFAULT DA CONSULTA
	
	// TRATAMENTO PARA ENVIO VAZIO DE VARIAVEIS NO REQUEST
	$intLimitTarefas = ($intLimitTarefas == "") ? DATA_LIMIT_DEFAULT : $intLimitTarefas;
	$strTipoUsuario	 = ($strTipoUsuario  == "") ? "executor" : $strTipoUsuario;
	$strSituacao	 = ($strSituacao 	 == "") ? "aberto"   : $strSituacao;
	 
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL LOCALIZA TAREFAS POR ORDEM DE ATIVIDADE
	// O FOCO DO PAINEL É EXIBIR TAREFAS, ENTÃO FAZEMOS
	// LAÇO DE TAREFAS E QUANDO A ATIVIDADE FOR ALTERADA
	// ADICIONAMOS UMA NOVA LINHA
	$objConn->beginTransaction();
	try{
		// LOCALIZA O APANHADO DE TAREFAS
		$strSQL = "
			SELECT 
				  tl_todolist.cod_todolist
				, tl_todolist.cod_atividade
				, UPPER(tl_categoria.nome) AS categoria
				, tl_todolist.prev_dt_ini
				, tl_todolist.prev_hr_ini
				, tl_todolist.titulo
				, tl_todolist.id_responsavel
				, tl_todolist.id_ult_executor
				, tl_todolist.prev_horas
				, CASE 
				  WHEN situacao = 'aberto' THEN 
					  '<img src=''../img/icon_situacao_aberto.png'' alt=''ABERTO'' title=''ABERTO''>'
				  WHEN situacao = 'executando' THEN
					  '<img src=''../img/icon_situacao_executando.png'' alt=''EXECUTANDO'' title=''EXECUTANDO''>'
				  WHEN situacao = 'fechado' THEN
					  '<img src=''../img/icon_situacao_fechado.png'' alt=''FECHADO'' title=''FECHADO''>'
				  END AS situacao_grid 
				, CASE
				  WHEN prioridade = 'baixa' THEN 
					  '<img src=''../img/icon_prioridade_baixa.png'' alt='' PRIORIDADE BAIXA'' title=''PRIORIDADE BAIXA''>'
				  WHEN prioridade = 'normal' THEN 
					  '<img src=''../img/icon_prioridade_normal.png'' alt='' PRIORIDADE NORMAL'' title=''PRIORIDADE NORMAL''>'
				  WHEN prioridade = 'media' THEN 
					  '<img src=''../img/icon_prioridade_media.png'' alt='' PRIORIDADE MEDIA'' title=''PRIORIDADE MEDIA''>'
				  WHEN prioridade = 'alta' THEN 
					  '<img src=''../img/icon_prioridade_alta.png'' alt='' PRIORIDADE ALTA'' title=''PRIORIDADE ALTA''>'
				  END AS prioridade_grid
			FROM tl_todolist 
			LEFT JOIN tl_categoria ON (tl_todolist.cod_categoria = tl_categoria.cod_categoria) 
			WHERE 1 = 1 ";
		$strSQL .= ($strSituacao != "") ? " AND situacao = '".$strSituacao."'" : "";
		$strSQL .= ($strTipoUsuario == "responsavel") ? " AND (id_responsavel  = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."')" : "";
		$strSQL .= ($strTipoUsuario == "executor")    ? " AND (id_ult_executor = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."')" : "";
		$strSQL .= ($strTipoUsuario == "equipe")      ? " AND ('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_usuario FROM bs_equipe WHERE cod_atividade = tl_todolist.cod_atividade))" : "";
		/*
		 $strSQL .= (($strTipoUsuario == "todos") && (getValue($objRS,"id_responsavel") == getsession(CFG_SYSTEM_NAME."_id_usuario"))) ? "" : "";
		 $strSQL .= (($strTipoUsuario == "todos") && (getValue($objRS,"id_responsavel") != getsession(CFG_SYSTEM_NAME."_id_usuario"))) ? " AND (id_responsavel = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' OR id_ult_executor = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."')" : "";
		*/
		
		$strSQL .= " ORDER BY prev_dt_ini, prev_hr_ini ASC ";
		$strSQL .= ($intLimitTarefas != "") ? " LIMIT ".$intLimitTarefas." OFFSET 0" : ""; 
		
		$objResult = $objConn->query($strSQL);
		
		// COMMIT TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// INICIALIZAÇÃO DE VAR PARA TROCA DE COR EM LINHA
	$strColor = CL_CORLINHA_1;
	
	// FUNCTION PARA TROCAR COR DE LINHAS
	function retLineColor(&$prColor,$prReturn=false){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		if($prReturn){
			return($prColor);
		} else{
			echo($prColor);
		}
	}
		
	// MONTA TODAS AS LINHAS DE TAREFAS EM UM ARRAY
	$arrTarefas  = array();
	
	foreach($objResult as $objRS){
		// TESTA SE A TAREFA NAO TEM ATIVIDADE
		// OBS: TAREFAS SEM ATIVIDADE TEM INDEX '0' NO ARRAY
		// GERAL DE TAREFAS E ATIVIDADES QUE SERÁ
		// EXIBIDO NA STdata.php
		$auxCurrentPos = (getValue($objRS,"cod_atividade") == "") ? 1 : getValue($objRS,"cod_atividade");
		if(!isset($arrTarefas[$auxCurrentPos])){
			$arrTarefas[$auxCurrentPos] = "";
		}
		$strColorT = "";
		$strColorT = (date("Y-m-d") >  getValue($objRS,"prev_dt_ini")) ? "#FFC0CB" : $strColorT;
		$strColorT = (date("Y-m-d") == getValue($objRS,"prev_dt_ini")) ? "#FFFACD" : $strColorT;
		$strColorT = (date("Y-m-d") <  getValue($objRS,"prev_dt_ini")) ? "#E6E6FA" : $strColorT;
		$arrTarefas[$auxCurrentPos] .= '
			<tr bgcolor="'.$strColorT.'">
				<td style="padding:2px;"><img src="../img/icon_write.gif"     border="0" style="cursor:pointer;" onclick="AbreJanelaPAGE(\'../modulo_Todolist/STupdtarefa.php?var_chavereg='.getValue($objRS,"cod_todolist").'&var_cod_atividade='.getValue($objRS,"cod_atividade").'&var_flag_bg=TRUE&var_js_action=window.close();\',\'800\',\'700\');" /></a></td> <!-- EDIÇÃO DA TAREFA -->
				<td style="padding:2px;"><img src="../img/icon_respostas.gif" border="0" style="cursor:pointer;" onclick="AbreJanelaPAGE(\'../modulo_Todolist/STifrrespostas.php?var_chavereg='.getValue($objRS,"cod_todolist").'\',\'800\',\'700\');" /></td> <!-- RESPOSTAS DA TAREFA --> 
				<td style="padding:2px;"><img src="../img/icon_confirmar.gif" border="0" style="cursor:pointer;" onclick="AbreJanelaPAGE(\'../modulo_Todolist/STfinalizartarefa.php?var_chavereg='.getValue($objRS,"cod_todolist").'&var_cod_atividade='.getValue($objRS,"cod_atividade").'&var_flag_bg=TRUE&var_js_action=window.close();\',\'800\',\'700\');" /></td> <!-- FINALIZAR TAREFA --> 
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"cod_todolist").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false).'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"prev_hr_ini").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"categoria").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"titulo").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"id_responsavel").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"id_ult_executor").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"prev_horas").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"situacao_grid").'</td>
				<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"prioridade_grid").'</td>
			</tr>';
	}

?>
<script type="text/javascript" language="javascript">
	var intCurrentPos = 1;
	var intCurrentPosMouse;
	var strDefaultAction = "<?php echo(DATA_GRADE_ACAO_DEFAULT); ?>"; 
	var intTotalPaginas = parseInt("<?php echo(DATA_GRADE_NUM_ITENS); ?>");

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
</script>
<center>
<?php athBeginWhiteBox("100%"); ?>
<form name="formstatic" action="<?php echo($_SERVER['PHP_SELF']);?>" method="post">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="padrao_gde" align="left" width="20%" nowrap="nowrap" valign="top"><a href="../modulo_Todolist/" target="<?php echo(CFG_SYSTEM_NAME."_frmain");?>"><b><?php echo(getTText("painel_de_tarefas_todolist",C_UCWORDS)); ?></b></a></td>
			<td align="right" valign="top" width="99%">
				<!--
				<span style="border-right:2px dotted #CCC;margin-right:20px;padding-right:05px;">
				<php echo(getTText("exibindo",C_NONE))?>
				<select name="var_tipo_exibicao" id="var_tipo_exibicao" style="width:130px;" onChange="document.formstatic.submit();">
					<option value="tarefas" <php echo(($strTipoExibicao == "tarefas") ? "selected=\"selected\"" : "");?> ><php echo(getTText("tarefas",C_NONE));?></option>
					<option value="tarefas_com_atividades" <php echo((($strTipoExibicao == "tarefas_com_atividades") || ($strTipoExibicao == "")) ? "selected=\"selected\"" : "");?> ><php echo(getTText("tarefas_com_atividades",C_NONE));?></option>
					<option value="todos" <php echo(($strTipoExibicao == "todos") ? "selected=\"selected\"" : "");?> ><php echo(getTText("todos",C_NONE));?></option>
				</select>
				</span>
				-->
				<?php echo(getTText("ultimas",C_NONE))?>
				<select name="var_limit_tarefas" id="var_limit_tarefas" style="width:50px;" onChange="document.formstatic.submit();">
				<?php for($auxCounter = 25; $auxCounter <= 150; $auxCounter++){?>
				<?php if(($auxCounter % 25) == 0){?>
					<option value="<?php echo($auxCounter);?>" <?php echo(($intLimitTarefas == $auxCounter) ? "selected=\"selected\"" : "");?>><?php echo($auxCounter);?></option>
				<?php }?>
				<?php }?>
				</select>
				<?php echo(getTText("tarefas",C_NONE))?>
				<select name="var_situacao_tarefa" id="var_situacao_tarefa" style="width:80px;" onChange="document.formstatic.submit();">
					<option value="aberto" <?php echo((($strSituacao == "aberto") || ($strSituacao == "")) ? "selected=\"selected\"" : "");?>><?php echo(getTText("abertas",C_NONE));?></option>
					<option value="executando" <?php echo(($strSituacao == "executando") ? "selected=\"selected\"" : "");?>><?php echo(getTText("executando",C_NONE));?></option>
					<option value="fechado" <?php echo(($strSituacao == "fechado") ? "selected=\"selected\"" : "");?>><?php echo(getTText("fechadas",C_NONE));?></option>
					<!--option value="" <php echo(($strSituacao == "") ? "selected=\"selected\"" : "");?>><php echo(getTText("todos",C_NONE));?></option-->
				</select>
				<?php echo(getTText("onde_sou",C_NONE))?>
				<select name="var_tipo_tarefa_usuario" id="var_tipo_tarefa_usuario" style="width:90px;" onChange="document.formstatic.submit();">
					<option value="executor" <?php echo((($strTipoUsuario == "executor") || ($strTipoUsuario == "")) ? "selected=\"selected\"" : "");?>><?php echo(getTText("executor",C_NONE));?></option>
					<option value="responsavel" <?php echo(($strTipoUsuario == "responsavel") ? "selected=\"selected\"" : "");?>><?php echo(getTText("responsavel",C_NONE));?></option>
					<option value="equipe" <?php echo(($strTipoUsuario == "equipe") ? "selected=\"selected\"" : "");?>><?php echo(getTText("equipe",C_NONE));?></option>
					<!-- option value="todos" <php echo(($strTipoUsuario == "todos") ? "selected=\"selected\"" : "");?>><php echo(getTText("todos",C_NONE));?></option -->
				</select>
				&nbsp;
				<span>
					<img src="../img/icon_update_page.png" border="0" style="cursor:pointer;" onclick="document.location.reload();" title="<?php echo(getTText("atualizar_pagina",C_NONE));?>" />
				</span>
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
								<!-- VARRE ARRAY DE TAREFAS COLOCANDO INFORMAÇÕES DE ATIVIDADES ANTES E CABEÇALHO -->
								<?php 
									for($auxCounter = 1; $auxCounter <= count($arrTarefas); $auxCounter++){
										if(key($arrTarefas) == 1){
											$arrTarefas[1] = '
												<tr>
													<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
													<td width="01%"></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
													<td class="titulo_grade" width="01%" nowrap></td><!-- CODIGO -->
													<td class="titulo_grade" width="40%" nowrap></td><!-- TITULO -->
													<td class="titulo_grade" width="20%" nowrap></td><!-- PERIODO -->
													<td class="titulo_grade" width="10%" nowrap></td><!-- RENDIMENTO -->
													<td class="titulo_grade" width="05%" nowrap></td><!-- PREV HORAS -->
													<td class="titulo_grade" width="01%" nowrap></td><!-- CLIENTE ICON -->
													<!-- CABEÇALHO DA GRADE - [FIM] -->
												</tr>
												<tr><td colspan="7" height="3"></td></tr>
												
												<tr bgcolor="'.retLineColor($strColor,true).'" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
													<td>
														<table border="0" cellspacing="0" cellpadding="0" width="'.(DATA_ICONES_WIDTH * DATA_ICONES_NUM).'">
															<tr>
																<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_tree_minus.gif" border="0" style="cursor:pointer;" id="img_bs_atividade_0" onClick="showArea(\'table_bs_atividade_0\',\'img_bs_atividade_0\');" /></td>
																<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_zoom_off.gif" border="0" title="'.getTText("visualizar",C_NONE).'" /></td>
																<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_tarefas_off.gif" border="0" title="'.getTText("tarefas",C_NONE).'" /></td>
																<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_equipe_off.gif" border="0" title="'.getTText("equipe",C_NONE).'" /></td>
															</tr>
														</table>
													</td>
													<td height="22" colspan="6" align="left" style="padding:0px 0px 0px 10px;">'.getTText("atividade_geral_tarefas_sem_atividades",C_TOUPPER).'</td>
												</tr>
												<tr>
													<td></td>
													<td colspan="6">
														<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_bs_atividade_0" >
														<!-- CABEÇALHO TAREFAS -->
														<tr height="20">
															<td width="01%"></td> <!-- EDIÇÃO DA TAREFA -->
															<td width="01%"></td> <!-- RESPOSTAS DA TAREFA --> 
															<td width="01%"></td> <!-- FINALIZAR TAREFA --> 
															<td class="titulo_grade" width="01%" nowrap>'.getTText("cod_todolist",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("prev_dt_ini",C_NONE).'</td>
															<td class="titulo_grade" width="05%" nowrap>'.getTText("prev_hr_ini",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("categoria",C_NONE).'</td>
															<td class="titulo_grade" width="30%" nowrap>'.getTText("titulo",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("id_responsavel",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("id_ult_executor",C_NONE).'</td>
															<td class="titulo_grade" width="05%" nowrap>'.getTText("prev_horas",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("situacao_grid",C_NONE).'</td>
															<td class="titulo_grade" width="10%" nowrap>'.getTText("prioridade_grid",C_NONE).'</td>
														</tr>
														<tr bgcolor="#CCCCCC" height="2"><td colspan="13"></td></tr>
														'.$arrTarefas[1].'
													</table>
													</td>
												</tr>
												<tr><td colspan="12" height="30"></td></tr>';
										} else{
											// LOCALIZA OS DADOS DA ATIVIDADE COM BASE NO CODIGO DO TODO ENCAMINHADO
											$objConn->beginTransaction();
											try{
												$strSQL = "
													SELECT 
														  bs_atividade.cod_atividade
														, bs_atividade.id_responsavel
														, CASE
														  WHEN bs_atividade.tipo = 'cad_pf' THEN
														  (SELECT cad_pf.nome FROM cad_pf WHERE cod_pf = bs_atividade.codigo)  
														  WHEN bs_atividade.tipo = 'cad_pj' THEN
														  (SELECT cad_pj.razao_social FROM cad_pj WHERE cod_pj = bs_atividade.codigo) 
														  WHEN bs_atividade.tipo = 'cad_pj_fornec' THEN
														  (SELECT cad_pj_fornec.razao_social FROM cad_pj_fornec WHERE cod_pj_fornec = bs_atividade.codigo) 
														  END AS cliente
														, bs_atividade.codigo
														, bs_atividade.tipo
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
													WHERE bs_atividade.cod_atividade = ".key($arrTarefas);
												/*
												$strSQL .= ($strTipoUsuario == "todos") ? " AND (('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' = bs_atividade.id_responsavel) OR ('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_usuario FROM bs_equipe WHERE cod_atividade = bs_atividade.cod_atividade)) OR ('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_responsavel FROM tl_todolist WHERE cod_atividade = bs_atividade.cod_atividade)) OR	('".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_ult_executor FROM tl_todolist WHERE cod_atividade = bs_atividade.cod_atividade)))" : "";
												$strSQL .= ($strTipoUsuario == "equipe") ? " AND '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' IN (SELECT id_usuario FROM bs_equipe WHERE cod_atividade = bs_atividade.cod_atividade)" : "";	
												$strSQL .= ($intCodDado != "") ? " AND bs_atividade.cod_atividade = ".$intCodDado : "";
												$strSQL .= ($intCategoria != "") ? " AND bs_atividade.cod_categoria = ".$intCategoria : "";
												$strSQL .= ($strSituacao != "") ? " AND bs_atividade.situacao = '".$strSituacao."'" : "";
												$strSQL .= ($strPrioridade != "") ? " AND bs_atividade.prioridade = '".$strPrioridade."'" : "";
												$strSQL .= ($strIDResponsavel != "") ? " AND bs_atividade.id_responsavel = '".$strIDResponsavel."'" : "";
												$strSQL .= ($strTitulo != "") ? " AND bs_atividade.titulo ILIKE '".$strTitulo."%'" : "";
												$strSQL .= (($dtPeriodoMin != "") && ($dtPeriodoMax == "")) ? " AND tl_todolist.prev_dt_ini = '".$dtPeriodoMin."'" : "";
												$strSQL .= (($dtPeriodoMin == "") && ($dtPeriodoMax != "")) ? " AND tl_todolist.prev_dt_ini = '".$dtPeriodoMax."'" : "";
												$strSQL .= (($dtPeriodoMin != "") && ($dtPeriodoMax != "")) ? " AND tl_todolist.prev_dt_ini BETWEEN '".$dtPeriodoMin."' AND '".$dtPeriodoMax."'" : "";
												*/
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
												// echo($strSQL);
												$objResult = $objConn->query($strSQL);
												$objRS     = $objResult->fetch();
												
												// COMMIT TRANSAÇÃO
												$objConn->commit();
											}catch(PDOException $e){
												$objConn->rollBack();
												mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
												die();
											}
										
										// MONTA O CABEÇALHO COM A ATIVIDADE CORRENTE
										$arrTarefas[key($arrTarefas)] = '
											<tr>
												<!-- CABEÇALHO DA GRADE - [INÍCIO] -->
												<td width="01%"></td> <!-- Coloca uma coluna mesclada para ajustar a tabela com os ícones que virão abaixo -->
												<td class="titulo_grade" width="01%" nowrap></td><!-- CODIGO -->
												<td class="titulo_grade" width="40%" nowrap></td><!-- TITULO -->
												<td class="titulo_grade" width="20%" nowrap></td><!-- PERIODO -->
												<td class="titulo_grade" width="10%" nowrap></td><!-- RENDIMENTO -->
												<td class="titulo_grade" width="05%" nowrap></td><!-- PREV HORAS -->
												<td class="titulo_grade" width="01%" nowrap></td><!-- CLIENTE ICON -->
												<!-- CABEÇALHO DA GRADE - [FIM] -->
											</tr>
											<tr><td colspan="7" height="3"></td></tr>
											<tr bgcolor="'.retLineColor($strColor,true).'" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);">
												<td>
													<table border="0" cellspacing="0" cellpadding="0" width="'.(DATA_ICONES_WIDTH * DATA_ICONES_NUM).'">
														<tr>
															<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_tree_minus.gif" border="0" style="cursor:pointer;" id="img_bs_atividade_'.getValue($objRS,"cod_atividade").'" onClick="showArea(\'table_bs_atividade_'.getValue($objRS,"cod_atividade").'\',\'img_bs_atividade_'.getValue($objRS,"cod_atividade").'\');" /></td>
															<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_zoom.gif" border="0" title="'.getTText("visualizar",C_NONE).'" onclick="AbreJanelaPAGE(\'../modulo_BsAtividade/STvieatividade.php?var_chavereg='.getValue($objRS,"cod_atividade").'&var_close=TRUE\',\'800\',\'600\');" style="cursor:pointer;" /></td>
															<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_tarefas.gif" border="0" title="'.getTText("tarefas",C_NONE).'" onclick="AbreJanelaPAGE(\'../modulo_BsAtividade/STifrtarefas.php?var_chavereg='.getValue($objRS,"cod_atividade").'\',\'800\',\'700\');" style="cursor:pointer;" /></td>
															<td width="'.DATA_ICONES_WIDTH.'"><img src="../img/icon_equipe.gif" border="0" title="'.getTText("equipe",C_NONE).'" onclick="AbreJanelaPAGE(\'../modulo_BsAtividade/STequipe.php?var_chavereg='.getValue($objRS,"cod_atividade").'\',\'500\',\'200\');" style="cursor:pointer;" /></td>
														</tr>
													</table>
												</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"cod_atividade").'</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"titulo").'</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getTText("de_grid",C_NONE).' '.dDate(CFG_LANG,getValue($objRS,"de_grid"),false).' '.getTText("ate_grid",C_NONE).' '.dDate(CFG_LANG,getValue($objRS,"ate_grid"),false).'</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;">'.getValue($objRS,"rendimento",false).'</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;cursor:default;" title="'.getTText("total_de_horas_previstas",C_NONE).'">'.getValue($objRS,"prev_horas").'</td>
												<td height="22" align="left" style="padding:0px 0px 0px 10px;">
													<span style="float:left"><img src="../img/icon_cliente_atividade.gif" border="0" style="cursor:pointer" title="'.getTText("dados_do_cliente",C_NONE).'" onclick="AbreJanelaPAGE(\'STshowdadoscliente.php?var_chavereg='.getValue($objRS,"codigo").'&var_tipo='.getValue($objRS,"tipo").'\',\'800\',\'600\');" /></span>
													<span><img src="../img/icon_responsavel.gif" border="0" title="'.getTText("responsavel",C_TOUPPER).': '.getValue($objRS,"id_responsavel").'" /></span>
												</td>
											</tr>
											<tr>
												<td></td>
												<td colspan="6">
													<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_bs_atividade_'.getValue($objRS,"cod_atividade").'" >
													<!-- CABEÇALHO TAREFAS -->
													<tr height="20">
														<td width="01%"></td> <!-- EDIÇÃO DA TAREFA -->
														<td width="01%"></td> <!-- RESPOSTAS DA TAREFA --> 
														<td width="01%"></td> <!-- FINALIZAR TAREFA --> 
														<td class="titulo_grade" width="01%" nowrap>'.getTText("cod_todolist",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("prev_dt_ini",C_NONE).'</td>
														<td class="titulo_grade" width="05%" nowrap>'.getTText("prev_hr_ini",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("categoria",C_NONE).'</td>
														<td class="titulo_grade" width="30%" nowrap>'.getTText("titulo",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("id_responsavel",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("id_ult_executor",C_NONE).'</td>
														<td class="titulo_grade" width="05%" nowrap>'.getTText("prev_horas",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("situacao_grid",C_NONE).'</td>
														<td class="titulo_grade" width="10%" nowrap>'.getTText("prioridade_grid",C_NONE).'</td>
													</tr>
													<tr bgcolor="#CCCCCC" height="2"><td colspan="13"></td></tr>
													'.$arrTarefas[key($arrTarefas)].'
												</table>
												</td>
											</tr>
											<tr><td colspan="12" height="30"></td></tr>';
										}
										
										// PRINTA O RESULTADO EM TELA
										echo($arrTarefas[key($arrTarefas)]);
										
										// MOVE O PONTEIRO DO ARRAY EM UM
										next($arrTarefas);
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
	</table>
</form>
<?php athEndWhiteBox(); ?>
</center>