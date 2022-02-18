<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA AS TAREFAS
	$objConn->beginTransaction();
	try{
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
		<style>
			.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
			body{ margin: 10px; background-color:#FFFFFF; } 
			ul{ margin-top: 0px; margin-bottom: 0px; }
			li{ margin-left: 0px; }
		</style>
	</head>
<body bgcolor="#FFFFFF">
	<table cellpadding="0" cellspacing="0" width="100%" class="menu_css">
		<tr>
			<td align="left">
			<?php
				athBeginCssMenu();
					athCssMenuAddItem("","_self",getTText("tarefas",C_TOUPPER),1);
					athBeginCssSubMenu();
						athCssMenuAddItem("../modulo_Todolist/STinstarefa.php?var_cod_atividade=".$intCodDado."&var_location=../modulo_BsAtividade/STifrtarefas.php?var_chavereg=".$intCodDado,"_self",getTText("inserir_tarefa",C_NONE));
					athEndCssSubMenu();
				athEndCssMenu();		
			?>
			</td>
		</tr>
	</table>
	
	<?php
	// VERIFICA EXISTENCIA DE TAREFAS
	if($objResultT->rowCount() == 0) {
		mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc",getTText("nenhuma_tarefa_para_esta_atividade",C_NONE),"","aviso",1,"","");
	} else{
	?>
	<table align="center" cellpadding="0" cellspacing="1" style="width:100%;" class="tablesort">
		<thead>
			<tr>
				<th width="01%" nowrap="nowrap"></th> <!-- EDITAR -->
				<th width="01%" nowrap="nowrap"></th> <!-- VISUALIZAR -->
				<th width="01%" nowrap="nowrap"></th> <!-- RESPOSTAS -->
				<th width="01%" nowrap="nowrap"></th> <!-- FECHAMENTO -->
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
				<td style="vertical-align:top;text-align:center;"><a href="../modulo_Todolist/STupdtarefa.php?var_chavereg=<?php echo(getValue($objRSR,"cod_todolist"));?>&var_cod_atividade=<?php echo($intCodDado);?>&var_location=../modulo_BsAtividade/STifrtarefas.php?var_chavereg=<?php echo($intCodDado);?>" style="border:none;"><img src="../img/icon_write.gif" border="0" style="cursor:pointer;" title="<?php echo(getTText("editar",C_NONE));?>" /></a></td> 
				<td style="vertical-align:top;text-align:center;"><a href="../modulo_Todolist/STvietarefa.php?var_chavereg=<?php echo(getValue($objRSR,"cod_todolist"));?>&var_cod_atividade=<?php echo($intCodDado);?>&var_location=../modulo_BsAtividade/STifrtarefas.php?var_chavereg=<?php echo($intCodDado);?>" style="border:none;"><img src="../img/icon_zoom.gif" border="0" style="cursor:pointer;" title="<?php echo(getTText("visualizar",C_NONE));?>" /></a></td> 
				<td style="vertical-align:top;text-align:center;"><img src="../img/icon_respostas.gif" border="0" title="<?php echo(getTText("respostas",C_NONE));?>" style="cursor:pointer;" onClick="AbreJanelaPAGE('../modulo_Todolist/STifrrespostas.php?var_chavereg=<?php echo(getValue($objRSR,"cod_todolist"));?>',800,700);" /></td>
				<td style="vertical-align:top;text-align:center;"><a href="../modulo_Todolist/STfinalizartarefa.php?var_chavereg=<?php echo(getValue($objRSR,"cod_todolist"));?>&var_cod_atividade=<?php echo($intCodDado);?>&var_location=../modulo_BsAtividade/STifrtarefas.php?var_chavereg=<?php echo($intCodDado);?>" style="border:none;" ><img src="../img/icon_confirmar_homologacao.gif" border="0" title="<?php echo(getTText("finalizar",C_NONE));?>" /></a></td>
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
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>