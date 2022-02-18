<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

	// REQUESTS
	// $intCodDado    	 = request("var_chavereg");			 // COD_TAREFA
	// $intCodAtividade = request("var_cod_atividade");	 // COD_ATIVIDADE / BS
	// $intLimitTarefas = request("var_limit_tarefas");
	// $strSituacao	 = request("var_situacao_tarefa");
	// $strTipoUsuario	 = request("var_tipo_tarefa_usuario");
	
	// DEFINES
	// define("ICONES_NUM"     ,4);     // NÚMERO DE ÍCONES DA GRADE
	// define("ICONES_WIDTH"   ,17);    // LARGURA DOS ÍCONES DA GRADE
	// define("GRADE_NUM_ITENS",20);    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
	// define("GRADE_ACAO_DEFAULT",""); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
	// define("LIMIT_DEFAULT",25); 	 // LIMIT DEFAULT DA CONSULTA
	
	// TRATAMENTO PARA ENVIO VAZIO DE VARIAVEIS NO REQUEST
	// $intLimitTarefas = ($intLimitTarefas == "") ? LIMIT_DEFAULT : $intLimitTarefas;
	// $strTipoUsuario	 = ($strTipoUsuario  == "") ? "todos" : $strTipoUsuario;
	
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA A TAREFA
	$objConn->beginTransaction();
	try{
		$strSQLAdiant = "
			SELECT 
				  cod_todolist
				, prev_dt_ini
				, prev_hr_ini
				, titulo
				, id_responsavel
				, id_ult_executor
				, prev_horas
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
			INNER JOIN tl_categoria ON (tl_todolist.cod_categoria = tl_categoria.cod_categoria) 
			WHERE current_date < prev_dt_ini AND situacao <> 'fechado' ";
		$strSQLAdiant .= ($strTipoUsuario == "todos") ? " AND (id_responsavel = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' OR id_ult_executor = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."')" : "";
		$strSQLAdiant .= " ORDER BY prev_dt_ini ASC, situacao ";
		// echo($strSQL);
		$objResultAdiant = $objConn->query($strSQLAdiant);
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
?>

<!-- FOREACH DADOS -->
<?php if($objResultAdiant->rowCount() > 0){?>
	<?php foreach($objResultAdiant as $objRSAdiant){?>
	<div style="width:100%;background-color:#E6E6FA;border:1px solid #CCC;margin-top:5px;cursor:default;">
		<div style="float:left;width:15px;padding:5px;">
			<?php echo(getValue($objRSAdiant,"situacao_grid"));?><br />
			<?php echo(getValue($objRSAdiant,"prioridade_grid"));?>
		</div>
		<div style="width:150px;height:100%;overflow:hidden;text-align:left;padding-top:7px;">
			<strong><?php echo(getValue($objRSAdiant,"titulo"))?></strong>
			<br/>
			<?php echo("(".dDate(CFG_LANG,getValue($objRSAdiant,"prev_dt_ini"),false)." ".getValue($objRSAdiant,"prev_hr_ini").") ");?>
		</div>
	</div>
	<?php }?>
<?php } else{?>
	<div style="width:100%;background-color:#FFF;border:1px solid #CCC;margin-top:5px;color:#999;font-style:italic;padding:10px;cursor:default;"><?php echo(getTText("nenhuma_tarefa",C_NONE)."!");?></div>
<?php }?>