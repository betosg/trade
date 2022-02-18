<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	$intCodDado		= request("var_bs_atividade_modelo");
	$strOpcao 		= request("var_bs_atividade_opcao");
	$dtPrevIni		= request("var_bs_atividade_data_ini");
	$strLocation	= request("DEFAULT_LOCATION");
	$boolMail		= false;
	
	// ABRE OBJETO PARA MANIPULAÇÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
			
	$objConn->beginTransaction();
	try{
		// INSERT DE ATIVIDADE
		$strSQL = "
			INSERT INTO bs_atividade(codigo, tipo, titulo, descricao, id_responsavel, situacao, cod_categoria, prioridade, modelo, sys_usr_ins, sys_dtt_ins)
			SELECT codigo, tipo, titulo, descricao, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' AS id_responsavel, situacao, cod_categoria, prioridade, FALSE, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' AS sys_usr_ins, CURRENT_TIMESTAMP AS sys_dtt_ins FROM bs_atividade WHERE cod_atividade = ".$intCodDado;
		// echo($strSQL);
		$objConn->query($strSQL);
		
		// COLETA ULTIMA ATIVIDADE INSERIDA, PARA INSERCAO DAS TAREFAS
		$objResultA = $objConn->query("SELECT MAX(cod_atividade) AS cod_atividade FROM bs_atividade");
		$objRSA		= $objResultA->fetch();
		
		// SELECT DE TAREFAS PARA INSERCAO
		$strSQL = "SELECT id_responsavel, id_ult_executor, cod_categoria, titulo, descricao, situacao, prioridade, prev_horas, prev_dt_ini, sys_dtt_ins, sys_usr_ins, prev_hr_ini, arquivo_anexo, cod_atividade FROM tl_todolist WHERE cod_atividade = ".$intCodDado;
		$objResultT = $objConn->query($strSQL);
		
		foreach($objResultT as $objRST){
			// INSERÇÃO DE TAREFAS
			$dtPrevIni = ($strOpcao == "S") ? cDate(CFG_LANG,$dtPrevIni,false) : getValue($objRST,"prev_dt_ini");
			$strSQL = "INSERT INTO tl_todolist (id_responsavel, id_ult_executor, cod_categoria, titulo, descricao, situacao, prioridade, prev_horas, prev_dt_ini, sys_dtt_ins, sys_usr_ins, prev_hr_ini, arquivo_anexo, cod_atividade) 
					   VALUES ('".getsession(CFG_SYSTEM_NAME."_id_usuario")."', '".getValue($objRST,"id_ult_executor")."', ".getValue($objRST,"cod_categoria").", '".getValue($objRST,"titulo")."', '".getValue($objRST,"descricao")."', '".getValue($objRST,"situacao")."', '".getValue($objRST,"prioridade")."', '".getValue($objRST,"prev_horas")."', '".$dtPrevIni."', CURRENT_TIMESTAMP, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."', '".getValue($objRST,"prev_hr_ini")."', '".getValue($objRST,"arquivo_anexo")."', ".getValue($objRSA,"cod_atividade").")";
			echo($strSQL);
			$objConn->query($strSQL);
		}
		
		// SELECT DE EQUIPE PARA INSERÇÃO
		$strSQL = "SELECT id_usuario FROM bs_equipe WHERE cod_atividade = ".$intCodDado;
		$objResultE = $objConn->query($strSQL);
		
		foreach($objResultE as $objRSE){
			// INSERÇÃO DE EQUIPE
			$strSQL = "INSERT INTO bs_equipe (id_usuario, sys_dtt_ins, sys_usr_ins, cod_atividade) 
					   VALUES ('".getValue($objRSE,"id_usuario")."', CURRENT_TIMESTAMP, '".getsession(CFG_SYSTEM_NAME."_id_usuario")."',".getValue($objRSA,"cod_atividade").")";
			$objConn->query($strSQL);
		}
		
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// ENVIO DE EMAIL
	if($boolMail){
		// LOCALIZA O EMAIL DO USUÁRIO RESPONSÁVEL E EXECUTOR
		$objConn->beginTransaction();
		try{
			$strSQL = "SELECT email FROM sys_usuario WHERE id_usuario = '".$strIDResp."' OR id_usuario = '".$strIDExec."'";
			$objResult = $objConn->query($strSQL);
			$objConn->commit();
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConn->rollBack();
			die();
		}
		
		// MONTA O CORPO DO EMAIL
		$strBodyEmail  = '';
		$strBodyEmail .= '
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
				<tr>
					<td colspan="2">
						<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
							<tr>
								<td class="td_label">&nbsp;</td>
								<td><strong>'.getTText("insercao_de_tarefa",C_NONE).'</strong></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="td_label">'.getTText("cod_todolist",C_NONE).':</td>
								<td>'.getValue($objRS,"cod_todolist").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("titulo",C_NONE).':</td>
								<td>'.$strTitulo.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("situacao",C_NONE).':</td>
								<td>'.strtoupper($strSituacao).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prioridade",C_NONE).':</td>
								<td>'.strtoupper($strPrioridade).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("responsavel",C_NONE).':</td>
								<td>'.$strIDResp.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("id_ult_executor",C_NONE).':</td>
								<td>'.$strIDExec.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_dt_ini",C_NONE).':</td>
								<td>'.$strPrevDtIni.' '.getTText("as_crase",C_NONE).' '.$strPrevHrIni1.':'.$strPrevHrIni2.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_horas",C_NONE).':</td>
								<td>'.$strPrevHoras1.':'.$strPrevHoras2.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("descricao",C_NONE).':</td>
								<td>'.$strDescricao.'</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
						</table>
					</td>
				</tr>
			</table>';
		
		// CONFIGURA LINHA DE DESTINATÁRIOS
		$strEmailLINE  = "";
		foreach($objResult as $objRS){
			$strEmailLINE .= (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email").",";
			$strEmailLINE  = trim($strEmailLINE,",");
		}
		// echo($strEmailLINE);
		
		// CONFIGURA TÍTULO DO EMAIL / SUBJECT
		$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("insercao_de_tarefa",C_NONE);
		
		// Encaminha o email somente se estiver ONLINE
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
			emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
		}
	}
	
    // REDIRECT para a pagina
	redirect($strLocation);
?>