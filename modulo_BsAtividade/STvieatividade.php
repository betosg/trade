<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	// INICIALIZA MODULO
	initModuloParams(basename(getcwd()));             
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

	// REQUESTS
	$intCodDado    	 = request("var_chavereg");			 // COD_ATIVIDADE / BS
	$strLocation	 = request("var_location");
	$flagClose		 = request("var_close");
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA A ATIVIDADE
	$objConn->beginTransaction();
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
			    , (SELECT out_previsao_horas FROM spi_previsao_horas_atividade(bs_atividade.cod_atividade)) AS prev_horas
				, (SELECT out_bar_progress FROM spi_gera_bar_progresso_atividade(bs_atividade.cod_atividade)) AS rendimento
				, bs_atividade.titulo
				, bs_atividade.situacao
				, bs_categoria.cod_categoria||' - '||bs_categoria.nome AS categoria
				, bs_atividade.prioridade
				, bs_atividade.id_responsavel
				, bs_atividade.descricao
				, CASE 
				  WHEN bs_atividade.modelo = false THEN 'Não'
				  WHEN bs_atividade.modelo = true THEN 'Sim'
				  END as modelo 
				, sys_usuario_responsavel.grp_user AS grupo_responsavel
			FROM  bs_atividade
			LEFT JOIN bs_categoria ON (bs_categoria.cod_categoria = bs_atividade.cod_categoria)
			LEFT JOIN sys_usuario sys_usuario_responsavel ON (sys_usuario_responsavel.id_usuario = bs_atividade.id_responsavel)
			WHERE bs_atividade.cod_atividade = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS 	   = $objResult->fetch();
		
		// SQL LOCALIZA AS TAREFAS
		$strSQL = "
			SELECT 
				  tl_todolist.cod_todolist
				, tl_todolist.titulo
				, tl_todolist.situacao
				, tl_categoria.nome AS categoria
				, tl_todolist.prioridade
				, tl_todolist.id_responsavel
				, tl_todolist.id_ult_executor
				, tl_todolist.prev_dt_ini
				, tl_todolist.prev_hr_ini
				, tl_todolist.prev_horas
				, tl_todolist.arquivo_anexo
				, tl_todolist.descricao
				, sys_usuario_responsavel.grp_user AS grupo_responsavel
				, sys_usuario_executor.grp_user AS grupo_executor
			FROM  tl_todolist
			LEFT JOIN tl_categoria ON (tl_categoria.cod_categoria = tl_todolist.cod_categoria)
			LEFT JOIN sys_usuario sys_usuario_responsavel ON (sys_usuario_responsavel.id_usuario = tl_todolist.id_responsavel)
			LEFT JOIN sys_usuario sys_usuario_executor ON (sys_usuario_executor.id_usuario = tl_todolist.id_ult_executor)
			WHERE tl_todolist.cod_atividade = ".$intCodDado;
		$objResultT = $objConn->query($strSQL);
		
		// SQL LOCALIZA A EQUIPE DA ATIVIDADE
		// LOCALIZA A EQUIPE
		$strEquipe  = "";
		$strSQL = "SELECT id_usuario FROM bs_equipe WHERE dtt_inativo IS NULL AND cod_atividade = ".$intCodDado;
		$objResultE = $objConn->query($strSQL);
		if($objResultE->rowCount() > 0){
			foreach($objResultE as $objRSE){
				$strEquipe .= "; ".getValue($objRSE,"id_usuario");
			}
		}
		
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #CCC;
				border-right: 1px solid #CCC;
				border-bottom:1px solid #CCC;
				border-left:  1px solid #CCC;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
			
			#img_drop_dt_ini{ cursor:pointer; display:none }
						
			#img_drop_dt_fim{ cursor:pointer; display:none }
			
			#lst_dt_ini{ width:250px;height:100px;overflow:scroll;display:none }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				<?php if($flagClose != ""){?>
					window.close();
				<?php } elseif($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					document.location.href = "../modulo_BsAtividade/STdata.php";
				<?php }?>
			}

			function cancelar() {
				<?php if($flagClose != ""){?>
					window.close();
				<?php } elseif($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					document.location.href = "../modulo_BsAtividade/STdata.php";
				<?php }?>
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("bs_atividade",C_UCWORDS)." - (".getTText("visualizacao",C_NONE).")",CL_CORBAR_GLASS_1); ?>
	<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_atividade",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<!-- PARA LIGAÇÃO DA ATIVIDADE / BS -->
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_atividade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($intCodDado);?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cliente",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"cliente"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"titulo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("situacao",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"situacao")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"categoria")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"prioridade")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("prev_horas",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"prev_horas"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("rendimento",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"rendimento",false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("responsavel",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"id_responsavel"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong><?php echo(getTText("equipe",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($strEquipe);?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("modelo",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"modelo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong>*<?php echo(getTText("descricao_da_atividade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(str_replace("\n","<br />",getValue($objRS,"descricao")));?></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
				<tr>
					<td colspan="2">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<!--td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td><td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td-->
								<td width="35%" align="right">
									<button onClick="ok();"><?php echo(getTText("ok",C_NONE));?></button>
									<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>			
		</td>
	</tr>
	<!-- LINHA ACIMA DOS BOTÕES -->
	</table>
</form>
<?php athEndFloatingBox();?>


<br />
<br />

<?php athBeginWhiteBox("710","","<span style='float:right;'><img src='../img/icon_tree_minus.gif' border='0' style='cursor:pointer;' id='img_bullet_tarefas' onclick=\"showArea('table_tarefas','img_bullet_tarefas');\" /></span><strong>".getTText("todolist",C_UCWORDS)."</strong>",CL_CORBAR_GLASS_2); ?>
<div id="table_tarefas">
<?php
	// VERIFICA SE EXISTE ALGUMA RESPOSTA INSERIDA PARA ESTA TAREFA
	if($objResultT->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","aviso_nenhuma_tarefa","","","info",1,"","");
	} else{
	?>
<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
	<thead>
		<tr>
			<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_todolist",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("categoria",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("situacao",C_TOUPPER));?></th>
			<th width="12%" class="sortable" nowrap><?php echo(getTText("prev_dt_ini_abrev",C_TOUPPER));?></th>
			<th width="05%" class="sortable" nowrap><?php echo(getTText("prev_horas_abrev",C_TOUPPER));?></th>
			<th width="25%" class="sortable" nowrap><?php echo(getTText("titulo",C_TOUPPER));?></th>
			<th width="01%" nowrap></th>
			<th width="01%" nowrap></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($objResultT as $objRSR){?>
		<tr bgcolor="<?php echo(getLineColor($strColor));?>">
			<td style="vertical-align:top;text-align:center;"><?php echo(getValue($objRSR,"cod_todolist"));?></td>
			<td style="vertical-align:top;text-align:center;"><?php echo(strtoupper(getValue($objRSR,"categoria")));?></td>
			<td style="vertical-align:top;text-align:center;"><?php echo(strtoupper(getValue($objRSR,"situacao")));?></td>
			<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;"><?php echo(dDate(CFG_LANG,getValue($objRSR,"prev_dt_ini"),false)." ".getValue($objRSR,"prev_hr_ini"));?></td>
			<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;"><?php echo(getValue($objRSR,"prev_horas"));?></td>
			<td style="vertical-align:top;text-align:left;"><?php echo(getValue($objRSR,"titulo"));?></td>
			<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;">
				<img src="../img/icon_responsavel.gif" border="0" title="<?php echo(getTText("responsavel",C_TOUPPER).": ".getValue($objRSR,"id_responsavel"));?>" />
			</td>
			<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;">
				<img src="../img/icon_executor.gif" border="0" title="<?php echo(getTText("executor",C_TOUPPER).": ".getValue($objRSR,"id_ult_executor"));?>" />
			</td>
		</tr>
	<?php }?>
	</tbody>
</table>
<?php }?>
</div>
<?php athEndWhiteBox();?>
</center>
</body>
</html>