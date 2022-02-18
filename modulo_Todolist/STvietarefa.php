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
	$intCodDado    	 = request("var_chavereg");			 // COD_TAREFA
	$intCodAtividade = request("var_cod_atividade");	 // COD_ATIVIDADE / BS
	$strLocation	 = request("var_location");
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA A TAREFA
	$objConn->beginTransaction();
	try{
		$strSQL = "
			SELECT 
				  tl_todolist.cod_todolist
				, tl_todolist.titulo
				, tl_todolist.situacao
				, tl_categoria.cod_categoria AS categoria
				, tl_todolist.prioridade
				, tl_todolist.id_responsavel
				, tl_todolist.id_ult_executor
				, tl_todolist.prev_dt_ini
				, tl_todolist.prev_hr_ini
				, tl_todolist.prev_horas
				, tl_todolist.arquivo_anexo
				, tl_todolist.descricao
				, tl_todolist.dt_realizado
				, sys_usuario_responsavel.grp_user AS grupo_responsavel
				, sys_usuario_executor.grp_user AS grupo_executor
			FROM  tl_todolist
			LEFT JOIN tl_categoria ON (tl_categoria.cod_categoria = tl_todolist.cod_categoria)
			LEFT JOIN sys_usuario sys_usuario_responsavel ON (sys_usuario_responsavel.id_usuario = tl_todolist.id_responsavel)
			LEFT JOIN sys_usuario sys_usuario_executor ON (sys_usuario_executor.id_usuario = tl_todolist.id_ult_executor)
			WHERE tl_todolist.cod_todolist = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS 	   = $objResult->fetch();
		
		// LOCALIZA POSSÍVEIS RESPOSTAS PARA ESTA TAREFA
		$strSQL = "				
			SELECT	
				  tl_resposta.cod_resposta
				, tl_resposta.dtt_resposta
				, tl_resposta.id_from
				, tl_resposta.id_to
				, tl_resposta.resposta
				, tl_resposta.horas
				, tl_resposta.arquivo_anexo
			FROM tl_resposta
			INNER JOIN tl_todolist ON (tl_todolist.cod_todolist = tl_resposta.cod_todolist)
			WHERE tl_resposta.cod_todolist = ".$intCodDado."
			ORDER BY dtt_resposta DESC";
		$objResultR = $objConn->query($strSQL);
		
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
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					document.location.href = "../modulo_Todolist/data.php";
				<?php }?>
			}

			function cancelar() {
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					document.location.href = "../modulo_Todolist/data.php";
				<?php }?>
			}
		</script>
	</head>
<body style="margin:10px;<?php echo(($intCodAtividade != "") ? "background-color:#FFF;" : "background-image:url(../img/bgFrame_".CFG_SYSTEM_THEME."_main.jpg);");?>">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("todolist",C_UCWORDS)." - (".getTText("visualizacao",C_NONE).")",CL_CORBAR_GLASS_1); ?>
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
						<strong><?php echo(getTText("dados_da_tarefa",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<!-- PARA LIGAÇÃO DA ATIVIDADE / BS -->
				<?php if($intCodAtividade != ""){?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_atividade_bs",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($intCodAtividade);?></td>
				</tr>
				<?php }?>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_todolist",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($intCodDado);?></td>
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
					<td align="right"><strong>*<?php echo(getTText("responsavel",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"id_responsavel"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("executor",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"id_ult_executor"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(($intCodAtividade != "") ? "*".getTText("prev_dt_ini",C_UCWORDS) : getTText("prev_dt_ini",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(($intCodAtividade != "") ? "*".getTText("prev_horas",C_UCWORDS) : getTText("prev_horas",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"prev_horas"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(($intCodAtividade != "") ? "*".getTText("dt_realizado",C_UCWORDS) : getTText("dt_realizado",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dt_realizado"),false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("anexo",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"arquivo_anexo"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong>*<?php echo(getTText("descricao_da_tarefa",C_UCWORDS));?>:</strong></td>
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
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>

<br />
<br />

<?php athBeginWhiteBox("710","","<span style='float:right;'><img src='../img/icon_tree_minus.gif' border='0' style='cursor:pointer;' id='img_bullet_respostas' onclick=\"showArea('table_respostas','img_bullet_respostas');\" /></span><strong>".getTText("respostas",C_UCWORDS)."</strong>",CL_CORBAR_GLASS_2); ?>
<div id="table_respostas">
<?php
	// VERIFICA SE EXISTE ALGUMA RESPOSTA INSERIDA PARA ESTA TAREFA
	if($objResultR->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","aviso_nenhuma_resposta_tarefa","","","info",1,"","");
	} else{
	?>
<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
	<thead>
		<tr>
			<th width="05%" class="sortable" nowrap><?php echo(getTText("cod_resposta",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("id_from",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("id_to",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("dtt_resposta",C_TOUPPER));?></th>
			<th width="10%" class="sortable" nowrap><?php echo(getTText("horas",C_TOUPPER));?></th>
			<th width="50%" class="sortable" nowrap><?php echo(getTText("resposta",C_TOUPPER));?></th>
			<th width="01%"></th><!-- ANEXO -->
		</tr>
	</thead>
	<tbody>
	<?php foreach($objResultR as $objRSR){?>
		<tr bgcolor="<?php echo(getLineColor($strColor));?>">
			<td style="vertical-align:top;text-align:center;"><?php echo(getValue($objRSR,"cod_resposta"));?></td>
			<td style="vertical-align:top;text-align:center;">
			<?php if(getValue($objRSR,"id_from") == "sistema"){?>
				<span style="color:#AAA;font-size:9px;font-style:italic;"><?php echo(getTText("sistema",C_NONE));?></span>
			<?php } else{?>
				<?php echo(getValue($objRSR,"id_from"));?>
			<?php }?>
			</td>
			<td style="vertical-align:top;text-align:center;">
			<?php if(getValue($objRS,"id_to") == "sistema"){?>
				<span style="color:#AAA;font-size:9px;font-style:italic;"><?php echo(getTText("sistema",C_NONE));?></span>
			<?php } else{?>
				<?php echo(getValue($objRSR,"id_to"));?>
			<?php }?>
			</td>
			<td style="vertical-align:top;text-align:center;font-size:9px;color:#AAA;"><?php echo(dDate(CFG_LANG,getValue($objRSR,"dtt_resposta"),true));?></td>
			<td style="vertical-align:top;text-align:center;"><?php echo(getValue($objRSR,"horas"));?></td>
			<td style="vertical-align:top;text-align:left;"><?php echo(str_replace("\n","<br />",getValue($objRSR,"resposta")));?></td>
			<td style="vertical-align:top;text-align:center;">
			<?php if(getValue($objRSR,"arquivo_anexo") != ""){?>
				<img src="../img/icon_anexo.gif" border="0" style="cursor:pointer;" onClick="AbreJanelaPAGE('../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"));?>/upload/docspf/<?php echo(getValue($objRSR,"arquivo_anexo"));?>','500','500');" />
			<?php }?>				
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
<script type="text/javascript">
	<?php if($intCodAtividade != ""){?>
	resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_atividade")); ?>',200);
	<?php }?>
</script>
</html>