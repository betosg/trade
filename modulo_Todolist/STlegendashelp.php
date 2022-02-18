<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

	// REQUESTS
	// $intCodDado    	 = request("var_chavereg");			 // COD_TAREFA
	// $intCodAtividade = request("var_cod_atividade");	 // COD_ATIVIDADE / BS
	$strLocation	 = request("var_location");
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	// $objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA A TAREFA
	// $objConn->beginTransaction();
	/*try{
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
	}*/
	
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
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					window.close();
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
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginWhiteBox("650","","<strong>".getTText("todolist",C_UCWORDS)." - ".getTText("ajuda",C_UCWORDS)."</strong>",CL_CORBAR_GLASS_1); ?>
<div style="padding:15px;">
	<div style="width:100%;font-weight:bold;font-size:11px;"><?php echo(getTText("listagem_padrao",C_NONE));?></div>
	<div style="width:100%;"><?php echo(getTText("o_que_e_listagem_padrao",C_NONE));?></div>
	
	<br />
	
	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("situacao",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_aberto.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("aberto",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_executando.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("executando",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_fechado.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("fechado",C_NONE));?></div>
		</div>
	</div>
	
	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("prioridade",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_baixa.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("baixa",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_normal.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("normal",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_media.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("media",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_alta.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("alta",C_NONE));?></div>
		</div>
	</div>	

	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("previsao_inicio",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_periodo_antes.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("antecipado",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_periodo_depois.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("atrasado",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_periodo_dia.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("dia_atual",C_NONE));?></div>
		</div>
	</div>
	
	<br />
	
	<div style="width:100%;font-weight:bold;font-size:11px;margin-top:60px;"><?php echo(getTText("painel_tarefas",C_NONE));?></div>
	<div style="width:100%;"><?php echo(getTText("o_que_e_painel_tarefas",C_NONE));?></div>
	
	<br />
	
	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("situacao",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_aberto.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("aberto",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_executando.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("executando",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_situacao_fechado.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("fechado",C_NONE));?></div>
		</div>
	</div>
	
	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("prioridade",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_baixa.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("baixa",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_normal.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("normal",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_media.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("media",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;">
			<span style="float:left;"><img src="../img/icon_prioridade_alta.png" border="0" /></span>
			<div style="padding-top:3px;"><?php echo(getTText("alta",C_NONE));?></div>
		</div>
	</div>	

	<div style="float:left;width:200px;">
		<div style="width:100%;font-weight:bold">
			<img src="../img/BulletBusca.gif" border="0" />
			<?php echo(getTText("previsao_inicio",C_NONE));?>
		</div>
		<div style="width:100%;vertical-align:middle;background-color:#E6E6FA;margin-top:2px;">
			<div style="padding-top:3px;"><?php echo(getTText("linhas_desta_cor_sao",C_NONE)." ".getTText("tarefas_antecipadas",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;background-color:#FFFACD;margin-top:2px;">
			<div style="padding-top:3px;"><?php echo(getTText("linhas_desta_cor_sao",C_NONE)." ".getTText("tarefas_do_dia",C_NONE));?></div>
		</div>
		<div style="width:100%;vertical-align:middle;background-color:#FFB6C1;margin-top:2px;">
			<div style="padding-top:3px;"><?php echo(getTText("linhas_desta_cor_sao",C_NONE)." ".getTText("tarefas_atrasadas",C_NONE));?></div>
		</div>
	</div>
	<div style="width:100%;text-align:right;padding-top:15px;margin-top:70px;border-top:2px solid #CCC;"><button onClick="ok();"><?php echo(getTText("ok",C_NONE));?></button></div>
</div>
<?php athEndWhiteBox();?>
</center>
</body>
</html>